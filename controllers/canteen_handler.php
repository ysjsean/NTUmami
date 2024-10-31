<?php
session_start();
include '../includes/db_connect.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

$action = $_GET['action'] ?? '';

// Helper function to validate image
function validateImage($file) {
    $maxFileSize = 2 * 1024 * 1024; // 2MB in bytes
    $allowedTypes = ['image/jpeg', 'image/png'];

    if ($file['size'] > $maxFileSize) {
        return "Image size should not exceed 2MB.";
    }

    if (!in_array($file['type'], $allowedTypes)) {
        return "Only JPEG and PNG image formats are allowed.";
    }

    return null; // No errors
}

// Function to upload image
function uploadImage($file) {
    $uploadDir = __DIR__ . '/../assets/images/locations/';
    
    if (!is_dir($uploadDir)) {
        if (!mkdir($uploadDir, 0755, true)) {
            return false; // Failed to create directory
        }
    }

    $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $fileName = uniqid() . '.' . $fileExtension;
    $uploadPath = $uploadDir . $fileName;

    if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
        return '/NTUmami/assets/images/locations/' . $fileName; // Return the web path for display
    }

    return false; // Failed to upload file
}

// Function to validate business hours input
function validateBusinessHours($open_times, $close_times, $days) {
    foreach ($open_times as $index => $open_time) {
        if (empty($open_time) || empty($close_times[$index])) {
            return "Both open and close times are required for each time block.";
        }

        if (strtotime($open_time) >= strtotime($close_times[$index])) {
            return "Open time must be earlier than close time for each time block.";
        }

        if (!isset($days[$index]) || empty($days[$index])) {
            return "At least one day must be selected for each time block.";
        }
    }
    return null; // No errors
}

// Helper function to gather validation errors
function gatherValidationErrors($name, $address, $image, $open_times, $close_times, $days, $isUpdate = false) {
    $errors = [];

    if (empty($name)) {
        $errors[] = "Canteen name is required.";
    }

    if (empty($address)) {
        $errors[] = "Address is required.";
    }

    // Only validate the image if it's provided during an update or if it's required during an add action
    if (!$isUpdate || ($image && $image['size'] > 0)) { 
        $imageError = validateImage($image);
        if ($imageError) {
            $errors[] = $imageError;
        }
    }

    $hoursError = validateBusinessHours($open_times, $close_times, $days);
    if ($hoursError) {
        $errors[] = $hoursError;
    }

    return $errors;
}

if ($action === 'add') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description'] ?? '');
    $address = trim($_POST['address']);
    $image = $_FILES['image'] ?? null;

    $open_times = $_POST['open_time'] ?? [];
    $close_times = $_POST['close_time'] ?? [];
    $days = $_POST['days'] ?? [];

    // Gather all validation errors
    $errors = gatherValidationErrors($name, $address, $image, $open_times, $close_times, $days);

    if (!empty($errors)) {
        $_SESSION['error_msg'] = implode("<br>", $errors); // Display all errors as a single message
    } else {
        $imagePath = uploadImage($image);
        if ($imagePath) {
            $stmt = $conn->prepare("INSERT INTO canteens (name, description, address, image_url) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $name, $description, $address, $imagePath);
            $stmt->execute();
            $canteen_id = $stmt->insert_id;
            $stmt->close();

            // Insert business hours
            foreach ($open_times as $index => $open) {
                $close = $close_times[$index];
                foreach ($days[$index] as $day) {
                    $stmt = $conn->prepare("INSERT INTO canteen_hours (canteen_id, days, open_time, close_time) VALUES (?, ?, ?, ?)");
                    $stmt->bind_param("isss", $canteen_id, $day, $open, $close);
                    $stmt->execute();
                    $stmt->close();
                }
            }

            $_SESSION['success_msg'] = "Canteen added successfully!";
        } else {
            $_SESSION['error_msg'] = "Failed to upload image.";
        }
    }

    header("Location: ../pages/admin_dashboard.php?tab=tab-canteens");
    exit();
} elseif ($action === 'update') {
    $id = $_POST['id'];
    $name = trim($_POST['name']);
    $description = trim($_POST['description'] ?? '');
    $address = trim($_POST['address']);
    $image = $_FILES['image'] ?? null;

    $open_times = $_POST['open_time'] ?? [];
    $close_times = $_POST['close_time'] ?? [];
    $days = $_POST['days'] ?? [];

    // Gather all validation errors
    $errors = gatherValidationErrors($name, $address, $image, $open_times, $close_times, $days, true);

    if (!empty($errors)) {
        $_SESSION['error_msg'] = implode("<br>", $errors); // Display all errors as a single message
    } else {
        $imagePath = null;
        if ($image && $image['size'] > 0) { // Only process if a new image is uploaded
            $imagePath = uploadImage($image);
            if (!$imagePath) {
                $_SESSION['error_msg'] = "Failed to upload new image.";
                header("Location: ../pages/admin_dashboard.php?tab=tab-canteens");
                exit();
            }
        }

        if ($imagePath) {
            $stmt = $conn->prepare("UPDATE canteens SET name = ?, description = ?, address = ?, image_url = ? WHERE id = ?");
            $stmt->bind_param("ssssi", $name, $description, $address, $imagePath, $id);
        } else {
            $stmt = $conn->prepare("UPDATE canteens SET name = ?, description = ?, address = ? WHERE id = ?");
            $stmt->bind_param("sssi", $name, $description, $address, $id);
        }

        $stmt->execute();
        $stmt->close();

        // Update business hours: Delete old hours and reinsert new ones
        $conn->query("DELETE FROM canteen_hours WHERE canteen_id = $id");

        foreach ($open_times as $index => $open) {
            $close = $close_times[$index];
            foreach ($days[$index] as $day) {
                $stmt = $conn->prepare("INSERT INTO canteen_hours (canteen_id, days, open_time, close_time) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("isss", $id, $day, $open, $close);
                $stmt->execute();
                $stmt->close();
            }
        }

        $_SESSION['success_msg'] = "Canteen updated successfully!";
    }

    header("Location: ../pages/admin_dashboard.php?tab=tab-canteens");
    exit();
} elseif ($action === 'delete') {
    $id = $_GET['id'] ?? null;
    $basePath = __DIR__ . '/../assets/images/locations/';

    if ($id) {
        try {
            // First, fetch the image path
            $stmt = $conn->prepare("SELECT image_url FROM canteens WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->bind_result($imagePath);
            $stmt->fetch();
            $stmt->close();

            // Delete the image file if it exists
            $filePath = $basePath . basename($imagePath);
            if ($imagePath && file_exists($filePath)) {
                unlink($filePath);
            }

            // Delete business hours
            $conn->query("DELETE FROM canteen_hours WHERE canteen_id = $id");

            // Prepare and execute the delete query
            $stmt = $conn->prepare("DELETE FROM canteens WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();

            $_SESSION['success_msg'] = "Canteen deleted successfully!";
        } catch (Exception $e) {
            $_SESSION['error_msg'] = 'Caught exception: ' . $e->getMessage();
        }
    } else {
        $_SESSION['error_msg'] = "Failed to delete canteen!";
    }

    header("Location: ../pages/admin_dashboard.php?tab=tab-canteens");
    exit();
}
