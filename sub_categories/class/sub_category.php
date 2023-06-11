<?php

class SubCategory {
    private $conn;
    private $table_name = "sub_categories";

    public $id;
    public $sub_category_name;
    public $category_id;
    public $created_at;
    public $updated_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Get all sub categories
    public function getSubCategories() {
        $query = "SELECT * FROM " . $this->table_name;

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    // Get single sub category
    public function getSingleSubCategory() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->id = $row['id'];
        $this->sub_category_name = $row['sub_category_name'];
        $this->category_id = $row['category_id'];
        $this->created_at = $row['created_at'];
        $this->updated_at = $row['updated_at'];
    }

    // Get subcategories by category_id
    public function getSubCategoriesByCategoryId($category_id) {
    $query = "SELECT * FROM " . $this->table_name . " WHERE category_id = :category_id";

    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(":category_id", $category_id);
    $stmt->execute();

    return $stmt;
}


    // Create sub category
    public function createSubCategory() {
        $query = "INSERT INTO " . $this->table_name . " (sub_category_name, category_id) VALUES (:sub_category_name, :category_id)";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":sub_category_name", $this->sub_category_name);
        $stmt->bindParam(":category_id", $this->category_id);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Update sub category
    public function updateSubCategory() {
        $query = "UPDATE " . $this->table_name . " SET sub_category_name = :sub_category_name, category_id = :category_id, updated_at = CURRENT_TIMESTAMP WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":sub_category_name", $this->sub_category_name);
        $stmt->bindParam(":category_id", $this->category_id);
        $stmt->bindParam(":id", $this->id);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Delete sub category
    public function deleteSubCategory() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }
}
?>
