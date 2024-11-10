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

// Check if dining option is set, default to "Eat-In" if missing
$diningOption = $_POST['dining_option'] ?? 'Eat-In';

// Save the dining option to session for use in payment or confirmation pages
$_SESSION['dining_option'] = $diningOption;

$userId = $_SESSION['user_id'];
$query = "SELECT ci.id AS cart_item_id, f.name AS food_name, f.price, f.image_url, ci.qty, 
                 (f.price * ci.qty) AS item_total, s.name AS stall_name, l.name AS location_name, ci.special_request
            FROM cart_items ci
            JOIN carts c ON ci.cart_id = c.id
            JOIN foods f ON ci.food_id = f.id
            JOIN stalls s ON f.stall_id = s.id
            JOIN canteens l ON s.canteen_id = l.id
            WHERE c.user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$cartItems = [];
$totalPrice = 0;
while ($row = $result->fetch_assoc()) {
    $cartItems[] = $row;
    $totalPrice += $row['item_total'];
}

// Fetch user's saved payment methods
$savedPaymentsQuery = "SELECT id, cardholder_name, card_last_four, card_expiry, is_default FROM saved_payment_methods WHERE user_id = ?";
$savedPaymentsStmt = $conn->prepare($savedPaymentsQuery);
$savedPaymentsStmt->bind_param("i", $userId);
$savedPaymentsStmt->execute();
$savedPaymentsResult = $savedPaymentsStmt->get_result();
$savedPayments = $savedPaymentsResult->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout - NTUmami</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/global.css">
    <link rel="stylesheet" href="../assets/css/checkout.css">

    <script src="https://cdn.jsdelivr.net/npm/cleave.js@1.6.0/dist/cleave.min.js"></script>

    <script src="../assets/js/header.js" defer></script>
    <script defer src="../assets/js/notification.js"></script>

    <script defer src="../assets/js/secure_payment_validation.js"></script>

    <style>
        .payment-method-toggle {
            margin-bottom: 15px;
            font-size: 16px;
            font-weight: bold;
        }

        .payment-method-toggle label {
            display: inline-block;
            margin-right: 20px;
            cursor: pointer;
            color: var(--primary-color);
        }

        .saved-payment-section,
        .new-payment-section {
            padding: 15px;
            margin-top: 15px;
            border: 1px solid var(--input-border-color);
            border-radius: 8px;
            background-color: var(--primary-bg-color);
            transition: all 0.3s ease;
        }

        .saved-payment-section {
            display: <?php echo !empty($savedPayments) ? 'block' : 'none'; ?>;
        }

        .new-payment-section {
            display: <?php echo empty($savedPayments) ? 'block' : 'none'; ?>;
        }

        .saved-card-option {
            display: block;
            margin: 8px 0;
            font-size: 14px;
            color: var(--text-color-dark);
        }

        .save-card-checkbox {
            display: flex;
            align-items: center;
            margin-top: 15px;
            font-size: 14px;
            color: var(--primary-color);
        }


    </style>
