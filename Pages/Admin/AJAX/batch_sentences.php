<?php
// Confirm button: streams the validated rows into example_sentence (one transaction) and returns a small JSON summary.
session_start();
ini_set('memory_limit', '256M');
set_time_limit(300);
header('Content-Type: application/json; charset=utf-8');

include_once "../../../SQL_Queries/connection.php";
require_once "../Components/SentenceImport.php";

function reply(int $status, array $payload): never
{
    http_response_code($status);
    echo json_encode($payload);
    exit;
}

$userId = SentenceImport::requireAdmin($con, true);

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !SentenceImport::checkCsrf((string) ($_POST['csrf'] ?? ''))) {
    reply(403, ['success' => false, 'message' => 'Invalid request.']);
}

$meta = $_SESSION['sentence_import'] ?? null;
if (!$meta) {
    reply(409, ['success' => false, 'message' => 'No pending import (it may already have been imported).']);
}

// Take the import out of the session BEFORE the long work and release the session lock:
// a double click / second tab finds nothing to import, and other pages are not blocked meanwhile.
unset($_SESSION['sentence_import']);
session_write_close();

try {
    $summary = SentenceImport::commit($con, $meta, $userId);
    reply(200, ['success' => true, 'message' => 'Sentence import completed.'] + $summary);
} catch (SentenceImportException $e) {
    $message = $e->getMessage();
} catch (Throwable $e) {
    error_log('batch_sentences: ' . $e->getMessage());
    $message = 'Unexpected error. The existing sentences were not changed.';
}

// failure: put the import back so the admin can retry (files are still on disk if commit() rolled back)
if (SentenceImport::isAlive($meta)) {
    session_start();
    $_SESSION['sentence_import'] = $meta;
    session_write_close();
}
reply(500, ['success' => false, 'message' => $message]);
