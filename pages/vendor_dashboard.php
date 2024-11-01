<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'vendor') {
    header('Location: ../index.php');
    exit();
}

$vendorName = $_SESSION['username'] ?? "Vendor";
$userId = $_SESSION['user_id']; // Assuming `user_id` is stored in the session

$notificationMessage = '';
$notificationType = '';

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

$vendorId = $conn->query("SELECT * FROM vendors WHERE user_id = $userId")->fetch_all(MYSQLI_ASSOC)[0]['id'];

// Fetch stalls and food items for this vendor
$stalls = $conn->query("SELECT * FROM stalls WHERE vendor_id = $vendorId")->fetch_all(MYSQLI_ASSOC) ?? [];
$foods = $conn->query("SELECT * FROM foods WHERE stall_id IN (SELECT id FROM stalls WHERE vendor_id = $vendorId)")->fetch_all(MYSQLI_ASSOC) ?? [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Vendor Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/global.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <script defer src="../assets/js/notification.js"></script>
</head>
<body>
    <!-- Notification -->
    <div id="notification" class="notification <?php echo $notificationType; ?>">
        <?php echo $notificationMessage; ?>
    </div>

    <div class="container">
        <h1>Vendor Dashboard</h1>
        <p class="welcome-message">Welcome, <?= htmlspecialchars($vendorName); ?>! Here’s your vendor dashboard.</p>
        <a href="../controllers/logout.php" class="logout-btn"><i class="fa fa-user"></i> Logout</a>

        <!-- Tab Navigation -->
        <div class="tabs">
            <button class="tab-link" onclick="openTab(event, 'tab-stalls')">Stalls</button>
            <button class="tab-link" onclick="openTab(event, 'tab-foods')">Foods</button>
        </div>

        <!-- Stalls Tab Content -->
        <div id="tab-stalls" class="tab-content active">
            <div class="columns">
                <div class="column card">
                    <h2>Your Stalls</h2>
                    <div class="item-list">
                        <?php foreach ($stalls as $stall): ?>
                            <div class="item" id="stall-<?= $stall['id']; ?>">
                                <p><strong>Stall Name:</strong> <?= htmlspecialchars($stall['name']); ?></p>
                                <p><strong>Cuisine Type:</strong> <?= htmlspecialchars($stall['cuisine_type']); ?></p>
                                <p><strong>Status:</strong> <?= $stall['is_open'] ? 'Open' : 'Closed'; ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Foods Tab Content -->
        <div id="tab-foods" class="tab-content" style="display: none;">
            <div class="columns">
                <div class="column card">
                    <h2>Add New Food Item</h2>
                    <form method="POST" action="../controllers/food_handler.php?action=add" enctype="multipart/form-data">
                        <label for="food-name">Food Name*</label>
                        <input type="text" id="food-name" name="name" placeholder="Enter Food Name" required>
                        
                        <label for="food-price">Price*</label>
                        <input type="number" id="food-price" name="price" step="0.01" required>

                        <label for="food-description">Description</label>
                        <textarea id="food-description" name="description" placeholder="Enter Description"></textarea>

                        <label for="is-halal">Halal</label>
                        <input type="checkbox" id="is-halal" name="is_halal" value="1">

                        <label for="is-vegetarian">Vegetarian</label>
                        <input type="checkbox" id="is-vegetarian" name="is_vegetarian" value="1">

                        <label for="is-in-stock">In Stock</label>
                        <input type="checkbox" id="is-in-stock" name="is_in_stock" value="1" checked>

                        <button type="submit" class="btn btn-primary">Add Food Item</button>
                    </form>
                </div>

                <!-- Existing Foods -->
                <div class="column card">
                    <h2>Your Food Items</h2>
                    <div class="item-list">
                        <?php foreach ($foods as $food): ?>
                            <div class="item" id="food-<?= $food['id']; ?>">
                                <p><strong>Food Name:</strong> <?= htmlspecialchars($food['name']); ?></p>
                                <p><strong>Price:</strong> $<?= number_format($food['price'], 2); ?></p>
                                <p><strong>Description:</strong> <?= htmlspecialchars($food['description']); ?></p>
                                <p><strong>Halal:</strong> <?= $food['is_halal'] ? 'Yes' : 'No'; ?></p>
                                <p><strong>Vegetarian:</strong> <?= $food['is_vegetarian'] ? 'Yes' : 'No'; ?></p>
                                <p><strong>Status:</strong> <?= $food['is_in_stock'] ? 'In Stock' : 'Out of Stock'; ?></p>
                                <div class="buttons">
                                    <button class="btn btn-edit" onclick="toggleEdit('food-<?= $food['id']; ?>')">Edit</button>
                                    <button class="btn btn-delete" onclick="if(confirm('Delete this food item?')) { window.location.href='../controllers/food_handler.php?action=delete&id=<?= $food['id']; ?>' }">Delete</button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/js/dashboard.js"></script>
</body>
</html>
