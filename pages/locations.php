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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Locations</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/global.css">
    <link rel="stylesheet" href="../assets/css/locations.css">
    <script src="../assets/js/header.js" defer></script>
    
</head>

<body>

<?php include '../includes/header.php'; ?>

    <h1 class="title">Locations</h1>
    <div class="locations-grid">
        <?php
        include '../includes/db_connect.php';

        // Fetch canteen data
        $sql = "SELECT * FROM canteens";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo '<form method="GET" action="menu.php" class="location-card">';
                echo '<input type="hidden" name="canteenFilter" value="' . htmlspecialchars($row["id"]) . '">';
                echo '<img src="' . htmlspecialchars($row["image_url"]) . '" alt="' . htmlspecialchars($row["name"]) . '">';
                echo '<h3>' . htmlspecialchars($row["name"]) . '</h3>';
                echo '<p class="location-address">' . htmlspecialchars($row["address"]) . '</p>';
                echo '<p>' . htmlspecialchars($row["description"]) . '</p>';
                echo '</form>';
            }
        } else {
            echo "<p>No locations available.</p>";
        }

        $conn->close();
        ?>
    </div>

<?php include '../includes/footer.php'; ?>

<script>
    document.querySelectorAll('.location-card').forEach(card => {
        card.addEventListener('click', () => {
            card.submit();
        });
    });
</script>

</body>
</html>
