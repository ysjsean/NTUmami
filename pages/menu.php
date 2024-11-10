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

//Remove Hashtag if using website outside of Canteen Hours
#date_default_timezone_set('Asia/Singapore');

if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    header('Location: ./pages/admin_dashboard.php');
    exit();
}
if (isset($_SESSION['role']) && $_SESSION['role'] === 'vendor') {
    header('Location: ./pages/vendor_dashboard.php');
    exit();
}

include '../includes/db_connect.php';

// Check if the reset button was clicked
if (isset($_POST['reset'])) {
    // Redirect to the same page without any POST data (clears the filters)
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}


// Fetch canteens, stalls, and foods
$canteens = $conn->query("SELECT * FROM canteens")->fetch_all(MYSQLI_ASSOC);
$stalls = $conn->query("SELECT * FROM stalls")->fetch_all(MYSQLI_ASSOC);
$foods = $conn->query("SELECT * FROM foods")->fetch_all(MYSQLI_ASSOC);
$canteen_hours = $conn->query("SELECT * FROM canteen_hours")->fetch_all(MYSQLI_ASSOC);


// Apply filters if set
$canteenFilter = $_GET['canteenFilter'] ?? $_POST['canteenFilter'] ?? '';
$cuisineFilter = $_GET['cuisineFilter'] ?? $_POST['cuisineFilter'] ?? '';
$dietaryFilter = $_POST['dietaryFilter'] ?? '';

function isCanteenOpen($canteen_id, $canteen_hours) {
    $currentDay = date('D'); // Current day in Mon, Tue, etc. format
    $currentTime = date('H:i:s'); // Current time in HH:MM:SS format

    foreach ($canteen_hours as $hour) {
        if ($hour['canteen_id'] == $canteen_id && strpos($hour['days'], $currentDay) !== false) {
            // Check if current time is within the open and close time
            if ($currentTime >= $hour['open_time'] && $currentTime <= $hour['close_time']) {
                return true; // Canteen is open
            }
        }
    }
    return false; // Canteen is closed
}

// Filter canteens based on the selected filter
$filteredCanteens = array_filter($canteens, function($canteen) use ($canteenFilter, $canteen_hours) {
    // If no specific canteen filter is applied, include all canteens (both open and closed)
    if ($canteenFilter === '') {
        return true;
    }

    // If a canteen filter is applied, check if it matches the selected canteen
    if ($canteen['id'] == $canteenFilter) {
        return true;
    }

    return false;
});

// Filter stalls and foods based on selections
$filteredStalls = array_filter($stalls, function($stall) use ($canteenFilter, $cuisineFilter) {
    return ($canteenFilter === '' || $stall['canteen_id'] == $canteenFilter) &&
            ($cuisineFilter === '' || $stall['cuisine_type'] === $cuisineFilter);
});

$filteredFoods = array_filter($foods, function($food) use ($dietaryFilter) {
    $isHalal = $food['is_halal'] == 1;
    $isVegetarian = $food['is_vegetarian'] == 1;
    return $dietaryFilter === '' ||
            ($dietaryFilter === 'halal' && $isHalal) ||
            ($dietaryFilter === 'vegetarian' && $isVegetarian);
});

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/global.css">
    <link rel="stylesheet" href="../assets/css/menu.css">

    <script defer src="../assets/js/notification.js"></script>
    <script src="../assets/js/header.js" defer></script>
</head>
<body>

<!-- Notification container -->
<div id="notification" class="notification <?php echo $notificationType; ?>">
    <?php echo $notificationMessage; ?>
</div>

<?php 
    include '../includes/cart_number.php'; 
    include '../includes/header.php'; 
?>

