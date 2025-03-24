<?php
session_start();
require 'database_connection.php'; // Ensure this file includes the database connection

if ($_SESSION['role'] != 'employee') {
    echo "Access denied.";
    exit;
}

// Add product
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_product'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];

    $sql = "INSERT INTO products (name, description, price, stock) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdi", $name, $description, $price, $stock);
    $stmt->execute();
}

// Edit product
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_product'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];

    $sql = "UPDATE products SET name = ?, description = ?, price = ?, stock = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdii", $name, $description, $price, $stock, $id);
    $stmt->execute();
}

// Delete product
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_product'])) {
    $id = $_POST['id'];

    $sql = "DELETE FROM products WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
}

// List customers
$sql = "SELECT * FROM users WHERE role_id = 1";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
    echo "<p>" . $row['username'] . "</p>";
}

// Add credit to customer account
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_credit'])) {
    $user_id = $_POST['user_id'];
    $amount = $_POST['amount'];

    $sql = "UPDATE credits SET amount = amount + ? WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("di", $amount, $user_id);
    $stmt->execute();

    echo "Credit added.";
}
?>


