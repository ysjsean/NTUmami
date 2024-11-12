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
    <title>Sign Up</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/global.css">
    <link rel="stylesheet" href="../assets/css/form.css">
    
    <script src="../assets/js/header.js" defer></script>
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
            <h2>Sign Up</h2>
            <?php if (isset($_SESSION['error'])): ?>
                <p class="error"><?= $_SESSION['error'] ?></p>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>
            <form method="POST" action="../controllers/signup_handler.php" onsubmit="return finalValidateForm()" novalidate>
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" required oninput="validateName()">
                <span class="error-message" id="nameError"></span>

                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required oninput="validateUsername()">
                <span class="error-message" id="usernameError"></span>

                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required oninput="validateEmail()">
                <span class="error-message" id="emailError"></span>

                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required oninput="validatePassword()">
                <span class="error-message" id="passwordError"></span>

                <label for="cpassword">Confirm Password:</label>
                <input type="password" id="cpassword" name="cpassword" required oninput="validateConfirmPassword()">
                <span class="error-message" id="cpasswordError"></span>

                <button type="submit" id="submitBtn">Sign Up</button>
            </form>
            <p>Already have an account? <a href="login.php">Login here</a>.</p>
        </div>
    </main>
    <?php include '../includes/footer.php'; ?>

    <script src="../assets/js/signupValidation.js"></script>
</body>
</html>
