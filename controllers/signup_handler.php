<?php
session_start();

// Database connection
include '../includes/db_connect.php'; // Adjust this path based on your project structure

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data and sanitize input
    $name = trim($_POST['name']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirmPassword = trim($_POST['cpassword']);

    // Server-side validation
    if (empty($name) || !preg_match("/^[a-zA-Z\s]+$/", $name)) {
        $_SESSION['error'] = 'Invalid name format.';
        header('Location: ../pages/signup.php');
        exit();
    }
    if (empty($username) || !preg_match("/^[a-zA-Z0-9]+$/", $username)) {
        $_SESSION['error'] = 'Invalid username format.';
        header('Location: ../pages/signup.php');
        exit();
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = 'Invalid email format.';
        header('Location: ../pages/signup.php');
        exit();
    }
    if (!preg_match('/[A-Z]/', $password) || 
            !preg_match('/[0-9]/', $password) || 
            !preg_match('/[^a-zA-Z0-9]/', $password) || 
            strlen($password) < 8) {
        $_SESSION['error'] = 'Password must be at least 8 characters, with 1 uppercase letter, 1 number, and 1 special character.';
        header('Location: ../pages/signup.php');
        exit();
    }
    if ($password !== $confirmPassword) {
        $_SESSION['error'] = 'Passwords do not match.';
        header('Location: ../pages/signup.php');
        exit();
    }

    // Hash the password for security using Bcrypt
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // Insert into the database
    try {
        // Prepare the SQL statement
        $stmt = $conn->prepare("INSERT INTO users (name, username, email, password) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $username, $email, $hashedPassword);

        // Execute the statement
        if ($stmt->execute()) {
            // Redirect to a success page or login page
            $_SESSION['success_msg'] = "Account created!";
            header('Location: ../pages/login.php');
        } else {
            $_SESSION['error'] = 'Error inserting data. Please try again.';
            $_SESSION['error_msg'] = "Error inserting data. Please try again.";
            header('Location: ../pages/signup.php');
        }
    } catch (Exception $e) {
        $_SESSION['error'] = 'Database error: ' . $e->getMessage();
        $_SESSION['error_msg'] = 'Database error: ' . $e->getMessage();
        header('Location: ../pages/signup.php');
    }

    $conn->close();
    exit();
}
