<?php
session_start();
include '../includes/db_connect.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

$action = $_GET['action'] ?? '';
$basePath = __DIR__ . '/../assets/images/locations/';

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
        try {
            // Begin transaction
            $conn->begin_transaction();
        
            // Upload the image and get the path
            $imagePath = uploadImage($image);
            if (!$imagePath) {
                throw new Exception("Failed to upload image.");
            }
        
            // Insert the main canteen record
            $stmt = $conn->prepare("INSERT INTO canteens (name, description, address, image_url) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $name, $description, $address, $imagePath);
            if (!$stmt->execute()) {
                throw new Exception("Failed to insert canteen details.");
            }
            $canteen_id = $stmt->insert_id;
            $stmt->close();
        
            // Insert business hours
            foreach ($open_times as $index => $open) {
                $close = $close_times[$index];
                foreach ($days[$index] as $day) {
                    $stmt = $conn->prepare("INSERT INTO canteen_hours (canteen_id, days, open_time, close_time) VALUES (?, ?, ?, ?)");
                    $stmt->bind_param("isss", $canteen_id, $day, $open, $close);
                    if (!$stmt->execute()) {
                        throw new Exception("Failed to insert business hours.");
                    }
                    $stmt->close();
                }
            }
        
            // Commit the transaction if all operations succeeded
            $conn->commit();
        
            $_SESSION['success_msg'] = "Canteen added successfully!";
        } catch (Exception $e) {
            // Rollback the transaction on any error
            $conn->rollback();
        
            // Delete the uploaded image if it exists (in case of failure)
            if ($imagePath && file_exists($imagePath)) {
                unlink($imagePath);
            }
        
            $_SESSION['error_msg'] = "Error adding canteen: " . $e->getMessage();
        } finally {
            // Close the connection
            $conn->close();
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
        try {
            // Fetch the current image path in case we need to delete it later
            $stmt = $conn->prepare("SELECT image_url FROM canteens WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->bind_result($currentImagePath);
            $stmt->fetch();
            $stmt->close();
        
            // Begin transaction
            $conn->begin_transaction();
        
            // Handle image upload if a new image is provided
            $newImagePath = null;
            if ($image && $image['size'] > 0) {
                $newImagePath = uploadImage($image);  // uploadImage() should handle image validation and return path or false
                if (!$newImagePath) {
                    throw new Exception("Failed to upload new image.");
                }
            }
        
            // Update canteen information
            if ($newImagePath) {
                $stmt = $conn->prepare("UPDATE canteens SET name = ?, description = ?, address = ?, image_url = ? WHERE id = ?");
                $stmt->bind_param("ssssi", $name, $description, $address, $newImagePath, $id);
            } else {
                $stmt = $conn->prepare("UPDATE canteens SET name = ?, description = ?, address = ? WHERE id = ?");
                $stmt->bind_param("sssi", $name, $description, $address, $id);
            }
            if (!$stmt->execute()) {
                throw new Exception("Failed to update canteen details.");
            }
            $stmt->close();
        
            // Delete old business hours and insert new ones
            $deleteHoursStmt = $conn->prepare("DELETE FROM canteen_hours WHERE canteen_id = ?");
            $deleteHoursStmt->bind_param("i", $id);
            if (!$deleteHoursStmt->execute()) {
                throw new Exception("Failed to delete previous business hours.");
            }
            $deleteHoursStmt->close();
        
            foreach ($open_times as $index => $open) {
                $close = $close_times[$index];
                foreach ($days[$index] as $day) {
                    $stmt = $conn->prepare("INSERT INTO canteen_hours (canteen_id, days, open_time, close_time) VALUES (?, ?, ?, ?)");
                    $stmt->bind_param("isss", $id, $day, $open, $close);
                    if (!$stmt->execute()) {
                        throw new Exception("Failed to insert new business hours.");
                    }
                    $stmt->close();
                }
            }
        
            // Commit the transaction if everything succeeded
            $conn->commit();
        
            // After committing, delete the old image if a new one was uploaded
            $filePath = $basePath . basename($currentImagePath);
            if ($newImagePath && $filePath && file_exists($filePath)) {
                unlink($filePath);
            }
        
            $_SESSION['success_msg'] = "Canteen updated successfully!";
        } catch (Exception $e) {
            // Rollback transaction on error
            $conn->rollback();
        
            // Delete the newly uploaded image if it exists (since the transaction failed)
            if ($newImagePath && file_exists($newImagePath)) {
                unlink($newImagePath);
            }
        
            $_SESSION['error_msg'] = "Error updating canteen: " . $e->getMessage();
        } finally {
            // Close the connection
            $conn->close();
        }
        
    }

    header("Location: ../pages/admin_dashboard.php?tab=tab-canteens");
    exit();
} elseif ($action === 'delete') {
    $id = $_GET['id'] ?? null;

    if ($id) {
        try {
            // Step 1: Fetch the image URL before deleting any records
            $stmt = $conn->prepare("SELECT image_url FROM canteens WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->bind_result($imagePath);
            $stmt->fetch();
            $stmt->close();
        
            // Step 2: Start transaction
            $conn->begin_transaction();
        
            // Delete related business hours first
            $stmt = $conn->prepare("DELETE FROM canteen_hours WHERE canteen_id = ?");
            $stmt->bind_param("i", $id);
            if (!$stmt->execute()) {
                throw new Exception("Failed to delete canteen hours.");
            }
            $stmt->close();
        
            // Delete the main canteen record
            $stmt = $conn->prepare("DELETE FROM canteens WHERE id = ?");
            $stmt->bind_param("i", $id);
            if (!$stmt->execute()) {
                throw new Exception("Failed to delete canteen.");
            }
            $stmt->close();
        
            // Commit the transaction if everything above succeeds
            $conn->commit();
        
            // Step 3: Delete the image file if it exists
            $filePath = $basePath . basename($imagePath);
            if ($imagePath && file_exists($filePath) && !unlink($filePath)) {
                // Log an error if file deletion fails (but do not roll back the database transaction)
                error_log("Failed to delete image file: " . $filePath);
            }
        
            $_SESSION['success_msg'] = "Canteen deleted successfully!";
        } catch (Exception $e) {
            // Roll back the transaction if any error occurs
            $conn->rollback();
            $_SESSION['error_msg'] = "Error deleting canteen: " . $e->getMessage();
        } finally {
            // Close the connection
            $conn->close();
        }
        
    } else {
        $_SESSION['error_msg'] = "Failed to delete canteen!";
    }

    header("Location: ../pages/admin_dashboard.php?tab=tab-canteens");
    exit();
}
