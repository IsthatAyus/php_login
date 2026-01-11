<?php

$targetDir = "../assets/uploads/";

$targetFile = $targetDir .basename($_FILES["file"]["name"]);

$tempFile = $_FILES["file"]["tmp_name"];

if(move_uploaded_file($tempFile, $targetFile)) {
    header("Location: ../View/dashboard.php");
    exit;
} else {
    echo "Sorry, there was an error uploading your file.";
}

?>