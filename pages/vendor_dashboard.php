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
                                    <p><strong>Status:</strong> <?= $stall['is_open'] ? 'Open' : 'Closed'; ?></p>
                                    <button class="btn btn-edit" onclick="toggleEdit('stall')">Edit</button>
                                </div>

                                <form id="stall-edit" class="edit-mode" method="POST" action="../controllers/stall_handler.php?action=update" style="display: none;">
                                    <input type="hidden" name="stall_id" value="<?= $stall['id']; ?>">
                                    <label>Stall Name</label>
                                    <input type="text" name="name" value="<?= htmlspecialchars($stall['name']); ?>" required>

                                    <label>Cuisine Type</label>
                                    <select name="cuisine_type" required>
                                        <option value="Chinese" <?= $stall['cuisine_type'] === 'Chinese' ? 'selected' : ''; ?>>Chinese</option>
                                        <!-- Add more cuisine options here -->
                                    </select>

                                    <label>Status</label>
                                    <select name="is_open">
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
                        <input type="text" id="food-name" name="name" required>
                        <div class="error-message" id="food-name-error"></div> <!-- Error message div -->

                        <!-- Price Field -->
                        <label for="food-price">Price*</label>
                        <input type="number" id="food-price" name="price" step="0.01" required>
                        <div class="error-message" id="food-price-error"></div> <!-- Error message div -->

                        <!-- Description Field -->
                        <label for="food-description">Description</label>
                        <textarea id="food-description" name="description"></textarea>

                        <!-- Image Upload Field -->
                        <label for="food-image">Upload Image*</label>
                        <input type="file" id="food-image" name="image" accept="image/*" required>
                        <p class="file-input-info">Max size 2MB, JPEG or PNG only.</p>
                        <div class="error-message" id="food-image-error"></div> <!-- Error message div -->

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

                            <form id="profile-edit" class="edit-mode" method="POST" action="../controllers/vendor_handler.php?action=update" style="display: none;">
                                <input type="hidden" name="user_id" value="<?= $userId; ?>">

                                <label for="username">Username</label>
                                <input type="text" id="username" name="username" value="<?= htmlspecialchars($user['username']); ?>" disabled>

                                <label for="name">Name*</label>
                                <input type="text" id="name" name="name" value="<?= htmlspecialchars($user['name']); ?>" required>

                                <label for="business_name">Business Name*</label>
                                <input type="text" id="business_name" name="business_name" value="<?= htmlspecialchars($vendor['business_name']); ?>" required>

                                <label for="contact_number">Contact Number*</label>
                                <input type="text" id="contact_number" name="contact_number" value="<?= htmlspecialchars($vendor['contact_number']); ?>" required>

                                <!-- Password Change Section -->
                                <label for="password">New Password</label>
                                <input type="password" id="password" name="password" placeholder="Enter new password">

                                <label for="confirm_password">Confirm New Password</label>
                                <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm new password">

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
