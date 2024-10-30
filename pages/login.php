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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/global.css">
    <link rel="stylesheet" href="../assets/css/form.css">

    <script defer src="../assets/js/notification.js"></script>
</head>
<body>
    <!-- Notification container -->
    <div id="notification" class="notification <?php echo $notificationType; ?>">
        <?php echo $notificationMessage; ?>
    </div>

    <?php include '../includes/header.php'; ?>
    <main class="form-main">
        <div class="form-container">
            <h2>Login</h2>
            <?php if (isset($_SESSION['error'])): ?>
                <p class="error"><?= $_SESSION['error'] ?></p>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>
            <form method="POST" action="../controllers/login_handler.php" novalidate>
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>

                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>

                <button type="submit">Login</button>
            </form>
            <p>Don't have an account? <a href="signup.php">Sign up here</a>.</p>
        </div>
    </main>
    <?php include '../includes/footer.php'; ?>
</body>
</html>
