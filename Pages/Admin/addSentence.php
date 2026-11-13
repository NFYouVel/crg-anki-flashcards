<?php
session_start();
ini_set('memory_limit', '256M');   // a runaway request now dies alone instead of getting Apache OOM-killed
set_time_limit(300);

include_once "../../SQL_Queries/connection.php";
require_once "Components/SentenceImport.php";
require_once "../../Composer_Excel/vendor/autoload.php";   // must now contain openspout/openspout

$userId = SentenceImport::requireAdmin($con);              // redirects + exit for non-admins
$csrf   = SentenceImport::csrfToken();
$self   = basename($_SERVER['PHP_SELF']);

// "Cancel" throws away the pending import (only this admin's own temp folder) and leaves the page
if (isset($_GET['cancel'])) {
    SentenceImport::discard($_SESSION['sentence_import']['id'] ?? null);
    unset($_SESSION['sentence_import']);
    header("Location: overview_sentence.php");
    exit;
}

// ---- upload: stream the workbook to disk, keep only a few counters in the session ----
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $error = null;

    if (!isset($_FILES['sentence'])) {
        $error = 'No file received. The file is probably larger than post_max_size.';
    } elseif ($_FILES['sentence']['error'] !== UPLOAD_ERR_OK) {
        $error = 'Upload failed (PHP upload error ' . (int) $_FILES['sentence']['error'] . '). Check upload_max_filesize and post_max_size.';
    } elseif (strtolower(pathinfo($_FILES['sentence']['name'], PATHINFO_EXTENSION)) !== 'xlsx') {
        $error = 'Only .xlsx files are supported. Open the file in Excel and use Save As .xlsx.';
    } else {
        // replace this admin's previous pending import and clean up abandoned ones
        SentenceImport::discard($_SESSION['sentence_import']['id'] ?? null);
        unset($_SESSION['sentence_import']);
        SentenceImport::purgeStale();

        try {
            [$id, $dir] = SentenceImport::createDir();
            date_default_timezone_set('Asia/Jakarta');
            $safeUid = preg_replace('/[^A-Za-z0-9_-]/', '', $userId);
            $backup  = 'CRG_backup_sentence_' . date('ymd_Hi') . '_' . $safeUid . '_' . substr($id, 4, 6) . '.xlsx';

            if (!move_uploaded_file($_FILES['sentence']['tmp_name'], "$dir/$backup")) {
                throw new SentenceImportException('Could not save the uploaded file.');
            }
            $counts = SentenceImport::parse("$dir/$backup", $dir);
            $_SESSION['sentence_import'] = ['id' => $id, 'backup' => $backup] + $counts;
        } catch (SentenceImportException $e) {
            if (isset($id)) {
                SentenceImport::discard($id);
            }
            $error = $e->getMessage();
        }
    }

    if ($error !== null) {
        $_SESSION['sentence_import_error'] = $error;
    }
    header("Location: $self");     // post/redirect/get: refreshing the page no longer re-uploads the file
    exit;
}

