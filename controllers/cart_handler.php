<?php
session_start();
include '../includes/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../pages/login.php');
    exit();
}

$userId = $_SESSION['user_id'];
$action = $_GET['action'] ?? '';
$redirectUrl = '../pages/cart.php'; // Set default redirect back to cart

// Begin a database transaction
$conn->begin_transaction();
echo $action;
try {
    switch ($action) {
        case 'add_to_cart':
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['food_id'])) {
                $foodId = $_POST['food_id'];
                $quantity = $_POST['quantity'] ?? 1; // Default to 1 if no quantity specified

                // 1. Check for an existing cart
                $cartId = null;
                $stmt = $conn->prepare("SELECT id FROM carts WHERE user_id = ? LIMIT 1");
                $stmt->bind_param("i", $userId);
                $stmt->execute();
                $stmt->bind_result($existingCartId);
                
                // Check if we retrieved a cart ID
                if ($stmt->fetch()) {
                    $cartId = $existingCartId;
                } else {
                    // 2. No cart found, create a new one
                    $stmt->close();
                    $stmt = $conn->prepare("INSERT INTO carts (user_id, created_by) VALUES (?, ?)");
                    $stmt->bind_param("ii", $userId, $userId);
                    $stmt->execute();

                    if ($stmt->affected_rows > 0) {
                        $cartId = $stmt->insert_id;
                    } else {
                        throw new Exception("Failed to create a new cart.");
                    }
                }
                $stmt->close();

                // 3. Check if the food item already exists in the cart_items
                $stmt = $conn->prepare("SELECT qty FROM cart_items WHERE cart_id = ? AND food_id = ?");
                $stmt->bind_param("ii", $cartId, $foodId);
                $stmt->execute();
                $stmt->bind_result($existingQty);

                if ($stmt->fetch()) {
                    // 4. If item exists, update the quantity
                    $stmt->close();
                    $newQuantity = $existingQty + $quantity;
                    $stmt = $conn->prepare("UPDATE cart_items SET qty = ? WHERE cart_id = ? AND food_id = ?");
                    $stmt->bind_param("iii", $newQuantity, $cartId, $foodId);
                    $stmt->execute();
                } else {
                    // 5. If item does not exist, insert a new entry
                    $stmt->close();
                    $stmt = $conn->prepare("INSERT INTO cart_items (cart_id, food_id, qty) VALUES (?, ?, ?)");
                    $stmt->bind_param("iii", $cartId, $foodId, $quantity);
                    $stmt->execute();
                }

                if ($stmt->affected_rows > 0) {
                    $_SESSION['success_msg'] = "Item added to cart successfully!";
                } else {
                    throw new Exception("Failed to add item to cart.");
                }

                // 6. Commit transaction and redirect
                $conn->commit();
                header("Location: ../pages/menu.php");
                exit();
            }
            break;

        case 'update':
            // Update all cart items at once based on the posted data
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if (isset($_POST['quantity'], $_POST['special_request'])) {
                    $updatedRows = 0;
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
                    $cartquery = "SELECT SUM(qty) AS item_count
                        FROM cart_items ci
                        JOIN carts c ON ci.cart_id = c.id
                        WHERE c.user_id = $userId";
                    $cart = $conn->query($cartquery)->fetch_assoc();
                    
                    $itemCount = $cart['item_count'] ?? 0;
                    
                    if ($itemCount === 0) {
                        $cartId = null;
                        $stmt = $conn->prepare("SELECT id FROM carts WHERE user_id = ? LIMIT 1");
                        $stmt->bind_param("i", $userId);
                        $stmt->execute();
                        $stmt->bind_result($existingCartId);
                        if ($stmt->fetch()) {
                            $cartId = $existingCartId;
                        } else {
                            throw new Exception("Failed to find cart to delete.");
                        }

                        $stmt->close();

                        
                        $deleteCart = "DELETE c FROM carts c WHERE c.id = ? and c.user_id = ?";
                        $deleteCartStmt = $conn->prepare($deleteCart);
                        $deleteCartStmt->bind_param("ii", $cartId, $userId);
                        $deleteCartStmt->execute();
                        
                        if ($deleteCartStmt->affected_rows > 0) {
                            $_SESSION['success_msg'] = "Cart item deleted successfully.";
                            $_SESSION['cart_count']--;
                        } else {
                            throw new Exception("Failed to delete cart.");
                        }
                    } else {
                        $_SESSION['success_msg'] = "Cart item deleted successfully.";
                        $_SESSION['cart_count']--;
                    }

                    
                } else {
                    $_SESSION['error_msg'] = "Failed to delete cart item.";
                }
            }
            break;

        default:
            $_SESSION['error_msg'] = "Invalid action.";
            break;
    }

    $conn->commit();
    header("Location: $redirectUrl");
    exit();

} catch (Exception $e) {
    $conn->rollback();
    $_SESSION['error_msg'] = "Error: " . $e->getMessage();
    header("Location: $redirectUrl");
    exit();
}
