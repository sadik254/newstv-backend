<?php

class ArticleCategories {
    private $conn;
    private $db_table = "article_categories";

    public $article_id;
    public $category_id;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function createArticleCategory() {
        $query = "INSERT INTO " . $this->db_table . " SET article_id=:article_id, category_id=:category_id";
        
        $stmt = $this->conn->prepare($query);
        
        $this->article_id = htmlspecialchars(strip_tags($this->article_id));
        $this->category_id = htmlspecialchars(strip_tags($this->category_id));
        
        $stmt->bindParam(":article_id", $this->article_id);
        $stmt->bindParam(":category_id", $this->category_id);
        
        if ($stmt->execute()) {
            return true;
        }
        
        return false;
    }

    public function getArticleCategories() {
        $query = "SELECT * FROM " . $this->db_table;
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt;
    }

    public function getSingleArticleCategory() {
        $query = "SELECT * FROM " . $this->db_table . " WHERE article_id=:article_id AND category_id=:category_id";
        
        $stmt = $this->conn->prepare($query);
        
        $this->article_id = htmlspecialchars(strip_tags($this->article_id));
        $this->category_id = htmlspecialchars(strip_tags($this->category_id));
        
        $stmt->bindParam(":article_id", $this->article_id);
        $stmt->bindParam(":category_id", $this->category_id);
        
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row) {
            $this->article_id = $row['article_id'];
            $this->category_id = $row['category_id'];
            return true;
        }
        
        return false;
    }

    // get article categories by ID 
    public function getArticleTagsById($article_id) {
        $stmt = $this->conn->prepare("SELECT * FROM ". $this->db_table ." WHERE `article_id` = ?");
        $stmt->bind_param("i", $article_id);
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

    public function updateArticleCategory() {
        $query = "UPDATE " . $this->db_table . " SET category_id=:category_id WHERE article_id=:article_id";
        
        $stmt = $this->conn->prepare($query);
        
        $this->article_id = htmlspecialchars(strip_tags($this->article_id));
        $this->category_id = htmlspecialchars(strip_tags($this->category_id));
        
        $stmt->bindParam(":article_id", $this->article_id);
        $stmt->bindParam(":category_id", $this->category_id);
        
        if ($stmt->execute()) {
            return true;
        }
        
        return false;
    }

    public function deleteArticleCategory() {
        $query = "DELETE FROM " . $this->db_table . " WHERE article_id=:article_id AND category_id=:category_id";
        
        $stmt = $this->conn->prepare($query);
        
        $this->article_id = htmlspecialchars(strip_tags($this->article_id));
        $this->category_id = htmlspecialchars(strip_tags($this->category_id));
        
        $stmt->bindParam(":article_id", $this->article_id);
        $stmt->bindParam(":category_id", $this->category_id);
        
        if ($stmt->execute()) {
            return true;
        }
        
        return false;
    }


    public function checkArticleCategoryExists($article_id, $category_id) {
        $query = "SELECT * FROM " . $this->db_table . " WHERE article_id=:article_id AND category_id=:category_id";
        
        $stmt = $this->conn->prepare($query);
        
        $article_id = htmlspecialchars(strip_tags($article_id));
        $category_id = htmlspecialchars(strip_tags($category_id));
        
        $stmt->bindParam(":article_id", $article_id);
        $stmt->bindParam(":category_id", $category_id);
        
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            return true;
        }
        
        return false;
    }
}
?>
