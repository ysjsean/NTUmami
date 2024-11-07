<?php
session_start();
include '../includes/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../pages/login.php');
    exit();
}

$userId = $_SESSION['user_id'];
$action = $_POST['action'] ?? ''; // Change from GET to POST to match form submission
$redirectUrl = '../pages/cart.php'; // Redirect to cart page after each action

// Begin a database transaction
$conn->begin_transaction();

try {
    switch ($action) {
        case 'add_to_cart':
            // Handle adding an item to the cart
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['food_id'])) {
                $foodId = $_POST['food_id'];
                $quantity = $_POST['quantity'] ?? 1; // Default to 1 if no quantity specified

                // Check if the user already has an active cart
                $cartId = null;
                $stmt = $conn->prepare("SELECT id FROM carts WHERE user_id = ? LIMIT 1");
                $stmt->bind_param("i", $userId);
                $stmt->execute();
                $stmt->bind_result($existingCartId);
                if ($stmt->fetch()) {
                    $cartId = $existingCartId; // Use the existing cart
                } else {
                    // If no cart exists, create a new one
                    $stmt = $conn->prepare("INSERT INTO carts (user_id, created_by) VALUES (?, ?)");
                    $stmt->bind_param("ii", $userId, $userId);
                    $stmt->execute();
                    $cartId = $stmt->insert_id; // Get the new cart ID
                }
                $stmt->close();

                // Insert the food item into cart_items
                $stmt = $conn->prepare("INSERT INTO cart_items (cart_id, food_id, qty) VALUES (?, ?, ?)
                                        ON DUPLICATE KEY UPDATE qty = qty + VALUES(qty)");
                $stmt->bind_param("iii", $cartId, $foodId, $quantity);
                $stmt->execute();

                // Update cart count in the session
                $_SESSION['cart_count'] = ($_SESSION['cart_count'] ?? 0) + 1;
                $_SESSION['success_msg'] = "Item added to cart successfully!";
            }
            break;

        case 'update':
            // Update all cart items at once based on the posted data
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if (isset($_POST['quantity'], $_POST['special_request'])) {
                    $cartItemQuantities = $_POST['quantity']; // Array of quantities indexed by cart_item_id
                    $cartItemRequests = $_POST['special_request']; // Array of special requests indexed by cart_item_id

                    foreach ($cartItemQuantities as $cartItemId => $newQuantity) {
                        $newQuantity = max(1, (int)$newQuantity); // Ensure quantity doesn't go below 1
                        $specialRequest = $cartItemRequests[$cartItemId] ?? ''; // Get the special request for this item

                        // Update quantity and special request in the database for each cart item
                        $updateStmt = $conn->prepare("UPDATE cart_items SET qty = ?, special_request = ? WHERE id = ? AND cart_id = (SELECT id FROM carts WHERE user_id = ?)");
                        $updateStmt->bind_param("isii", $newQuantity, $specialRequest, $cartItemId, $userId);
                        $updateStmt->execute();

                        if ($updateStmt->affected_rows > 0) {
                            $updatedRows += $updateStmt->affected_rows;
                        }
                    }

                    // Set a success message indicating the number of items updated
                    if ($updatedRows > 0) {
                        $_SESSION['success_msg'] = "$updatedRows item(s) updated successfully in your cart.";
                    }

                    $redirectUrl = "../pages/checkout.php";
                } else {
                    $_SESSION['error_msg'] = "";
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
