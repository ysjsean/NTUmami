<?php
session_start();
include '../includes/db_connect.php';

$userId = $_SESSION['user_id'] ?? null;

$role = $_SESSION['role'] ?? '';

if ($role !== 'vendor') {
    header("Location: ../index.php");
    exit();
}

if (!$userId || !isset($_GET['action']) || $_GET['action'] !== 'update') {
    header('Location: ../pages/vendor_dashboard.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $businessName = $_POST['business_name'] ?? '';
    $contactNumber = $_POST['contact_number'] ?? '';
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    $errors = [];

    // Basic field validation
    if (empty($name)) $errors[] = "Name is required.";
    if (empty($businessName)) $errors[] = "Business Name is required.";
    if (empty($contactNumber)) $errors[] = "Contact Number is required.";
    if (empty($contactNumber) || !preg_match("/^[689]\d{7}$/", $contactNumber)) {
        $errors[] = "Contact number must be a valid Singapore phone number.";
    }

    // Check if password change is requested
    if ($newPassword || $confirmPassword || $currentPassword) {
        if (empty($currentPassword)) {
            $errors[] = "Current password is required to set a new password.";
        } elseif ($newPassword !== $confirmPassword) {
            $errors[] = "New password and confirmation password do not match.";
        } elseif (!preg_match('/[A-Z]/', $password) || 
                    !preg_match('/[0-9]/', $password) || 
                    !preg_match('/[^a-zA-Z0-9]/', $password) || 
                    strlen($password) < 8) {
            $errorMessage = "Password must be at least 8 characters, with 1 uppercase letter, 1 number, and 1 special character.";
        } else {
            // Verify current password
            $user = $conn->query("SELECT password FROM users WHERE id = $userId")->fetch_assoc();
            if (!password_verify($currentPassword, $user['password'])) {
                $errors[] = "Current password is incorrect.";
            }
        }
    }

    // If no errors, proceed with the update
    if (empty($errors)) {
        $conn->begin_transaction();
        try {
            // Update user info
            $stmt = $conn->prepare("UPDATE users SET name = ? WHERE id = ?");
            $stmt->bind_param("si", $name, $userId);
            $stmt->execute();

            // Update vendor info
            $stmt = $conn->prepare("UPDATE vendors SET business_name = ?, contact_number = ? WHERE user_id = ?");
            $stmt->bind_param("ssi", $businessName, $contactNumber, $userId);
            $stmt->execute();

            // Update password if provided
            if ($newPassword) {
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmt->bind_param("si", $hashedPassword, $userId);
                $stmt->execute();
            }

            $conn->commit();
            $_SESSION['success_msg'] = "Profile updated successfully.";
        } catch (Exception $e) {
            $conn->rollback();
            $_SESSION['error_msg'] = "Failed to update profile. Please try again.";
        }
    } else {
        $_SESSION['error_msg'] = implode("<br>", $errors);
    }
}

header('Location: ../pages/vendor_dashboard.php?tab=tab-profile');
exit();

