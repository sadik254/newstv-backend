<?php
  
    class Categories{
        // Connection
        private $conn;
        // Table
        private $db_table = "categories";
        // Columns
        public $category_id;
        public $name;
        public $created_at;
        public $updated_at;

        // Db connection
        public function __construct($db){
            $this->conn = $db;
        }
        // GET ALL
        public function getCategories(){
            $sqlQuery = "SELECT category_id, name, created_at, updated_at
            FROM " . $this->db_table . "";
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->execute();
            return $stmt;
        }
        // CREATE
        public function createCategories(){
            if($this->isAlreadyExist()){
                return false;
            }
            
            $sqlQuery = "INSERT INTO
                            ". $this->db_table ."
                        SET
                        name = :name";
                            
            $stmt = $this->conn->prepare($sqlQuery);
        
            // sanitize
            $this->name=htmlspecialchars(strip_tags($this->name));
        
            // bind data
            $stmt->bindParam(":name", $this->name);

            if($stmt->execute()){
               return true;
                  
            }
            return false;
        }

        // GET ALL with pagination
    // public function getPaginatedData($page, $limit) {
    //     $offset = ($page - 1) * $limit;

    //     $sqlQuery = "SELECT id, company_name, bgmea_reg_no, epb_reg_no, director_information, contact_person_name, contact_person_designation, contact_person_phone, contact_person_email, mailing_address, phone, fax, email, factory_address, website, date_of_establishment, factory_type, factory_priority, no_of_employees, no_of_machines, production_capacity, certifications, principal_exportable_product, annual_turnover FROM " . $this->db_table . " LIMIT :limit OFFSET :offset";

    //     $stmt = $this->conn->prepare($sqlQuery);
    //     $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    //     $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    //     $stmt->execute();

    //     return $stmt;
    // }

        // Filtering empty rows from the output
    // public function getPaginatedCategories($page, $limit) {
    //     $offset = ($page - 1) * $limit;
    
    //     $sqlQuery = "
    //         SELECT category_id, category_name, data
    //         FROM " . $this->db_table . "
    //         WHERE
    //             (category_name <> '' OR data <> '' )
    //         LIMIT :limit OFFSET :offset";
    
    //     $stmt = $this->conn->prepare($sqlQuery);
    //     $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    //     $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    //     $stmt->execute();
    
    //     return $stmt;
    // }
    
    
        // READ single
        public function getSingleCategory(){
            $sqlQuery = "SELECT
                        name,
                        created_at,
                        updated_at
                      FROM
                        ". $this->db_table ."
                    WHERE 
                       category_id = ?
                    LIMIT 0,1";
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bindParam(1, $this->category_id);
            $stmt->execute();
            $dataRow = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$dataRow) {
                return "Data not found.";
            }
            
            $this->name = $dataRow['name'];
            $this->created_at = $dataRow['created_at'];
            $this->updated_at = $dataRow['updated_at'];
        }    
        
        // Get Category By ID
        public function getCategoryById($category_id) {
            $stmt = $this->conn->prepare("SELECT * FROM ". $this->db_table ." WHERE `category_id` = ?");
            $stmt->bind_param("i", $category_id);
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
        

        public function updateCategories(){
            $sqlQuery = "UPDATE
            ". $this->db_table ."
                    SET
                    name = :name
                    WHERE
                        category_id = :category_id";

            $stmt = $this->conn->prepare($sqlQuery);
            $this->name=htmlspecialchars(strip_tags($this->name));
            $this->category_id=htmlspecialchars(strip_tags($this->category_id));

            // bind data
            $stmt->bindParam(":name", $this->name);
            $stmt->bindParam(":category_id", $this->category_id);
            

            if($stmt->execute()){
            return true;
            }
            return false;
                    }
        

        // DELETE
        function deleteCategory(){
            $sqlQuery = "DELETE FROM " . $this->db_table . " WHERE category_id = ?";
            $stmt = $this->conn->prepare($sqlQuery);
        
            $this->category_id=htmlspecialchars(strip_tags($this->category_id));
        
            $stmt->bindParam(1, $this->category_id);
        
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
                    name='".$this->name."'";
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

        public function check_data_exists($category_id) {
            $query = "SELECT category_id FROM " . $this->db_table . " WHERE category_id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $category_id);
            $stmt->execute();
            $num = $stmt->rowCount();
            return $num;
          }
        
        
    }
?>