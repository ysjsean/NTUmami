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
    <title>About Us</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="./assets/css/global.css">
    <link rel="stylesheet" href="./assets/css/index.css">

    <script src="./assets/js/header.js" defer></script>

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }

        .about-container {
            width: 100%;
            max-width: 1200px;
            margin: auto;
            padding: 20px;
        }

        .about-header {
            text-align: center;
            padding: 40px 0;
        }

        .about-header h1 {
            font-size: 36px;
            color: #274E44;
        }

        .section {
            background-color: #E7F0ED;
            padding: 40px;
            margin: 20px 0;
            border-radius: 8px;
        }

        .story-section {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
        }

        .story-section img {
            max-width: 40%;
            border-radius: 8px;
        }

        .story-section p {
            font-size: 18px;
            color: #333;
        }

        .faq-section {
            padding: 20px;
        }

        .faq-section h2 {
            font-size: 24px;
            color: #274E44;
        }

        .faq-item {
            margin-top: 10px;
        }

        .faq-item summary {
            font-weight: bold;
            cursor: pointer;
            color: #274E44;
            list-style-type: none;
        }

        .faq-item p {
            padding: 10px 0;
            color: #555;
        }

        .contact-section {
            text-align: center;
            padding: 20px;
        }

        .contact-section h2 {
            font-size: 24px;
            color: #274E44;
        }

        .contact-info {
            margin-top: 10px;
            color: #555;
        }

        .contact-info p {
            margin: 5px 0;
        }

        .feedback-form {
            max-width: 500px;
            margin: 20px auto;
        }

        .feedback-form input, .feedback-form textarea {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 4px;
            border: 1px solid #ccc;
        }

        .feedback-form button {
            background-color: #274E44;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .feedback-form button:hover {
            background-color: #2b6d5f;
        }
    </style>
</head>
<body>

<?php include './includes/header.php'; ?>

<div class="about-container">
    <div class="about-header">
        <h1>About Us</h1>
    </div>

    <!-- Our Story Section -->
    <div class="section story-section">
        <div>
            <h2>Our Story</h2>
            <p>
                At NTU, we know the struggle is real—racing between lectures, juggling assignments, and then realizing
                you're starving with ten minutes to spare! That's where we come in. We've crafted this platform to make
                your foodie dreams a reality, connecting you to your favorite campus canteens with just a few clicks.
                Skip the queues, grab your meal, and get back to crushing those deadlines. No delivery, no drama—just
                delicious food waiting for you. Because we believe refueling shouldn't slow you down—it's your NTU pit
                stop, done right!
            </p>
        </div>
        <img src="your-image.jpg" alt="NTU students holding NTU letters">
    </div>

    <!-- FAQ Section -->
    <div class="section faq-section">
        <h2>FAQ</h2>
        <div class="faq-item">
            <details>
                <summary>How does the ordering process work?</summary>
                <p>It's simple! Browse through the canteens, select your favorite dishes, place your order, and choose
                   a pickup time. Once your order is confirmed, just head over to the canteen at your chosen time and
                   grab your meal—no waiting in line!</p>
            </details>
        </div>
        <div class="faq-item">
            <details>
                <summary>Is there a delivery option available?</summary>
                <p>Currently, we only offer pickup options for all canteens on campus.</p>
            </details>
        </div>
        <div class="faq-item">
            <details>
                <summary>Can I cancel or modify my order?</summary>
                <p>Orders can be modified or canceled within 5 minutes of placing the order. After that, changes cannot be guaranteed.</p>
            </details>
        </div>
        <div class="faq-item">
            <details>
                <summary>What if I miss my pickup time?</summary>
                <p>If you miss your pickup time, please contact the canteen directly for assistance.</p>
            </details>
        </div>
        <div class="faq-item">
            <details>
                <summary>How do I know when my food is ready for pickup?</summary>
                <p>You will receive a notification when your food is ready for pickup.</p>
            </details>
        </div>
    </div>

    <!-- Contact Section -->
    <div class="section contact-section">
        <h2>Give us your feedback</h2>
        <div class="contact-info">
            <p>📞 Tel: 123-456-7890</p>
            <p>✉️ Mail: info@ntu.com</p>
            <p>📍 Address: 50 Nanyang Ave, Singapore 639798</p>
        </div>

        <!-- Feedback Form -->
        <div class="feedback-form">
            <form action="#" method="post">
                <input type="text" name="first_name" placeholder="First Name" required>
                <input type="text" name="last_name" placeholder="Last Name" required>
                <input type="email" name="email" placeholder="Email" required>
                <textarea name="message" placeholder="Message" rows="4" required></textarea>
                <button type="submit">Send</button>
            </form>
        </div>
    </div>
</div>

<?php include './includes/footer.php'; ?>

</body>
</html>
