<?php
session_start();
include '../includes/db_connect.php';

// Ensure POST request for payment processing
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $payment_method_type = $_POST['payment_method_type'];
    $userId = $_SESSION['user_id'];

    $eatInTakeOut = $_SESSION['dining_option'];

    // Error messages array
    $errors = [];

    if ($payment_method_type === "saved") {
        $savedPaymentId = $_POST['saved_payment_id'];

        $query = "SELECT * from saved_payment_methods WHERE user_id = $userId and id = $savedPaymentId;";
        $saved_payment_details = $conn->query($query)->fetch_assoc();

        $cardNumber = $saved_payment_details['card_last_four'];
        $expiryDate = $saved_payment_details['expiry_date'];
        $cardholderName = $saved_payment_details['cardholder_name'];
    } else {
        $cardNumber = $_POST['card_number'];
        $expiryDate = $_POST['expiry_date'];
        $cvv = $_POST['cvv'];
        $cardholderName = $_POST['cardholder_name'];

        // Server-side Validation
        // Validate Card Number (Numeric)
        str_replace(' ', '', $cardNumber);
        if (!is_numeric($cardNumber)) {
            $errors[] = "Card number must be numeric.";
        }

        // Validate Expiry Date (Format MM/YY and not expired)
        if (!preg_match('/^(0[1-9]|1[0-2])\/\d{2}$/', $expiryDate)) {
            $errors[] = "Expiry date must be in MM/YY format.";
        } else {
            // Check if expiry date has passed
            $expiryParts = explode('/', $expiryDate);
            $expiryMonth = (int)$expiryParts[0];
            $expiryYear = (int)("20" . $expiryParts[1]); // Convert YY to YYYY
            $currentYear = (int)date('Y');
            $currentMonth = (int)date('m');
            if ($expiryYear < $currentYear || ($expiryYear === $currentYear && $expiryMonth < $currentMonth)) {
                $errors[] = "The card has already expired.";
            }
        }

        // Validate CVV (3 digits)
        if (!is_numeric($cvv) || strlen($cvv) !== 3) {
            $errors[] = "CVV must be a 3-digit number.";
        }

        // Validate Cardholder Name (Letters and spaces only)
        if (empty($cardholderName) || !preg_match("/^[a-zA-Z\s]+$/", $cardholderName)) {
            $errors[] = "Cardholder name is required and should only contain letters and spaces.";
        }

        // If there are validation errors, store in session and redirect back
        if (!empty($errors)) {
            $_SESSION['error_msg'] = implode("<br>", $errors);
            header("Location: ../pages/checkout.php");
            exit();
        }
    }

    


    // If validation passes, proceed with database operations

    // Calculate total price directly from `cart_items` table
    $cartQuery = "SELECT SUM(f.price * ci.qty) AS total_price
                    FROM cart_items ci
                    JOIN carts c ON ci.cart_id = c.id
                    JOIN foods f ON ci.food_id = f.id
                    WHERE c.user_id = ?";
    $stmtCart = $conn->prepare($cartQuery);
    $stmtCart->bind_param("i", $userId);
    $stmtCart->execute();
    $result = $stmtCart->get_result();
    $row = $result->fetch_assoc();
    $totalPrice = $row['total_price'];

    if (!$totalPrice) {
        $_SESSION['error_msg'] = "Your cart is empty. Please add items to proceed.";
        header("Location: ../pages/cart.php");
        exit();
    }

    $cardLastFour = substr($cardNumber, -4);      // Last four digits
    $paymentStatus = "Paid"; 

    // Start transaction for atomic operation
    $conn->begin_transaction();

    try {
        // Step 1: Insert into `orders`
        $orderQuery = "INSERT INTO orders (user_id, eat_in_take_out, total_price, created_by)
                        VALUES (?, ?, ?, NOW())";
        $stmtOrder = $conn->prepare($orderQuery);
        $stmtOrder->bind_param("isd", $userId, $eatInTakeOut, $totalPrice);
        $stmtOrder->execute();
        $orderId = $conn->insert_id;

        // Step 2: Transfer items from `cart_items` to `order_items`
        $cartItemsQuery = "SELECT ci.food_id, ci.qty, f.price, ci.special_request
                            FROM cart_items ci
                            JOIN foods f ON ci.food_id = f.id
                            JOIN carts c ON ci.cart_id = c.id
                            WHERE c.user_id = ?";
        $stmtCartItems = $conn->prepare($cartItemsQuery);
        $stmtCartItems->bind_param("i", $userId);
        $stmtCartItems->execute();
        $cartItemsResult = $stmtCartItems->get_result();

        while ($item = $cartItemsResult->fetch_assoc()) {
            $foodId = $item['food_id'];
            $quantity = $item['qty'];
            $price = $item['price'];
            $special_request = $item['special_request'] ?? "";

            $orderItemQuery = "INSERT INTO order_items (order_id, food_id, qty, price, special_request, status)
                                VALUES (?, ?, ?, ?, ?, 'Pending')";
            $stmtOrderItem = $conn->prepare($orderItemQuery);
            $stmtOrderItem->bind_param("iiids", $orderId, $foodId, $quantity, $price, $special_request);
            $stmtOrderItem->execute();
        }

        // Step 3: Insert payment record in `payments` table
        $paymentQuery = "INSERT INTO payments (order_id, user_id, card_last_four, status, created_by)
                            VALUES (?, ?, ?, ?, NOW())";
        $stmtPayment = $conn->prepare($paymentQuery);
        $stmtPayment->bind_param("iiss", $orderId, $userId, $cardLastFour, $paymentStatus);
        $stmtPayment->execute();

        // Step 4: Delete `cart_items` and the `cart` itself for the user
        $deleteCartItemsQuery = "DELETE ci FROM cart_items ci
                                    JOIN carts c ON ci.cart_id = c.id
                                    WHERE c.user_id = ?";
        $stmtDeleteCartItems = $conn->prepare($deleteCartItemsQuery);
        $stmtDeleteCartItems->bind_param("i", $userId);
        $stmtDeleteCartItems->execute();

        $deleteCartQuery = "DELETE FROM carts WHERE user_id = ?";
        $stmtDeleteCart = $conn->prepare($deleteCartQuery);
        $stmtDeleteCart->bind_param("i", $userId);
        $stmtDeleteCart->execute();

        // Commit transaction if all steps are successful
        $conn->commit();

        // Set success message and redirect
        $_SESSION['success_msg'] = "Payment successful! Your order has been placed.";
        header("Location: ../pages/menu.php");
        exit();

    } catch (Exception $e) {
        // Rollback transaction if any error occurs
        $conn->rollback();
        $_SESSION['error_msg'] = "Payment failed. Please try again.";

        header("Location: ../pages/checkout.php");
        exit();
    }
} else {
    // Invalid request method
    $_SESSION['error_msg'] = "Invalid request.";
    header("Location: ../pages/checkout.php");
    exit();
}

