<?php
session_start();
include '../includes/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../pages/login.php');
    exit();
}

$userId = $_SESSION['user_id'];
$action = $_GET['action'] ?? '';
$page = $_GET['page'] ?? '';
$redirectUrl = '../pages/cart.php'; // Redirect to cart page after each action

// Begin a database transaction
$conn->begin_transaction();

try {
    switch ($action) {
        case 'count':
            // Get the count of items in the cart
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
            $redirectUrl = $page === "index.php" ? "../$page" : "../pages/$page";
            break;

        case 'retrieve':
            // Redirect back to cart.php; retrieval logic will be in cart.php
            break;

        case 'update':
            // Update the quantity of a cart item based on change value
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $cartItemId = $_POST['cart_item_id'];
                $change = (int)$_POST['change']; // Get the change value as integer

                // Fetch the current quantity of the item
                $stmt = $conn->prepare("SELECT qty FROM cart_items WHERE id = ? AND cart_id = (SELECT id FROM carts WHERE user_id = ?)");
                $stmt->bind_param("ii", $cartItemId, $userId);
                $stmt->execute();
                $result = $stmt->get_result();
                $currentQty = $result->fetch_assoc()['qty'] ?? 0;

                // Calculate the new quantity
                $newQuantity = max(1, $currentQty + $change); // Ensure quantity doesn't go below 1

                // Update the item quantity in the database
                $updateStmt = $conn->prepare("UPDATE cart_items SET qty = ? WHERE id = ?");
                $updateStmt->bind_param("ii", $newQuantity, $cartItemId);
                $updateStmt->execute();

                if ($updateStmt->affected_rows > 0) {
                    if ($newQuantity > $currentQty) {
                        $_SESSION['cart_count']++;
                    }
                    if ($newQuantity < $currentQty) {
                        $_SESSION['cart_count']--;
                    }

                    $_SESSION['success_msg'] = "Cart item updated successfully.";
                } else {
                    $_SESSION['error_msg'] = "Failed to update cart item.";
                }
            }
            break;

        case 'delete':
            // Delete a cart item, then redirect back to cart.php
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $cartItemId = $_POST['cart_item_id'];

                $query = "DELETE ci
                            FROM cart_items ci
                            JOIN carts c ON ci.cart_id = c.id
                            WHERE ci.id = ? AND c.user_id = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("ii", $cartItemId, $userId);
                $stmt->execute();

                if ($stmt->affected_rows > 0) {
                    $_SESSION['success_msg'] = "Cart item deleted successfully.";
                    $_SESSION['cart_count']--;
                } else {
                    $_SESSION['error_msg'] = "Failed to delete cart item.";
                }
            }
            break;

        default:
            $_SESSION['error_msg'] = "Invalid action.";
            break;
    }

    // Commit transaction
    $conn->commit();
    header("Location: $redirectUrl");
    exit();

} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    $_SESSION['error_msg'] = "Error: " . $e->getMessage();
    header("Location: $redirectUrl");
    exit();
}
