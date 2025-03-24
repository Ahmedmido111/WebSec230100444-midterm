<?php
session_start();
require_once '../../database_connection.php';
require_once '../../controllers/CustomerController.php';

if ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'employee') {
    echo "Access denied.";
    exit;
}

$database = new Database();
$db = $database->getConnection();

$customerController = new CustomerController($db);
$customers = $customerController->listCustomers();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['update_customer'])) {
        $customerController->updateCustomer($_POST['id'], $_POST['username'], $_POST['password']);
    } elseif (isset($_POST['delete_customer'])) {
        $customerController->deleteCustomer($_POST['id']);
    }
    header("Location: list.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Customer List</title>
</head>
<body>
    <h1>Customer List</h1>
    <?php if (count($customers) > 0): ?>
        <ul>
            <?php foreach ($customers as $customer): ?>
                <li>
                    <?php echo $customer['username']; ?>
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="id" value="<?php echo $customer['id']; ?>">
                        <input type="text" name="username" value="<?php echo $customer['username']; ?>">
                        <input type="password" name="password" placeholder="New Password">
                        <button type="submit" name="update_customer">Update</button>
                        <button type="submit" name="delete_customer">Delete</button>
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No customers found.</p>
    <?php endif; ?>
</body>
</html>
