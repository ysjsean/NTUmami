<?php
session_start();

include '../includes/db_connect.php';
include '../includes/cart_number.php';

// Retrieve and unset success or error messages
$userId = $_SESSION['user_id'];
$successMsg = $_SESSION['success_msg'] ?? '';
$errorMsg = $_SESSION['error_msg'] ?? '';
unset($_SESSION['success_msg'], $_SESSION['error_msg']);

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
        o.id AS order_id, o.created_by, o.status AS order_status,
        oi.id AS order_item_id, oi.food_id, oi.qty, oi.status AS item_status,
        f.name AS food_name, f.image_url,
        s.name AS stall_name, c.name AS canteen_name
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
    $orders[$row['order_id']]['order_date'] = $row['created_by'];
    $orders[$row['order_id']]['order_status'] = $row['order_status'];
    $orders[$row['order_id']]['items'][] = $row;
}
$stmt->close();
$conn->close();
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
<main>
    <h1>Order History</h1>

    <?php foreach ($orders as $order): ?>
        <div class="order-section">
            <p>Ordered on: <?= htmlspecialchars($order['created_by']) ?></p>
            <p>Order ID: #<?= htmlspecialchars($order['id']) ?></p>
            <p>Pick Up Address: <?= htmlspecialchars($order['canteen_name']) ?>, <?= htmlspecialchars($order['stall_name']) ?></p>
            <p>Order status: <span class="order-status"><?= htmlspecialchars($order['status']) ?></span></p>

            <div class="food-items">
                <?php foreach ($orderItems as $item): ?>
                    <?php if ($item['order_id'] === $order['id']): ?>
                        <div class="food-item">
                            <img src="<?= htmlspecialchars($item['image_url']) ?>" alt="<?= htmlspecialchars($item['name']) ?>">
                            <p class="name"><?= htmlspecialchars($item['name']) ?></p>
                            <p class="quantity"><?= htmlspecialchars($item['qty']) ?>x</p>
                            <p class="status-label"><?= htmlspecialchars($item['status']) ?></p>

                            <?php if ($item['status'] === 'Ready for Pickup'): ?>
                                <form method="POST" action="myorders.php">
                                    <input type="hidden" name="mark_collected" value="1">
                                    <input type="hidden" name="order_item_id" value="<?= $item['id'] ?>">
                                    <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                    <button type="submit" class="collect-btn">Collected</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
        <hr>
    <?php endforeach; ?>
<main>

<?php include '../includes/footer.php'; ?>

</body>
</html>