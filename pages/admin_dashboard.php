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
    SELECT stalls.*, vendors.business_name, canteens.name
    FROM stalls
    INNER JOIN vendors ON stalls.vendor_id = vendors.id
    INNER JOIN canteens ON stalls.canteen_id = canteens.id
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
                    <form method="POST" action="../controllers/canteen_handler.php?action=add&tab=tab-canteens" enctype="multipart/form-data" novalidate>
                        <label for="canteen-name">Canteen Name*</label>
                        <input type="text" id="canteen-name" name="name" placeholder="Enter Canteen Name" required>

                        <label for="canteen-description">Description</label>
                        <textarea id="canteen-description" name="description" placeholder="Enter Description"></textarea>

                        <label for="canteen-address">Address*</label>
                        <input type="text" id="canteen-address" name="address" placeholder="Enter Address" required>

                        <label for="canteen-image">Upload Image (Max: 2MB, JPEG/PNG)*</label>
                        <input type="file" id="canteen-image" name="image" accept="image/*">
                        <p class="file-input-info">Max size 2MB, JPEG or PNG only.</p>

                        <!-- Business Hours Section -->
                        <label class="section-label">Business Hours</label>
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
                                            <button class="btn btn-delete" onclick="confirmDelete(<?= $canteen['id']; ?>)">Delete</button>
                                        </div>
                                    </div>

                                    <!-- Edit Mode Form -->
                                    <form class="edit-mode" method="POST" action="../controllers/canteen_handler.php?action=update&tab=tab-canteens" enctype="multipart/form-data" style="display: none;">
                                        <input type="hidden" name="id" value="<?= $canteen['id']; ?>">
                                        <label for="name">Canteen Name*</label>
                                        <input type="text" name="name" value="<?= htmlspecialchars($canteen['name']); ?>" required>
                                        <label for="description">Description</label>
                                        <textarea name="description"><?= htmlspecialchars($canteen['description']); ?></textarea>
                                        <label for="address">Address*</label>
                                        <input type="text" name="address" value="<?= htmlspecialchars($canteen['address']); ?>" required>
                                        <label for="image">Upload New Image (Max: 2MB, JPEG/PNG)*</label>
                                        <input type="file" name="image" accept="image/*">

                                        <label class="section-label">Business Hours</label>
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
                    <form method="POST" action="../controllers/vendor_handler.php?action=add&tab=tab-vendors" novalidate>
                        <label for="vendor-username">Username*</label>
                        <input type="text" id="vendor-username" name="username" placeholder="Enter Username" required>
                        <label for="vendor-email">Email*</label>
                        <input type="email" id="vendor-email" name="email" placeholder="Enter Email" required>
                        <label for="vendor-password">Password*</label>
                        <input type="password" id="vendor-password" name="password" placeholder="Enter Password" required>
                        <label for="vendor-cpassword">Confirm Password*</label>
                        <input type="password" id="vendor-cpassword" name="cpassword" placeholder="Reenter Password" required>
                        <label for="vendor-name">Name*</label>
                        <input type="text" id="vendor-name" name="vendor-name" placeholder="Enter Name" required>
                        <label for="vendor-business-name">Business Name*</label>
                        <input type="text" id="vendor-business-name" name="business_name" placeholder="Enter Business Name" required>
                        <label for="vendor-contact-number">Contact Number*</label>
                        <input type="text" id="vendor-contact-number" name="contact_number" placeholder="Enter Contact Number" required>
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
                                    <form class="edit-mode" method="POST" action="../controllers/vendor_handler.php?action=update&tab=tab-vendors" style="display: none;">
                                        <input type="hidden" name="user_id" value="<?= $vendor['user_id']; ?>">
                                        <label for="username-<?= $vendor['user_id']; ?>">Username</label>
                                        <input type="text" id="username-<?= $vendor['user_id']; ?>" name="username" value="<?= htmlspecialchars($vendor['username']); ?>" required>
                                        <label for="email-<?= $vendor['user_id']; ?>">Email</label>
                                        <input type="email" id="email-<?= $vendor['user_id']; ?>" name="email" value="<?= htmlspecialchars($vendor['email']); ?>" required>
                                        <label for="vendor-name-<?= $vendor['user_id']; ?>">Name</label>
                                        <input type="text" id="vendor-name-<?= $vendor['user_id']; ?>" name="vendor_name" value="<?= htmlspecialchars($vendor['name']); ?>" required>
                                        <label for="business-name-<?= $vendor['user_id']; ?>">Business Name</label>
                                        <input type="text" id="business-name-<?= $vendor['user_id']; ?>" name="business_name" value="<?= htmlspecialchars($vendor['business_name']); ?>" required>
                                        <label for="contact-number-<?= $vendor['user_id']; ?>">Contact Number</label>
                                        <input type="text" id="contact-number-<?= $vendor['user_id']; ?>" name="contact_number" value="<?= htmlspecialchars($vendor['contact_number']); ?>" required>
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
                    <form method="POST" action="../controllers/stall_handler.php?action=add&tab=tab-stalls">
                        <label for="stall-name">Stall Name*</label>
                        <input type="text" id="stall-name" name="name" placeholder="Enter Stall Name" required>
                        <label for="stall-cuisine-type">Cuisine Type*</label>
                        <select id="stall-cuisine-type" name="cuisine_type" required>
                            <option disabled selected>Choose a cuisine</option>
                            <option value="Chinese">Chinese</option>
                            <option value="Malay">Malay</option>
                            <option value="Indian">Indian</option>
                            <option value="Western">Western</option>
                            <option value="Japanese">Japanese</option>
                            <option value="Korean">Korean</option>
                            <option value="Fusion">Fusion</option>
                        </select>
                        <label for="vendor-id">Vendor*</label>
                        <select id="vendor-id" name="vendor_id" required>
                            <option disabled selected>Choose a vendor</option>
                            <?php foreach ($vendors as $vendor): ?>
                                <option value="<?= $vendor['user_id']; ?>"><?= htmlspecialchars($vendor['business_name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" class="btn btn-primary">Add Stall</button>
                    </form>
                </div>

                <!-- Existing Stalls -->
                <div class="column card">
                    <h2>Existing Stalls</h2>
                    <div class="item-list">
                        <?php if (count($stalls) > 0): ?>
                            <?php foreach ($stalls as $stall): ?>
                                <div class="item" id="stall-<?= $stall['id']; ?>">
                                    <div class="view-mode">
                                        <p><strong>Stall Name:</strong> <?= htmlspecialchars($stall['name']); ?></p>
                                        <p><strong>Cuisine Type:</strong> <?= htmlspecialchars($stall['cuisine_type']); ?></p>
                                        <div class="buttons">
                                            <button class="btn btn-edit" onclick="toggleEdit('stall-<?= $stall['id']; ?>')">Edit</button>
                                            <button class="btn btn-delete" onclick="if(confirm('Delete this stall?')) { window.location.href='../controllers/stall_handler.php?action=delete&stall_id=<?= $stall['id']; ?>' }">Delete</button>
                                        </div>
                                    </div>
                                    <form class="edit-mode" method="POST" action="../controllers/stall_handler.php?action=update" style="display: none;">
                                        <input type="hidden" name="stall_id" value="<?= $stall['id']; ?>">
                                        <label>Stall Name</label>
                                        <input type="text" name="name" value="<?= htmlspecialchars($stall['name']); ?>" required>
                                        <label>Cuisine Type</label>
                                        <select name="cuisine_type" required>
                                            <option value="Chinese" <?= $stall['cuisine_type'] === 'Chinese' ? 'selected' : ''; ?>>Chinese</option>
                                            <option value="Western" <?= $stall['cuisine_type'] === 'Western' ? 'selected' : ''; ?>>Western</option>
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

    <script>
        function openTab(event, tabId) {
            // Hide all tab contents and reset their forms
            const tabContents = document.querySelectorAll(".tab-content");
            tabContents.forEach(content => {
                content.style.display = "none";
                resetFormsInContent(content);
            });
            // Show the selected tab
            document.getElementById(tabId).style.display = "block";
            // Set the clicked tab as active
            const tabLinks = document.querySelectorAll(".tab-link");
            tabLinks.forEach(link => link.classList.remove("active"));

            if (event)
                event.currentTarget.classList.add("active");
            else
                document.querySelector(`[onclick="openTab(event, '${tabId}')"]`).classList.add('active');
        }

        function toggleEdit(itemId) {
            const item = document.getElementById(itemId);
            const viewMode = item.querySelector('.view-mode');
            const editMode = item.querySelector('.edit-mode');
            viewMode.style.display = viewMode.style.display === 'none' ? 'flex' : 'none';
            editMode.style.display = editMode.style.display === 'none' ? 'flex' : 'none';
        }

        function resetForm(itemId) {
            const item = document.getElementById(itemId);
            const editMode = item.querySelector('.edit-mode');
            const viewMode = item.querySelector('.view-mode');

            // Reset the edit form fields
            editMode.reset();
            // Toggle back to view mode
            editMode.style.display = 'none';
            viewMode.style.display = 'flex';
        }

        function resetFormsInContent(content) {
            // Find all forms within the specified tab content and reset them
            const forms = content.querySelectorAll("form");
            forms.forEach(form => {
                form.reset();
                const editModes = form.parentElement.querySelectorAll('.edit-mode');
                const viewModes = form.parentElement.querySelectorAll('.view-mode');
                editModes.forEach(mode => mode.style.display = 'none');
                viewModes.forEach(mode => mode.style.display = 'flex');
            });
        }

        // On page load, check the URL for the 'tab' parameter and set the active tab accordingly
        document.addEventListener("DOMContentLoaded", function() {
            const urlParams = new URLSearchParams(window.location.search);
            const activeTab = urlParams.get('tab') || 'tab-canteens'; // Default to canteens tab
            openTab(null, activeTab)
        });

        // Keep separate counters for 'add' and each 'edit' section
        let hoursBlockIndices = {
            add: 0,
        };

        // Function to initialize an index for editing if not already set
        function initializeEditHoursIndex(canteenId) {
            if (!(canteenId in hoursBlockIndices)) {
                hoursBlockIndices[canteenId] = document.querySelectorAll(`#business-hours-section-${canteenId} .hours-block`).length;
            }
        }

        // Function to add an hours block in the specified section
        function addHoursBlock(sectionId, canteenId = 'add') {
            const template = document.getElementById('hours-block-template');
            const clone = template.content.cloneNode(true);

            const index = hoursBlockIndices[canteenId];
            clone.querySelectorAll("input[type='checkbox']").forEach(checkbox => {
                checkbox.name = `days[${index}][]`;
            });

            // Update the index for the current section
            hoursBlockIndices[canteenId]++;
            document.getElementById(sectionId).appendChild(clone);
        }

        // Function to remove an hours block
        function removeHoursBlock(button) {
            const section = button.closest(".business-hours-section");
            if (section.querySelectorAll(".hours-block").length > 1) {
                button.closest(".hours-block").remove();
            } else {
                alert("At least one business hours block is required.");
            }
        }

        function confirmDelete(id) {
            if (confirm("Are you sure you want to delete this canteen?")) {
                window.location.href = `../controllers/canteen_handler.php?action=delete&id=${id}&tab=tab-canteens`;
            }
        }

    </script>

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