// ---- display ----
$meta  = $_SESSION['sentence_import'] ?? null;
$flash = $_SESSION['sentence_import_error'] ?? null;
unset($_SESSION['sentence_import_error']);
if ($meta && !SentenceImport::isAlive($meta)) {
    unset($_SESSION['sentence_import']);
    $meta  = null;
    $flash = 'The pending upload has expired. Please upload the file again.';
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Sentence</title>
    <link rel="icon" href="../../Assets/Icons/1080.png">
    <style>
        h1 {
            color: white;
        }

        #header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        #form {
            display: flex;
            gap: 16px;
        }

        .button {
            font-family: 'Arial', sans-serif;
            width: 200px;
            height: 50px;
            background-color: #ffa72a;
            border-radius: 25px;
            font-size: 24px;
            color: black;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
        }

        .button:disabled {
            opacity: .5;
            cursor: default;
        }

        table {
            width: 100%;
            font-size: 20px;
            border-collapse: collapse;
            margin-bottom: 24px;
        }

        th {
            color: white;
            background-color: #003b58;
            position: sticky;
            z-index: 200;
            top: 0;
        }

        th,
        td {
            border: 2px solid black;
            padding: 5px 10px;
            white-space: normal;
            word-break: break-word;
        }

        .short {
            word-break: normal;
        }

        td {
            padding: 5px;
        }

        tr:nth-child(even) {
            background-color: #838383;
        }

        tr:nth-child(odd) {
            background-color: #a5a5a5;
        }

        caption {
            color: white;
            border: 2px solid black;
        }

        select {
            appearance: none;
            width: 250px;
            padding: 10px 16px;
            border: 2px solid #e9a345;
            border-radius: 12px;
            background-color: white;
            font-size: 18px;
            color: #333;
            cursor: pointer;
        }

        select:focus {
            outline: none;
            border-color: #ffa72a;
            box-shadow: 0 0 5px #ffa72a;
        }

        select option[disabled] {
            color: #999;
        }

        .pager {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 24px;
            color: white;
            font-size: 20px;
            margin-bottom: 48px;
        }

        .pgbtn {
            padding: 8px 20px;
            border: 0;
            border-radius: 20px;
            background-color: #ffa72a;
            font-size: 18px;
            cursor: pointer;
        }

        .pgbtn:disabled {
            opacity: .4;
            cursor: default;
        }

        #summary p {
            color: white;
            font-size: 24px;
        }

        #loadingScreen {
            background-color: #262626;
            position: fixed;
            border-radius: 24px;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            width: 25%;
            height: 50%;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            display: none;
        }
    </style>
    <script>
        const CSRF = <?php echo json_encode($csrf); ?>;

        // Loads ONE page (100 rows) of the preview; the server filters by mode.
        async function previewModes(mode, page) {
            try {
                const res = await fetch("AJAX/sentencePreviewMode.php?mode=" + encodeURIComponent(mode) + "&page=" + (page || 1), {
                    credentials: "same-origin"
                });
                if (!res.ok) throw new Error("HTTP " + res.status);
                document.getElementById("tables").innerHTML = await res.text();
                document.getElementById("filter").value = mode;
            } catch (e) {
                alert("Could not load the preview: " + e.message);
            }
        }

        async function uploadSentences() {
            const importButton = document.getElementById("importButton");
            importButton.disabled = true; // no double submit
            document.getElementById("loadingScreen").style.display = "flex";
            try {
                const res = await fetch("AJAX/batch_sentences.php", {
                    method: "POST",
                    credentials: "same-origin",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded"
                    },
                    body: "csrf=" + encodeURIComponent(CSRF)
                });
                let data = null;
                try {
                    data = await res.json();
                } catch (_) {}
                if (!res.ok || !data || !data.success) {
                    throw new Error((data && data.message) || ("Import failed (HTTP " + res.status + ")"));
                }

                const box = document.getElementById("tables");
                box.replaceChildren();
                const wrap = document.createElement("div");
                wrap.id = "summary";
                [
                    ["Sentences imported", data.imported],
                    ["Invalid rows skipped", data.skipped],
                    ["Total rows in file", data.total]
                ].forEach(([label, value]) => {
                    const p = document.createElement("p");
                    p.textContent = label + ": " + Number(value).toLocaleString();
                    wrap.appendChild(p);
                });
                box.appendChild(wrap);

                document.getElementById("cancel").innerText = "Back";
                document.getElementById("cancel").href = "overview_sentence.php"; // import is done: nothing to discard
                importButton.style.display = "none";
                document.getElementById("filter").style.display = "none";
            } catch (e) {
                importButton.disabled = false;
                alert(e.message);
            } finally {
                document.getElementById("loadingScreen").style.display = "none";
            }
        }
    </script>
</head>

<body>
    <?php include "Components/sidebar.php"; ?>
    <div id="loadingScreen">
        <img src="Components/loading.gif" alt="">
        <h1>Importing</h1>
    </div>
    <div id="container">
        <div id="header">
            <h1>Add Sentence (Preview)</h1>
            <?php if ($meta) { ?>
                <select id="filter" onchange="previewModes(this.value, 1)">
                    <option value="preview">Preview (Default)</option>
                    <option value="valid">Valid Sentences</option>
                    <option value="invalid">Invalid Sentences</option>
                </select>
            <?php } ?>
            <div id="form">
                <a href="<?php echo htmlspecialchars($self); ?>?cancel=1" id="cancel" class="button">Cancel</a>
                <?php if ($meta) { ?>
                    <button class="button" id="importButton" onclick="uploadSentences()">Confirm</button>
                <?php } ?>
            </div>
        </div>
        <?php if ($flash) { ?>
            <h1><?php echo htmlspecialchars($flash); ?></h1>
        <?php } ?>
        <?php if ($meta) { ?>
            <h1 style="display: flex; justify-content: space-evenly;">
                <span>Total Sentences: <?php echo number_format($meta['total']); ?></span>
                <span>Valid Sentences: <?php echo number_format($meta['valid']); ?></span>
                <span>Invalid Sentences: <?php echo number_format($meta['invalid']); ?></span>
            </h1>
            <div id="tables">
                <?php echo SentenceImport::renderPreview($meta, 'preview', 1); ?>
            </div>
        <?php } else if (!$flash) { ?>
            <h1>No import in progress. Upload an .xlsx file from the overview page.</h1>
        <?php } ?>
    </div>
    <style>
        #sentence {
            color: #ffa72a;
        }

        #sentence+ul {
            display: block;
        }

        #overview_sentence {
            color: #ffa72a;
        }
    </style>
</body>

</html>