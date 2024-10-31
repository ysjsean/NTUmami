<?php
session_start();
include '../includes/db_connect.php';

if ($_GET['action'] === 'update') {
    // Update stall
    $stall_id = $_POST['stall_id'];
    $name = $_POST['name'];
    $cuisine_type = $_POST['cuisine_type'];
    $is_open = isset($_POST['is_open']) ? 1 : 0;

    $stmt = $conn->prepare("UPDATE stalls SET name = ?, cuisine_type = ?, is_open = ? WHERE id = ? AND vendor_id = ?");
    $stmt->bind_param("ssiii", $name, $cuisine_type, $is_open, $stall_id, $_SESSION['vendor_id']);
    $stmt->execute();
}
header('Location: ../pages/vendor_dashboard.php');
