<?php
require_once '../models/Customer.php';

class CustomerController {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function createCustomer($username, $password) {
        $customer = new Customer($this->conn);
        $customer->username = $username;
        $customer->password = password_hash($password, PASSWORD_BCRYPT);
        $customer->role_id = 1; // Customer role

        if ($customer->create()) {
            echo "Customer created successfully.";
        } else {
            echo "Unable to create customer.";
        }
    }

    public function listCustomers() {
        $customer = new Customer($this->conn);
        $stmt = $customer->read();
        $num = $stmt->rowCount();

        if ($num > 0) {
            $customers_arr = array();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                $customer_item = array(
                    "id" => $id,
                    "username" => $username,
                    "role_id" => $role_id
                );
                array_push($customers_arr, $customer_item);
            }
            return $customers_arr;
        } else {
            return array();
        }
    }

    public function updateCustomer($id, $username, $password) {
        $customer = new Customer($this->conn);
        $password = password_hash($password, PASSWORD_BCRYPT);

        if ($customer->update($id, $username, $password)) {
            echo "Customer updated successfully.";
        } else {
            echo "Unable to update customer.";
        }
    }

    public function deleteCustomer($id) {
        $customer = new Customer($this->conn);

        if ($customer->delete($id)) {
            echo "Customer deleted successfully.";
        } else {
            echo "Unable to delete customer.";
        }
    }
}
?>
