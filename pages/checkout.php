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
    <meta charset="UTF-8">
    <title>Checkout - NTUmami</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/global.css">
    <link rel="stylesheet" href="../assets/css/checkout.css">

    <script src="../assets/js/header.js" defer></script>
    <script defer src="../assets/js/notification.js"></script>
</head>
<body>
    <div id="notification" class="notification <?php echo $notificationType; ?>">
        <?php echo $notificationMessage; ?>
    </div>

    <?php include '../includes/cart_number.php';  include '../includes/header.php'; ?>

    <main>
        <div class="container">
            <div class="checkout-container">
                <a href="cart.php" class="back-to-cart"><i class="fa fa-arrow-left"></i> Back to Cart</a>

                <h1 class="checkout-title">Payment Page</h1>

                <!-- Begin Form -->
                <form action="secure_payment.php" method="POST" class="checkout-form">
                    <div class="checkout-content">
                        <!-- Payment Options -->
                        <div class="payment-options">
                            <h2>Payment</h2>
                            <label><input type="radio" name="payment_method" value="card" checked> Credit/Debit Card</label>
                            <!-- <label><input type="radio" name="payment_method" value="paynow"> PayNow/PayLah!</label> -->
                            
                            <h3>Remarks</h3>
                            <textarea name="remarks" placeholder="Use this area for special requests/questions about your order."></textarea>
                        </div>

                        <!-- Order Summary -->
                        <div class="order-summary">
                            <h2>Order Summary</h2>
                            <table>
                                <thead>
                                    <tr>
                                        <th>Product Details</th>
                                        <th>Qty</th>
                                        <th>Price</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($cartItems as $item): ?>
                                        <tr>
                                            <td>
                                                <img src="<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['food_name']); ?>" class="product-image">
                                                <div class="product-details">
                                                    <strong><?php echo htmlspecialchars($item['food_name']); ?></strong><br>
                                                    <small>Location: <?php echo htmlspecialchars($item['location_name']); ?></small><br>
                                                    <small>Stall: <?php echo htmlspecialchars($item['stall_name']); ?></small>
                                                </div>
                                            </td>
                                            <td><?php echo $item['qty']; ?></td>
                                            <td>$<?php echo number_format($item['price'], 2); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                            <div class="summary-totals">
                                <p>Subtotal <span>$<?php echo number_format($totalPrice, 2); ?></span></p>
                                <p class="total">Total <span>$<?php echo number_format($totalPrice, 2); ?></span></p>
                            </div>
                            <!-- Submit Button for Checkout -->
                            <button type="submit" class="checkout-button">Continue to Secure Payment</button>
                        </div>
                    </div>
                </form>
                <!-- End Form -->
            </div>
        </div>
    </main>
    <?php include '../includes/footer.php'; ?>
</body>
</html>
