<?php
class PollOption
{
    private $conn;
    private $db_table = "poll_options";

    public $id;
    public $poll_id;
    public $option_text;
    public $vote_count;
    public $percentage;
    public $created_at;
    public $updated_at;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getOptionsByPoll()
    {
        $query = "SELECT * FROM " . $this->db_table . " WHERE poll_id = :poll_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":poll_id", $this->poll_id);
        $stmt->execute();

        return $stmt;
    }

    public function createOption()
    {
        $query = "INSERT INTO " . $this->db_table . "
            SET
                poll_id = :poll_id,
                option_text = :option_text,
                vote_count = :vote_count,
                percentage = :percentage";

        $stmt = $this->conn->prepare($query);

        $this->poll_id = htmlspecialchars(strip_tags($this->poll_id));
        $this->option_text = htmlspecialchars(strip_tags($this->option_text));
        $this->vote_count = 0; // Assuming the count will be zero initially
        $this->percentage = 0; // Assuming a new option will have zero percentage initially.

        $stmt->bindParam(":poll_id", $this->poll_id);
        $stmt->bindParam(":option_text", $this->option_text);
        $stmt->bindParam(":vote_count", $this->vote_count);
        $stmt->bindParam(":percentage", $this->percentage);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    public function updateOption()
    {
        $query = "UPDATE " . $this->db_table . "
            SET
                option_text = :option_text,
                vote_count = :vote_count,
                percentage = :percentage
            WHERE
                id = :id";

        $stmt = $this->conn->prepare($query);

        $this->id = htmlspecialchars(strip_tags($this->id));
        $this->option_text = htmlspecialchars(strip_tags($this->option_text));
        $this->vote_count = htmlspecialchars(strip_tags($this->vote_count));
        $this->percentage = htmlspecialchars(strip_tags($this->percentage));

        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":option_text", $this->option_text);
        $stmt->bindParam(":vote_count", $this->vote_count);
        $stmt->bindParam(":percentage", $this->percentage);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    public function deleteOption()
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