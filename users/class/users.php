<?php
  
    class Users{
        // Connection
        private $conn;
        // Table
        private $db_table = "users";
        // Columns
        public $user_id;
        public $username;
        public $email;
        public $password;
        public $role;
        public $created_at;
        public $updated_at;

        // Db connection
        public function __construct($db){
            $this->conn = $db;
        }
        // GET ALL
        public function getUsers(){
            $sqlQuery = "SELECT user_id, username, email, role, created_at, updated_at
            FROM " . $this->db_table . "";
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->execute();
            return $stmt;
        }
        // CREATE
        public function createUsers(){
            if($this->isAlreadyExist()){
                return false;
            }
            
            $sqlQuery = "INSERT INTO
                            ". $this->db_table ."
                        SET
                        username = :username,
                        email = :email,
                        password = :password,
                        role = :role";
                            
            $stmt = $this->conn->prepare($sqlQuery);
        
            // sanitize
            $this->username=htmlspecialchars(strip_tags($this->username));
            $this->email=htmlspecialchars(strip_tags($this->email));
            $this->password=htmlspecialchars(strip_tags($this->password));
            $this->role=htmlspecialchars(strip_tags($this->role));
            
            // Generate hash from plain password
            $hashed_password = password_hash($this->password, PASSWORD_DEFAULT);

            // bind data
            $stmt->bindParam(":username", $this->username);
            $stmt->bindParam(":email", $this->email);
            $stmt->bindParam(":password", $hashed_password);
            $stmt->bindParam(":role", $this->role);

            if($stmt->execute()){
               return true;
                  
            }
            return false;
        }
        

        public function login(){
            // select all query with user inputed username
            $query = "SELECT
                        `user_id`, `username`, `email`, `password`, `role`
                    FROM
                        " . $this->db_table . " 
                    WHERE
                        username=:username";
        
            // prepare query statement
            $stmt = $this->conn->prepare($query);
        
            // bind data
            $stmt->bindParam(":username", $this->username);
        
            // execute query
            $stmt->execute();
        
            // check if username exists
            if ($stmt->rowCount() == 1) {
                // get retrieved row
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
                // verify password
                if (password_verify($this->password, $row['password'])) {
                    // set properties
                    $this->user_id = $row['user_id'];
                    $this->username = $row['username'];
                    $this->email = $row['email'];
                    $this->role = $row['role'];
        
                    return true;
                }
            }
        
            return false;
        }
        
    
    
        // READ single
        public function getSingleUser(){
            $sqlQuery = "SELECT
                        username,
                        email,
                        role,
                        created_at,
                        updated_at
                      FROM
                        ". $this->db_table ."
                    WHERE 
                       user_id = ?
                    LIMIT 0,1";
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bindParam(1, $this->user_id);
            $stmt->execute();
            $dataRow = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$dataRow) {
                return "User not found.";
            }
            
            $this->username = $dataRow['username'];
            $this->email = $dataRow['email'];
            $this->role = $dataRow['role'];
            $this->created_at = $dataRow['created_at'];
            $this->updated_at = $dataRow['updated_at'];
        }    
        
        // Get Category By ID
        public function getUserById($user_id) {
            $stmt = $this->conn->prepare("SELECT * FROM ". $this->db_table ." WHERE `user_id` = ?");
            $stmt->bind_param("i", $user_id);
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
        

        public function updateUsers(){
            $sqlQuery = "UPDATE
                            ". $this->db_table ."
                        SET
                            username = :username, 
                            email = :email, 
                            role = :role";
        
            if(isset($this->password)) {
                $hashed_password = password_hash($this->password, PASSWORD_DEFAULT);
                $sqlQuery .= ", password = :password";
            }
        
            $sqlQuery .= " WHERE 
                            user_id = :user_id";
        
            $stmt = $this->conn->prepare($sqlQuery);
        
            $this->username=htmlspecialchars(strip_tags($this->username));
            $this->email=htmlspecialchars(strip_tags($this->email));
            $this->role=htmlspecialchars(strip_tags($this->role));
            $this->user_id=htmlspecialchars(strip_tags($this->user_id));
        
            // bind data
            $stmt->bindParam(":username", $this->username);
            if(isset($this->password)) {
                $stmt->bindParam(":password", $hashed_password);
            }
            $stmt->bindParam(":email", $this->email);
            $stmt->bindParam(":role", $this->role);
            $stmt->bindParam(":user_id", $this->user_id);
        
            if($stmt->execute()){
               return true;
            }
            return false;
        }
        
        

        // DELETE
        function deleteUsers(){
            $sqlQuery = "DELETE FROM " . $this->db_table . " WHERE user_id = ?";
            $stmt = $this->conn->prepare($sqlQuery);
        
            $this->user_id=htmlspecialchars(strip_tags($this->user_id));
        
            $stmt->bindParam(1, $this->user_id);
        
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
                    username='".$this->username."'";
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

        public function check_data_exists($user_id) {
            $query = "SELECT user_id FROM " . $this->db_table . " WHERE user_id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $user_id);
            $stmt->execute();
            $num = $stmt->rowCount();
            return $num;
          }
        
        
    }
?>