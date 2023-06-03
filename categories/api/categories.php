<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../../config/database.php';
include_once '../class/categories.php';

$categoriesbase = new Database();
$db = $categoriesbase->getConnection();

$categories = new Categories($db);

$httpMethod = $_SERVER['REQUEST_METHOD'];
// Add this block before the switch statement
if ($httpMethod == 'OPTIONS') {
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
    http_response_code(200);
    exit();
}


switch ($httpMethod) {
    case 'GET':
        // GET all categories or single categories
        if (!empty($_GET["category_id"])) {
            $categories->category_id = $_GET["category_id"];
            $categories->getSingleCategory();
            if ($categories->name) {
                $categories_arr = array(
                    "category_id" =>  $categories->category_id,
                    "name" => $categories->name,
                    "created_at" => $categories->created_at,
                    "updated_at" => $categories->updated_at
                );
                http_response_code(200);
                echo json_encode($categories_arr);
            } else {
                http_response_code(404);
                echo json_encode(array("message" => "Category not found."));
            }
        } else {
            $stmt = $categories->getCategories();
            $categories_arr = array();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                $categories_item = array(
                    "category_id" => $category_id,
                    "name" => $name,
                    "created_at" => $created_at,
                    "updated_at" => $updated_at
                );
                array_push($categories_arr, $categories_item);
            }
            http_response_code(200);
            echo json_encode($categories_arr);
        }
        break;    
    
    case 'POST':
        // CREATE 
        $data = json_decode(file_get_contents("php://input"));
        $categories->name = $data->name;

        if ($categories->createCategories()) {
            http_response_code(201);
            echo json_encode(array("message" => "category was created."));
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "Unable to create category."));
        }
        break;
    case 'PUT':
        // UPDATE 
        $data = json_decode(file_get_contents("php://input"));
        $categories->category_id = $data->category_id;
        $categories->name = $data->name;

        if ($categories->updateCategories()) {
            http_response_code(200);
            echo json_encode(array("message" => "category updated."));
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "Unable to update category."));
        }
        break;
    case 'DELETE':
        // DELETE Data
        $data = json_decode(file_get_contents("php://input"));
        $category_id = $data->category_id;
        $categories->category_id = $category_id;

        // Check if the Data exists
        if ($categories->check_data_exists($category_id)) {
            // Data exists, delete it
            if ($categories->deleteCategory()) {
                http_response_code(200);
                echo json_encode(array("message" => "category deleted successfully."));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Unable to delete category."));
            }
        } else {
            // Data does not exist
            http_response_code(404);
            echo json_encode(array("message" => "category does not exist."));
        }
        break;

    default:
        // Invalid request method
        http_response_code(405);
        echo json_encode(array("message" => "Invalid request method."));
        break;
}



?>