<?php
session_start();

$_SESSION["rolePage"] = ($_SESSION["rolePage"] === "Teacher") ? "Student" : "Teacher";

echo $_SESSION["rolePage"];
echo "ok";
exit;
?>