<?php
session_start();

include '../includes/db_connect.php';
include '../includes/cart_number.php';

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

// Redirect users based on their roles
if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    header('Location: ./pages/admin_dashboard.php');
    exit();
}
if (isset($_SESSION['role']) && $_SESSION['role'] === 'vendor') {
    header('Location: ./pages/vendor_dashboard.php');
    exit();
}

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Handle form submission for marking an item as collected
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark_collected'])) {
    $orderItemId = $_POST['order_item_id'];
    $orderId = $_POST['order_id'];

    // Update the status of the specific food item to 'Completed'
    $stmt = $conn->prepare("UPDATE order_items SET status = 'Completed' WHERE id = ?");
    $stmt->bind_param("i", $orderItemId);
    $stmt->execute();
    $stmt->close();

    // Check if all items in the order are now marked as 'Completed'
    $checkStmt = $conn->prepare("SELECT COUNT(*) FROM order_items WHERE order_id = ? AND status != 'Completed'");
    $checkStmt->bind_param("i", $orderId);
    $checkStmt->execute();
    $checkStmt->bind_result($remainingCount);
    $checkStmt->fetch();
    $checkStmt->close();

    if ($remainingCount == 0) {
        // Mark the entire order as 'Completed' if all items are collected
        $updateOrderStmt = $conn->prepare("UPDATE orders SET status = 'Completed' WHERE id = ?");
        $updateOrderStmt->bind_param("i", $orderId);
        $updateOrderStmt->execute();
        $updateOrderStmt->close();
    }

    // Redirect to prevent form resubmission
    header("Location: myorders.php");
    exit();
}

// Fetch orders and associated items
$stmt = $conn->prepare("
    SELECT 
        o.id AS order_id, 
        o.created_by AS order_date, 
        o.status AS order_status,
        oi.id AS order_item_id, 
        oi.food_id, 
        oi.qty AS quantity, 
        oi.status AS item_status,
        f.name AS food_name, 
        f.image_url,
        s.name AS stall_name, 
        c.name AS canteen_name
    FROM orders o
    JOIN order_items oi ON o.id = oi.order_id
    JOIN foods f ON oi.food_id = f.id
    JOIN stalls s ON f.stall_id = s.id
    JOIN canteens c ON s.canteen_id = c.id
    WHERE o.user_id = ?
    ORDER BY o.created_by DESC
");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$orders = [];
while ($row = $result->fetch_assoc()) {
    $orderId = $row['order_id'];
    
    // Initialize the order if it doesn't exist in the array
    if (!isset($orders[$orderId])) {
        $orders[$orderId] = [
            'order_date' => $row['order_date'],
            'order_status' => $row['order_status'],
            'items' => []
        ];
    }
    
    // Add each item to the order's 'items' array, including its own canteen and stall names
    $orders[$orderId]['items'][] = [
        'order_item_id' => $row['order_item_id'],
        'food_name' => $row['food_name'],
        'quantity' => $row['quantity'],
        'item_status' => $row['item_status'],
        'image_url' => $row['image_url'],
        'canteen_name' => $row['canteen_name'],
        'stall_name' => $row['stall_name']
    ];
}
$stmt->close();

?>

<!-- HTML Form -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/global.css">
    <link rel="stylesheet" href="../assets/css/myorders.css">
</head>

<body>
<?php include '../includes/header.php'; ?>

<div id="notification" class="notification <?php echo $notificationType; ?>">
    <?php echo $notificationMessage; ?>
</div>

<main>
    <h1 class="order-history-title">Order History</h1>

    <?php foreach ($orders as $orderId => $order): ?>
        <!-- Order Card -->
        <div class="order-card">
            <div class="order-header">
                <p class="order-date">Ordered on: <?= htmlspecialchars($order['order_date']) ?></p>
                <p class="order-status-label">Order status: <span class="order-status <?= strtolower(str_replace(' ', '-', $order['order_status'])) ?>">
                    <?= htmlspecialchars($order['order_status']) ?>
                </span></p>
            </div>

            <hr class="order-separator">

            <div class="order-details">
                <p class="order-id">Order ID: #<?= htmlspecialchars($orderId) ?></p>

                <!-- Food Items List -->
                <div class="food-items">
                    <?php foreach ($order['items'] as $item): ?>
                        <div class="food-item-card">
                            <img class="food-item-image" src="<?= htmlspecialchars($item['image_url']) ?>" alt="<?= htmlspecialchars($item['food_name']) ?>">
                            
                            <div class="food-item-details">
                                <p class="food-item-name"><?= htmlspecialchars($item['food_name']) ?></p>
                                <p class="food-item-quantity">Quantity: <?= htmlspecialchars($item['quantity']) ?>x</p>
                                
                                <div class="food-item-status-container">
                                    <p class="food-item-address">Pick Up Address: <?= htmlspecialchars($item['canteen_name']) ?>, <?= htmlspecialchars($item['stall_name']) ?></p>
                                    <!-- Status with colored label -->
                                    <span class="food-item-status-label <?= strtolower(str_replace(' ', '-', $item['item_status'])) ?>">
                                        <?= htmlspecialchars($item['item_status']) ?>
                                    </span>
                                </div>

                                <?php if ($item['item_status'] === 'Ready for Pickup'): ?>
                                    <form method="POST" action="myorders.php" class="collect-form">
                                        <input type="hidden" name="mark_collected" value="1">
                                        <input type="hidden" name="order_item_id" value="<?= $item['order_item_id'] ?>">
                                        <input type="hidden" name="order_id" value="<?= $orderId ?>">
                                        <button type="submit" class="collect-btn">Click if Collected</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</main>

<?php include '../includes/footer.php'; ?>

</body>
</html>