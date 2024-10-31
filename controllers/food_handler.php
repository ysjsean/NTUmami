<?php
session_start();
include '../includes/db_connect.php';

$action = $_GET['action'];

if ($action === 'add') {
    // Add food
    $stall_id = $_POST['stall_id'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $is_halal = isset($_POST['is_halal']) ? 1 : 0;
    $is_vegetarian = isset($_POST['is_vegetarian']) ? 1 : 0;
    $is_in_stock = isset($_POST['is_in_stock']) ? 1 : 0;

    $stmt = $conn->prepare("INSERT INTO foods (stall_id, name, description, price, is_halal, is_vegetarian, is_in_stock) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issdiii", $stall_id, $name, $description, $price, $is_halal, $is_vegetarian, $is_in_stock);
    $stmt->execute();
} elseif ($action === 'update') {
    // Update food
    $food_id = $_POST['food_id'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $is_halal = isset($_POST['is_halal']) ? 1 : 0;
    $is_vegetarian = isset($_POST['is_vegetarian']) ? 1 : 0;
    $is_in_stock = isset($_POST['is_in_stock']) ? 1 : 0;

    $stmt = $conn->prepare("UPDATE foods SET name = ?, description = ?, price = ?, is_halal = ?, is_vegetarian = ?, is_in_stock = ? WHERE id = ?");
    $stmt->bind_param("ssdiiii", $name, $description, $price, $is_halal, $is_vegetarian, $is_in_stock, $food_id);
    $stmt->execute();
} elseif ($action === 'delete') {
    // Delete food
    $food_id = $_POST['food_id'];
    $stmt = $conn->prepare("DELETE FROM foods WHERE id = ?");
    $stmt->bind_param("i", $food_id);
    $stmt->execute();
}
header('Location: ../pages/vendor_dashboard.php');
