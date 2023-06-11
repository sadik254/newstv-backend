<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE");
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
    header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
    http_response_code(200);
    exit();
}

switch ($httpMethod) {
    case 'GET':
        // GET all sub categories or single sub category
        if (!empty($_GET["id"])) {
            $subCategory->id = $_GET["id"];
            $subCategory->getSingleSubCategory();
            if ($subCategory->id) {
                $subCategory_arr = array(
                    "id" => $subCategory->id,
                    "sub_category_name" => $subCategory->sub_category_name,
                    "category_id" => $subCategory->category_id,
                    "created_at" => $subCategory->created_at,
                    "updated_at" => $subCategory->updated_at
                );
                http_response_code(200);
                echo json_encode($subCategory_arr);
            } else {
                http_response_code(404);
                echo json_encode(array("message" => "Sub category not found."));
            }
        } else {
            $stmt = $subCategory->getSubCategories();
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
        }
        break;

    case 'POST':
        // CREATE sub category
        $data = json_decode(file_get_contents("php://input"));
        $subCategory->sub_category_name = $data->sub_category_name;
        $subCategory->category_id = $data->category_id;

        if ($subCategory->createSubCategory()) {
            http_response_code(201);
            echo json_encode(array("message" => "Sub category was created."));
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "Unable to create sub category."));
        }
        break;

    case 'PUT':
        // UPDATE sub category
        $data = json_decode(file_get_contents("php://input"));
        $subCategory->id = $data->id;
        $subCategory->sub_category_name = $data->sub_category_name;
        $subCategory->category_id = $data->category_id;

        if ($subCategory->updateSubCategory()) {
            http_response_code(200);
            echo json_encode(array("message" => "Sub category updated."));
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "Unable to update sub category."));
        }
        break;

    case 'DELETE':
        // DELETE sub category
        $data = json_decode(file_get_contents("php://input"));
        $subCategory->id = $data->id;

        if ($subCategory->deleteSubCategory()) {
            http_response_code(200);
            echo json_encode(array("message" => "Sub category deleted successfully."));
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "Unable to delete sub category."));
        }
        break;

    default:
        // Invalid request method
        http_response_code(405);
        echo json_encode(array("message" => "Invalid request method."));
        break;
}
?>
