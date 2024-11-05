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
                 (f.price * ci.qty) AS item_total, s.name AS stall_name, l.name AS location_name
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
                    <?php foreach ($cartItems as $item): ?>
                        <div class="cart-item">
                            <!-- Image Section -->
                            <img src="<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['food_name']); ?>" class="cart-item-image">
                            
                            <!-- Details Section -->
                            <div class="cart-item-details">
                                <p><strong>Location:</strong> <?php echo htmlspecialchars($item['location_name']); ?></p>
                                <p><strong>Stall:</strong> <?php echo htmlspecialchars($item['stall_name']); ?></p>
                                <p><strong>Food:</strong> <?php echo htmlspecialchars($item['food_name']); ?></p>
                                <p><strong>Price:</strong> $<?php echo number_format($item['price'], 2); ?></p>
                                
                                <!-- Quantity and Subtotal Row -->
                                <div class="cart-item-actions">
                                    <div class="quantity-controls">
                                        <label><strong>Quantity:</strong></label>
                                        <form action="../controllers/cart_handler.php?action=update" method="post" style="display: inline;">
                                            <input type="hidden" name="cart_item_id" value="<?php echo $item['cart_item_id']; ?>">
                                            <button type="submit" name="change" value="-1">-</button>
                                            <input type="text" name="quantity" value="<?php echo $item['qty']; ?>" readonly>
                                            <button type="submit" name="change" value="1">+</button>
                                        </form>
                                    </div>
                                    <p class="subtotal">Subtotal: $<?php echo number_format($item['item_total'], 2); ?></p>
                                </div>
                            </div>

                            <!-- Delete Button -->
                            <form action="../controllers/cart_handler.php?action=delete" method="post">
                                <input type="hidden" name="cart_item_id" value="<?php echo $item['cart_item_id']; ?>">
                                <button type="submit" class="cart-item-delete">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                    <div class="cart-total">Cart Total: $<?php echo number_format($totalPrice, 2); ?></div>

                    <!-- Proceed to Checkout Button -->
                    <div class="checkout-container">
                        <a href="./checkout.php" class="checkout-button">Proceed to Checkout</a>
                    </div>
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
</body>
</html>
