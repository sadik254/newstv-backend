<?php

class ArticleMetadata
{
    private $conn;
    private $table_name = "article_metadata";

    public $metadata_id;
    public $metadata_content;
    public $article_id;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getMetadata()
    {
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function getSingleMetadata()
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE metadata_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->metadata_id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $this->metadata_id = $row['metadata_id'];
            $this->metadata_content = $row['metadata_content'];
            $this->article_id = $row['article_id'];
            return true;
        } else {
            return false;
        }
    }

    public function createMetadata()
    {
        $query = "INSERT INTO " . $this->table_name . " SET metadata_content = :metadata_content, article_id = :article_id";
        $stmt = $this->conn->prepare($query);
        $this->metadata_content = htmlspecialchars(strip_tags($this->metadata_content));
        $this->article_id = htmlspecialchars(strip_tags($this->article_id));
        $stmt->bindParam(':metadata_content', $this->metadata_content);
        $stmt->bindParam(':article_id', $this->article_id);
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function updateMetadata()
    {
        $query = "UPDATE " . $this->table_name . " SET metadata_content = :metadata_content, article_id = :article_id WHERE metadata_id = :metadata_id";
        $stmt = $this->conn->prepare($query);
        $this->metadata_content = htmlspecialchars(strip_tags($this->metadata_content));
        $this->article_id = htmlspecialchars(strip_tags($this->article_id));
        $this->metadata_id = htmlspecialchars(strip_tags($this->metadata_id));
        $stmt->bindParam(':metadata_content', $this->metadata_content);
        $stmt->bindParam(':article_id', $this->article_id);
        $stmt->bindParam(':metadata_id', $this->metadata_id);
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function deleteMetadata()
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE metadata_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->metadata_id);
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }
}

?>
