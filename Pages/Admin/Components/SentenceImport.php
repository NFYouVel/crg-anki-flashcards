<?php

declare(strict_types=1);

use OpenSpout\Reader\XLSX\Options;
use OpenSpout\Reader\XLSX\Reader;

/** Error whose message is safe to show to the admin. */
final class SentenceImportException extends RuntimeException {}

/**
 * Low-memory sentence import (no staging table).
 *
 * Flow:
 *   1. parse()   streams the .xlsx row by row (OpenSpout) and appends every row to
 *                <import dir>/rows.jsonl as one line: "V[json]" (valid) or "I[json]" (invalid).
 *                Only counters + a code=>row map live in memory; the PHP session keeps just
 *                {id, backup, total, valid, invalid}.
 *   2. readPage() / renderPreview() read one page (100 rows) from rows.jsonl.
 *   3. commit()  streams rows.jsonl again and inserts the valid rows in batches inside ONE
 *                transaction, then rotates the backups.
 *
 * Peak memory is therefore independent of the number of rows (roughly 10-20 MB).
 */
final class SentenceImport
{
    public const PER_PAGE     = 100;
    public const INSERT_BATCH = 250;     // rows per INSERT statement (250 * 6 = 1500 placeholders)
    public const BACKUP_KEEP  = 7;       // same rotation as before
    public const MAX_ROWS     = 200000;  // safety net against absurd files
    public const MAX_BLANK_RUN = 5000;   // stop after this many consecutive blank rows

    /** Override in tests only. */
    public static ?string $backupDirOverride = null;

    /* ------------------------------------------------------------------ paths */

    /** Folder that holds the permanent backups. Adjust the relative path if your tree differs. */
    public static function backupDir(): string
    {
        // this file lives in <page dir>/Components, the old code used <page dir>/../../../Backup/sentence
        return rtrim(self::$backupDirOverride ?? (__DIR__ . '/../../../../Backup/sentence'), '/');
    }

    public static function tempRoot(): string
    {
        return self::backupDir() . '/temp';
    }

    public static function dirFor(string $id): string
    {
        if (!preg_match('/^imp_[a-f0-9]{16}$/', $id)) {
            throw new SentenceImportException('Invalid import id.');
        }
        return self::tempRoot() . '/' . $id;
    }

    /** @return array{0:string,1:string} [id, absolute dir] */
    public static function createDir(): array
    {
        $id  = 'imp_' . bin2hex(random_bytes(8));
        $dir = self::tempRoot() . '/' . $id;
        if (!is_dir($dir) && !mkdir($dir, 0775, true)) {
            throw new SentenceImportException('Cannot create the temporary import folder.');
        }
        return [$id, $dir];
    }

    /** True while the pending import's files still exist on disk. */
    public static function isAlive(array $meta): bool
    {
        try {
            return is_file(self::dirFor((string) $meta['id']) . '/rows.jsonl');
        } catch (SentenceImportException) {
            return false;
        }
    }

    /** Delete one pending import (only its own folder, never anyone else's). */
    public static function discard(?string $id): void
    {
        if ($id === null || !preg_match('/^imp_[a-f0-9]{16}$/', $id)) {
            return;
        }
        self::removeDir(self::tempRoot() . '/' . $id);
    }

    /** Remove abandoned import folders (older than a day). */
    public static function purgeStale(int $maxAgeSeconds = 86400): void
    {
        foreach (glob(self::tempRoot() . '/imp_*', GLOB_ONLYDIR) ?: [] as $dir) {
            if (filemtime($dir) < time() - $maxAgeSeconds) {
                self::removeDir($dir);
            }
        }
    }

