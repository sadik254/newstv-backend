<?php
class Videos {
    // Connection
    private $conn;
    // Table
    private $db_table = "videos";
    // Columns
    public $video_id;
    public $video_url;
    public $video_title;
    public $video_meta;
    public $created_at;
    public $updated_at;

    // Db connection
    public function __construct($db) {
        $this->conn = $db;
    }

    // GET ALL
    public function getVideos() {
        $sqlQuery = "SELECT video_id, video_url, video_title, video_meta, created_at, updated_at
        FROM " . $this->db_table . "";
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->execute();
        return $stmt;
    }

    // CREATE
    public function createVideo() {

        // if($this->isAlreadyExist()){
        //     return false;
        // }

        $sqlQuery = "INSERT INTO
                        " . $this->db_table . "
                    SET
                        video_url = :video_url,
                        video_title = :video_title,
                        video_meta = :video_meta";

        $stmt = $this->conn->prepare($sqlQuery);

        // sanitize
        $this->video_url = htmlspecialchars(strip_tags($this->video_url));
        $this->video_title = htmlspecialchars(strip_tags($this->video_title));
        $this->video_meta = htmlspecialchars(strip_tags($this->video_meta));

        // bind data
        $stmt->bindParam(":video_url", $this->video_url);
        $stmt->bindParam(":video_title", $this->video_title);
        $stmt->bindParam(":video_meta", $this->video_meta);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // READ single
    public function getSingleVideo() {
        $sqlQuery = "SELECT video_url, video_title, video_meta, created_at, updated_at
                      FROM " . $this->db_table . "
                    WHERE 
                      video_id = ?
                    LIMIT 0,1";
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(1, $this->video_id);
        $stmt->execute();
        $dataRow = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$dataRow) {
            return "Video not found.";
        }

        $this->video_url = $dataRow['video_url'];
        $this->video_title = $dataRow['video_title'];
        $this->video_meta = $dataRow['video_meta'];
        $this->created_at = $dataRow['created_at'];
        $this->updated_at = $dataRow['updated_at'];
    }

    // get videos by ID 
    public function getVideoById($video_id) {
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

    // UPDATE
    public function updateVideo() {
        $sqlQuery = "UPDATE
                        " . $this->db_table . "
                    SET
                        video_url = :video_url,
                        video_title = :video_title,
                        video_meta = :video_meta
                    WHERE
                        video_id = :video_id";

        $stmt = $this->conn->prepare($sqlQuery);

        // sanitize
        $this->video_url = htmlspecialchars(strip_tags($this->video_url));
        $this->video_title = htmlspecialchars(strip_tags($this->video_title));
        $this->video_meta = htmlspecialchars(strip_tags($this->video_meta));
        $this->video_id = htmlspecialchars(strip_tags($this->video_id));

        // bind data
        $stmt->bindParam(":video_url", $this->video_url);
        $stmt->bindParam(":video_title", $this->video_title);
        $stmt->bindParam(":video_meta", $this->video_meta);
        $stmt->bindParam(":video_id", $this->video_id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // DELETE
    public function deleteVideo() {
        $sqlQuery = "DELETE FROM " . $this->db_table . " WHERE video_id = ?";
        $stmt = $this->conn->prepare($sqlQuery);

        $this->video_id = htmlspecialchars(strip_tags($this->video_id));

        $stmt->bindParam(1, $this->video_id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    function isAlreadyExist(){
        $query = "SELECT *
            FROM
                " . $this->db_table . " 
            WHERE
                video_url='".$this->video_url."'";
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

    public function check_data_exists($video_id) {
        $query = "SELECT video_id FROM " . $this->db_table . " WHERE video_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $video_id);
        $stmt->execute();
        $num = $stmt->rowCount();
        return $num;
      }
    
}
?>