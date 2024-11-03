<?php
session_start();

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

include '../includes/db_connect.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['stall_id'])) {
    $stallId = $_POST['stall_id'];
    $name = $_POST['name'];
    $cuisineType = $_POST['cuisine_type'];
    $isOpen = isset($_POST['is_open']) && $_POST['is_open'] == "1" ? 1 : 0;

    // Start a transaction
    $conn->begin_transaction();

    try {
        $stmt = $conn->prepare("UPDATE stalls SET name = ?, cuisine_type = ?, is_open = ? WHERE id = ?");
        $stmt->bind_param("ssii", $name, $cuisineType, $isOpen, $stallId);

        if ($stmt->execute()) {
            $conn->commit();
            $_SESSION['success_msg'] = "Stall updated successfully.";
        } else {
            throw new Exception("Failed to update stall.");
        }

    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['error_msg'] = $e->getMessage();
    }

    header('Location: ../pages/vendor_dashboard.php?tab=tab-stalls');
    exit();
}

