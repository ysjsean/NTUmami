<?php
session_start();
include '../includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orderId = $_POST['order_id'];
    $foodId = $_POST['food_id'];

    // Update the status of the specific food item to 'Completed'
    $stmt = $conn->prepare("UPDATE order_items SET status = 'Completed' WHERE order_id = ? AND food_id = ?");
    $stmt->bind_param("ii", $orderId, $foodId);
    $stmt->execute();
    $stmt->close();

    // Check if all items in the order are completed
    $stmt = $conn->prepare("SELECT COUNT(*) AS pending_items FROM order_items WHERE order_id = ? AND status != 'Completed'");
    $stmt->bind_param("i", $orderId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    if ($row['pending_items'] == 0) {
        // Mark the entire order as 'Completed'
        $stmt = $conn->prepare("UPDATE orders SET status = 'Completed' WHERE id = ?");
        $stmt->bind_param("i", $orderId);
        $stmt->execute();
    }
    $stmt->close();
    $conn->close();
}

header("Location: myorders.php");
exit();
