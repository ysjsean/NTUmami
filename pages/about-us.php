<?php

session_start();

include '../includes/db_connect.php';
include '../includes/cart_number.php';
  
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
    <title>About Us</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/global.css">
    <link rel="stylesheet" href="../assets/css/About Us/feedback.css">
    <link rel="stylesheet" href="../assets/css/About Us/faq.css">
    <link rel="stylesheet" href="../assets/css/About Us/our-story.css">

    <script src="../assets/js/header.js" defer></script>
</head>

<body>

<?php include '../includes/header.php'; ?>

<main>
    <div class="about-header">
        <h1>About Us</h1>
    </div>

<!-- Our Story Section -->
    <div class="section story-section">
        <div class="container">
            <div class="story-title">
                <h2>Our Story</h2>
            </div>
            <div class="story-content">
                <p>
                    At NTU, we know the struggle is real—racing between lectures, juggling assignments, and then realizing
                    you're starving with ten minutes to spare! That's where we come in. We've crafted this platform to make
                    your foodie dreams a reality, connecting you to your favorite campus canteens with just a few clicks.
                    Skip the queues, grab your meal, and get back to crushing those deadlines. No delivery, no drama—just
                    delicious food waiting for you. Because we believe refueling shouldn't slow you down—it's your NTU pit
                    stop, done right!
                </p>
            </div>
            <div class="story-image">
                <img src="../assets/images/About Us/our-story.png" alt="NTU students holding NTU letters">
            </div>
        </div>
    </div>

    <!-- FAQ Section -->
    <div id="faq" class="faq-section">
        <div class="container">
            <div class="faq-title">
                <h2>FAQ</h2>
            </div>
            <div class="faq-questions">
                <h3>Questions</h3>
                <div class="faq-item">
                    <details>
                        <summary>How does the ordering process work?</summary>
                        <p>It's simple! Browse through the canteens, select your favorite dishes, place your order, and choose a pickup time. Once your order is confirmed, just head over to the canteen at your chosen time and grab your meal—no waiting in line!</p>
                    </details>
                </div>
                <hr>
                <div class="faq-item">
                    <details>
                        <summary>Is there a delivery option available?</summary>
                        <p>Nope, no delivery here! We focus on pickup to keep things quick and convenient. You can place your order online and pick it up directly from the canteen.</p>
                    </details>
                </div>
                <hr>
                <div class="faq-item">
                    <details>
                        <summary>Can I cancel or modify my order?</summary>
                        <p>You can cancel or modify your order within a short window after placing it, as long as the canteen hasn't started preparing your meal. Check your order status and act quickly if you need to make changes!</p>
                    </details>
                </div>
                <hr>
                <div class="faq-item">
                    <details>
                        <summary>What if I miss my pickup time?</summary>
                        <p>If you're running late, no worries—your food will be kept aside for a short while. However, we recommend picking it up as close to your selected time as possible to ensure freshness!</p>
                    </details>
                </div>
                <hr>
                <div class="faq-item">
                    <details>
                        <summary>How do I know when my food is ready for pickup?</summary>
                        <p>You'll receive a notification via email once your order is ready. Just show up, grab your food, and go!</p>
                    </details>
                </div>
            </div>
        </div>
        
    </div>

    <div id="feedback" class="feedback-section">
        <div class="container">
            <!-- Title -->
            <div class="feedback-title">
                <h2>Give us your feedback</h2>
            </div>

            <!-- Feedback Form -->
            <div class="feedback-form">
                <form action="#" method="post">
                    <div class="form-group">
                        <input type="text" name="first_name" placeholder="First Name" required>
                        <input type="text" name="last_name" placeholder="Last Name" required>
                    </div>
                    <input type="email" name="email" placeholder="Email" required>
                    <textarea name="message" placeholder="Message" rows="4" required></textarea>
                    <button type="submit">Send</button>
                </form>
            </div>
        </div>
        
    </div>


</main>

<?php include '../includes/footer.php'; ?>

</body>
</html>
