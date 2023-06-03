<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../../config/database.php';
include_once '../class/images.php';

$imagesbase = new Database();
$db = $imagesbase->getConnection();

$images = new Images($db);

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
        // GET all images or single image
        if (!empty($_GET["image_id"])) {
            $images->image_id = $_GET["image_id"];
            $images->getSingleImage();
            if ($images->article_id) {
                $images_arr = array(
                    "image_id" =>  $images->image_id,
                    "article_id" => $images->article_id,
                    "file_name" => $images->file_name,
                    "file_path" => $images->file_path,
                    "created_at" => $images->created_at,
                    "updated_at" => $images->updated_at
                );
                http_response_code(200);
                echo json_encode($images_arr);
            } else {
                http_response_code(404);
                echo json_encode(array("message" => "Image not found."));
            }
        } else {
            $stmt = $images->getImages();
            $images_arr = array();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                $images_item = array(
                    "image_id" => $image_id,
                    "article_id" => $article_id,
                    "file_name" => $file_name,
                    "file_path" => $file_path,
                    "created_at" => $created_at,
                    "updated_at" => $updated_at
                );
                array_push($images_arr, $images_item);
            }
            http_response_code(200);
            echo json_encode($images_arr);
        }
        break;    
    
    case 'POST':
        // CREATE 
        $data = json_decode(file_get_contents("php://input"));
        $images->article_id = $data->article_id;
        $images->file_name = $data->file_name;
        $images->file_path = $data->file_path;

        if ($images->createImage()) {
            http_response_code(201);
            echo json_encode(array("message" => "Image was created."));
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "Unable to create Image."));
        }
        break;
    case 'PUT':
        // UPDATE 
        $data = json_decode(file_get_contents("php://input"));
        $images->image_id = $data->image_id;
        $images->article_id = $data->article_id;
        $images->file_name = $data->file_name;
        $images->file_path = $data->file_path;

        if ($images->updateImage()) {
            http_response_code(200);
            echo json_encode(array("message" => "Image updated."));
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "Unable to update Image."));
        }
        break;
    case 'DELETE':
        // DELETE 
        $data = json_decode(file_get_contents("php://input"));
        $image_id = $data->image_id;
        $images->image_id = $image_id;

        // Check if the Article exists
        if ($images->check_data_exists($image_id)) {
            // Data exists, delete it
            if ($images->deleteImage()) {
                http_response_code(200);
                echo json_encode(array("message" => "Image deleted successfully."));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Unable to delete Image."));
            }
        } else {
            // Video does not exist
            http_response_code(404);
            echo json_encode(array("message" => "Image does not exist."));
        }
        break;

    default:
        // Invalid request method
        http_response_code(405);
        echo json_encode(array("message" => "Invalid request method."));
        break;
}



?>