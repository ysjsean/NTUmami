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
    $orderItemId = intval($_POST['order_item_id']);
    $itemStatus = $_POST['item_status'];

    // Update the item status in the order_items table
    $stmt = $conn->prepare("UPDATE order_items SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $itemStatus, $orderItemId);

    if ($stmt->execute()) {
        $_SESSION['success_msg'] = "Item status updated successfully.";
    } else {
        $_SESSION['error_msg'] = "Failed to update item status.";
    }

    $stmt->close();
}

// Redirect back to the vendor dashboard
header('Location: ../pages/vendor_dashboard.php?tab=tab-orders');
exit();