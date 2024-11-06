<?php
    session_start();
    if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
        header('Location: ./pages/admin_dashboard.php');
        exit();
    }
    if (isset($_SESSION['role']) && $_SESSION['role'] === 'vendor') {
        header('Location: ./pages/vendor_dashboard.php');
        exit();
    }

    // Database connection
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "NTUmami";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Fetch data from database
    $canteens = $conn->query("SELECT * FROM canteens")->fetch_all(MYSQLI_ASSOC);
    $stalls = $conn->query("SELECT * FROM stalls")->fetch_all(MYSQLI_ASSOC);
    $foods = $conn->query("SELECT * FROM foods")->fetch_all(MYSQLI_ASSOC);

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

    <script src="../assets/js/header.js" defer></script>
</head>

<body>

<?php include '../includes/header.php'; ?>

<div class="container">

    <!-- Filter Section -->
    <aside class="filter">
        <h3>Filter:</h3>

        <!-- Location Filter -->
        <div class="dropdown">
            <label for="canteenFilter">Location ▼</label>
            <div class="dropdown-content" id="canteenFilter">
                <?php foreach ($canteens as $canteen): ?>
                    <label>
                        <input type="checkbox" class="filter-option canteen-option" value="<?= $canteen['id'] ?>"> 
                        <?= htmlspecialchars($canteen['name']) ?>
                    </label><br>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Cuisine Filter -->
        <div class="dropdown">
            <label for="cuisineFilter">Cuisine ▼</label>
            <div class="dropdown-content" id="cuisineFilter">
                <?php 
                $cuisines = ["Chinese", "Western", "Indian", "Malay", "Japanese", "Korean", "Taiwan", "Thai", "Fusion", "Drinks"];
                foreach ($cuisines as $cuisine): ?>
                    <label>
                        <input type="checkbox" class="filter-option cuisine-option" value="<?= $cuisine ?>"> 
                        <?= $cuisine ?>
                    </label><br>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Dietary Filter -->
        <div class="dropdown">
            <label for="dietaryFilter">Dietary ▼</label>
            <div class="dropdown-content" id="dietaryFilter">
                <label><input type="checkbox" class="filter-option dietary-option" value="halal"> Halal</label><br>
                <label><input type="checkbox" class="filter-option dietary-option" value="vegetarian"> Vegetarian</label><br>
            </div>
        </div>
    </aside>


    <!-- Menu Display Section -->
    <main class="menu">
        <h1>Menu</h1>
        <div id="canteenContainer">
            <?php foreach ($canteens as $canteen): ?>
                <div class="canteen">
                    <h2><?= htmlspecialchars($canteen['name']) ?></h2>
                    <?php foreach ($stalls as $stall): ?>
                        <?php if ($stall['canteen_id'] == $canteen['id']): ?>
                            <div class="stall">
                                <h3><?= htmlspecialchars($stall['name']) ?></h3>
                                <div class="food-container">
                                <?php foreach ($foods as $food): ?>
                                    <?php if ($food['stall_id'] == $stall['id']): ?>
                                        <a href="food_details.php?id=<?= $food['id'] ?>" class="food-card-link">
                                            <div class="food-item">
                                                <img src="<?= htmlspecialchars($food['image_url']) ?>" alt="<?= htmlspecialchars($food['name']) ?>">
                                                <p><?= htmlspecialchars($food['name']) ?></p>
                                                <p class="description"><?= htmlspecialchars($food['description']) ?></p>
                                                <div class="dietary-icons">
                                                    <?= $food['is_halal'] == 1 ? '<span class="icon halal">🕌 Halal</span>' : '' ?>
                                                    <?= $food['is_vegetarian'] == 1 ? '<span class="icon vegetarian">🌱 Vegetarian</span>' : '' ?>
                                                </div>
                                                <p class="price">$<?= number_format($food['price'], 2) ?></p>
                                                <button class="view-details-btn">View Details</button>
                                            </div>
                                        </a>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </main>
</div>

<script src="../assets/js/menu.js" defer></script>

<?php include '../includes/footer.php'; ?>

</body>
</html>