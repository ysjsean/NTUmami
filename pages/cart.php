<?php
session_start();

// Prepare the notification message if available
$notificationMessage = '';
$notificationType = ''; // 'success' or 'error'

if (isset($_SESSION['success_msg'])) {
    $notificationMessage = $_SESSION['success_msg'];
    $notificationType = 'success';
    unset($_SESSION['success_msg']);
}
if (isset($_SESSION['error_msg'])) {
    $notificationMessage = $_SESSION['error_msg'];
    $notificationType = 'error';
    unset($_SESSION['error_msg']);
}

include '../includes/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

include '../includes/cart_number.php';

$userId = $_SESSION['user_id'];
$query = "SELECT ci.id AS cart_item_id, f.name AS food_name, f.price, f.image_url, ci.qty, 
                 (f.price * ci.qty) AS item_total, s.name AS stall_name, l.name AS location_name, ci.special_request
            FROM cart_items ci
            JOIN carts c ON ci.cart_id = c.id
            JOIN foods f ON ci.food_id = f.id
            JOIN stalls s ON f.stall_id = s.id
            JOIN canteens l ON s.canteen_id = l.id
            WHERE c.user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$cartItems = [];
$totalPrice = 0;
while ($row = $result->fetch_assoc()) {
    $cartItems[] = $row;
    $totalPrice += $row['item_total'];
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Cart - NTUmami</title>

    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/global.css">
    <link rel="stylesheet" href="../assets/css/cart.css">

    <script src="../assets/js/header.js" defer></script>
    <script defer src="../assets/js/notification.js"></script>
</head>
<body>
    <!-- Notification container -->
    <div id="notification" class="notification <?php echo $notificationType; ?>">
        <?php echo $notificationMessage; ?>
    </div>

    <?php include '../includes/cart_number.php';  include '../includes/header.php'; ?>

    <main>
        <div class="container">
            <h1 id="title">Your Cart</h1>
            <h2 id="subtitle">Please confirm your order details below:</h2>
            <div class="cart-items">
                <?php if (!empty($cartItems)): ?>
                    <!-- Start of the Checkout Form -->
                    <form action="../controllers/cart_handler.php?action=update" method="post">
                        <?php foreach ($cartItems as $item): ?>
                            <div class="cart-item">
                                <!-- Image Section -->
                                <img src="<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['food_name']); ?>" class="cart-item-image">
                                
                                <!-- Details Section -->
                                <div class="cart-item-details">
                                    <p><strong>Location:</strong> <?php echo htmlspecialchars($item['location_name']); ?></p>
                                    <p><strong>Stall:</strong> <?php echo htmlspecialchars($item['stall_name']); ?></p>
                                    <p><strong>Food:</strong> <?php echo htmlspecialchars($item['food_name']); ?></p>
                                    <p id="price_<?php echo $item['cart_item_id']; ?>"><strong>Price:</strong> $<?php echo number_format($item['price'], 2); ?></p>
                                    
                                    <!-- Special Request Field -->
                                    <div class="special-request-container">
                                        <label for="special_request_<?php echo $item['cart_item_id']; ?>"><strong>Special Request:</strong></label>
                                        <textarea name="special_request[<?php echo $item['cart_item_id']; ?>]" id="special_request_<?php echo $item['cart_item_id']; ?>" rows="2"><?php echo htmlspecialchars($item['special_request']); ?></textarea>
                                    </div>

                                    <!-- Quantity Controls -->
                                    <div class="quantity-controls">
                                        <label><strong>Quantity:</strong></label>
                                        <button type="button" onclick="changeQuantity(<?php echo $item['cart_item_id']; ?>, -1)">-</button>
                                        <input type="text" name="quantity[<?php echo $item['cart_item_id']; ?>]" value="<?php echo $item['qty']; ?>" readonly id="quantity_<?php echo $item['cart_item_id']; ?>">
                                        <button type="button" onclick="changeQuantity(<?php echo $item['cart_item_id']; ?>, 1)">+</button>
                                    </div>

                                    <p id="subtotal_<?php echo $item['cart_item_id']; ?>" class="subtotal">Subtotal: $<?php echo number_format($item['item_total'], 2); ?></p>

                                    <!-- Delete Button -->
                                    <button type="submit" formaction="../controllers/cart_handler.php?action=delete" formmethod="post" name="cart_item_id" value="<?php echo $item['cart_item_id']; ?>" class="cart-item-delete">
                                        <input type="hidden" name="action" value="delete"> 
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>

                        <!-- Cart Total and Dining Option -->
                        <div id="cart-total" class="cart-total">Cart Total: $<?php echo number_format($totalPrice, 2); ?></div>

                        <div class="dining-option">
                            <h3>Select Dining Option:</h3>
                            <label>
                                <input type="radio" name="dining_option" value="Eat-In" <?php echo (!isset($_SESSION['dining_option']) || $_SESSION['dining_option'] === "Eat-In") ? "checked" : ""; ?> required>
                                Eat-In
                            </label>
                            <label>
                                <input type="radio" name="dining_option" value="Take-Out" <?php echo (isset($_SESSION['dining_option']) && $_SESSION['dining_option'] === "Take-Out") ? "checked" : ""; ?>>
                                Take-Out
                            </label>
                        </div>

                        <!-- Proceed to Checkout Button -->
                        <div class="checkout-container">
                            <button type="submit" class="checkout-button">Proceed to Checkout</button>
                        </div>
                    </form>
                <?php else: ?>
                    <div class="empty-cart">
                        <i class="fa fa-shopping-cart"></i>
                        <p>Your cart is empty.</p>
                        <p>Looks like you haven’t added anything to your cart yet!</p>
                        <a href="./menu.php" class="browse-menu-button">Browse Menu</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    

    <?php include '../includes/footer.php'; ?>

    <script>
        function changeQuantity(cartItemId, delta) {
            const quantityInput = document.getElementById(`quantity_${cartItemId}`);
            const price = document.getElementById(`price_${cartItemId}`).innerText.split("$")[1];
            let quantity = parseInt(quantityInput.value) + delta;
            
            // Ensure quantity doesn't go below 1
            if (quantity < 1) quantity = 1;
            
            // Update the quantity input field with the new value
            quantityInput.value = quantity;

            // Calculate the new subtotal for the item
            const itemSubtotal = quantity * price;
            document.getElementById(`subtotal_${cartItemId}`).innerText = `Subtotal: $${itemSubtotal.toFixed(2)}`;

            // Update the cart total
            updateCartTotal();
        }

        function updateCartTotal() {
            let total = 0;

            // Select all subtotal elements and sum their values
            const subtotalElements = document.querySelectorAll('.subtotal');
            subtotalElements.forEach((element) => {
                total += parseFloat(element.innerText.split("$")[1]);
            });
            
            // Update the cart total display
            document.getElementById('cart-total').innerText = `Cart Total: $${total.toFixed(2)}`;
        }
    </script>

</body>
</html>
