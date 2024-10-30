<?php
session_start();

// Database connection
include '../includes/db_connect.php'; // Adjust this path based on your project structure

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data and sanitize input
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Validate input
    if (empty($username) || empty($password)) {
        $_SESSION['error'] = 'Please enter both username and password.';
        $_SESSION['error_msg'] = 'Please enter both username and password.';
        header('Location: ../pages/login.php');
        exit();
    }

    // Fetch user from database
    try {
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            // Verify password
            if (password_verify($password, $user['password'])) {
                // Password is correct, start session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];

                // Redirect to a dashboard or homepage
                header('Location: ../index.php');
            } else {
                // Invalid password
                $_SESSION['error'] = 'Invalid username or password.';
                $_SESSION['error_msg'] = 'Invalid username or password.';
                header('Location: ../pages/login.php');
            }
        } else {
            // Username does not exist
            $_SESSION['error'] = 'Invalid username or password.';
            $_SESSION['error_msg'] = 'Invalid username or password.';
            header('Location: ../pages/login.php');
        }
    } catch (Exception $e) {
        $_SESSION['error'] = 'Database error: ' . $e->getMessage();
        $_SESSION['error_msg'] = 'Database error: ' . $e->getMessage();
        header('Location: ../pages/login.php');
    }

    $conn->close();
    exit();
}