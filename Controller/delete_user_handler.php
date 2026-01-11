<?php

require_once __DIR__ . '/../model/auth.php';

session_start();

// Check if user is logged in
$userModel = new User();
if (!$userModel->isLoggedIn()) {
    header('Location: ../index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'] = 'Invalid request method';
    header('Location: ../View/userdetails.php');
    exit();
}

$userId = $_POST['user_id'] ?? '';

$result = $userModel->deleteUser($userId);

if ($result['success']) {
    $_SESSION['success'] = $result['message'];
} else {
    $_SESSION['error'] = $result['message'];
}

header('Location: ../View/userdetails.php');
exit();

?>
