<?php
class User
{
    private $conn;

    public function __construct()
    {
        $this->conn = require __DIR__ . '/db.php';
    }

    public function authenticate($username, $password)
    {
        if (!$this->conn) {
            return false;
        }
        
        $stmt = $this->conn->prepare("SELECT password FROM users WHERE username = ?");
        if (!$stmt) {
            return false;
        }
        
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            $stmt->close();
            return false;
        }
        
        $user = $result->fetch_assoc();
        $stmt->close();
        
        return password_verify($password, $user['password']);
    }

    public function register($username, $password)
    {
        if (!$this->conn) {
            return ['success' => false, 'message' => 'Database connection failed'];
        }

        $username = trim($username);
        if ($username === '' || $password === '') {
            return ['success' => false, 'message' => 'Username and password are required'];
        }

        $checkStmt = $this->conn->prepare("SELECT id FROM users WHERE username = ? LIMIT 1");
        if (!$checkStmt) {
            return ['success' => false, 'message' => 'Unable to prepare user lookup'];
        }

        $checkStmt->bind_param("s", $username);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();
        if ($checkResult && $checkResult->num_rows > 0) {
            $checkStmt->close();
            return ['success' => false, 'message' => 'Username already exists'];
        }
        $checkStmt->close();

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $insertStmt = $this->conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        if (!$insertStmt) {
            return ['success' => false, 'message' => 'Unable to prepare user creation'];
        }

        $insertStmt->bind_param("ss", $username, $hashedPassword);
        $created = $insertStmt->execute();
        $insertStmt->close();

        if (!$created) {
            return ['success' => false, 'message' => 'Failed to create user'];
        }

        return ['success' => true, 'message' => 'User created'];
    }

    public function login($username)
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['user'] = $username;
        $_SESSION['logged_in'] = true;
        $_SESSION['login_time'] = time();
    }

    public function isLoggedIn()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }

    public function logout()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        session_unset();
        session_destroy();
    }

    public function getAllUsers()
    {
        if (!$this->conn) {
            return [];
        }

        $stmt = $this->conn->prepare("SELECT id, username, created_at FROM users ORDER BY id DESC");
        if (!$stmt) {
            return [];
        }

        $stmt->execute();
        $result = $stmt->get_result();
        $users = [];

        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }

        $stmt->close();
        return $users;
    }

    public function deleteUser($userId)
    {
        if (!$this->conn) {
            return ['success' => false, 'message' => 'Database connection failed'];
        }

        if (empty($userId) || !is_numeric($userId)) {
            return ['success' => false, 'message' => 'Invalid user ID'];
        }

        $stmt = $this->conn->prepare("DELETE FROM users WHERE id = ?");
        if (!$stmt) {
            return ['success' => false, 'message' => 'Unable to prepare delete statement'];
        }

        $stmt->bind_param("i", $userId);
        $deleted = $stmt->execute();
        $affectedRows = $stmt->affected_rows;
        $stmt->close();

        if (!$deleted || $affectedRows === 0) {
            return ['success' => false, 'message' => 'User not found or could not be deleted'];
        }

        return ['success' => true, 'message' => 'User deleted successfully'];
    }

    public function updateUser($userId, $username, $password = null)
    {
        if (!$this->conn) {
            return ['success' => false, 'message' => 'Database connection failed'];
        }

        if (empty($userId) || !is_numeric($userId)) {
            return ['success' => false, 'message' => 'Invalid user ID'];
        }

        $username = trim($username);
        if ($username === '') {
            return ['success' => false, 'message' => 'Username cannot be empty'];
        }

        // Check if username already exists for a different user
        $checkStmt = $this->conn->prepare("SELECT id FROM users WHERE username = ? AND id != ? LIMIT 1");
        if (!$checkStmt) {
            return ['success' => false, 'message' => 'Unable to check username availability'];
        }

        $checkStmt->bind_param("si", $username, $userId);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();
        if ($checkResult && $checkResult->num_rows > 0) {
            $checkStmt->close();
            return ['success' => false, 'message' => 'Username already taken'];
        }
        $checkStmt->close();

        // Update with or without password
        if (!empty($password)) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $updateStmt = $this->conn->prepare("UPDATE users SET username = ?, password = ? WHERE id = ?");
            if (!$updateStmt) {
                return ['success' => false, 'message' => 'Unable to prepare update statement'];
            }
            $updateStmt->bind_param("ssi", $username, $hashedPassword, $userId);
        } else {
            $updateStmt = $this->conn->prepare("UPDATE users SET username = ? WHERE id = ?");
            if (!$updateStmt) {
                return ['success' => false, 'message' => 'Unable to prepare update statement'];
            }
            $updateStmt->bind_param("si", $username, $userId);
        }

        $updated = $updateStmt->execute();
        $affectedRows = $updateStmt->affected_rows;
        $updateStmt->close();

        if (!$updated) {
            return ['success' => false, 'message' => 'Failed to update user'];
        }

        return ['success' => true, 'message' => 'User updated successfully'];
    }
}
