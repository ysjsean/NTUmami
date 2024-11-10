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

$vendor = $conn->query("SELECT * FROM vendors WHERE user_id = $userId")->fetch_assoc();
$user = $conn->query("SELECT * FROM users WHERE id = $userId")->fetch_assoc();
$vendorId = $vendor['id'];

// Fetch stalls and food items for this vendor
$stalls = $conn->query("SELECT * FROM stalls WHERE vendor_id = $vendorId LIMIT 1")->fetch_all(MYSQLI_ASSOC) ?? [];
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
    <script defer src="../assets/js/dashboard.js"></script>
    <script defer src="../assets/js/vendor_formValidation.js"></script>

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
            <button class="tab-link" onclick="openTab(event, 'tab-orders')">Orders</button>
            <button class="tab-link" onclick="openTab(event, 'tab-summary')">Order Summary</button>
            <button class="tab-link" onclick="openTab(event, 'tab-profile')">Profile</button>
        </div>

        <!-- Stalls Tab Content -->
        <div id="tab-stalls" class="tab-content active">
            <div class="columns">
                <div class="column card">
                    <h2>Your Stall</h2>
                    <div class="item-list">
                        <?php if (!empty($stalls)): ?>
                            <?php $stall = $stalls[0]; ?>
                            <div class="item" id="stall">
                                <div id="stall-view" class="view-mode">
                                    <p><strong>Stall Name:</strong> <?= htmlspecialchars($stall['name']); ?></p>
                                    <p><strong>Cuisine Type:</strong> <?= htmlspecialchars($stall['cuisine_type']); ?></p>
                                    <p class="<?= $stall['is_open'] ? 'green-status' : 'red-status'; ?>"><strong>Status:</strong> <?= $stall['is_open'] ? 'Open' : 'Closed'; ?></p>
                                    <button class="btn btn-edit" onclick="toggleEdit('stall')">Edit</button>
                                </div>

                                <form id="stall-edit" class="edit-mode" method="POST" action="../controllers/vendor_stall_handler.php?action=update" style="display: none;">
                                    <input type="hidden" name="stall_id" value="<?= $stall['id']; ?>">

                                    <label for="stall-name">Stall Name*</label>
                                    <input type="text" id="stall-name" name="name" value="<?= htmlspecialchars($stall['name']); ?>" required>
                                    <div class="error-message" id="stall-name-error"></div>

                                    <label for="stall-cuisine-type">Cuisine Type</label>
                                    <select id="stall-cuisine-type" name="cuisine_type" required>
                                        <option value="Chinese" <?= $stall['cuisine_type'] === 'Chinese' ? 'selected' : ''; ?>>Chinese</option>
                                        <option value="Malay" <?= $stall['cuisine_type'] === 'Malay' ? 'selected' : ''; ?>>Malay</option>
                                        <option value="Indian" <?= $stall['cuisine_type'] === 'Indian' ? 'selected' : ''; ?>>Indian</option>
                                        <option value="Western" <?= $stall['cuisine_type'] === 'Western' ? 'selected' : ''; ?>>Western</option>
                                        <option value="Japanese" <?= $stall['cuisine_type'] === 'Japanese' ? 'selected' : ''; ?>>Japanese</option>
                                        <option value="Korean" <?= $stall['cuisine_type'] === 'Korean' ? 'selected' : ''; ?>>Korean</option>
                                        <option value="Taiwan" <?= $stall['cuisine_type'] === 'Taiwan' ? 'selected' : ''; ?>>Taiwan</option>
                                        <option value="Fusion" <?= $stall['cuisine_type'] === 'Fusion' ? 'selected' : ''; ?>>Fusion</option>
                                    </select>
                                    <div class="error-message" id="stall-cuisine-type-error"></div>

                                    <label for="is-open">Status</label>
                                    <select id="is-open" name="is_open">
                                        <option value="1" <?= $stall['is_open'] ? 'selected' : ''; ?>>Open</option>
                                        <option value="0" <?= !$stall['is_open'] ? 'selected' : ''; ?>>Closed</option>
                                    </select>

                                    <div class="buttons">
                                        <button type="submit" class="btn btn-primary">Save</button>
                                        <button type="button" class="btn btn-cancel" onclick="toggleEdit('stall')">Cancel</button>
                                    </div>
                                </form>

                            </div>
                        <?php else: ?>
                            <p>No stall found.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Foods Tab Content -->
        <div id="tab-foods" class="tab-content" style="display: none;">
            <div class="columns">
                <!-- Left card for adding a new food item -->
                <div class="column card add-food-card">
                    <h2>Add New Food Item</h2>
                    <form id="add-food-form" method="POST" action="../controllers/food_handler.php?action=add" enctype="multipart/form-data">
                        
                        <!-- Food Name Field -->
                        <label for="food-name">Food Name*</label>
                        <input type="text" id="food-form-name" name="name" required>
                        <div class="error-message" id="food-form-name-error"></div> <!-- Error message div -->

                        <!-- Price Field -->
                        <label for="food-price">Price*</label>
                        <input type="number" id="food-form-price" name="price" step="0.01" required>
                        <div class="error-message" id="food-form-price-error"></div> <!-- Error message div -->

                        <!-- Description Field -->
                        <label for="food-description">Description</label>
                        <textarea id="food-description" name="description"></textarea>

                        <!-- Image Upload Field -->
                        <label for="food-image">Upload Image*</label>
                        <input type="file" id="food-form-image" name="image" accept="image/*" required>
                        <p class="file-input-info">Max size 2MB, JPEG or PNG only.</p>
                        <div class="error-message" id="food-form-image-error"></div> <!-- Error message div -->

                        <!-- Halal Checkbox -->
                        <div class="checkbox-group">
                            <input type="checkbox" id="is-halal" name="is_halal" value="1">
                            <label for="is-halal">Halal</label>
                        </div>

                        <!-- Vegetarian Checkbox -->
                        <div class="checkbox-group">
                            <input type="checkbox" id="is-vegetarian" name="is_vegetarian" value="1">
                            <label for="is-vegetarian">Vegetarian</label>
                        </div>

                        <!-- In Stock Checkbox -->
                        <div class="checkbox-group">
                            <input type="checkbox" id="is-in-stock" name="is_in_stock" value="1" checked>
                            <label for="is-in-stock">In Stock</label>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-primary">Add Food Item</button>
                    </form>
                </div>


                <!-- Right card for displaying existing food items -->
                <div class="column card food-items-card">
                    <h2>Your Food Items</h2>
                    <div class="item-list">
                        <?php foreach ($foods as $food): ?>
                            <div class="item food-item" id="food-<?= $food['id']; ?>">
                                <div class="view-mode">
                                    <p><strong>Food Name:</strong> <?= htmlspecialchars($food['name']); ?></p>
                                    <p><strong>Price:</strong> $<?= number_format($food['price'], 2); ?></p>
                                    <p><strong>Halal:</strong> <?= $food['is_halal'] ? 'Yes' : 'No'; ?></p>
                                    <p><strong>Vegetarian:</strong> <?= $food['is_vegetarian'] ? 'Yes' : 'No'; ?></p>
                                    <p class="<?= $food['is_in_stock'] ? 'green-status' : 'red-status'; ?>"><strong>Status:</strong> <?= $food['is_in_stock'] ? 'In Stock' : 'Out of Stock'; ?></p>
                                    <?php if (!empty($food['image_url'])): ?>
                                        <div class="image-view">
                                            <p><strong>Image:</strong></p>
                                            <img src="<?= htmlspecialchars($food['image_url']); ?>" alt="Food Image" class="food-image">
                                        </div>
                                    <?php endif; ?>
                                    <div class="buttons">
                                        <button class="btn btn-edit" onclick="toggleEdit('food-<?= $food['id']; ?>')">Edit</button>
                                        <button class="btn btn-delete" onclick="if(confirm('Delete this food item?')) { window.location.href='../controllers/food_handler.php?action=delete&id=<?= $food['id']; ?>' }">Delete</button>
                                    </div>
                                </div>

                                <!-- Edit Form -->
                                <form id="food-edit-<?= $food['id']; ?>" class="edit-mode" method="POST" action="../controllers/food_handler.php?action=update" enctype="multipart/form-data" style="display: none;">
                                    
                                    <!-- Food ID (Hidden) -->
                                    <input type="hidden" name="food_id" value="<?= $food['id']; ?>">

                                    <!-- Food Name Field -->
                                    <label for="food-edit-<?= $food['id']; ?>-name">Food Name*</label>
                                    <input type="text" id="food-edit-<?= $food['id']; ?>-name" name="name" value="<?= htmlspecialchars($food['name']); ?>" required>
                                    <div class="error-message" id="food-edit-<?= $food['id']; ?>-name-error"></div>

                                    <!-- Price Field -->
                                    <label for="food-edit-<?= $food['id']; ?>-price">Price*</label>
                                    <input type="number" id="food-edit-<?= $food['id']; ?>-price" name="price" step="0.01" value="<?= $food['price']; ?>" required>
                                    <div class="error-message" id="food-edit-<?= $food['id']; ?>-price-error"></div>

                                    <!-- Description Field -->
                                    <label for="food-edit-<?= $food['id']; ?>-description">Description</label>
                                    <textarea id="food-edit-<?= $food['id']; ?>-description" name="description"><?= htmlspecialchars($food['description']); ?></textarea>

                                    <!-- Image Upload Field -->
                                    <label for="food-edit-<?= $food['id']; ?>-image">Update Image*</label>
                                    <input type="file" id="food-edit-<?= $food['id']; ?>-image" name="image" accept="image/*">
                                    <p class="file-input-info">Max size 2MB, JPEG or PNG only.</p>
                                    <div class="error-message" id="food-edit-<?= $food['id']; ?>-image-error"></div>

                                    <!-- Halal Checkbox -->
                                    <div class="checkbox-group">
                                        <input type="checkbox" id="is_halal-<?= $food['id']; ?>" name="is_halal" value="1" <?= $food['is_halal'] ? 'checked' : ''; ?>>
                                        <label for="is_halal-<?= $food['id']; ?>">Halal</label>
                                    </div>

                                    <!-- Vegetarian Checkbox -->
                                    <div class="checkbox-group">
                                        <input type="checkbox" id="is_vegetarian-<?= $food['id']; ?>" name="is_vegetarian" value="1" <?= $food['is_vegetarian'] ? 'checked' : ''; ?>>
                                        <label for="is_vegetarian-<?= $food['id']; ?>">Vegetarian</label>
                                    </div>

                                    <!-- In Stock Checkbox -->
                                    <div class="checkbox-group">
                                        <input type="checkbox" id="is_in_stock-<?= $food['id']; ?>" name="is_in_stock" value="1" <?= $food['is_in_stock'] ? 'checked' : ''; ?>>
                                        <label for="is_in_stock-<?= $food['id']; ?>">In Stock</label>
                                    </div>

                                    <!-- Submit and Cancel Buttons -->
                                    <div class="buttons">
                                        <button type="submit" class="btn btn-primary">Save</button>
                                        <button type="button" class="btn btn-cancel" onclick="toggleEdit('food-<?= $food['id']; ?>')">Cancel</button>
                                    </div>
                                </form>

                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

            </div>
        </div>


        <!-- Orders Tab Content -->
        <div id="tab-orders" class="tab-content" style="display: none;">
            <div class="columns">
                <div class="column card">
                    <h2>Incoming Orders</h2>
                    <div class="item-list">
                        <?php
                        // Fetch orders and items for this vendor's stalls
                        $ordersQuery = "
                            SELECT o.id AS order_id, o.total_price, o.eat_in_take_out, o.created_by, oi.special_request,
                                oi.id AS order_item_id, oi.qty, oi.price, f.name AS food_name, oi.status AS item_status
                            FROM orders o
                            JOIN order_items oi ON o.id = oi.order_id
                            JOIN foods f ON oi.food_id = f.id
                            JOIN stalls s ON f.stall_id = s.id
                            WHERE s.vendor_id = $vendorId AND o.status != 'Completed'
                            ORDER BY o.id
                        ";
                        $ordersResult = $conn->query($ordersQuery);

                        $currentOrderId = null;

                        if ($ordersResult && $ordersResult->num_rows > 0) {
                            while ($order = $ordersResult->fetch_assoc()) {
                                if ($currentOrderId !== $order['order_id']) {
                                    // Close the previous order div if there is one
                                    if ($currentOrderId !== null) echo "</div></div>";

                                    // Start a new order block
                                    $currentOrderId = $order['order_id'];
                                    echo "<div class='item order-item'>";
                                    echo "<p><strong>Order ID:</strong> {$order['order_id']}</p>";
                                    echo "<p><strong>Total Price:</strong> \${$order['total_price']}</p>";
                                    echo "<p><strong>Type:</strong> {$order['eat_in_take_out']}</p>";
                                    echo "<h3>Items</h3>";
                                    echo "<div class='order-items-grid'>"; // Start grid layout for items
                                }

                                // Display each item with its own status update form
                                echo "<div class='order-item-detail'>";
                                echo "<p><strong>Item:</strong> {$order['food_name']}</p>";
                                echo "<p><strong>Quantity:</strong> {$order['qty']}</p>";
                                echo "<p><strong>Price:</strong> \${$order['price']}</p>";
                                echo "<p><strong>Special Request:</strong> {$order['special_request']}</p>";

                                // Form for updating individual item status
                                echo "<form method='POST' action='../controllers/vendor_order_handler.php?action=update'>";
                                echo "<input type='hidden' name='order_item_id' value='{$order['order_item_id']}'>";
                                echo "<label for='item-status-{$order['order_item_id']}'>Status:</label>";
                                echo "<select id='item-status-{$order['order_item_id']}' name='item_status'>";
                                echo "<option value='Pending'" . ($order['item_status'] == 'Pending' ? " selected" : "") . ">Pending</option>";
                                echo "<option value='Preparing'" . ($order['item_status'] == 'Preparing' ? " selected" : "") . ">Preparing</option>";
                                echo "<option value='Ready for Pickup'" . ($order['item_status'] == 'Ready for Pickup' ? " selected" : "") . ">Ready for Pickup</option>";
                                echo "</select>";
                                echo "<button type='submit' class='btn btn-primary'>Update Item Status</button>";
                                echo "</form>";

                                echo "</div>"; // Close order-item-detail
                            }
                            // Close the last grid and order div
                            echo "</div></div>";
                        } else {
                            echo "<p class='no-data'>No incoming orders.</p>";
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Summary Tab Content -->
        <div id="tab-summary" class="tab-content" style="display: none;">
            <div class="columns">
                <div class="column card order-summary">
                    <h2>Order Summary</h2>

                    <?php
                    // Query to get each food item, its total quantity ordered, and revenue
                    $foodSummaryQuery = "
                        SELECT f.name AS food_name, 
                            SUM(oi.qty) AS total_quantity, 
                            SUM(oi.qty * oi.price) AS total_revenue
                        FROM order_items oi
                        JOIN foods f ON oi.food_id = f.id
                        JOIN stalls s ON f.stall_id = s.id
                        WHERE s.vendor_id = $vendorId
                        GROUP BY f.id
                        ORDER BY total_quantity DESC
                    ";
                    $foodSummaryResult = $conn->query($foodSummaryQuery);

                    // Initialize variables for overall summary
                    $overallRevenue = 0;
                    $bestSellingFood = null;
                    $highestQuantity = 0;
                    ?>

                    <!-- Table to display each food item summary -->
                    <table class="summary-table">
                        <thead>
                            <tr>
                                <th>Food Item</th>
                                <th>Quantity Ordered</th>
                                <th>Revenue Earned ($)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($food = $foodSummaryResult->fetch_assoc()): ?>
                                <?php
                                // Calculate overall revenue
                                $overallRevenue += $food['total_revenue'];

                                // Check for best-selling food
                                if ($food['total_quantity'] > $highestQuantity) {
                                    $highestQuantity = $food['total_quantity'];
                                    $bestSellingFood = $food['food_name'];
                                }
                                ?>
                                <tr>
                                    <td><?= htmlspecialchars($food['food_name']); ?></td>
                                    <td><?= $food['total_quantity']; ?></td>
                                    <td><?= number_format($food['total_revenue'], 2); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>

                    <!-- Display overall summary below the table -->
                    <div class="overall-summary">
                        <p><strong>Overall Revenue:</strong> $<?= number_format($overallRevenue, 2); ?></p>
                        <p><strong>Best-Selling Food:</strong> <?= htmlspecialchars($bestSellingFood); ?></p>
                    </div>
                </div>
            </div>
        </div>





        <!-- Profile Tab Content -->
        <div id="tab-profile" class="tab-content" style="display: none;">
            <div class="columns">
                <div class="column card">
                    <h2>Your Profile</h2>
                    <div class="item-list">
                        <div class="item" id="profile">
                            <div id="profile-view" class="view-mode">
                                <p><strong>Name:</strong> <?= htmlspecialchars($user['name']); ?></p>
                                <p><strong>Username:</strong> <?= htmlspecialchars($user['username']); ?></p>
                                <p><strong>Email:</strong> <?= htmlspecialchars($user['email']); ?></p>
                                <p><strong>Business Name:</strong> <?= htmlspecialchars($vendor['business_name']); ?></p>
                                <p><strong>Contact Number:</strong> <?= htmlspecialchars($vendor['contact_number']); ?></p>
                                <button class="btn btn-edit" onclick="toggleEdit('profile')">Edit</button>
                            </div>

                            <form id="profile-edit" class="edit-mode" method="POST" action="../controllers/vendor_profile_handler.php?action=update" style="display: none;" novalidate>
                                <input type="hidden" name="user_id" value="<?= $userId; ?>">

                                <label for="name">Name*</label>
                                <input type="text" id="name" name="name" value="<?= htmlspecialchars($user['name']); ?>" required>
                                <div class="error-message" id="name-error"></div>

                                <label for="business_name">Business Name*</label>
                                <input type="text" id="business_name" name="business_name" value="<?= htmlspecialchars($vendor['business_name']); ?>" required>
                                <div class="error-message" id="business_name-error"></div>

                                <label for="contact_number">Contact Number*</label>
                                <input type="text" id="contact_number" name="contact_number" value="<?= htmlspecialchars($vendor['contact_number']); ?>" required>
                                <div class="error-message" id="contact_number-error"></div>

                                <label for="current_password">Current Password</label>
                                <input type="password" id="current_password" name="current_password" placeholder="Enter current password">

                                <label for="password">New Password</label>
                                <input type="password" id="password" name="password" placeholder="Enter new password">
                                <div class="error-message" id="password-error"></div>

                                <label for="confirm_password">Confirm New Password</label>
                                <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm new password">
                                <div class="error-message" id="confirm_password-error"></div>

                                <div class="buttons">
                                    <button type="submit" class="btn btn-primary">Save</button>
                                    <button type="button" class="btn btn-cancel" onclick="toggleEdit('profile')">Cancel</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
