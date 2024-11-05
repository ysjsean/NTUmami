<?php
session_start();
include '../includes/db_connect.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'vendor') {
    header('Location: ../index.php');
    exit();
}

$userId = $_SESSION['user_id'];
$action = $_GET['action'] ?? '';
$redirectUrl = '../pages/vendor_dashboard.php?tab=tab-foods';
$basePath = __DIR__ . '/../assets/images/food_images/';

/**
 * Generalized validation function for fields.
 * 
 * @param string $field - The field name.
 * @param mixed $value - The value of the field.
 * @return array - Returns an array of error messages, if any.
 */
function validateField($field, $value) {
    $errors = [];
    switch ($field) {
        case 'name':
            if (empty($value)) {
                $errors[] = "Name is required.";
            }
            break;
        case 'price':
            if (empty($value)) {
                $errors[] = "Price is required.";
            } else if (!is_numeric($value) || $value <= 0) {
                $errors[] = "Price must be a positive number.";
            }
            break;
        case 'image':
            if ($value['error'] === UPLOAD_ERR_NO_FILE) {
                $errors[] = "Image file is required.";
            } elseif ($value['error'] !== UPLOAD_ERR_OK) {
                $errors[] = "Error uploading the file.";
            } else {
                $allowedExtensions = ['png', 'jpeg', 'jpg'];
                $maxFileSize = 2 * 1024 * 1024; // Max size 2MB
                $fileExtension = strtolower(pathinfo($value['name'], PATHINFO_EXTENSION));

                if (!in_array($fileExtension, $allowedExtensions)) {
                    $errors[] = "Invalid file extension. Allowed: JPG, JPEG, PNG.";
                }
                if ($value['size'] > $maxFileSize) {
                    $errors[] = "Image size must be under 2MB.";
                }
            }
            break;
    }
    return $errors;
}

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

    // Validate file size and type
    $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if ($file['size'] > $maxFileSize || !in_array($fileExtension, $allowedExtensions)) {
        return false;
    }

    // Prepare upload directory
    $uploadDir = __DIR__ . '/../assets/images/' . $uploadSubDir . '/';
    if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true)) {
        return false;
    }

    // Create a unique file name and move the uploaded file
    $fileName = uniqid() . '.' . $fileExtension;
    $uploadPath = $uploadDir . $fileName;
    if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
        return '/NTUmami/assets/images/' . $uploadSubDir . '/' . $fileName; // Return web path for database
    }

    return false;
}

/**
 * Delete an image file from the server.
 *
 * @param string $filePath - The relative path to the file from the root of the project.
 * @return bool - Returns true if file deleted successfully or does not exist, false otherwise.
 */
function deleteImage($basePath, $imagePath) {
    $filePath = $basePath . basename($imagePath);
    if ($imagePath && file_exists($filePath) && !unlink($filePath)) {
        error_log("Failed to delete image file: " . $filePath);
        return false;
    }
    return true;
}

// Start transaction
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

                // Field validation
                $errors = array_merge(
                    validateField('name', $name),
                    validateField('price', $price),
                    isset($_FILES['image']) ? validateField('image', $_FILES['image']) : []
                );

                if (!empty($errors)) {
                    throw new Exception(implode("<br>", $errors));
                }

                // Handle image upload
                $imagePath = null;
                if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                    $imagePath = uploadImage($_FILES['image'], 'food_images');
                    if (!$imagePath) {
                        throw new Exception("Failed to upload image.");
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

                // Field validation
                $errors = array_merge(
                    validateField('name', $name),
                    validateField('price', $price),
                    isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK ? validateField('image', $_FILES['image']) : []
                );

                if (!empty($errors)) {
                    throw new Exception(implode("<br>", $errors));
                }

                // Fetch existing image path for deletion if necessary
                $existingImage = $conn->query("SELECT image_url FROM foods WHERE id = $foodId")->fetch_assoc()['image_url'];

                // Handle new image upload if provided
                $newImagePath = $existingImage;
                if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                    $newImagePath = uploadImage($_FILES['image'], 'food_images');
                    if (!$newImagePath) {
                        throw new Exception("Failed to upload new image.");
                    }

                    // Delete the old image if a new one was uploaded successfully
                    if ($existingImage) {
                        $imageDeleted = deleteImage($basePath, $existingImage);
                        if (!$imageDeleted)
                            throw new Exception("Failed to replace food image.");
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
                    $imageDeleted = deleteImage($basePath, $existingImage);
                    if (!$imageDeleted)
                        throw new Exception("Failed to delete food image.");
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