</head>
<body>
    <div id="notification" class="notification <?php echo $notificationType; ?>">
        <?php echo $notificationMessage; ?>
    </div>

    <?php include '../includes/cart_number.php';  include '../includes/header.php'; ?>

    <main>
        <div class="container">
            <h1 id="title">Payment Page</h1>
            
            <!-- Back to Cart Button -->
            <a href="cart.php" class="back-to-cart"><i class="fa fa-arrow-left"></i> Back to Cart</a>

            <div class="checkout-container">
                <!-- Order Summary Section -->
                <div class="order-summary">
                    <h2>Order Summary</h2>
                    
                    <table>
                        <thead>
                            <tr>
                                <th>Product Details</th>
                                <th>Qty</th>
                                <th>Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cartItems as $item): ?>
                                <tr>
                                    <td>
                                        <img src="<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['food_name']); ?>" class="product-image">
                                        <div class="product-details">
                                            <strong><?php echo htmlspecialchars($item['food_name']); ?></strong><br>
                                            <small>Location: <?php echo htmlspecialchars($item['location_name']); ?></small><br>
                                            <small>Stall: <?php echo htmlspecialchars($item['stall_name']); ?></small><br>
                                            <small>Special Request: <?php echo htmlspecialchars($item['special_request']); ?></small>
                                        </div>
                                    </td>
                                    <td><?php echo $item['qty']; ?></td>
                                    <td>$<?php echo number_format($item['price'], 2); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <div class="dining-option-summary">
                        <strong>Dining Option:</strong> <?php echo htmlspecialchars($diningOption); ?>
                    </div>
                    <div class="summary-totals">
                        <p>Subtotal <span>$<?php echo number_format($totalPrice, 2); ?></span></p>
                        <p class="total">Total <span>$<?php echo number_format($totalPrice, 2); ?></span></p>
                    </div>
                </div>

                <!-- Payment Info Section -->
                <div class="payment-info">
                    <h2>Payment Info</h2>
                    <form class="secure-payment-form" action="../controllers/payment_processing.php" method="POST">
                        <!-- Payment Method Type Toggle -->
                        <div class="payment-method-toggle">
                            <label>
                                <input type="radio" name="payment_method_type" value="saved" id="use_saved_payment" <?php echo !empty($savedPayments) ? "checked" : "" ?> onclick="togglePaymentMethod()">
                                Use Saved Payment Method
                            </label>
                            <label>
                                <input type="radio" name="payment_method_type" value="new" id="use_new_payment" <?php echo empty($savedPayments) ? "checked" : "" ?> onclick="togglePaymentMethod()">
                                Enter New Payment Details
                            </label>
                        </div>

                        <!-- Saved Payment Methods Section -->
                        <div id="saved_payment_methods" class="saved-payment-section" style="display: <?php echo empty($savedPayments) ? 'none' : 'block'; ?>;">
                            <?php if (!empty($savedPayments)): ?>
                                <?php foreach ($savedPayments as $index => $payment): ?>
                                    <label class="saved-card-option">
                                        <input type="radio" name="saved_payment_id" id="payment_option_<?php echo $index; ?>" value="<?php echo $payment['id']; ?>" <?php echo $payment['is_default'] === 1 ? "checked" : "" ?>>
                                        <?php echo htmlspecialchars($payment['cardholder_name']) . " ending in " . htmlspecialchars($payment['card_last_four']) . " (Exp: " . htmlspecialchars($payment['card_expiry']) . ")" . ($payment['is_default'] === 1 ? " - Default" : ""); ?>

                                        <!-- CVV Field -->
                                        <div class="cvv-input" id="cvv_section_<?php echo $index; ?>" style="display: <?php echo $payment['is_default'] === 1 ? 'block' : 'none'; ?>;">
                                            <label for="cvv_saved_<?php echo $index; ?>">CVV</label>
                                            <input type="password" id="cvv_saved_<?php echo $index; ?>" oninput="return validateCVVPerCard(this)" name="cvv_saved_<?php echo $index; ?>" placeholder="123" maxlength="3">
                                            <small id="cvv_saved_<?php echo $index; ?>_error" class="error-message"></small>
                                        </div>
                                    </label>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p>No saved payment methods available.</p>
                            <?php endif; ?>
                        </div>


                        <!-- New Payment Details Section -->
                        <div id="new_payment_details" class="new-payment-section" style="display: <?php echo empty($savedPayments) ? 'block' : 'none'; ?>;">
                            <label for="cardholder_name">Name on Card</label>
                            <input type="text" id="cardholder_name" name="cardholder_name" placeholder="John Doe">
                            <small id="cardholder_name_error" class="error-message"></small>
                            
                            <label for="card_number">Card Number</label>
                            <input type="text" id="card_number" name="card_number" placeholder="1234 5678 9012 3456" maxlength="19">
                            <small id="card_number_error" class="error-message"></small>
                            
                            <label for="expiry_date">Expiration Date</label>
                            <input type="text" id="expiry_date" name="expiry_date" placeholder="MM/YY" maxlength="5">
                            <small id="expiry_date_error" class="error-message"></small>
                            
                            <label for="cvv">CVV</label>
                            <input type="password" id="cvv" name="cvv" placeholder="123" maxlength="3">
                            <small id="cvv_error" class="error-message"></small>

                            <!-- <label class="save-card-checkbox">
                                <input type="checkbox" name="save_payment" value="yes"> Save this card for future purchases
                            </label> -->
                        </div>

                        <button type="submit" class="checkout-button">Pay Securely</button>
                        <!-- Security Information -->
                        <div class="security-notice">
                            <i class="fa fa-lock"></i> Your payment is secured with 256-bit SSL encryption.
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </main>

    <?php include '../includes/footer.php'; ?>

    <!-- JavaScript for Toggling Payment Method Views -->
    <script>
        function togglePaymentMethod() {
            const useSavedPayment = document.getElementById('use_saved_payment').checked;
            document.getElementById('saved_payment_methods').style.display = useSavedPayment ? 'block' : 'none';
            document.getElementById('new_payment_details').style.display = useSavedPayment ? 'none' : 'block';
        }

        const paymentOptions = document.querySelectorAll('input[name="saved_payment_id"]');
        const cvvSections = document.querySelectorAll(".cvv-input");

        paymentOptions.forEach((option, index) => {
            option.addEventListener("change", function() {
                // Hide all CVV sections
                cvvSections.forEach(section => {
                    section.style.display = "none";
                    section.querySelector(`#cvv_saved_${section.id.split("_")[2]}_error`).textContent = "";
                });

                // Show the selected CVV section
                document.getElementById(`cvv_section_${index}`).style.display = "block";
            });

            // Trigger change on the initially checked option to show its CVV field
            if (option.checked) {
                option.dispatchEvent(new Event("change"));
            }
        });
    </script>
</body>
</html>
