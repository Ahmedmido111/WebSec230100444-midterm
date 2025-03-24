<?php
session_start();
require 'database_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);
    $role_id = 1; // Default role is customer

    $sql = "INSERT INTO users (username, password, role_id) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $username, $hashed_password, $role_id);
    if ($stmt->execute()) {
        // Log in the user
        $user_id = $stmt->insert_id;
        $_SESSION['user_id'] = $user_id;
        $_SESSION['username'] = $username;
        $_SESSION['role'] = 'customer';

        // Redirect to the product listing page
        header("Location: show_products.php");
        exit;
    } else {
        echo "Registration failed.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
</head>
<body>
    <h1>Register</h1>
    <form method="post">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>
        <br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
        <br>
        <button type="submit">Register</button>
    </form>
</body>
</html>