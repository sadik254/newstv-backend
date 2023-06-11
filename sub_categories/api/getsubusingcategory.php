<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../../config/database.php';
include_once '../class/sub_category.php';

$database = new Database();
$db = $database->getConnection();

$subCategory = new SubCategory($db);

$httpMethod = $_SERVER['REQUEST_METHOD'];

// Add this block before the switch statement
if ($httpMethod == 'OPTIONS') {
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: GET");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
    http_response_code(200);
    exit();
}

switch ($httpMethod) {
    case 'GET':
        // GET subcategories by category_id
        if (!empty($_GET["category_id"])) {
            $category_id = $_GET["category_id"];
            $stmt = $subCategory->getSubCategoriesByCategoryId($category_id);
            $subCategories_arr = array();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                $subCategory_item = array(
                    "id" => $id,
                    "sub_category_name" => $sub_category_name,
                    "category_id" => $category_id,
                    "created_at" => $created_at,
                    "updated_at" => $updated_at
                );
                array_push($subCategories_arr, $subCategory_item);
            }
            http_response_code(200);
            echo json_encode($subCategories_arr);
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Missing category_id parameter."));
        }
        break;

    default:
        // Invalid request method
        http_response_code(405);
        echo json_encode(array("message" => "Invalid request method."));
        break;
}

?>
