<?php
session_start();

include '../includes/db_connect.php';
include '../includes/header.php';
include '../includes/cart_number.php';

// Retrieve and unset success or error messages
$userId = $_SESSION['user_id'];
$successMsg = $_SESSION['success_msg'] ?? '';
$errorMsg = $_SESSION['error_msg'] ?? '';
unset($_SESSION['success_msg'], $_SESSION['error_msg']);

// Redirect users based on their roles
if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    header('Location: ./pages/admin_dashboard.php');
    exit();
}
if (isset($_SESSION['role']) && $_SESSION['role'] === 'vendor') {
    header('Location: ./pages/vendor_dashboard.php');
    exit();
}

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch user data (name and email)
$userQuery = "SELECT name, email FROM users WHERE id = ?";
$stmt = $conn->prepare($userQuery);
$stmt->bind_param("i", $userId);
$stmt->execute();
$userResult = $stmt->get_result();
$userData = $userResult->fetch_assoc();
$stmt->close();

// Fetch user profile data
$profileQuery = "SELECT phone, birthdate, street, street2, city, postal_code, country FROM user_profiles WHERE user_id = ?";
$stmt = $conn->prepare($profileQuery);
$stmt->bind_param("i", $userId);
$stmt->execute();
$profileResult = $stmt->get_result();
$profileData = $profileResult->fetch_assoc();
$stmt->close();

// Fetch saved payment methods for the current user
$savedCardsQuery = "SELECT id, cardholder_name, card_last_four, card_expiry, card_type, is_default FROM saved_payment_methods WHERE user_id = ?";
$stmt = $conn->prepare($savedCardsQuery);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$savedCards = [];
while ($row = $result->fetch_assoc()) {
    $savedCards[] = $row;
}
$stmt->close();

// Determine which tab to display
$activeTab = isset($_POST['active_tab']) ? $_POST['active_tab'] : 'account';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['change_password'])) {
        // Change Password Logic
        $newPassword = $_POST['new_password'];
        $confirmPassword = $_POST['confirm_password'];

        if ($newPassword !== $confirmPassword) {
            $_SESSION['error_msg'] = "Passwords do not match!";
        } else {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $updatePasswordQuery = "UPDATE users SET password = ? WHERE id = ?";
            $stmt = $conn->prepare($updatePasswordQuery);
            $stmt->bind_param("si", $hashedPassword, $userId);
            $_SESSION['success_msg'] = $stmt->execute() ? "Password updated successfully!" : "Failed to update password!";
            $stmt->close();
        }
        $activeTab = 'password';

    } elseif (isset($_POST['set_default_card'])) {
        // Handle setting the default card
        $selectedCardId = $_POST['card_id'];
        if (!empty($selectedCardId)) {
            // Set all other cards to non-default
            $conn->query("UPDATE saved_payment_methods SET is_default = 0 WHERE user_id = $userId");
            // Set the selected card as default
            $stmt = $conn->prepare("UPDATE saved_payment_methods SET is_default = 1 WHERE id = ? AND user_id = ?");
            $stmt->bind_param("ii", $selectedCardId, $userId);
            $_SESSION['success_msg'] = $stmt->execute() ? "Card set as default successfully!" : "Failed to set card as default!";
            $stmt->close();
        }
        $activeTab = 'payment';

    } elseif (isset($_POST['delete_card'])) {
        // Handle deleting a card
        $cardId = $_POST['card_id'];
        $stmt = $conn->prepare("DELETE FROM saved_payment_methods WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $cardId, $userId);
        $_SESSION['success_msg'] = $stmt->execute() ? "Card deleted successfully!" : "Failed to delete card!";
        $stmt->close();
        $activeTab = 'payment';

    } elseif (isset($_POST['add_card'])) {
        // Add a new card
        $cardholderName = $_POST['cardholder_name'];
        $cardLastFour = $_POST['card_last_four'];
        $cardExpiryMonth = $_POST['card_expiry_month'];
        $cardExpiryYear = $_POST['card_expiry_year'];
        $cardExpiry = $cardExpiryMonth . '/' . $cardExpiryYear;
        $cardType = $_POST['card_type'];

        if (strlen($cardLastFour) !== 4 || !ctype_digit($cardLastFour)) {
            $_SESSION['error_msg'] = "Card last four digits must be exactly 4 digits!";
        } else {
            $currentMonth = date('m');
            $currentYear = date('y');
            if ($cardExpiryYear < $currentYear || ($cardExpiryYear == $currentYear && $cardExpiryMonth < $currentMonth)) {
                $_SESSION['error_msg'] = "Card expiry date cannot be in the past.";
            } else {
                $insertCardQuery = "INSERT INTO saved_payment_methods (user_id, cardholder_name, card_last_four, card_expiry, card_type) VALUES (?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($insertCardQuery);
                $stmt->bind_param("issss", $userId, $cardholderName, $cardLastFour, $cardExpiry, $cardType);
                $_SESSION['success_msg'] = $stmt->execute() ? "Card added successfully!" : "Failed to add card!";
                $stmt->close();
            }
        }
        $activeTab = 'payment';

    } else {
        // Update Profile Logic
        $name = $_POST['name'];
        $phone = $_POST['phone'];
        $birthdate = $_POST['birthdate'];
        $street = $_POST['street'];
        $street2 = $_POST['street2'];
        $city = $_POST['city'];
        $postal_code = $_POST['postal_code'];
        $country = $_POST['country'];

        // Update the `users` table (for name)
        $updateUserQuery = "UPDATE users SET name = ? WHERE id = ?";
        $stmt = $conn->prepare($updateUserQuery);
        $stmt->bind_param("si", $name, $userId);
        $stmt->execute();
        $stmt->close();

        // Check if the user has a profile entry already
        $checkProfileQuery = "SELECT user_id FROM user_profiles WHERE user_id = ?";
        $stmt = $conn->prepare($checkProfileQuery);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // Update existing profile
            $updateProfileQuery = "UPDATE user_profiles SET phone = ?, birthdate = ?, street = ?, street2 = ?, city = ?, postal_code = ?, country = ? WHERE user_id = ?";
            $stmt = $conn->prepare($updateProfileQuery);
            $stmt->bind_param("sssssssi", $phone, $birthdate, $street, $street2, $city, $postal_code, $country, $userId);
        } else {
            // Insert a new profile entry
            $insertProfileQuery = "INSERT INTO user_profiles (user_id, phone, birthdate, street, street2, city, postal_code, country) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($insertProfileQuery);
            $stmt->bind_param("isssssss", $userId, $phone, $birthdate, $street, $street2, $city, $postal_code, $country);
        }

        if ($stmt->execute()) {
            $_SESSION['success_msg'] = "Profile updated successfully!";
        } else {
            $_SESSION['error_msg'] = "Failed to update profile!";
        }
        $stmt->close();
    }

    // Redirect to the profile page to display the message
    header("Location: profile.php");
    exit();
}


