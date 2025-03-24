<?php
session_start();
require 'database_connection.php'; // Ensure this file includes the database connection

if (!isset($_SESSION['user_id'])) {
    echo "Access denied.";
    exit;
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT amount FROM credits WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$credit = $result->fetch_assoc()['amount'];

echo "<p>Credit Amount: $" . $credit . "</p>";
echo '<a href="show_products.php">View Products</a>';
?>

