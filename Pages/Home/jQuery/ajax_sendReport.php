<?php
    session_start();
    include "../../../SQL_Queries/connection.php";

    
    $user_id = $_SESSION["user_id"];
    if (isset($_GET['reason']) && !empty($_GET['reason'])) {
        $reasons = $_GET['reason'];
        $reasons_text = implode(", ", $reasons);
    }
    
    if (isset($_GET['details']) && !empty($_GET['details'])) {
        $details = $_GET['details'];
    }
    $sentence_code = $_GET['sentence_code'];
    
    if (mysqli_num_rows(mysqli_query($con, "SELECT * FROM sentence_report WHERE user_id = '$user_id' AND sentence_code = '$sentence_code' AND report_status = 'pending'")) !== 0) {
        echo "You have reported this sentence!";
        exit;
    }

    $sql = "INSERT INTO sentence_report (user_id, sentence_code, reason, details, created_at) 
            VALUES (?, ?, ?, ?, NOW())";

    $stmt = $con->prepare($sql);
    $stmt->bind_param("ssss",$user_id, $sentence_code, $reasons_text, $details);

    if ($stmt->execute()) {
        echo "Your report has been successfully sent, we will process it as soon as possible, Thank you.";
    }

    $stmt->close();
    $con->close();
    
?>