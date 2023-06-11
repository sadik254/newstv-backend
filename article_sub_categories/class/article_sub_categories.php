<?php

class ArticleSubCategory {
    private $conn;
    private $table_name = "article_sub_categories";

    public $article_id;
    public $sub_category_id;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Create article-subcategory relationship
    public function createArticleSubCategory() {
        $query = "INSERT INTO " . $this->table_name . " (article_id, sub_category_id) VALUES (:article_id, :sub_category_id)";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":article_id", $this->article_id);
        $stmt->bindParam(":sub_category_id", $this->sub_category_id);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function getSingleArticleSubCategory($articleId, $subCategoryId)
{
    // Query to fetch a single article-subcategory relationship
    $query = "SELECT article_id, sub_category_id FROM article_sub_categories WHERE article_id = :article_id AND sub_category_id = :sub_category_id";

    // Prepare the query statement
    $stmt = $this->conn->prepare($query);

    // Bind the parameters
    $stmt->bindParam(":article_id", $articleId, PDO::PARAM_INT);
    $stmt->bindParam(":sub_category_id", $subCategoryId, PDO::PARAM_INT);

    // Execute the query
    $stmt->execute();

    // Fetch the row
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    return $row;
}


    public function getArticleSubCategories()
{
    // Query to fetch all article-subcategory relationships
    $query = "SELECT article_id, sub_category_id FROM article_sub_categories";

    // Prepare the query statement
    $stmt = $this->conn->prepare($query);

    // Execute the query
    $stmt->execute();

    return $stmt;
}


    // Delete article-subcategory relationship
    public function deleteArticleSubCategory() {
        $query = "DELETE FROM " . $this->table_name . " WHERE article_id = :article_id AND sub_category_id = :sub_category_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":article_id", $this->article_id);
        $stmt->bindParam(":sub_category_id", $this->sub_category_id);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }
}
?>
