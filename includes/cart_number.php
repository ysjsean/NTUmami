<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'db_connect.php';


if (isset($_SESSION['user_id'])) {
    // Get the count of items in the cart
    $userId = $_SESSION['user_id'];

    $query = "SELECT SUM(qty) AS item_count
    FROM cart_items ci
    JOIN carts c ON ci.cart_id = c.id
    WHERE c.user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $count = $result->fetch_assoc()['item_count'] ?? 0;
    
    $_SESSION['cart_count'] = $count; // Store count in session for display
}