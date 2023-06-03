<?php

class ArticleComment
{
    private $conn;
    private $db_table = "article_comment";

    public $comment_id;
    public $comment_content;
    public $article_id;
    public $created_at;
    public $updated_at;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getComments()
    {
        $query = "SELECT * FROM " . $this->db_table;

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    public function getSingleComment()
    {
        $query = "SELECT * FROM " . $this->db_table . " WHERE comment_id = :comment_id LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":comment_id", $this->comment_id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->comment_id = $row['comment_id'];
            $this->comment_content = $row['comment_content'];
            $this->article_id = $row['article_id'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];
            return true;
        }

        return false;
    }

    public function createComment()
    {
        $query = "INSERT INTO " . $this->db_table . "
            SET
                comment_content = :comment_content,
                article_id = :article_id";

        $stmt = $this->conn->prepare($query);

        $this->comment_content = htmlspecialchars(strip_tags($this->comment_content));
        $this->article_id = htmlspecialchars(strip_tags($this->article_id));

        $stmt->bindParam(":comment_content", $this->comment_content);
        $stmt->bindParam(":article_id", $this->article_id);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    public function updateComment()
    {
        $query = "UPDATE " . $this->db_table . "
            SET
                comment_content = :comment_content,
                article_id = :article_id
            WHERE
                comment_id = :comment_id";

        $stmt = $this->conn->prepare($query);

        $this->comment_id = htmlspecialchars(strip_tags($this->comment_id));
        $this->comment_content = htmlspecialchars(strip_tags($this->comment_content));
        $this->article_id = htmlspecialchars(strip_tags($this->article_id));

        $stmt->bindParam(":comment_id", $this->comment_id);
        $stmt->bindParam(":comment_content", $this->comment_content);
        $stmt->bindParam(":article_id", $this->article_id);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    public function deleteComment()
    {
        $query = "DELETE FROM " . $this->db_table . " WHERE comment_id = :comment_id";

        $stmt = $this->conn->prepare($query);

        $this->comment_id = htmlspecialchars(strip_tags($this->comment_id));

        $stmt->bindParam(":comment_id", $this->comment_id);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }
}

?>
