<?php
session_start();
include '../includes/db_connect.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

$action = $_GET['action'] ?? '';

// Reusable validation function for stall data
function validateStallData($name, $cuisine_type, $vendor_id, $canteen_id, $is_open) {
    $errors = [];

    if (empty($name)) {
        $errors[] = "Stall name is required.";
    }

    $allowedCuisines = ['Chinese', 'Malay', 'Indian', 'Western', 'Japanese', 'Korean', 'Taiwan', 'Fusion'];
    if (empty($cuisine_type) || !in_array($cuisine_type, $allowedCuisines)) {
        $errors[] = "Valid cuisine type is required.";
    }

    if (empty($vendor_id) || !is_numeric($vendor_id)) {
        $errors[] = "Valid vendor selection is required.";
    }

    if (empty($canteen_id) || !is_numeric($canteen_id)) {
        $errors[] = "Valid canteen selection is required.";
    }

    if (!isset($is_open) || !in_array($is_open, [0, 1])) {
        $errors[] = "Is Open status must be selected.";
    }

    return $errors;
}

// Handle add action
if ($action === 'add') {
    $name = trim($_POST['name']);
    $cuisine_type = trim($_POST['cuisine_type']);
    $vendor_id = $_POST['vendor_id'];
    $canteen_id = $_POST['canteen_id'];
    $is_open = $_POST['is_open'];

    // Validate input
    $errors = validateStallData($name, $cuisine_type, $vendor_id, $canteen_id, $is_open);
    if (!empty($errors)) {
        $_SESSION['error_msg'] = implode("<br>", $errors);
    } else {
        $stmt = $conn->prepare("INSERT INTO stalls (name, cuisine_type, vendor_id, canteen_id, is_open) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssiii", $name, $cuisine_type, $vendor_id, $canteen_id, $is_open);
        if ($stmt->execute()) {
            $_SESSION['success_msg'] = "Stall added successfully!";
        } else {
            $_SESSION['error_msg'] = "Failed to add stall.";
        }
        $stmt->close();
    }

    header("Location: ../pages/admin_dashboard.php?tab=tab-stalls");
    exit();
}

// Handle update action
elseif ($action === 'update') {
    $id = $_POST['stall_id'];
    $name = trim($_POST['name']);
    $cuisine_type = trim($_POST['cuisine_type']);
    $vendor_id = $_POST['vendor_id'];
    $canteen_id = $_POST['canteen_id'];
    $is_open = $_POST['is_open'];

    // Validate input
    $errors = validateStallData($name, $cuisine_type, $vendor_id, $canteen_id, $is_open);
    if (!empty($errors)) {
        $_SESSION['error_msg'] = implode("<br>", $errors);
    } else {
        $stmt = $conn->prepare("UPDATE stalls SET name = ?, cuisine_type = ?, vendor_id = ?, canteen_id = ?, is_open = ? WHERE id = ?");
        $stmt->bind_param("ssiiii", $name, $cuisine_type, $vendor_id, $canteen_id, $is_open, $id);
        if ($stmt->execute()) {
            $_SESSION['success_msg'] = "Stall updated successfully!";
        } else {
            $_SESSION['error_msg'] = "Failed to update stall.";
        }
        $stmt->close();
    }

    header("Location: ../pages/admin_dashboard.php?tab=tab-stalls");
    exit();
}

// Handle delete action
elseif ($action === 'delete') {
    $id = $_GET['stall_id'] ?? null;

    if ($id) {
        $stmt = $conn->prepare("DELETE FROM stalls WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $_SESSION['success_msg'] = "Stall deleted successfully!";
        } else {
            $_SESSION['error_msg'] = "Failed to delete stall.";
        }
        $stmt->close();
    } else {
        $_SESSION['error_msg'] = "Invalid stall ID.";
    }

    header("Location: ../pages/admin_dashboard.php?tab=tab-stalls");
    exit();
}
