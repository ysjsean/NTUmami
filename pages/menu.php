<?php
include_once '../includes/db_connect.php';

// Filters and pagination
$canteenId = isset($_GET['canteen']) ? $_GET['canteen'] : null;
$filter = isset($_GET['filter']) ? $_GET['filter'] : null;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$itemsPerPage = 5;
$offset = ($page - 1) * $itemsPerPage;

// Query to get canteens
$canteensQuery = "SELECT * FROM canteens";
$canteensResult = mysqli_query($conn, $canteensQuery);

// Query to fetch foods based on canteen and filter
$foodsQuery = "SELECT foods.*, stalls.name AS stall_name, stalls.cuisine_type, canteens.name AS canteen_name, canteens.location
                FROM foods
                JOIN stalls ON foods.stall_id = stalls.id
                JOIN canteens ON stalls.canteen_id = canteens.id";

if ($canteenId) {
    $foodsQuery .= " WHERE canteens.id = $canteenId";
}
if ($filter) {
    $foodsQuery .= " AND (foods.is_vegetarian = $filter OR foods.is_halal = $filter)";
}
$foodsQuery .= " LIMIT $itemsPerPage OFFSET $offset";
$foodsResult = mysqli_query($conn, $foodsQuery);

// Count for pagination
$totalItemsQuery = "SELECT COUNT(*) as count FROM foods";
$totalItemsResult = mysqli_query($conn, $totalItemsQuery);
$totalItems = mysqli_fetch_assoc($totalItemsResult)['count'];
$totalPages = ceil($totalItems / $itemsPerPage);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NTU Menu</title>
    <style>
        :root {
            --primary-color: #14532d;
            --primary-bg-color: #EDF4EE;
            --secondary-color: #ff774e;
            --text-color-light: white;
            --text-color-dark: #14532d;
            --font-size-large: 24px;
            --font-size-medium: 18px;
            --font-size-small: 14px;
        }

        .container {
            width: 1280px;
            max-width: 100%;
            margin: auto;
            background-color: var(--primary-bg-color);
        }

        .filter-section {
            padding: 20px;
            background-color: var(--primary-color);
            color: var(--text-color-light);
        }

        .foods-section {
            margin-top: 20px;
        }

        .canteen-group {
            margin-bottom: 30px;
        }

        .stall-group {
            margin-top: 15px;
            padding: 15px;
            background-color: var(--text-color-light);
            border: 1px solid var(--primary-color);
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        }

        .food-item {
            display: flex;
            margin-top: 10px;
        }

        .food-image {
            width: 100px;
            height: 100px;
            border-radius: 8px;
            margin-right: 15px;
        }

        .food-details {
            font-size: var(--font-size-small);
        }

        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        .pagination a {
            margin: 0 5px;
            padding: 8px 12px;
            color: var(--primary-color);
            text-decoration: none;
            border: 1px solid var(--primary-color);
            border-radius: 4px;
        }

        .pagination a.active {
            background-color: var(--secondary-color);
            color: var(--text-color-light);
        }
    </style>
</head>
<body>
<main class="container">
    <!-- Filter Section -->
    <div class="filter-section">
        <h2>Filter by Canteen & Preferences</h2>
        <form method="GET" action="">
            <label for="canteen">Select Canteen:</label>
            <select name="canteen" id="canteen">
                <option value="">All</option>
                <?php while ($canteen = mysqli_fetch_assoc($canteensResult)) : ?>
                    <option value="<?= $canteen['id'] ?>" <?= $canteenId == $canteen['id'] ? 'selected' : '' ?>>
                        <?= $canteen['name'] ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <label for="filter">Dietary Filter:</label>
            <select name="filter" id="filter">
                <option value="">None</option>
                <option value="1" <?= $filter === '1' ? 'selected' : '' ?>>Vegetarian</option>
                <option value="2" <?= $filter === '2' ? 'selected' : '' ?>>Halal</option>
            </select>

            <button type="submit">Apply Filters</button>
        </form>
    </div>

    <!-- Foods by Canteen and Location -->
    <div class="foods-section">
        <?php while ($food = mysqli_fetch_assoc($foodsResult)) : ?>
            <div class="canteen-group">
                <h3><?= $food['canteen_name'] ?> - <?= $food['location'] ?></h3>
                
                <div class="stall-group">
                    <h4>Stall: <?= $food['stall_name'] ?> (<?= $food['cuisine_type'] ?>)</h4>
                    
                    <div class="food-item">
                        <img src="<?= $food['image_url'] ?>" alt="<?= $food['name'] ?> image" class="food-image">
                        <div class="food-details">
                            <h5><?= $food['name'] ?> - $<?= $food['price'] ?></h5>
                            <p><?= $food['description'] ?></p>
                            <p>Diet: <?= $food['is_vegetarian'] ? 'Vegetarian' : '' ?> <?= $food['is_halal'] ? '| Halal' : '' ?></p>
                        </div>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>

    <!-- Pagination Section -->
    <div class="pagination">
        <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
            <a href="?page=<?= $i ?>&canteen=<?= $canteenId ?>&filter=<?= $filter ?>" 
                class="<?= $i == $page ? 'active' : '' ?>">
                <?= $i ?>
            </a>
        <?php endfor; ?>
    </div>
</main>
</body>
</html>
