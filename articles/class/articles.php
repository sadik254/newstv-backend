<?php
  
    class Articles{
        // Connection
        private $conn;
        // Table
        private $db_table = "articles";
        // Columns
        public $article_id;
        public $category_id;
        public $title;
        public $content;
        public $user_id;
        public $publication_date;
        public $status;
        public $created_at;
        public $updated_at;

        // Db connection
        public function __construct($db){
            $this->conn = $db;
        }
        // GET ALL
        public function getArticles(){
            $sqlQuery = "SELECT article_id, category_id, title, content, user_id, publication_date, status, created_at, updated_at
            FROM " . $this->db_table . "";
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->execute();
            return $stmt;
        }
        // CREATE
        public function createArticles(){
            if($this->isAlreadyExist()){
                return false;
            }
            
            $sqlQuery = "INSERT INTO
                            ". $this->db_table ."
                        SET
                        category_id = :category_id,
                        title = :title,
                        content = :content,
                        user_id = :user_id,
                        publication_date = :publication_date,
                        status = :status";
                            
            $stmt = $this->conn->prepare($sqlQuery);
        
            // sanitize
            $this->category_id=htmlspecialchars(strip_tags($this->category_id));
            $this->title=htmlspecialchars(strip_tags($this->title));
            $this->content=htmlspecialchars(strip_tags($this->content));
            $this->user_id=htmlspecialchars(strip_tags($this->user_id));
            $this->publication_date=htmlspecialchars(strip_tags($this->publication_date));
            $this->status=htmlspecialchars(strip_tags($this->status));
        
            // bind data
            $stmt->bindParam(":category_id", $this->category_id);
            $stmt->bindParam(":title", $this->title);
            $stmt->bindParam(":content", $this->content);
            $stmt->bindParam(":user_id", $this->user_id);
            $stmt->bindParam(":publication_date", $this->publication_date);
            $stmt->bindParam(":status", $this->status);

            if($stmt->execute()){
               return true;
                  
            }
            return false;
        }
    
    
        // READ single
        public function getSingleArticle(){
            $sqlQuery = "SELECT
                        category_id,
                        title,
                        content,
                        user_id,
                        publication_date,
                        status,
                        created_at,
                        updated_at
                      FROM
                        ". $this->db_table ."
                    WHERE 
                       article_id = ?
                    LIMIT 0,1";
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bindParam(1, $this->article_id);
            $stmt->execute();
            $dataRow = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$dataRow) {
                return "Article not found.";
            }
            
            $this->category_id = $dataRow['category_id'];
            $this->title = $dataRow['title'];
            $this->content = $dataRow['content'];
            $this->user_id = $dataRow['user_id'];
            $this->publication_date = $dataRow['publication_date'];
            $this->status = $dataRow['status'];
            $this->created_at = $dataRow['created_at'];
            $this->updated_at = $dataRow['updated_at'];
        }    
        
        // Get Articles By ID
        public function getArticleById($article_id) {
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
        

        public function updateArticles(){
            $sqlQuery = "UPDATE
            ". $this->db_table ."
                    SET
                    category_id = :category_id,
                    title = :title,
                    content = :content,
                    user_id = :user_id,
                    publication_date = :publication_date,
                    status = :status,
                    created_at = :created_at,
                    updated_at = :updated_at
                    WHERE
                        article_id = :article_id";

            $stmt = $this->conn->prepare($sqlQuery);
            $this->category_id=htmlspecialchars(strip_tags($this->category_id));
            $this->title=htmlspecialchars(strip_tags($this->title));
            $this->content=htmlspecialchars(strip_tags($this->content));
            $this->user_id=htmlspecialchars(strip_tags($this->user_id));
            $this->publication_date=htmlspecialchars(strip_tags($this->publication_date));
            $this->status=htmlspecialchars(strip_tags($this->status));
            $this->article_id=htmlspecialchars(strip_tags($this->article_id));

            // bind data
            $stmt->bindParam(":category_id", $this->category_id);
            $stmt->bindParam(":title", $this->title);
            $stmt->bindParam(":content", $this->content);
            $stmt->bindParam(":user_id", $this->user_id);
            $stmt->bindParam(":publication_date", $this->publication_date);
            $stmt->bindParam(":status", $this->status);
            $stmt->bindParam(":article_id", $this->article_id);
            

            if($stmt->execute()){
            return true;
            }
            return false;
                    }
        

        // DELETE
        function deleteArticles(){
            $sqlQuery = "DELETE FROM " . $this->db_table . " WHERE article_id = ?";
            $stmt = $this->conn->prepare($sqlQuery);
        
            $this->article_id=htmlspecialchars(strip_tags($this->article_id));
        
            $stmt->bindParam(1, $this->article_id);
        
            if($stmt->execute()){
                return true;
            }
            return false;
        }
        function isAlreadyExist(){
            $query = "SELECT *
                FROM
                    " . $this->db_table . " 
                WHERE
                    title='".$this->title."'";
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

        public function check_data_exists($article_id) {
            $query = "SELECT article_id FROM " . $this->db_table . " WHERE article_id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $article_id);
            $stmt->execute();
            $num = $stmt->rowCount();
            return $num;
          }
        
        
    }
?>