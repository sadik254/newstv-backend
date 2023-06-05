<?php

class Images
{
    // Database connection and table name
    private $conn;
    private $db_table = "images";

    // Object properties
    public $image_id;
    public $article_id;
    public $file_name;
    public $file_path;
    public $created_at;
    public $updated_at;

    // Constructor with database connection
    public function __construct($db)
    {
        $this->conn = $db;
    }

    // GET all images
    function getImages()
    {
        $sqlQuery = "SELECT * FROM " . $this->db_table;
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->execute();
        return $stmt;
    }

    // CREATE image
    function createImage()
    {

        // if($this->isAlreadyExist()){
        //     return false;
        // }

        $sqlQuery = "INSERT INTO " . $this->db_table . " SET
            article_id = :article_id,
            file_name = :file_name,
            file_path = :file_path";

        $stmt = $this->conn->prepare($sqlQuery);

        // Sanitize input values
        $this->article_id = htmlspecialchars(strip_tags($this->article_id));
        $this->file_name = htmlspecialchars(strip_tags($this->file_name));
        $this->file_path = htmlspecialchars(strip_tags($this->file_path));

        // Bind input values
        $stmt->bindParam(":article_id", $this->article_id);
        $stmt->bindParam(":file_name", $this->file_name);
        $stmt->bindParam(":file_path", $this->file_path);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // READ single
    public function getSingleImage() {
        $sqlQuery = "SELECT image_id, article_id, file_name, file_path, created_at, updated_at
                      FROM " . $this->db_table . "
                    WHERE 
                      image_id = ?
                    LIMIT 0,1";
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(1, $this->image_id);
        $stmt->execute();
        $dataRow = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$dataRow) {
            return "Image not found.";
        }

        $this->article_id = $dataRow['article_id'];
        $this->file_name = $dataRow['file_name'];
        $this->file_path = $dataRow['file_path'];
        $this->created_at = $dataRow['created_at'];
        $this->updated_at = $dataRow['updated_at'];
    }

    // get Image by ID 
    public function getImageById($image_id) {
        $stmt = $this->conn->prepare("SELECT * FROM ". $this->db_table ." WHERE `image_id` = ?");
        $stmt->bind_param("i", $image_id);
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

    // UPDATE image
    function updateImage()
    {
        $sqlQuery = "UPDATE " . $this->db_table . " SET
            article_id = :article_id,
            file_name = :file_name,
            file_path = :file_path
            WHERE image_id = :image_id";

        $stmt = $this->conn->prepare($sqlQuery);

        // Sanitize input values
        $this->article_id = htmlspecialchars(strip_tags($this->article_id));
        $this->file_name = htmlspecialchars(strip_tags($this->file_name));
        $this->file_path = htmlspecialchars(strip_tags($this->file_path));
        $this->image_id = htmlspecialchars(strip_tags($this->image_id));

        // Bind input values
        $stmt->bindParam(":article_id", $this->article_id);
        $stmt->bindParam(":file_name", $this->file_name);
        $stmt->bindParam(":file_path", $this->file_path);
        $stmt->bindParam(":image_id", $this->image_id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // DELETE image
    function deleteImage()
    {
        $sqlQuery = "DELETE FROM " . $this->db_table . " WHERE image_id = ?";
        $stmt = $this->conn->prepare($sqlQuery);

        $this->image_id = htmlspecialchars(strip_tags($this->image_id));

        $stmt->bindParam(1, $this->image_id);

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

    public function check_data_exists($image_id) {
        $query = "SELECT image_id FROM " . $this->db_table . " WHERE image_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $image_id);
        $stmt->execute();
        $num = $stmt->rowCount();
        return $num;
      }
}
?>
