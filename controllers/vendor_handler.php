<?php
session_start();
include '../includes/db_connect.php';

$action = $_GET['action'] ?? null;

$role = $_SESSION['role'] ?? '';

if ($role !== 'admin') {
    header("Location: ../index.php");
    exit();
}

function validateVendorData($data, $isUpdate = false) {
    $errors = [];

    // Required fields for adding or updating
    if (!$isUpdate && empty($data['username'])) $errors[] = "Username is required.";
    if (empty($data['email'])) $errors[] = "Email is required.";
    elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email format.";

    if (!$isUpdate) {
        if (empty($data['password']) || strlen($data['password']) < 8) $errors[] = "Password is required and must be at least 8 characters.";
        if ($data['password'] !== $data['cpassword']) $errors[] = "Passwords do not match.";
    }

    if (empty($data['vendor_name'])) $errors[] = "Name is required.";
    if (empty($data['business_name'])) $errors[] = "Business name is required.";
    if (empty($data['contact_number']) || !preg_match("/^[0-9]{8}$/", $data['contact_number'])) {
        $errors[] = "Contact number is required and must be between 8 digits.";
    }

    return $errors;
}

if ($action === 'add') {
    // Retrieve and sanitize form inputs
    $data = [
        'username' => trim($_POST['username'] ?? ''),
        'email' => trim($_POST['email'] ?? ''),
        'password' => $_POST['password'] ?? '',
        'cpassword' => $_POST['cpassword'] ?? '',
        'vendor_name' => trim($_POST['vendor_name'] ?? ''),
        'business_name' => trim($_POST['business_name'] ?? ''),
        'contact_number' => trim($_POST['contact_number'] ?? '')
    ];

    $errors = validateVendorData($data);

    if (empty($errors)) {
        try {
            $conn->begin_transaction();
            $hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT);

            // Insert into users table
            $stmt = $conn->prepare("INSERT INTO users (username, name, email, password, role) VALUES (?, ?, ?, ?, 'vendor')");
            $stmt->bind_param("ssss", $data['username'], $data['vendor_name'], $data['email'], $hashedPassword);
            if (!$stmt->execute()) throw new Exception("Failed to insert user details.");
            $userId = $stmt->insert_id;
            $stmt->close();

            // Insert into vendors table
            $stmt = $conn->prepare("INSERT INTO vendors (user_id, business_name, contact_number) VALUES (?, ?, ?)");
            $stmt->bind_param("iss", $userId, $data['business_name'], $data['contact_number']);
            if (!$stmt->execute()) throw new Exception("Failed to insert vendor details.");
            $stmt->close();

            $conn->commit();
            $_SESSION['success_msg'] = "Vendor added successfully!";
        } catch (Exception $e) {
            $conn->rollback();
            $_SESSION['error_msg'] = "Error adding vendor: " . $e->getMessage();
        }
    } else {
        $_SESSION['error_msg'] = implode("<br>", $errors);
    }
} elseif ($action === 'update') {
    // Retrieve and sanitize form inputs
    $data = [
        'user_id' => $_POST['user_id'] ?? null,
        'username' => trim($_POST['username'] ?? ''),
        'email' => trim($_POST['email'] ?? ''),
        'vendor_name' => trim($_POST['vendor_name'] ?? ''),
        'business_name' => trim($_POST['business_name'] ?? ''),
        'contact_number' => trim($_POST['contact_number'] ?? '')
    ];

    $errors = validateVendorData($data, true);

    if (empty($errors)) {
        try {
            $conn->begin_transaction();

            // Update users table
            $stmt = $conn->prepare("UPDATE users SET username = ?, name = ?, email = ? WHERE id = ?");
            $stmt->bind_param("sssi", $data['username'], $data['vendor_name'], $data['email'], $data['user_id']);
            if (!$stmt->execute()) throw new Exception("Failed to update user details.");
            $stmt->close();

            // Update vendors table
            $stmt = $conn->prepare("UPDATE vendors SET business_name = ?, contact_number = ? WHERE user_id = ?");
            $stmt->bind_param("ssi", $data['business_name'], $data['contact_number'], $data['user_id']);
            if (!$stmt->execute()) throw new Exception("Failed to update vendor details.");
            $stmt->close();

            $conn->commit();
            $_SESSION['success_msg'] = "Vendor updated successfully!";
        } catch (Exception $e) {
            $conn->rollback();
            $_SESSION['error_msg'] = "Error updating vendor: " . $e->getMessage();
        }
    } else {
        $_SESSION['error_msg'] = implode("<br>", $errors);
    }
} elseif ($action === 'delete') {
    $userId = $_GET['user_id'] ?? null;

    if ($userId) {
        try {
            $conn->begin_transaction();

            // Delete from vendors table
            $stmt = $conn->prepare("DELETE FROM vendors WHERE user_id = ?");
            $stmt->bind_param("i", $userId);
            if (!$stmt->execute()) throw new Exception("Failed to delete from vendors table.");
            $stmt->close();

            // Delete from users table
            $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
            $stmt->bind_param("i", $userId);
            if (!$stmt->execute()) throw new Exception("Failed to delete from users table.");
            $stmt->close();

            $conn->commit();
            $_SESSION['success_msg'] = "Vendor deleted successfully!";
        } catch (Exception $e) {
            $conn->rollback();
            $_SESSION['error_msg'] = "Error deleting vendor: " . $e->getMessage();
        }
    } else {
        $_SESSION['error_msg'] = "Failed to delete vendor!";
    }
}

$tab = $_GET['tab'] ?? 'tab-vendors';
header("Location: ../pages/admin_dashboard.php?tab=$tab");
exit();
