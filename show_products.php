<?php
session_start();
require 'database_connection.php'; // Ensure this file includes the database connection

// Check if the user is logged in and has the customer role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'customer') {
    echo "Access denied.";
    exit;
}

// Fetch available products
$sql = "SELECT id, name, description, price, stock FROM products WHERE stock > 0";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<h1>Available Products</h1>";
    while ($row = $result->fetch_assoc()) {
        echo "<div>";
        echo "<h2>" . $row['name'] . "</h2>";
        echo "<p>" . $row['description'] . "</p>";
        echo "<p>Price: $" . $row['price'] . "</p>";
        echo "<p>Stock: " . $row['stock'] . "</p>";
        echo '<form method="post" action="purchase.php">';
        echo '<input type="hidden" name="product_id" value="' . $row['id'] . '">';
        echo '<label for="quantity">Quantity:</label>';
        echo '<input type="number" name="quantity" min="1" required>';
        echo '<button type="submit">Purchase</button>';
        echo '</form>';
        echo "</div>";
    }
} else {
    echo "No products available.";
}

$conn->close();
?>