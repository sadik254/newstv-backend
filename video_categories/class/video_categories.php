<?php

class VideoCategories {
    private $conn;
    private $db_table = "video_categories";

    public $video_id;
    public $category_id;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getVideoCategories() {
        $query = "SELECT * FROM " . $this->db_table;

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    public function getSingleVideoCategory() {
        $query = "SELECT * FROM " . $this->db_table . " WHERE video_id = :video_id AND category_id = :category_id";

        $stmt = $this->conn->prepare($query);

        $this->video_id = htmlspecialchars(strip_tags($this->video_id));
        $this->category_id = htmlspecialchars(strip_tags($this->category_id));

        $stmt->bindParam(":video_id", $this->video_id);
        $stmt->bindParam(":category_id", $this->category_id);

        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return true;
        }

        return false;
    }

    // get video categories by ID 
    public function getArticleTagsById($video_id) {
        $stmt = $this->conn->prepare("SELECT * FROM ". $this->db_table ." WHERE `video_id` = ?");
        $stmt->bind_param("i", $video_id);
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

    public function createVideoCategory() {
        $query = "INSERT INTO " . $this->db_table . " (video_id, category_id) VALUES (:video_id, :category_id)";

        $stmt = $this->conn->prepare($query);

        $this->video_id = htmlspecialchars(strip_tags($this->video_id));
        $this->category_id = htmlspecialchars(strip_tags($this->category_id));

        $stmt->bindParam(":video_id", $this->video_id);
        $stmt->bindParam(":category_id", $this->category_id);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    public function updateVideoCategory() {
        $query = "UPDATE " . $this->db_table . " SET category_id = :category_id WHERE video_id = :video_id";

        $stmt = $this->conn->prepare($query);

        $this->video_id = htmlspecialchars(strip_tags($this->video_id));
        $this->category_id = htmlspecialchars(strip_tags($this->category_id));

        $stmt->bindParam(":video_id", $this->video_id);
        $stmt->bindParam(":category_id", $this->category_id);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    public function deleteVideoCategory() {
        $query = "DELETE FROM " . $this->db_table . " WHERE video_id = :video_id AND category_id = :category_id";

        $stmt = $this->conn->prepare($query);

        $this->video_id = htmlspecialchars(strip_tags($this->video_id));
        $this->category_id = htmlspecialchars(strip_tags($this->category_id));

        $stmt->bindParam(":video_id", $this->video_id);
        $stmt->bindParam(":category_id", $this->category_id);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    public function checkVideoCategoryExists($video_id, $category_id) {
        $query = "SELECT * FROM " . $this->db_table . " WHERE video_id = :video_id AND category_id = :category_id";

        $stmt = $this->conn->prepare($query);

        $video_id = htmlspecialchars(strip_tags($video_id));
        $category_id = htmlspecialchars(strip_tags($category_id));

        $stmt->bindParam(":video_id", $video_id);
        $stmt->bindParam(":category_id", $category_id);

        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return true;
        }

        return false;
    }
}