    private static function removeDir(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }
        $it = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($it as $f) {
            $f->isDir() ? @rmdir($f->getPathname()) : @unlink($f->getPathname());
        }
        @rmdir($dir);
    }

    /* ------------------------------------------------------------------ guards */

    /** Returns the admin's user id, or redirects / answers 403 and exits. */
    public static function requireAdmin(mysqli $con, bool $ajax = false): string
    {
        $uid  = (string) ($_SESSION['user_id'] ?? '');
        $role = null;
        if ($uid !== '') {
            $stmt = $con->prepare('SELECT role FROM users WHERE user_id = ?');
            $stmt->bind_param('s', $uid);
            $stmt->execute();
            $role = $stmt->get_result()->fetch_assoc()['role'] ?? null;
            $stmt->close();
        }
        if ((int) $role !== 1) {
            if ($ajax) {
                http_response_code(403);
                exit('Forbidden');
            }
            header('Location: ../Login');
            exit;
        }
        return $uid;
    }

    public static function csrfToken(): string
    {
        return $_SESSION['csrf'] ??= bin2hex(random_bytes(16));
    }

    public static function checkCsrf(string $token): bool
    {
        return isset($_SESSION['csrf']) && hash_equals($_SESSION['csrf'], $token);
    }

    /* ------------------------------------------------------------------ step 1: parse */

    /**
     * Streams $xlsxPath into <$dir>/rows.jsonl and returns the counters.
     *
     * @return array{total:int,valid:int,invalid:int}
     */
    public static function parse(string $xlsxPath, string $dir): array
    {
        $out = fopen($dir . '/rows.jsonl', 'wb');
        if ($out === false) {
            throw new SentenceImportException('Cannot create the row file.');
        }

        // SHOULD_PRESERVE_EMPTY_ROWS keeps $rowNo equal to the Excel row number; the temp folder keeps
        // OpenSpout's own scratch files inside the import folder. OpenSpout 5 takes options in the
        // constructor (read-only object), OpenSpout 4 uses public properties + setTempFolder().
        if (method_exists(Options::class, '__construct')) {
            $options = new Options(SHOULD_PRESERVE_EMPTY_ROWS: true, tempFolder: $dir);
        } else {
            $options = new Options();
            $options->SHOULD_PRESERVE_EMPTY_ROWS = true;
            $options->setTempFolder($dir);
        }
        $reader = new Reader($options);

        $flags     = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
            | JSON_INVALID_UTF8_SUBSTITUTE | JSON_PARTIAL_OUTPUT_ON_ERROR;
        $seen      = [];   // code => first row number (only memory that grows: ~5 MB for 55K rows)
        $valid     = 0;
        $invalid   = 0;
        $invalidId = 1;
        $rowNo     = 0;
        $blankRun  = 0;

        try {
            $reader->open($xlsxPath);
            foreach ($reader->getSheetIterator() as $sheet) {
                if (!$sheet->isActive()) {          // same sheet PhpSpreadsheet's getActiveSheet() used
                    continue;
                }
                foreach ($sheet->getRowIterator() as $row) {
                    $rowNo++;
                    if ($rowNo === 1) {             // header row
                        continue;
                    }

                    $cells = $row->toArray();
                    $f = [];
                    for ($c = 0; $c < 6; $c++) {
                        $f[$c] = self::cellText($cells[$c] ?? null);
                    }

                    if ($f === ['', '', '', '', '', '']) {   // completely blank row: ignore
                        if (++$blankRun > self::MAX_BLANK_RUN) {
                            break;
                        }
                        continue;
                    }
                    $blankRun = 0;

                    if ($valid + $invalid >= self::MAX_ROWS) {
                        throw new SentenceImportException('The file has more than ' . self::MAX_ROWS . ' rows.');
                    }

                    $code    = trim($f[0]);
                    $reasons = [];
                    if ($code === '') {
                        $code      = '#invalidID_' . $invalidId++;
                        $reasons[] = 'Sentence code is empty';
                    } elseif (isset($seen[$code])) {
                        $reasons[] = 'Duplicate sentence code (first used in row ' . $seen[$code] . ')';
                    } else {
                        $seen[$code] = $rowNo;
                    }

                    $ok = $reasons === [];
                    $ok ? $valid++ : $invalid++;

                    fwrite($out, ($ok ? 'V' : 'I') . json_encode(
                        [$rowNo, $code, $f[1], $f[2], $f[3], $f[4], $f[5], implode('; ', $reasons)],
                        $flags
                    ) . "\n");
                }
                break;                              // only the active sheet
            }
        } catch (SentenceImportException $e) {
            throw $e;
        } catch (Throwable $e) {
            error_log('Sentence import parse error: ' . $e->getMessage());
            throw new SentenceImportException('The workbook could not be read. Make sure it is a valid .xlsx file.');
        } finally {
            try {
                $reader->close();
            } catch (Throwable) {
            }
            fclose($out);
        }

        if ($valid + $invalid === 0) {
            throw new SentenceImportException('No data rows were found on the active sheet.');
        }
        return ['total' => $valid + $invalid, 'valid' => $valid, 'invalid' => $invalid];
    }

    private static function cellText(mixed $v): string
    {
        if ($v === null) {
            return '';
        }
        if ($v instanceof DateTimeInterface) {
            return $v->format('Y-m-d H:i:s');
        }
        if (is_bool($v)) {
            return $v ? '1' : '0';
        }
        return (string) $v;
    }

    /* ------------------------------------------------------------------ step 2: preview */

    /** @return list<array{0:string,1:array}> [flag, [row, code, tc, sc, pinyin, en, indo, reason]] */
    private static function readPage(string $id, string $mode, int $page): array
    {
        $fh = @fopen(self::dirFor($id) . '/rows.jsonl', 'rb');
        if ($fh === false) {
            return [];
        }
        $skip = ($page - 1) * self::PER_PAGE;
        $rows = [];
        while (($line = fgets($fh)) !== false) {
            $flag = $line[0];
            if (($mode === 'valid' && $flag !== 'V') || ($mode === 'invalid' && $flag !== 'I')) {
                continue;
            }
            if ($skip > 0) {
                $skip--;
                continue;
            }
            $rows[] = [$flag, json_decode(substr($line, 1), true) ?: []];
            if (count($rows) >= self::PER_PAGE) {
                break;
            }
        }
        fclose($fh);
        return $rows;
    }

    private static function e(mixed $s): string
    {
        return htmlspecialchars((string) $s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    /** One page of the preview (table + pager) as HTML. */
    public static function renderPreview(array $meta, string $mode, int $page): string
    {
        $mode  = in_array($mode, ['preview', 'valid', 'invalid'], true) ? $mode : 'preview';
        $count = match ($mode) {
            'valid'   => (int) $meta['valid'],
            'invalid' => (int) $meta['invalid'],
            default   => (int) $meta['total'],
        };
        $pages = max(1, (int) ceil($count / self::PER_PAGE));
        $page  = min(max(1, $page), $pages);
        $rows  = self::readPage($meta['id'], $mode, $page);

        $caption = match ($mode) {
            'valid'   => "<caption style='background-color: green;'>Valid Sentences</caption>",
            'invalid' => "<caption style='background-color: red;'>Invalid Sentences</caption>",
            default   => "<caption style='background-color: white; color: black;'>Preview</caption>",
        };

        $h  = '<table>' . $caption . '<tr><th class="short">Row</th><th class="short">Code</th><th>Traditional</th>'
            . '<th>Simplified</th><th>Pinyin</th><th>English</th><th>Indo</th>'
            . ($mode === 'invalid' ? '<th>Reason</th>' : '') . '</tr>';

        foreach ($rows as [$flag, $r]) {
            $style = $mode === 'preview' ? ($flag === 'V' ? " style='background-color: green;'" : " style='background-color: red;'") : '';
            $h .= "<tr$style>";
            for ($i = 0; $i <= 6; $i++) {
                $h .= '<td' . ($i < 2 ? ' class="short"' : '') . '>' . self::e($r[$i] ?? '') . '</td>';
            }
            if ($mode === 'invalid') {
                $h .= '<td>' . self::e($r[7] ?? '') . '</td>';
            }
            $h .= '</tr>';
        }
        $h .= '</table>';

        // mode is whitelisted above, page is an int: safe to place in the onclick attributes
        $prev = $page > 1      ? '' : ' disabled';
        $next = $page < $pages ? '' : ' disabled';
        $h .= "<div class='pager'>"
            . "<button class='pgbtn'$prev onclick=\"previewModes('$mode', " . ($page - 1) . ")\">&laquo; Prev</button>"
            . '<span>Page ' . $page . ' of ' . $pages . ' (' . number_format($count) . ' rows)</span>'
            . "<button class='pgbtn'$next onclick=\"previewModes('$mode', " . ($page + 1) . ")\">Next &raquo;</button>"
            . '</div>';
        return $h;
    }

    /* ------------------------------------------------------------------ step 3: commit */

    /**
     * Replaces example_sentence with the valid rows. One transaction, constant memory.
     *
     * @return array{total:int,imported:int,skipped:int,backup:string}
     */
    public static function commit(mysqli $con, array $meta, string $userId): array
    {
        $dir      = self::dirFor($meta['id']);
        $rowsFile = $dir . '/rows.jsonl';
        $backup   = basename((string) $meta['backup']);
        $src      = $dir . '/' . $backup;
        $final    = self::backupDir() . '/' . $backup;

        if (!is_file($rowsFile) || !is_file($src)) {
            throw new SentenceImportException('The upload has expired. Please upload the file again.');
        }
        if ((int) $meta['valid'] < 1) {
            throw new SentenceImportException('There are no valid rows to import; the current data was left untouched.');
        }
        // keep the backup safe first; it is moved back if the database work fails
        if (!rename($src, $final)) {
            throw new SentenceImportException('Could not store the backup file.');
        }

        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);   // mysqli errors become exceptions
        $oldFiles = [];
        $con->begin_transaction();
        try {
            $con->query('DELETE FROM example_sentence');

            $fh = fopen($rowsFile, 'rb');
            $buf = [];
            $imported = 0;
            while (($line = fgets($fh)) !== false) {
                if ($line[0] !== 'V') {
                    continue;
                }
                $r = json_decode(substr($line, 1), true);
                array_push($buf, $r[1], $r[2], $r[3], $r[4], $r[5], $r[6]);
                if (count($buf) === self::INSERT_BATCH * 6) {
                    $imported += self::insertBatch($con, $buf);
                }
            }
            $imported += self::insertBatch($con, $buf);
            fclose($fh);

            if ($imported !== (int) $meta['valid']) {
                throw new RuntimeException("Imported $imported rows, expected {$meta['valid']}");
            }

            // backup bookkeeping (same tables/columns as before)
            $con->query("UPDATE data_backup SET is_current_version = 0 WHERE import_type = 'sentence'");
            $old = $con->query("SELECT import_batch_id, import_batch_name FROM data_backup
                                WHERE import_type = 'sentence' ORDER BY imported_at ASC")->fetch_all(MYSQLI_ASSOC);
            $excess = count($old) - (self::BACKUP_KEEP - 1);          // leave room for the new row
            if ($excess > 0) {
                $del = $con->prepare('DELETE FROM data_backup WHERE import_batch_id = ?');
                foreach (array_slice($old, 0, $excess) as $o) {
                    $del->bind_param('s', $o['import_batch_id']);
                    $del->execute();
                    $oldFiles[] = basename((string) $o['import_batch_name']);
                }
                $del->close();
            }
            $total   = (int) $meta['total'];
            $skipped = (int) $meta['invalid'];
            $ins = $con->prepare(
                "INSERT INTO data_backup (import_type, import_batch_name, total_records, successful_import,
                                          skipped_import, imported_by, is_current_version)
                 VALUES ('sentence', ?, ?, ?, ?, ?, 1)"
            );
            $ins->bind_param('siiis', $backup, $total, $imported, $skipped, $userId);
            $ins->execute();
            $ins->close();

            $con->commit();
        } catch (Throwable $e) {
            $con->rollback();
            @rename($final, $src);                                    // import can be retried
            error_log('Sentence import commit failed: ' . $e->getMessage());
            throw new SentenceImportException('The database import failed and was rolled back. The existing sentences were not changed.');
        }

        foreach ($oldFiles as $f) {                                   // only after a successful commit
            @unlink(self::backupDir() . '/' . $f);
        }
        self::removeDir($dir);

        return ['total' => $total, 'imported' => $imported, 'skipped' => $skipped, 'backup' => $backup];
    }

    /** @param list<string> $buf flat list, 6 values per row; emptied on return */
    private static function insertBatch(mysqli $con, array &$buf): int
    {
        $n = intdiv(count($buf), 6);
        if ($n === 0) {
            return 0;
        }
        $stmt = $con->prepare(
            'INSERT INTO example_sentence (sentence_code, chinese_tc, chinese_sc, pinyin, meaning_eng, meaning_ina) VALUES '
                . implode(',', array_fill(0, $n, '(?,?,?,?,?,?)'))
        );
        $stmt->bind_param(str_repeat('s', $n * 6), ...$buf);
        $stmt->execute();
        $stmt->close();
        $buf = [];
        return $n;
    }
}
