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
</head>

<body>

<?php include '../includes/header.php'; ?>

    <h1 class="title">Locations</h1>
    <div class="locations-grid">
        <?php
        // Database credentials
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "ntumami";

        // Create connection
        $conn = new mysqli($servername, $username, $password, $dbname);

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // SQL query to fetch canteen data
        $sql = "SELECT * FROM canteens";
        $result = $conn->query($sql);

        // Check if there are results
        if ($result->num_rows > 0) {
            // Output data for each row
            while($row = $result->fetch_assoc()) {
                echo '<div class="location-card">';
                echo '<img src="' . htmlspecialchars($row["image_url"]) . '" alt="' . htmlspecialchars($row["name"]) . '">';
                echo '<h3>' . htmlspecialchars($row["name"]) . '</h3>';
                echo '<p class="location-address">' . htmlspecialchars($row["address"]) . '</p>';
                echo '<p>' . htmlspecialchars($row["description"]) . '</p>';
                echo '</div>';
            }
        } else {
            echo "<p>No locations available.</p>";
        }

        // Close connection
        $conn->close();
        ?>
    </div>

<?php include '../includes/footer.php'; ?>

</body>
</html>
