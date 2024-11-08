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

// Handle form submission
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
            
            if ($stmt->execute()) {
                $_SESSION['success_msg'] = "Password updated successfully!";
            } else {
                $_SESSION['error_msg'] = "Failed to update password!";
            }
            $stmt->close();
        }
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

<!-- Display Success/Error Messages -->
<?php
if (!empty($successMsg)) {
    echo "<div class='notification success'>$successMsg</div>";
}
if (!empty($errorMsg)) {
    echo "<div class='notification error'>$errorMsg</div>";
}
?>


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
            <p>Feature coming soon...</p>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

<script src="../assets/js/profile.js" defer></script>
<script src="../assets/js/password_checker.js" defer></script>

</body>
</html>
