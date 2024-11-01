<?php
session_start();
include '../includes/db_connect.php';
include '../helpers/file_helpers.php'; // Assuming the helper functions above are saved here

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'vendor') {
    header('Location: ../index.php');
    exit();
}

$userId = $_SESSION['user_id'];
$action = $_GET['action'] ?? '';
$redirectUrl = '../pages/vendor_dashboard.php?tab=tab-foods';

/**
 * Upload an image file to a specified directory with validation.
 * 
 * @param array $file - The file from $_FILES array.
 * @param string $uploadSubDir - The sub-directory within the images folder for uploading files.
 * @return string|false - Returns the file path for database storage or false on failure.
 */
function uploadImage($file, $uploadSubDir = 'food_images') {
    $allowedExtensions = ['png', 'jpeg', 'jpg'];
    $maxFileSize = 2 * 1024 * 1024; // Max size 2MB

    // Validate file size
    if ($file['size'] > $maxFileSize) {
        return false; // File too large
    }

    // Validate file type
    $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($fileExtension, $allowedExtensions)) {
        return false; // Invalid file extension
    }

    // Prepare upload directory
    $uploadDir = __DIR__ . '/../assets/images/' . $uploadSubDir . '/';
    if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true)) {
        return false; // Failed to create directory
    }

    // Create a unique file name
    $fileName = uniqid() . '.' . $fileExtension;
    $uploadPath = $uploadDir . $fileName;

    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
        return '/NTUmami/assets/images/' . $uploadSubDir . '/' . $fileName; // Return web path for database
    }

    return false; // Failed to upload file
}

/**
 * Delete an image file from the server.
 *
 * @param string $filePath - The relative path to the file from the root of the project.
 * @return bool - Returns true if file deleted successfully or does not exist, false otherwise.
 */
function deleteImage($filePath) {
    $absolutePath = __DIR__ . '/../' . ltrim($filePath, '/');
    return !file_exists($absolutePath) || unlink($absolutePath);
}

$conn->begin_transaction();

try {
    switch ($action) {
        case 'add':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $name = $_POST['name'];
                $price = $_POST['price'];
                $description = $_POST['description'] ?? '';
                $is_halal = isset($_POST['is_halal']) ? 1 : 0;
                $is_vegetarian = isset($_POST['is_vegetarian']) ? 1 : 0;
                $is_in_stock = isset($_POST['is_in_stock']) ? 1 : 0;

                // Handle image upload
                $imagePath = null;
                if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                    $imagePath = uploadImage($_FILES['image'], 'food_images');
                    if (!$imagePath) {
                        throw new Exception("Failed to upload image. Ensure the file is JPG, JPEG, PNG, or GIF and under 2MB.");
                    }
                }

                // Get vendor's stall ID
                $vendor = $conn->query("SELECT id FROM vendors WHERE user_id = $userId")->fetch_assoc();
                $stallId = $conn->query("SELECT id FROM stalls WHERE vendor_id = {$vendor['id']} LIMIT 1")->fetch_assoc()['id'];

                // Insert food item
                $stmt = $conn->prepare("INSERT INTO foods (stall_id, name, price, description, is_halal, is_vegetarian, is_in_stock, image_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("isdsssis", $stallId, $name, $price, $description, $is_halal, $is_vegetarian, $is_in_stock, $imagePath);
                if (!$stmt->execute()) {
                    throw new Exception("Failed to add food item.");
                }

                $conn->commit();
                $_SESSION['success_msg'] = "Food item added successfully.";
            }
            break;

        case 'update':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $foodId = $_POST['food_id'];
                $name = $_POST['name'];
                $price = $_POST['price'];
                $description = $_POST['description'] ?? '';
                $is_halal = isset($_POST['is_halal']) ? 1 : 0;
                $is_vegetarian = isset($_POST['is_vegetarian']) ? 1 : 0;
                $is_in_stock = isset($_POST['is_in_stock']) ? 1 : 0;

                // Fetch existing image path for deletion if necessary
                $existingImage = $conn->query("SELECT image_url FROM foods WHERE id = $foodId")->fetch_assoc()['image_url'];

                // Handle new image upload if provided
                $newImagePath = $existingImage;
                if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                    $newImagePath = uploadImage($_FILES['image'], 'food_images');
                    if (!$newImagePath) {
                        throw new Exception("Failed to upload new image. Ensure the file is JPG, JPEG, PNG, or GIF and under 2MB.");
                    }

                    // Delete the old image if a new one was uploaded successfully
                    if ($existingImage) {
                        deleteImage($existingImage);
                    }
                }

                // Update the food item
                $stmt = $conn->prepare("UPDATE foods SET name = ?, price = ?, description = ?, is_halal = ?, is_vegetarian = ?, is_in_stock = ?, image_url = ? WHERE id = ?");
                $stmt->bind_param("sdssissi", $name, $price, $description, $is_halal, $is_vegetarian, $is_in_stock, $newImagePath, $foodId);
                if (!$stmt->execute()) {
                    throw new Exception("Failed to update food item.");
                }

                $conn->commit();
                $_SESSION['success_msg'] = "Food item updated successfully.";
            }
            break;

        case 'delete':
            if (isset($_GET['id'])) {
                $foodId = $_GET['id'];

                // Fetch existing image path for deletion
                $existingImage = $conn->query("SELECT image_url FROM foods WHERE id = $foodId")->fetch_assoc()['image_url'];

                // Delete the food item
                if (!$conn->query("DELETE FROM foods WHERE id = $foodId")) {
                    throw new Exception("Failed to delete food item.");
                }

                // Delete the associated image file
                if ($existingImage) {
                    deleteImage($existingImage);
                }

                $conn->commit();
                $_SESSION['success_msg'] = "Food item deleted successfully.";
            }
            break;

        default:
            throw new Exception("Invalid action.");
    }

} catch (Exception $e) {
    $conn->rollback();
    $_SESSION['error_msg'] = $e->getMessage();
}

header("Location: $redirectUrl");
exit();

