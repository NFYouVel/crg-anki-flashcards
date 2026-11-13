<?php
// Returns ONE page (100 rows) of the preview for the chosen filter. Read-only, so release the session lock early.
session_start();
include_once "../../../SQL_Queries/connection.php";
require_once "../Components/SentenceImport.php";

SentenceImport::requireAdmin($con, true);   // 403 for non-admins (the old file had no check at all)
$meta = $_SESSION['sentence_import'] ?? null;
session_write_close();

if (!$meta || !SentenceImport::isAlive($meta)) {
    http_response_code(409);
    exit('<h2 style="color:white">No pending import. Please upload the file again.</h2>');
}

echo SentenceImport::renderPreview(
    $meta,
    (string) ($_GET['mode'] ?? 'preview'),
    (int) ($_GET['page'] ?? 1)
);
