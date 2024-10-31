<?php
session_start();
include '../includes/db_connect.php';

$action = $_GET['action'] ?? null;

if ($action === 'add') {
    // Retrieve and sanitize form inputs
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $cpassword = $_POST['cpassword'] ?? '';
    $name = trim($_POST['vendor_name'] ?? '');
    $businessName = trim($_POST['business_name'] ?? '');
    $contactNumber = trim($_POST['contact_number'] ?? '');

    // Validate inputs
    $errors = [];

    if (empty($username)) $errors[] = "Username is required.";
    if (empty($email)) $errors[] = "Email is required.";
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email format.";
    
    if (empty($password) || strlen($password) < 8) $errors[] = "Password is required and must be at least 8 characters.";
    if ($password !== $cpassword) $errors[] = "Passwords do not match.";

    if (empty($name)) $errors[] = "Name is required.";
    if (empty($businessName)) $errors[] = "Business name is required.";
    if (empty($contactNumber) || !preg_match("/^[0-9]{8}$/", $contactNumber)) {
        $errors[] = "Contact number is required and must be 8 digits.";
    }

    if (count($errors) === 0) {
        try {
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

            // Insert into users table
            $stmt = $conn->prepare("INSERT INTO users (username, name, email, password, role) VALUES (?, ?, ?, ?, 'vendor')");
            $stmt->bind_param("ssss", $username, $name, $email, $hashedPassword);
            $stmt->execute();
            $userId = $stmt->insert_id;
            $stmt->close();

            // Insert into vendors table
            $stmt = $conn->prepare("INSERT INTO vendors (user_id, business_name, contact_number) VALUES (?, ?, ?)");
            $stmt->bind_param("iss", $userId, $businessName, $contactNumber);
            $stmt->execute();
            $stmt->close();

            $_SESSION['success_msg'] = "Vendor added successfully!";
        } catch (Exception $e) {
            $_SESSION['error_msg'] = "Error adding vendor: " . $e->getMessage();
        }
    } else {
        $_SESSION['error_msg'] = implode("<br>", $errors);
    }
} elseif ($action === 'update') {
    // Retrieve and sanitize form inputs for updating
    $userId = $_POST['user_id'] ?? null;
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $name = trim($_POST['vendor_name'] ?? '');
    $businessName = trim($_POST['business_name'] ?? '');
    $contactNumber = trim($_POST['contact_number'] ?? '');

    // Validate inputs
    $errors = [];

    if (empty($userId)) $errors[] = "User ID is required.";
    if (empty($username)) $errors[] = "Username is required.";
    if (empty($email)) $errors[] = "Email is required.";
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email format.";
    
    if (empty($name)) $errors[] = "Name is required.";
    if (empty($businessName)) $errors[] = "Business name is required.";
    if (empty($contactNumber) || !preg_match("/^[0-9]{8,15}$/", $contactNumber)) {
        $errors[] = "Contact number is required and must be between 8-15 digits.";
    }

    if (count($errors) === 0) {
        try {
            // Update users table
            $stmt = $conn->prepare("UPDATE users SET username = ?, name = ?, email = ? WHERE id = ?");
            $stmt->bind_param("sssi", $username, $name, $email, $userId);
            $stmt->execute();
            $stmt->close();

            // Update vendors table
            $stmt = $conn->prepare("UPDATE vendors SET business_name = ?, contact_number = ? WHERE user_id = ?");
            $stmt->bind_param("ssi", $businessName, $contactNumber, $userId);
            $stmt->execute();
            $stmt->close();

            $_SESSION['success_msg'] = "Vendor updated successfully!";
        } catch (Exception $e) {
            $_SESSION['error_msg'] = "Error updating vendor: " . $e->getMessage();
        }
    } else {
        $_SESSION['error_msg'] = implode("<br>", $errors);
    }
} elseif ($action === 'delete') {
    // Deleting a vendor
    $userId = $_GET['user_id'] ?? null;

    if ($userId) {
        try {
            // Delete from vendors table
            $stmt = $conn->prepare("DELETE FROM vendors WHERE user_id = ?");
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $stmt->close();

            // Delete from users table
            $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $stmt->close();

            $_SESSION['success_msg'] = "Vendor deleted successfully!";
        } catch (Exception $e) {
            $_SESSION['error_msg'] = "Error deleting vendor: " . $e->getMessage();
        }
    } else {
        $_SESSION['error_msg'] = "Failed to delete vendor!";
    }
}
$tab = $_GET['tab'] ?? 'tab-locations';

header("Location: ../pages/admin_dashboard.php?tab=$tab");
exit();
