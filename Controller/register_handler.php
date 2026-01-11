<?php

require_once __DIR__ . '/../model/auth.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	header('Location: ../View/register.php');
	exit();
}

$username = trim($_POST['name'] ?? '');
$password = $_POST['password'] ?? '';

$userModel = new User();
$result = $userModel->register($username, $password);

if ($result['success'] ?? false) {
	$userModel->login($username);
	$_SESSION['success'] = 'Registration successful. You are now logged in.';
	header('Location: ../View/dashboard.php');
	exit();
}

$_SESSION['error'] = $result['message'] ?? 'Registration failed.';
header('Location: ../View/register.php');
exit();

?>
