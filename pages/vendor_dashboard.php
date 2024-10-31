<?php
session_start();
if (!isset($_SESSION['vendor_id'])) {
    header('Location: login.php');
    exit();
}

include '../includes/db_connect.php';

// Fetch vendor's stalls, canteen, and location details
$vendor_id = $_SESSION['vendor_id'];
$stmt = $db->prepare("
    SELECT stalls.*, canteens.name AS canteen_name, locations.name AS location_name
    FROM stalls
    INNER JOIN canteens ON stalls.canteen_id = canteens.id
    INNER JOIN location ON canteens.location_id = location.id
    WHERE stalls.vendor_id = ?
");
$stmt->bind_param("i", $vendor_id);
$stmt->execute();
$stalls = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Fetch food items for each stall
$foods = [];
foreach ($stalls as $stall) {
    $stmt = $db->prepare("SELECT * FROM foods WHERE stall_id = ?");
    $stmt->bind_param("i", $stall['id']);
    $stmt->execute();
    $foods[$stall['id']] = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Vendor Dashboard</title>
    <link rel="stylesheet" href="../assets/css/dashboard.css">
</head>
<body>
    <h1>Welcome, <?= htmlspecialchars($_SESSION['vendor_name']); ?>!</h1>

    <!-- Stall Details and Update Form -->
    <?php if ($stalls): ?>
        <?php foreach ($stalls as $stall): ?>
            <h2>Stall: <?= htmlspecialchars($stall['name']); ?> in <?= htmlspecialchars($stall['canteen_name']); ?> (<?= htmlspecialchars($stall['location_name']); ?>)</h2>
            <p>Cuisine: <?= htmlspecialchars($stall['cuisine_type']); ?></p>
            <p>Status: <?= $stall['is_open'] ? 'Open' : 'Closed'; ?></p>

            <!-- Update Stall -->
            <form method="POST" action="../controllers/stall_handler.php?action=update">
                <input type="hidden" name="stall_id" value="<?= $stall['id']; ?>">
                <input type="text" name="name" value="<?= htmlspecialchars($stall['name']); ?>" required placeholder="Stall Name">
                <select name="cuisine_type" required>
                    <option value="Chinese" <?= $stall['cuisine_type'] === 'Chinese' ? 'selected' : ''; ?>>Chinese</option>
                    <option value="Western" <?= $stall['cuisine_type'] === 'Western' ? 'selected' : ''; ?>>Western</option>
                    <!-- Add other cuisine types as needed -->
                </select>
                <label>
                    <input type="checkbox" name="is_open" <?= $stall['is_open'] ? 'checked' : ''; ?>> Open
                </label>
                <button type="submit">Update Stall</button>
            </form>

            <!-- Add Food Item to Stall -->
            <h3>Add Food Item</h3>
            <form method="POST" action="../controllers/food_handler.php?action=add">
                <input type="hidden" name="stall_id" value="<?= $stall['id']; ?>">
                <input type="text" name="name" placeholder="Food Name" required>
                <textarea name="description" placeholder="Description"></textarea>
                <input type="number" name="price" placeholder="Price" step="0.01" required>
                <label>
                    <input type="checkbox" name="is_halal"> Halal
                </label>
                <label>
                    <input type="checkbox" name="is_vegetarian"> Vegetarian
                </label>
                <label>
                    <input type="checkbox" name="is_in_stock" checked> In Stock
                </label>
                <button type="submit">Add Food</button>
            </form>

            <!-- List and Update/Delete Food Items -->
            <h3>Food Items</h3>
            <?php foreach ($foods[$stall['id']] as $food): ?>
                <div>
                    <h4><?= htmlspecialchars($food['name']); ?> - $<?= $food['price']; ?></h4>
                    <p><?= htmlspecialchars($food['description']); ?></p>
                    <p>Status: <?= $food['is_in_stock'] ? 'In Stock' : 'Out of Stock'; ?></p>

                    <!-- Update Food Item -->
                    <form method="POST" action="../controllers/food_handler.php?action=update">
                        <input type="hidden" name="food_id" value="<?= $food['id']; ?>">
                        <input type="text" name="name" value="<?= htmlspecialchars($food['name']); ?>" required>
                        <textarea name="description"><?= htmlspecialchars($food['description']); ?></textarea>
                        <input type="number" name="price" value="<?= $food['price']; ?>" step="0.01" required>
                        <label>
                            <input type="checkbox" name="is_halal" <?= $food['is_halal'] ? 'checked' : ''; ?>> Halal
                        </label>
                        <label>
                            <input type="checkbox" name="is_vegetarian" <?= $food['is_vegetarian'] ? 'checked' : ''; ?>> Vegetarian
                        </label>
                        <label>
                            <input type="checkbox" name="is_in_stock" <?= $food['is_in_stock'] ? 'checked' : ''; ?>> In Stock
                        </label>
                        <button type="submit">Update Food</button>
                    </form>

                    <!-- Delete Food Item -->
                    <form method="POST" action="../controllers/food_handler.php?action=delete">
                        <input type="hidden" name="food_id" value="<?= $food['id']; ?>">
                        <button type="submit" onclick="return confirm('Are you sure you want to delete this food item?');">Delete Food</button>
                    </form>
                </div>
            <?php endforeach; ?>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No stall found for this vendor.</p>
    <?php endif; ?>

    <a href="../controllers/logout.php">Logout</a>
</body>
</html>
