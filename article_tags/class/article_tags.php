<?php

class ArticleTags
{
    private $conn;
    private $db_table = 'article_tags';

    public $article_tag_id;
    public $article_id;
    public $tag_id;
    public $created_at;
    public $updated_at;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Get all article tags
    public function getArticleTags()
    {
        $query = "SELECT * FROM " . $this->db_table;

        $stmt = $this->conn->prepare($query);

        $stmt->execute();

        return $stmt;
    }

    // Get single article tag
    public function getSingleArticleTag()
    {
        $query = "SELECT * FROM " . $this->db_table . " WHERE article_tag_id = :article_tag_id LIMIT 1";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':article_tag_id', $this->article_tag_id);

        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->article_tag_id = $row['article_tag_id'];
        $this->article_id = $row['article_id'];
        $this->tag_id = $row['tag_id'];
        $this->created_at = $row['created_at'];
        $this->updated_at = $row['updated_at'];
    }

    // get articletags by ID 
    public function getArticleTagsById($article_tag_id) {
        $stmt = $this->conn->prepare("SELECT * FROM ". $this->db_table ." WHERE `article_tag_id` = ?");
        $stmt->bind_param("i", $article_tag_id);
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

    // Create article tag
    public function createArticleTag()
    {

        if($this->isAlreadyExist()){
            return false;
        }

        $query = "INSERT INTO " . $this->db_table . " (article_id, tag_id) VALUES (:article_id, :tag_id)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':article_id', $this->article_id);
        $stmt->bindParam(':tag_id', $this->tag_id);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Update article tag
    public function updateArticleTag()
    {
        $query = "UPDATE " . $this->db_table . " SET article_id = :article_id, tag_id = :tag_id, updated_at = CURRENT_TIMESTAMP WHERE article_tag_id = :article_tag_id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':article_id', $this->article_id);
        $stmt->bindParam(':tag_id', $this->tag_id);
        $stmt->bindParam(':article_tag_id', $this->article_tag_id);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Delete article tag
    public function deleteArticleTag()
    {
        $query = "DELETE FROM " . $this->db_table . " WHERE article_tag_id = :article_tag_id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':article_tag_id', $this->article_tag_id);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // is already exist
    function isAlreadyExist(){
        $query = "SELECT *
            FROM
                " . $this->db_table . " 
            WHERE
            article_id='".$this->article_id."'";
        // prepare query statement
        $stmt = $this->conn->prepare($query);
        // execute query
        $stmt->execute();
        if($stmt->rowCount() > 0){
            return true;
        }
        else{
            return false;
        }
    }

    // Check if article tag exists
    public function checkArticleTagExists($article_tag_id)
    {
        $query = "SELECT * FROM " . $this->db_table . " WHERE article_tag_id = :article_tag_id LIMIT 1";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':article_tag_id', $article_tag_id);

        $stmt->execute();

        $num = $stmt->rowCount();

        if ($num > 0) {
            return true;
        } else {
            return false;
        }
    }
}
?>
