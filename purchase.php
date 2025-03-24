<?php
session_start();
require 'database_connection.php'; // Ensure this file includes the database connection

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'customer') {
    echo "Access denied.";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];

    if (!filter_var($quantity, FILTER_VALIDATE_INT, array("options" => array("min_range" => 1)))) {
        echo "Invalid quantity.";
        exit;
    }

    // Check product stock
    $sql = "SELECT stock, price FROM products WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();

    if ($product['stock'] < $quantity) {
        echo "Not enough stock.";
        exit;
    }

    // Check user credit
    $sql = "SELECT amount FROM credits WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $credit = $result->fetch_assoc()['amount'];

    $total_price = $product['price'] * $quantity;

    if ($credit < $total_price) {
        echo "Not enough credit.";
        exit;
    }

    // Deduct credit and update stock
    $new_credit = $credit - $total_price;
    $new_stock = $product['stock'] - $quantity;

    $conn->begin_transaction();

    try {
        $sql = "UPDATE credits SET amount = ? WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("di", $new_credit, $user_id);
        $stmt->execute();

        $sql = "UPDATE products SET stock = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $new_stock, $product_id);
        $stmt->execute();

        $sql = "INSERT INTO purchases (user_id, product_id, quantity) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iii", $user_id, $product_id, $quantity);
        $stmt->execute();

        $conn->commit();
        echo "Purchase successful.";
    } catch (Exception $e) {
        $conn->rollback();
        echo "Purchase failed.";
    }
}
?>


