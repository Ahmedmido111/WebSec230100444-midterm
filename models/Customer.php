<?php
class Customer {
    private $conn;
    private $table_name = "users";

    public $id;
    public $username;
    public $password;
    public $role_id;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " SET username=:username, password=:password, role_id=:role_id";
        $stmt = $this->conn->prepare($query);

        $this->username = htmlspecialchars(strip_tags($this->username));
        $this->password = htmlspecialchars(strip_tags($this->password));
        $this->role_id = htmlspecialchars(strip_tags($this->role_id));

        $stmt->bindParam(":username", $this->username);
        $stmt->bindParam(":password", $this->password);
        $stmt->bindParam(":role_id", $this->role_id);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    public function read() {
        $query = "SELECT id, username, role_id FROM " . $this->table_name . " WHERE role_id = 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    public function update($id, $username, $password) {
        $query = "UPDATE " . $this->table_name . " SET username = :username, password = :password WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $username = htmlspecialchars(strip_tags($username));
        $password = htmlspecialchars(strip_tags($password));

        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':id', $id);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':id', $id);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }
}
?>
