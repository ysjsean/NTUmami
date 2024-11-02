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
    <title>NTU Food Delivery</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="./assets/css/global.css">
    <link rel="stylesheet" href="./assets/css/index.css">

    <script src="./assets/js/header.js" defer></script>
</head>
<body>
    <?php include './includes/header.php'; ?>

    <main>
        <!-- Banner Section -->
        <section class="banner">
            <img src="./assets/images/Main Page Images/banner.png" alt="Fresh Cooked Food">
            <div class="banner-text">
                <h1>Fresh Cooked Food, Only in NTU</h1>
            </div>
        </section>

        <div class="container">
            <!-- Availability Section -->
            <section class="availability">
            <img src="./assets/images/Main Page Images/ntu-foodguide.png" alt="Check Availability" class="availability-left-img">
                <div class="availability-content">
                    <img src="./assets/images/Main Page Images/check-availability.png" alt="New Availability Icon" class="availability-right-img">
                
                    <div class="availability-text">
                        <h2>Check where we are Available</h2>
                        <button>Check Now</button>
                    </div>
                </div>
            </section>

            <!-- Features Section -->
            <section class="features">
                <div class="feature">
                    <img src="./assets/images/Main Page Images/quality.png" alt="Quality Ingredients">
                    <p>Quality Ingredients</p>
                </div>
                <div class="feature">
                    <img src="./assets/images/Main Page Images/authentic.png" alt="Authentic Meals">
                    <p>Authentic Meals</p>
                </div>
                <div class="feature">
                    <img src="./assets/images/Main Page Images/order.png" alt="Order Online">
                    <p>Order Online</p>
                </div>
            </section>

            <!-- Popular Dishes Section -->
            <section class="dishes">
                <div class="dish">
                    <img src="./assets/images/Main Page Images/laksa.jpg" alt="Laksa">
                    <h3>Laksa</h3>
                    <button>Add to Cart</button>
                </div>
                <div class="dish">
                    <img src="./assets/images/Main Page Images/roti-prata.jpg" alt="Roti Prata">
                    <h3>Roti Prata</h3>
                    <button>Add to Cart</button>
                </div>
                <div class="dish">
                    <img src="./assets/images/Main Page Images/chicken-rice.jpg" alt="Chicken Rice">
                    <h3>Chicken Rice</h3>
                    <button>Add to Cart</button>
                </div>
            </section>
        </div>
        
    </main>

    <?php include './includes/footer.php'; ?>

</body>
</html>
