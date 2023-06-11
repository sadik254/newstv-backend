<?php
class Poll
{
    private $conn;
    private $db_table = "polls";

    public $id;
    public $question;
    public $total_votes;
    public $publication_date;
    public $image;
    public $created_at;
    public $updated_at;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getPolls()
    {
        $query = "SELECT * FROM " . $this->db_table;

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    public function getSinglePoll()
    {
        $query = "SELECT * FROM " . $this->db_table . " WHERE id = :id LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->id = $row['id'];
            $this->question = $row['question'];
            $this->total_votes = $row['total_votes'];
            $this->publication_date = $row['publication_date'];
            $this->image = $row['image'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];
            return true;
        }

        return false;
    }

    public function createPoll()
    {
        $query = "INSERT INTO " . $this->db_table . "
            SET
                question = :question,
                total_votes = :total_votes,
                publication_date = :publication_date,
                image = :image";

        $stmt = $this->conn->prepare($query);

        $this->question = htmlspecialchars(strip_tags($this->question));
        $this->total_votes = 0; // Assuming a new poll will have zero votes initially.
        $this->publication_date = htmlspecialchars(strip_tags($this->publication_date));

        $stmt->bindParam(":question", $this->question);
        $stmt->bindParam(":total_votes", $this->total_votes);
        $stmt->bindParam(":publication_date", $this->publication_date);
        $stmt->bindParam(":image", $this->image);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    public function updatePoll()
    {
        $query = "UPDATE " . $this->db_table . "
            SET
                question = :question,
                total_votes = :total_votes,
                publication_date = :publication_date,
                image = :image
            WHERE
                id = :id";

        $stmt = $this->conn->prepare($query);

        $this->id = htmlspecialchars(strip_tags($this->id));
        $this->question = htmlspecialchars(strip_tags($this->question));
        $this->total_votes = htmlspecialchars(strip_tags($this->total_votes));
        $this->publication_date = htmlspecialchars(strip_tags($this->publication_date));
        $this->image = htmlspecialchars(strip_tags($this->image));

        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":question", $this->question);
        $stmt->bindParam(":total_votes", $this->total_votes);
        $stmt->bindParam(":publication_date", $this->publication_date);
        $stmt->bindParam(":image", $this->image);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    public function deletePoll()
    {
        $query = "DELETE FROM " . $this->db_table . " WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(":id", $this->id);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }
}
?>
