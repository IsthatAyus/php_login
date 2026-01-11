<?php

require_once __DIR__ . '/../model/auth.php';

session_start();

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['name'] ?? '');  
    $password = $_POST['password'] ?? '';


    $userModel = new User();

    if ($userModel->authenticate($username, $password)) {
        $userModel->login($username);
        header('Location: ../View/dashboard.php');
        exit();
    } else {
        $_SESSION['error'] = 'Invalid username or password.';
        header('Location: ../index.php'); 
        exit();
    }
} else {
    header('Location: ../index.php'); 
    exit();
}
?>