<div class="container">
    <!-- Filter Section -->
    <form method="POST" class="filter">
        <h3>Filter:</h3>
        <div>
            <label for="canteenFilter">Location</label>
            <select id="canteenFilter" name="canteenFilter">
                <option value="">All Canteens</option>
                <?php foreach ($canteens as $canteen): ?>
                    <option value="<?= $canteen['id'] ?>" <?= $canteenFilter == $canteen['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($canteen['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label for="cuisineFilter">Cuisine</label>
            <select id="cuisineFilter" name="cuisineFilter">
                <option value="">All Cuisines</option>
                <option value="Chinese" <?= $cuisineFilter == 'Chinese' ? 'selected' : '' ?>>Chinese</option>
                <option value="Western" <?= $cuisineFilter == 'Western' ? 'selected' : '' ?>>Western</option>
                <option value="Indian" <?= $cuisineFilter == 'Indian' ? 'selected' : '' ?>>Indian</option>
                <option value="Malay" <?= $cuisineFilter == 'Malay' ? 'selected' : '' ?>>Malay</option>
                <option value="Japanese" <?= $cuisineFilter == 'Japanese' ? 'selected' : '' ?>>Japanese</option>
                <option value="Korean" <?= $cuisineFilter == 'Korean' ? 'selected' : '' ?>>Korean</option>
                <option value="Taiwan" <?= $cuisineFilter == 'Taiwan' ? 'selected' : '' ?>>Taiwan</option>
                <option value="Thai" <?= $cuisineFilter == 'Thai' ? 'selected' : '' ?>>Thai</option>
                <option value="Fusion" <?= $cuisineFilter == 'Fusion' ? 'selected' : '' ?>>Fusion</option>
                <option value="Drinks" <?= $cuisineFilter == 'Drinks' ? 'selected' : '' ?>>Drinks</option>
            </select>
        </div>
        <div>
            <label for="dietaryFilter">Dietary</label>
            <select id="dietaryFilter" name="dietaryFilter">
                <option value="">All</option>
                <option value="halal" <?= $dietaryFilter == 'halal' ? 'selected' : '' ?>>Halal</option>
                <option value="vegetarian" <?= $dietaryFilter == 'vegetarian' ? 'selected' : '' ?>>Vegetarian</option>
            </select>
        </div>
        <button type="submit" class="apply-filters-btn">Apply Filters</button>
        <button type="submit" name="reset" class="reset-filters-btn">Reset Filters</button>
    </form>

    <!-- Menu Display Section -->
    <main class="menu">
        <h1>Menu</h1>
        <div id="canteenContainer">
            <?php foreach ($filteredCanteens as $canteen): ?>
                <?php
                    $isOpen = isCanteenOpen($canteen['id'], $canteen_hours);
                    
                    // Skip displaying stalls and foods if the canteen is closed
                    if (!$isOpen) {
                        echo "<div class='canteen'>
                                <h2>" . htmlspecialchars($canteen['name']) . "</h2>
                                <p class='closed-notice'>This canteen is currently closed.</p>
                            </div>";
                        continue;
                    }
                    
                    // Filter stalls within this canteen based on filter selections
                    $canteenStalls = array_filter($filteredStalls, function($stall) use ($canteen) {
                        return $stall['canteen_id'] == $canteen['id'];
                    });

                    // Only include stalls that have matching food items
                    $canteenStalls = array_filter($canteenStalls, function($stall) use ($filteredFoods) {
                        foreach ($filteredFoods as $food) {
                            if ($food['stall_id'] == $stall['id']) {
                                return true; // At least one matching food item found
                            }
                        }
                        return false; // No matching food items in this stall
                    });

                    // If no stalls with matching foods, skip this canteen
                    if (count($canteenStalls) == 0) continue;
                ?>

                <!-- Display open canteens with their stalls and foods -->
                <div class="canteen">
                    <h2><?= htmlspecialchars($canteen['name']) ?></h2>
                    <?php foreach ($canteenStalls as $stall): ?>
                        <div class="stall">
                            <h3><?= htmlspecialchars($stall['name']) ?></h3>
                            <?php if ($stall['is_open'] == 0): ?>
                                <div class="stall-closed">This stall is currently closed.</div>
                            <?php else: ?>
                                <div class="food-container">
                                    <?php foreach ($filteredFoods as $food): ?>
                                        <?php if ($food['stall_id'] == $stall['id']): ?>
                                            <div class="food-item">
                                                <img src="<?= htmlspecialchars($food['image_url']) ?>" alt="<?= htmlspecialchars($food['name']) ?>">
                                                <p class="name"><?= htmlspecialchars($food['name']) ?></p>
                                                <p class="description"><?= htmlspecialchars($food['description']) ?></p>
                                                <div class="dietary-icons">
                                                    <?= $food['is_halal'] ? '<span class="icon halal">✡️ Halal</span>' : '' ?>
                                                    <?= $food['is_vegetarian'] ? '<span class="icon vegetarian">🌱 Vegetarian</span>' : '' ?>
                                                </div>
                                                <p class="price">$<?= number_format($food['price'], 2) ?></p>
                                                <?php if ($food['is_in_stock'] == 1): ?>
                                                    <form action="../controllers/cart_handler.php?action=add_to_cart" method="POST">
                                                        <input type="hidden" name="food_id" value="<?= $food['id'] ?>">
                                                        <input type="hidden" name="quantity" value="1">
                                                        <button type="submit" class="add-to-cart-btn">Add to Cart</button>
                                                    </form>
                                                <?php else: ?>
                                                    <button class="add-to-cart-btn disabled" disabled>Out of Stock</button>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </main>


</div>

<button id="backToTop" onclick="scrollToTop()">Back to Top</button>

<script src="../assets/js/menu.js"></script>

<?php include '../includes/footer.php'; ?>
</body>
</html>
