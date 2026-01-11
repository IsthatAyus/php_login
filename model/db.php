<?php
$conn = new mysqli(
    "127.0.0.1",
    "phpuser",
    "StrongPass123!",
    "testdb"
);

if ($conn->connect_error) {
    die("DB error");
}

return $conn;
?>