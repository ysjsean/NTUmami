<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit();
}

// Assuming `username` is stored in the session when the admin logs in
$adminName = $_SESSION['username'] ?? "Admin";

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

// Fetch data
$canteens = $conn->query("SELECT * FROM canteens")->fetch_all(MYSQLI_ASSOC) ?? [];

$vendors = $conn->query("
    SELECT users.id AS user_id, users.username, users.email, users.name, vendors.business_name, vendors.contact_number
    FROM users
    INNER JOIN vendors ON users.id = vendors.user_id
    WHERE users.role = 'vendor'
")->fetch_all(MYSQLI_ASSOC) ?? [];

$stalls = $conn->query("
    SELECT stalls.*, vendors.business_name, canteens.name AS canteen_name
    FROM stalls
    INNER JOIN vendors ON stalls.vendor_id = vendors.id
    INNER JOIN canteens ON stalls.canteen_id = canteens.id
    ORDER BY stalls.created_by
")->fetch_all(MYSQLI_ASSOC) ?? [];

// Fetch data for dropdown (ordered alphabetically)
$canteensForDropdown = $conn->query("SELECT * FROM canteens ORDER BY name ASC")->fetch_all(MYSQLI_ASSOC) ?? [];

// Fetch vendors ordered by business name for the dropdown
$vendorsForDropdown = $conn->query("
    SELECT users.id AS user_id, vendors.id AS vendor_id, users.username, users.email, users.name, vendors.business_name, vendors.contact_number
    FROM users
    INNER JOIN vendors ON users.id = vendors.user_id
    WHERE users.role = 'vendor'
    ORDER BY vendors.business_name ASC
")->fetch_all(MYSQLI_ASSOC) ?? [];

$daysOfWeek = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/global.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">

    <script defer src="../assets/js/notification.js"></script>
    <script defer src="../assets/js/dashboard.js"></script>
    <script defer src="../assets/js/admin_formValidation.js"></script>
</head>
<body>
    <!-- Notification container -->
    <div id="notification" class="notification <?php echo $notificationType; ?>">
        <?php echo $notificationMessage; ?>
    </div>

    <div class="container">
        <h1>Admin Dashboard</h1>

        <!-- Welcome Message -->
        <p class="welcome-message">Welcome, <?= htmlspecialchars($adminName); ?>! Here’s your admin dashboard.</p>

        <!-- Logout Button -->
        <!-- <button class="logout-btn" onclick="window.location.href='../controllers/logout.php'">Logout</button> -->
        <a href="/NTUmami/controllers/logout.php" class="user-icon logout-btn"><i class="fa fa-user"></i>Logout</a>

        <!-- Tab Navigation -->
        <div class="tabs">
            <button class="tab-link" onclick="openTab(event, 'tab-canteens')">Canteens</button>
            <button class="tab-link" onclick="openTab(event, 'tab-vendors')">Vendors</button>
            <button class="tab-link" onclick="openTab(event, 'tab-stalls')">Stalls</button>
        </div>

        <!-- Canteens Tab Content -->
        <div id="tab-canteens" class="tab-content active">
            <div class="columns">
                <!-- Add New Canteen Form -->
                <div class="column card">
                    <h2>Add New Canteen</h2>
                    <form method="POST" action="../controllers/canteen_handler.php?action=add&tab=tab-canteens" enctype="multipart/form-data" novalidate onsubmit="return validateCanteenForm(this)">
                        <label for="canteen-name">Canteen Name*</label>
                        <input type="text" id="canteen-name" name="name" placeholder="Enter Canteen Name" required oninput="validateText(this, 'Canteen Name')">
                        <div class="error-message" id="canteen-name-error"></div>

                        <label for="canteen-description">Description</label>
                        <textarea id="canteen-description" name="description" placeholder="Enter Description"></textarea>

                        <label for="canteen-address">Address*</label>
                        <input type="text" id="canteen-address" name="address" placeholder="Enter Address" required oninput="validateText(this, 'Address')">
                        <div class="error-message" id="canteen-address-error"></div>

                        <label for="canteen-image">Upload Image (Max: 2MB, JPEG/PNG)*</label>
                        <input type="file" id="canteen-image" name="image" accept="image/*" oninput="validateImage(this)">
                        <p class="file-input-info">Max size 2MB, JPEG or PNG only.</p>
                        <div class="error-message" id="canteen-image-error"></div>

                        <!-- Business Hours Section -->
                        <label class="section-label">Business Hours*</label>
                        <div id="business-hours-section-add" class="business-hours-section">
                            <div class="hours-block">
                                <label>Open at:</label>
                                <input type="time" name="open_time[]" required>

                                <label>Close at:</label>
                                <input type="time" name="close_time[]" required>

                                <div class="day-select">
                                    <?php foreach (['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $day): ?>
                                        <label><input type="checkbox" name="days[0][]" value="<?= $day; ?>"> <?= $day; ?></label>
                                    <?php endforeach; ?>
                                </div>
                                <button type="button" class="btn btn-delete" onclick="removeHoursBlock(this)">Remove</button>
                            </div>
                        </div>
                        <button type="button" class="btn btn-add-more" onclick="addHoursBlock('business-hours-section-add')">[+] Add More Hours</button>
                        <button type="submit" class="btn btn-primary">Add Canteen</button>
                    </form>
                </div>

                <!-- Existing Canteens -->
                <div class="column card">
                    <h2>Existing Canteens</h2>
                    <div class="item-list">
                        <?php if (count($canteens) > 0): ?>
                            <?php foreach ($canteens as $canteen): ?>
                                <?php
                                // Initialize and populate $groupedHours for each canteen
                                $groupedHours = [];
                                $hoursStmt = $conn->prepare("SELECT days, open_time, close_time FROM canteen_hours WHERE canteen_id = ? ORDER BY FIELD(days, 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun')");
                                $hoursStmt->bind_param("i", $canteen['id']);
                                $hoursStmt->execute();
                                $hoursStmt->bind_result($days, $open_time, $close_time);

                                // Arrays for open and closed days
                                $openDays = [];
                                $closedDays = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
                                while ($hoursStmt->fetch()) {
                                    $key = $open_time . '-' . $close_time;
                                    if (!isset($openDays[$key])) {
                                        $openDays[$key] = [
                                            'days' => [],
                                            'open' => $open_time,
                                            'close' => $close_time
                                        ];
                                    }
                                    $openDays[$key]['days'][] = $days;
                                    // Remove open days from closed days list
                                    if (($key = array_search($days, $closedDays)) !== false) {
                                        unset($closedDays[$key]);
                                    }
                                }
                                $hoursStmt->close();
                                ?>

                                <div class="item" id="canteen-<?= $canteen['id']; ?>">
                                    <div class="view-mode">
                                        <p><strong>Canteen Name:</strong> <?= htmlspecialchars($canteen['name']); ?></p>
                                        <p><strong>Description:</strong> <?= htmlspecialchars($canteen['description']); ?></p>
                                        <p><strong>Address:</strong> <?= htmlspecialchars($canteen['address']); ?></p>
                                        <?php if (!empty($canteen['image_url'])): ?>
                                            <div class="image-view">
                                                <p><strong>Image:</strong></p>
                                                <img src="<?= htmlspecialchars($canteen['image_url']); ?>" alt="Location Image" class="canteen-image">
                                            </div>
                                        <?php endif; ?>

                                        <!-- Display Business Hours in View Mode -->
                                        <p><strong>Business Hours:</strong></p>
                                        <div class="business-hours">
                                            <?php
                                            foreach ($openDays as $timeRange => $group) {
                                                $openTimeFormatted = date("g:i a", strtotime($group['open']));
                                                $closeTimeFormatted = date("g:i a", strtotime($group['close']));
                                                $daysArr = $group['days'];
                                                $groupedDays = [];
                                                $startDay = $daysArr[0];
                                                $lastDay = $daysArr[0];

                                                foreach ($daysArr as $index => $day) {
                                                    if ($index > 0 && date('N', strtotime($day)) - date('N', strtotime($lastDay)) > 1) {
                                                        $groupedDays[] = ($startDay === $lastDay) ? $startDay : "$startDay - $lastDay";
                                                        $startDay = $day;
                                                    }
                                                    $lastDay = $day;
                                                }
                                                $groupedDays[] = ($startDay === $lastDay) ? $startDay : "$startDay - $lastDay";

                                                echo "<div class='business-hours-item'>";
                                                echo "<span class='days'>" . implode(', ', $groupedDays) . ":</span> ";
                                                echo "<span class='hours'>{$openTimeFormatted} - {$closeTimeFormatted}</span>";
                                                echo "</div>";
                                            }

                                            // Display closed days with grouping
                                            if (!empty($closedDays)) {
                                                $groupedClosedDays = [];
                                                $startDay = reset($closedDays);
                                                $lastDay = reset($closedDays);

                                                foreach ($closedDays as $index => $day) {
                                                    if ($index > 0 && date('N', strtotime($day)) - date('N', strtotime($lastDay)) > 1) {
                                                        $groupedClosedDays[] = ($startDay === $lastDay) ? $startDay : "$startDay - $lastDay";
                                                        $startDay = $day;
                                                    }
                                                    $lastDay = $day;
                                                }
                                                $groupedClosedDays[] = ($startDay === $lastDay) ? $startDay : "$startDay - $lastDay";

                                                echo "<div class='business-hours-item'>";
                                                echo "<span class='days'>" . implode(', ', $groupedClosedDays) . ":</span> ";
                                                echo "<span class='hours'>Closed</span>";
                                                echo "</div>";
                                            }
                                            ?>
                                        </div>
                                        
                                        <div class="buttons">
                                            <button class="btn btn-edit" onclick="toggleEdit('canteen-<?= $canteen['id']; ?>')">Edit</button>
                                            <button class="btn btn-delete" onclick="confirmDelete(<?= $canteen['id']; ?>, 'canteen')">Delete</button>
                                        </div>
                                    </div>

                                    <!-- Edit Mode Form -->
                                    <form class="edit-mode" method="POST" action="../controllers/canteen_handler.php?action=update&tab=tab-canteens" enctype="multipart/form-data" style="display: none;" onsubmit="return validateEditCanteenForm(this)">
                                        <input type="hidden" name="id" value="<?= $canteen['id']; ?>">
                                        
                                        <label for="name">Canteen Name*</label>
                                        <input type="text" id="edit-canteen-<?= $canteen['id']; ?>-name" name="name" value="<?= htmlspecialchars($canteen['name']); ?>" oninput="validateText(this, 'Canteen Name')" required>
                                        <div class="error-message" id="edit-canteen-<?= $canteen['id']; ?>-name-error"></div>

                                        <label for="description">Description</label>
                                        <textarea name="description"><?= htmlspecialchars($canteen['description']); ?></textarea>
                                        
                                        <label for="address">Address*</label>
                                        <input type="text" id="edit-canteen-<?= $canteen['id']; ?>-address" name="address" value="<?= htmlspecialchars($canteen['address']); ?>" oninput="validateText(this, 'Address')" required>
                                        <div class="error-message" id="edit-canteen-<?= $canteen['id']; ?>-address-error"></div>

                                        <label for="image">Upload New Image (Max: 2MB, JPEG/PNG)*</label>
                                        <input type="file" id="edit-canteen-<?= $canteen['id']; ?>-image" name="image" accept="image/*" oninput="validateImage(this)">
                                        <div class="error-message" id="edit-canteen-<?= $canteen['id']; ?>-image-error"></div>

                                        <label class="section-label">Business Hours*</label>
                                        <div id="business-hours-section-<?= $canteen['id']; ?>" class="business-hours-section">
                                            <?php $count = 0;  foreach ($openDays as $index => $group): ?>
                                                <div class="hours-block">
                                                    <label>Open at:</label>
                                                    <input type="time" name="open_time[]" value="<?= htmlspecialchars($group['open']); ?>" required>
                                                    <label>Close at:</label>
                                                    <input type="time" name="close_time[]" value="<?= htmlspecialchars($group['close']); ?>" required>
                                                    <div class="day-select">
                                                        <?php foreach (['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'] as $day): ?>
                                                            <label><input type="checkbox" name="days[<?= $count; ?>][]" value="<?= $day; ?>" <?= in_array($day, $group['days']) ? 'checked' : ''; ?>> <?= $day; ?></label>
                                                        <?php endforeach; ?>
                                                    </div>
                                                    <button type="button" class="btn btn-delete" onclick="removeHoursBlock(this)">Remove</button>
                                                </div>
                                            <?php $count++; endforeach; ?>
                                        </div>
                                        <button type="button" class="btn btn-add-more" onclick="initializeEditHoursIndex(<?= $canteen['id']; ?>); addHoursBlock('business-hours-section-<?= $canteen['id']; ?>', <?= $canteen['id']; ?>)">[+] Add More Hours</button>

                                        <div class="buttons">
                                            <button type="submit" class="btn btn-primary">Save</button>
                                            <button type="button" class="btn btn-cancel" onclick="toggleEdit('canteen-<?= $canteen['id']; ?>')">Cancel</button>
                                        </div>
                                    </form>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="no-data">No canteens available. Add a new canteen to get started.</p>
                        <?php endif; ?>
                    </div>
                </div>

            </div>
        </div>

        <!-- Vendors Tab Content -->
        <div id="tab-vendors" class="tab-content" style="display: none;">
            <div class="columns">
                <!-- Add New Vendor Form -->
                <div class="column card">
                    <h2>Add New Vendor</h2>
                    <form method="POST" action="../controllers/vendor_handler.php?action=add&tab=tab-vendors" onsubmit="return validateVendorForm(this)" novalidate>
                        <label for="vendor-username">Username*</label>
                        <input type="text" id="vendor-username" name="username" placeholder="Enter Username" oninput="validateText(this, 'Username')" required>
                        <div class="error-message" id="vendor-username-error"></div>

                        <label for="vendor-email">Email*</label>
                        <input type="email" id="vendor-email" name="email" placeholder="Enter Email" oninput="validateEmail(this)" required>
                        <div class="error-message" id="vendor-email-error"></div>

                        <label for="vendor-password">Password*</label>
                        <input type="password" id="vendor-password" name="password" placeholder="Enter Password" oninput="validatePassword(this)" required>
                        <div class="error-message" id="vendor-password-error"></div>

                        <label for="vendor-cpassword">Confirm Password*</label>
                        <input type="password" id="vendor-cpassword" name="cpassword" placeholder="Reenter Password" oninput="validateConfirmPassword(this, document.getElementById('vendor-password'))" required>
                        <div class="error-message" id="vendor-cpassword-error"></div>

                        <label for="vendor-name">Name*</label>
                        <input type="text" id="vendor-name" name="vendor_name" placeholder="Enter Name" oninput="validateText(this, 'Name')" required>
                        <div class="error-message" id="vendor-name-error"></div>

                        <label for="vendor-business-name">Business Name*</label>
                        <input type="text" id="vendor-business-name" name="business_name" placeholder="Enter Business Name" oninput="validateText(this, 'Business Name')" required>
                        <div class="error-message" id="vendor-business-name-error"></div>

                        <label for="vendor-contact-number">Contact Number*</label>
                        <input type="text" id="vendor-contact-number" name="contact_number" placeholder="Enter Contact Number" oninput="validatePhoneNumber(this, 'Contact Number')" required>
                        <div class="error-message" id="vendor-contact-number-error"></div>

                        <button type="submit" class="btn btn-primary">Add Vendor</button>
                    </form>
                </div>

                <!-- Existing Vendors -->
                <div class="column card">
                    <h2>Existing Vendors</h2>
                    <div class="item-list">
                        <?php if (count($vendors) > 0): ?>
                            <?php foreach ($vendors as $vendor): ?>
                                <div class="item" id="vendor-<?= $vendor['user_id']; ?>">
                                    <div class="view-mode">
                                        <p><strong>Username:</strong> <?= htmlspecialchars($vendor['username']); ?></p>
                                        <p><strong>Email:</strong> <?= htmlspecialchars($vendor['email']); ?></p>
                                        <p><strong>Name:</strong> <?= htmlspecialchars($vendor['name']); ?></p>
                                        <p><strong>Business Name:</strong> <?= htmlspecialchars($vendor['business_name']); ?></p>
                                        <p><strong>Contact Number:</strong> <?= htmlspecialchars($vendor['contact_number']); ?></p>
                                        <div class="buttons">
                                            <button class="btn btn-edit" onclick="toggleEdit('vendor-<?= $vendor['user_id']; ?>')">Edit</button>
                                            <button class="btn btn-delete" onclick="if(confirm('Delete this vendor?')) { window.location.href='../controllers/vendor_handler.php?action=delete&user_id=<?= $vendor['user_id']; ?>&tab=tab-vendors' }">Delete</button>
                                        </div>
                                    </div>
                                    <form class="edit-mode" method="POST" action="../controllers/vendor_handler.php?action=update&tab=tab-vendors" style="display: none;" onsubmit="return validateEditVendorForm(this)">
                                        <input type="hidden" name="user_id" value="<?= $vendor['user_id']; ?>">
                                        
                                        <label for="username-<?= $vendor['user_id']; ?>">Username</label>
                                        <input type="text" id="username-<?= $vendor['user_id']; ?>" name="username" value="<?= htmlspecialchars($vendor['username']); ?>" oninput="validateText(this, 'Username')" required>
                                        <div class="error-message" id="username-<?= $vendor['user_id']; ?>-error"></div>

                                        <label for="email-<?= $vendor['user_id']; ?>">Email</label>
                                        <input type="email" id="email-<?= $vendor['user_id']; ?>" name="email" value="<?= htmlspecialchars($vendor['email']); ?>" oninput="validateEmail(this)" required>
                                        <div class="error-message" id="email-<?= $vendor['user_id']; ?>-error"></div>

                                        <label for="vendor-name-<?= $vendor['user_id']; ?>">Name</label>
                                        <input type="text" id="vendor-name-<?= $vendor['user_id']; ?>" name="vendor_name" value="<?= htmlspecialchars($vendor['name']); ?>" oninput="validateText(this, 'Name')" required>
                                        <div class="error-message" id="vendor-name-<?= $vendor['user_id']; ?>-error"></div>

                                        <label for="business-name-<?= $vendor['user_id']; ?>">Business Name</label>
                                        <input type="text" id="business-name-<?= $vendor['user_id']; ?>" name="business_name" value="<?= htmlspecialchars($vendor['business_name']); ?>" oninput="validateText(this, 'Business Name')" required>
                                        <div class="error-message" id="business-name-<?= $vendor['user_id']; ?>-error"></div>

                                        <label for="contact-number-<?= $vendor['user_id']; ?>">Contact Number</label>
                                        <input type="text" id="contact-number-<?= $vendor['user_id']; ?>" name="contact_number" value="<?= htmlspecialchars($vendor['contact_number']); ?>" oninput="validatePhoneNumber(this)" required>
                                        <div class="error-message" id="contact-number-<?= $vendor['user_id']; ?>-error"></div>

                                        <div class="buttons">
                                            <button type="submit" class="btn btn-primary">Save</button>
                                            <button type="button" class="btn btn-cancel" onclick="toggleEdit('vendor-<?= $vendor['user_id']; ?>')">Cancel</button>
                                        </div>
                                    </form>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="no-data">No vendors available. Add a new vendor to get started.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stalls Tab Content -->
        <div id="tab-stalls" class="tab-content" style="display: none;">
            <div class="columns">
                <!-- Add New Stall Form -->
                <div class="column card">
                    <h2>Add New Stall</h2>
                    <form method="POST" action="../controllers/stall_handler.php?action=add&tab=tab-stalls" onsubmit="return validateStallForm(this)" novalidate>
                        <label for="stall-name">Stall Name*</label>
                        <input type="text" id="stall-name" name="name" placeholder="Enter Stall Name" oninput="validateText(this, 'Stall Name')" required>
                        <div class="error-message" id="stall-name-error"></div>

                        <label for="stall-cuisine-type">Cuisine Type*</label>
                        <select id="stall-cuisine-type" name="cuisine_type" oninput="validateDropdown(this, 'Cuisine Type')" required>
                            <option disabled selected>Choose a cuisine</option>
                            <option value="Chinese">Chinese</option>
                            <option value="Malay">Malay</option>
                            <option value="Indian">Indian</option>
                            <option value="Western">Western</option>
                            <option value="Japanese">Japanese</option>
                            <option value="Korean">Korean</option>
                            <option value="Taiwan">Taiwan</option>
                            <option value="Fusion">Fusion</option>
                        </select>
                        <div class="error-message" id="stall-cuisine-type-error"></div>

                        <label for="vendor-id">Vendor*</label>
                        <select id="vendor-id" name="vendor_id" oninput="validateDropdown(this, 'Vendor')" required>
                            <option disabled selected>Choose a vendor</option>
                            <?php foreach ($vendorsForDropdown as $vendor): ?>
                                <option value="<?= $vendor['vendor_id']; ?>"><?= htmlspecialchars($vendor['business_name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="error-message" id="vendor-id-error"></div>

                        <label for="canteen-id">Canteen*</label>
                        <select id="canteen-id" name="canteen_id" oninput="validateDropdown(this, 'Canteen')" required>
                            <option disabled selected>Choose a canteen</option>
                            <?php foreach ($canteensForDropdown as $canteen): ?>
                                <option value="<?= $canteen['id']; ?>"><?= htmlspecialchars($canteen['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="error-message" id="canteen-id-error"></div>

                        <label for="is-open">Is Open?</label>
                        <select id="is-open" name="is_open" required>
                            <option value="0" selected>Closed</option>
                            <option value="1">Open</option>
                        </select>

                        <button type="submit" class="btn btn-primary">Add Stall</button>
                    </form>
                </div>


                <div class="column card">
                    <h2>Existing Stalls</h2>
                    <div class="item-list">
                        <?php if (count($stalls) > 0): ?>
                            <?php foreach ($stalls as $stall): ?>
                                <div class="item" id="stall-<?= $stall['id']; ?>">
                                    <div class="view-mode">
                                        <p><strong>Stall Name:</strong> <?= htmlspecialchars($stall['name']); ?></p>
                                        <p><strong>Cuisine Type:</strong> <?= htmlspecialchars($stall['cuisine_type']); ?></p>
                                        <p><strong>Vendor:</strong> <?= htmlspecialchars($stall['business_name']); ?></p>
                                        <p><strong>Canteen:</strong> <?= htmlspecialchars($stall['canteen_name']); ?></p>
                                        <p><strong>Status:</strong> <?= $stall['is_open'] ? 'Open' : 'Closed'; ?></p>
                                        <div class="buttons">
                                            <button class="btn btn-edit" onclick="toggleEdit('stall-<?= $stall['id']; ?>')">Edit</button>
                                            <button class="btn btn-delete" onclick="if(confirm('Delete this stall?')) { window.location.href='../controllers/stall_handler.php?action=delete&stall_id=<?= $stall['id']; ?>' }">Delete</button>
                                        </div>
                                    </div>

                                    <!-- Edit Mode Form -->
                                    <form class="edit-mode" method="POST" action="../controllers/stall_handler.php?action=update" style="display: none;" onsubmit="return validateEditStallForm(this);">
                                        <input type="hidden" name="stall_id" value="<?= $stall['id']; ?>">
                                        <label>Stall Name*</label>
                                        <input type="text" id="edit-stall-<?= $stall['id']; ?>-name" name="name" value="<?= htmlspecialchars($stall['name']); ?>" oninput="validateText(this)" required>
                                        <div class="error-message" id="edit-stall-<?= $stall['id']; ?>-name-error"></div>

                                        <label>Cuisine Type</label>
                                        <select name="cuisine_type" required>
                                            <option value="Chinese" <?= $stall['cuisine_type'] === 'Chinese' ? 'selected' : ''; ?>>Chinese</option>
                                            <option value="Malay" <?= $stall['cuisine_type'] === 'Malay' ? 'selected' : ''; ?>>Malay</option>
                                            <option value="Indian" <?= $stall['cuisine_type'] === 'Indian' ? 'selected' : ''; ?>>Indian</option>
                                            <option value="Western" <?= $stall['cuisine_type'] === 'Western' ? 'selected' : ''; ?>>Western</option>
                                            <option value="Japanese" <?= $stall['cuisine_type'] === 'Japanese' ? 'selected' : ''; ?>>Japanese</option>
                                            <option value="Korean" <?= $stall['cuisine_type'] === 'Korean' ? 'selected' : ''; ?>>Korean</option>
                                            <option value="Taiwan" <?= $stall['cuisine_type'] === 'Taiwan' ? 'selected' : ''; ?>>Taiwan</option>
                                            <option value="Fusion" <?= $stall['cuisine_type'] === 'Fusion' ? 'selected' : ''; ?>>Fusion</option>
                                        </select>

                                        <label>Vendor</label>
                                        <select name="vendor_id" required>
                                            <option disabled>Choose a vendor</option>
                                            <?php foreach ($vendorsForDropdown as $vendor): ?>
                                                <option value="<?= $vendor['vendor_id']; ?>" <?= $stall['vendor_id'] == $vendor['vendor_id'] ? 'selected' : ''; ?>><?= htmlspecialchars($vendor['business_name']); ?></option>
                                            <?php endforeach; ?>
                                        </select>

                                        <label>Canteen</label>
                                        <select name="canteen_id" required>
                                            <option disabled>Choose a canteen</option>
                                            <?php foreach ($canteensForDropdown as $canteen): ?>
                                                <option value="<?= $canteen['id']; ?>" <?= $stall['canteen_id'] == $canteen['id'] ? 'selected' : ''; ?>><?= htmlspecialchars($canteen['name']); ?></option>
                                            <?php endforeach; ?>
                                        </select>

                                        <label>Status</label>
                                        <select name="is_open" required>
                                            <option value="1" <?= $stall['is_open'] ? 'selected' : ''; ?>>Open</option>
                                            <option value="0" <?= !$stall['is_open'] ? 'selected' : ''; ?>>Closed</option>
                                        </select>

                                        <div class="buttons">
                                            <button type="submit" class="btn btn-primary">Save</button>
                                            <button type="button" class="btn btn-cancel" onclick="toggleEdit('stall-<?= $stall['id']; ?>')">Cancel</button>
                                        </div>
                                    </form>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="no-data">No stalls available. Add a new stall to get started.</p>
                        <?php endif; ?>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Template for hours block cloning -->
    <template id="hours-block-template">
        <div class="hours-block">
            <label>Open at:</label>
            <input type="time" name="open_time[]" required>
            <label>Close at:</label>
            <input type="time" name="close_time[]" required>
            <div class="day-select">
                <?php foreach ($daysOfWeek as $day): ?>
                    <label><input type="checkbox" name="days[INDEX][]" value="<?= $day; ?>"> <?= $day; ?></label>
                <?php endforeach; ?>
            </div>
            <button type="button" class="btn btn-delete" onclick="removeHoursBlock(this)">Remove</button>
        </div>
    </template>
</body>
</html>