?>

<!-- Add a hidden input to store the current tab -->
<input type="hidden" id="activeTab" value="<?php echo $activeTab; ?>">


<!-- HTML Form -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/global.css">
    <link rel="stylesheet" href="../assets/css/profile.css">
</head>

<body>
<div class="profile-container">
    <div class="profile-sidebar">
        <ul>
            <li onclick="showTab('account')" class="active">Account Details</li>
            <li onclick="showTab('password')">Change Password</li>
            <li onclick="showTab('payment')">Payment Details</li>
        </ul>
    </div>

    <div class="profile-content">
        <!-- Account Details Tab -->
        <div id="account" class="tab-content active">
            <form action="profile.php" method="POST">
                <input type="hidden" name="active_tab" value="account">
                <h2>Account</h2>

                <!-- Personal Info Section -->
                <h3>Personal Info</h3>
                <p>Update your personal information</p>

                <div class="form-row">
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" name="name" value="<?php echo htmlspecialchars($userData['name']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" name="email" value="<?php echo htmlspecialchars($userData['email']); ?>" readonly>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="phone">Phone</label>
                        <input type="text" name="phone" id="phone" value="<?php echo htmlspecialchars($profileData['phone'] ?? ''); ?>" placeholder="Phone">
                    </div>
                    <div class="form-group">
                        <label for="birthdate">Birthdate</label>
                        <input type="date" name="birthdate" value="<?php echo htmlspecialchars($profileData['birthdate'] ?? ''); ?>" max="<?php echo date('Y-m-d', strtotime('-12 years')); ?>" required>
                    </div>
                </div>

                <!-- Address Section -->
                <h3>Address</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label for="street">Street</label>
                        <input type="text" name="street" value="<?php echo htmlspecialchars($profileData['street'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label for="street2">Street 2</label>
                        <input type="text" name="street2" value="<?php echo htmlspecialchars($profileData['street2'] ?? ''); ?>">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="city">City</label>
                        <input type="text" name="city" value="<?php echo htmlspecialchars($profileData['city'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label for="postal_code">Postal Code</label>
                        <input type="text" name="postal_code" value="<?php echo htmlspecialchars($profileData['postal_code'] ?? ''); ?>">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="country">Country</label>
                        <input type="text" id="country" name="country" value="<?php echo htmlspecialchars($profileData['country'] ?? 'Singapore'); ?>" placeholder="Country">
                    </div>
                </div>

                <!-- Save and Cancel Buttons -->
                <div class="form-actions">
                    <button type="button" onclick="window.location.href='profile.php'">Cancel</button>
                    <button type="submit">Update Info</button>
                </div>
            </form>

        </div>

        <!-- Change Password Tab -->
        <div id="password" class="tab-content">
            <h2>Change Password</h2>
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
                <input type="hidden" name="change_password" value="1">
                <input type="hidden" name="active_tab" value="password">
                <div class="form-row">
                    <div class="form-group">
                        <label for="new_password">New Password</label>
                        <input type="password" name="new_password" id="new_password" required>
                        <div id="password-strength"></div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="confirm_password">Confirm New Password</label>
                        <input type="password" name="confirm_password" id="confirm_password" required>
                        <div id="password-match"></div>
                    </div>
                </div>
                <div class="form-actions">
                    <button type="submit">Update Password</button>
                </div>
            </form>
        </div>



        <!-- Payment Details Tab -->
        <div id="payment" class="tab-content">
            <h2>Payment Details</h2>
            <p>Update your payment methods below.</p>

            <!-- Display Saved Cards -->
            <div class="saved-cards">
                <?php if (!empty($savedCards)): ?>
                    <?php foreach ($savedCards as $card): ?>
                        <div class="card-item" onclick="toggleCardActions(<?php echo $card['id']; ?>)">
                            <div class="card-header">
                                <img src="../assets/images/Payment Methods/<?php echo strtolower($card['card_type']); ?>.jpg" alt="<?php echo $card['card_type']; ?>" class="card-icon">
                                <span>**** <?php echo htmlspecialchars($card['card_last_four']); ?></span>
                                <span><?php echo htmlspecialchars($card['card_expiry']); ?></span>
                                <?php if ($card['is_default']): ?>
                                    <span class="default-badge">Default</span>
                                <?php endif; ?>
                                <i class="arrow-icon" id="arrow-<?php echo $card['id']; ?>">&#9662;</i> <!-- Arrow icon -->
                            </div>
                            <form id="card-actions-<?php echo $card['id']; ?>" class="card-actions" action="profile.php" method="POST">
                                <input type="hidden" name="card_id" value="<?php echo $card['id']; ?>">
                                <button type="submit" name="set_default_card" class="btn-action">Set as Default</button>
                                <button type="submit" name="delete_card" class="btn-action btn-delete">Delete Card</button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No saved cards found. Please add a new card.</p>
                <?php endif; ?>
            </div>


            <!-- Button to Show Add New Card Form -->
            <button type="button" class="btn-show-form" onclick="toggleCardForm()">Add New Card</button>

            <!-- Add New Card Form (Initially Hidden) -->
            <div id="add-card-form" style="display: none; margin-top: 20px;">
                <form action="profile.php" method="POST">
                    <input type="hidden" name="add_card" value="1">
                    <input type="hidden" name="active_tab" value="payment">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="cardholder_name">Cardholder Name</label>
                            <input type="text" name="cardholder_name" required>
                        </div>
                        <div class="form-group">
                            <label for="card_last_four">Last 4 Digits</label>
                            <input type="text" name="card_last_four" maxlength="4" pattern="\d{4}" title="Enter exactly 4 digits" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="card_expiry_month">Expiry Month</label>
                            <select id="card_expiry_month" name="card_expiry_month" required>
                                <option value="">MM</option>
                                <?php 
                                    $currentMonth = date('m');
                                    $currentYear = date('y');
                                    for ($m = 1; $m <= 12; $m++) {
                                        $monthValue = str_pad($m, 2, '0', STR_PAD_LEFT);
                                        // Disable months that are earlier than the current month if the selected year is the current year
                                        $disabled = ($selectedYear == $currentYear && $m < $currentMonth) ? 'disabled' : '';
                                        echo "<option value='$monthValue' $disabled>$monthValue</option>";
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="card_expiry_year">Expiry Year</label>
                            <select id="card_expiry_year" name="card_expiry_year" onchange="checkExpiryMonth()" required>
                                <option value="">YY</option>
                                <?php 
                                    for ($y = $currentYear; $y <= $currentYear + 10; $y++) {
                                        echo "<option value='$y'>$y</option>";
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="card_type">Card Type</label>
                            <select name="card_type" required>
                                <option value="Visa">Visa</option>
                                <option value="Mastercard">Mastercard</option>
                            </select>
                        </div>
                    </div>
                
                    <button type="submit" class="btn-submit">Add Card</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

<script src="../assets/js/profile.js" defer></script>
<script src="../assets/js/password_checker.js" defer></script>

</body>
</html>
