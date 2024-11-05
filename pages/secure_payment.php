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

include '../includes/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

include '../includes/cart_number.php';

$userId = $_SESSION['user_id'];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Secure Payment - NTUmami</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/global.css">
    <link rel="stylesheet" href="../assets/css/secure_payment.css">

    <script src="https://cdn.jsdelivr.net/npm/cleave.js@1.6.0/dist/cleave.min.js"></script>

    <script src="../assets/js/header.js" defer></script>
    <script defer src="../assets/js/secure_payment_validation.js"></script>
    <script defer src="../assets/js/notification.js"></script>
    
</head>
<body>
    <div id="notification" class="notification <?php echo $notificationType; ?>">
        <?php echo $notificationMessage; ?>
    </div>

    <?php include '../includes/cart_number.php'; include '../includes/header.php'; ?>

    <main>
        <div class="container">
            <div class="secure-payment-container">
                <h1 class="secure-payment-title">Secure Payment</h1>
                <p class="payment-instruction">Please fill in your payment details below to complete your order securely.</p>

                <form action="payment_processing.php" method="POST" class="secure-payment-form">
                    <div class="payment-section">
                        <h2>Credit/Debit Card Information</h2>
                        
                        <label for="card_number">Card Number</label>
                        <input type="text" id="card_number" name="card_number" maxlength="19" placeholder="1234 5678 9012 3456" required>
                        <small id="card_number_error" class="error-message"></small>
                        
                        <div class="card-details-row">
                            <div class="card-detail">
                                <label for="expiry_date">Expiry Date</label>
                                <input type="text" id="expiry_date" name="expiry_date" placeholder="MM/YY" maxlength="5" required>
                                <small id="expiry_date_error" class="error-message"></small>
                            </div>
                            <div class="card-detail">
                                <label for="cvv">CVV</label>
                                <input type="password" id="cvv" name="cvv" maxlength="3" placeholder="123" required>
                                <small id="cvv_error" class="error-message"></small>
                            </div>
                        </div>

                        <label for="cardholder_name">Cardholder Name</label>
                        <input type="text" id="cardholder_name" name="cardholder_name" placeholder="John Doe" required>
                        <small id="cardholder_name_error" class="error-message"></small>
                    </div>

                    <div class="button-row">
                        <a href="cart.php" class="cancel-button">Cancel Payment</a>
                        <button type="submit" class="secure-payment-button">Pay Securely</button>
                    </div>
                </form>


                <!-- Security Information -->
                <div class="security-notice">
                    <i class="fa fa-lock"></i> Your payment is secured with 256-bit SSL encryption.
                </div>
            </div>
        </div>
    </main>


    <?php include '../includes/footer.php'; ?>
</body>
</html>
