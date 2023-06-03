<?php

class Tags
{
    // Database connection and table name
    private $conn;
    private $db_table = "tags";

    // Object properties
    public $tag_id;
    public $name;
    public $created_at;
    public $updated_at;

    // Constructor with database connection
    public function __construct($db)
    {
        $this->conn = $db;
    }

    // GET all tags
    function getTags()
    {
        $sqlQuery = "SELECT * FROM " . $this->db_table;
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->execute();
        return $stmt;
    }

    // CREATE tag
    function createTag()
    {
        if ($this->isAlreadyExist()) {
            return false;
        }

        $sqlQuery = "INSERT INTO " . $this->db_table . " SET
            name = :name";

        $stmt = $this->conn->prepare($sqlQuery);

        // Sanitize input values
        $this->name = htmlspecialchars(strip_tags($this->name));

        // Bind input values
        $stmt->bindParam(":name", $this->name);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // READ single tag
    function getSingleTag()
    {
        $sqlQuery = "SELECT * FROM " . $this->db_table . " WHERE tag_id = ?";
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(1, $this->tag_id);
        $stmt->execute();
        return $stmt;
    }

    // get tag by ID 
    public function getTagbyId($tag_id) {
        $stmt = $this->conn->prepare("SELECT * FROM ". $this->db_table ." WHERE `tag_id` = ?");
        $stmt->bind_param("i", $tag_id);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result->num_rows == 0) {
            return null;
        }
    
        $dataar = $result->fetch_assoc();
        if (is_array($dataar)) {
            return $dataar;
        } else {
            return null;
        }
    }

    // UPDATE tag
    function updateTag()
    {
        $sqlQuery = "UPDATE " . $this->db_table . " SET
            name = :name,
            updated_at = CURRENT_TIMESTAMP
            WHERE tag_id = :tag_id";

        $stmt = $this->conn->prepare($sqlQuery);

        // Sanitize input values
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->tag_id = htmlspecialchars(strip_tags($this->tag_id));

        // Bind input values
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":tag_id", $this->tag_id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // DELETE tag
    function deleteTag()
    {
        $sqlQuery = "DELETE FROM " . $this->db_table . " WHERE tag_id = ?";
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(1, $this->tag_id);
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Check if tag already exists
    function isAlreadyExist()
    {
        $query = "SELECT * FROM " . $this->db_table . " WHERE name = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->name);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    }

    // Check if tag exists by tag_id
    function checkTagExists($tag_id)
    {
        $query = "SELECT tag_id FROM " . $this->db_table . " WHERE tag_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $tag_id);
        $stmt->execute();
        $num = $stmt->rowCount();
        return $num;
    }
}
?>
