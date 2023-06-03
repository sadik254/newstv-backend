<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../../config/database.php';
include_once '../class/video_categories.php';

$database = new Database();
$db = $database->getConnection();

$videoCategories = new VideoCategories($db);

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
        // GET all video categories or single video category
        if (!empty($_GET["video_id"]) && !empty($_GET["category_id"])) {
            $videoCategories->video_id = $_GET["video_id"];
            $videoCategories->category_id = $_GET["category_id"];
            $exists = $videoCategories->getSingleVideoCategory();
            if ($exists) {
                $videoCategory = array(
                    "video_id" => $videoCategories->video_id,
                    "category_id" => $videoCategories->category_id
                );
                http_response_code(200);
                echo json_encode($videoCategory);
            } else {
                http_response_code(404);
                echo json_encode(array("message" => "Video category not found."));
            }
        } else {
            $stmt = $videoCategories->getVideoCategories();
            $videoCategoriesArr = array();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                $videoCategory = array(
                    "video_id" => $video_id,
                    "category_id" => $category_id
                );
                array_push($videoCategoriesArr, $videoCategory);
            }
            http_response_code(200);
            echo json_encode($videoCategoriesArr);
        }
        break;

    case 'POST':
        // CREATE
        $data = json_decode(file_get_contents("php://input"));
        $videoCategories->video_id = $data->video_id;
        $videoCategories->category_id = $data->category_id;

        if ($videoCategories->createVideoCategory()) {
            http_response_code(201);
            echo json_encode(array("message" => "Video category was created."));
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "Unable to create Video category."));
        }
        break;

    case 'PUT':
        // UPDATE
        $data = json_decode(file_get_contents("php://input"));
        $videoCategories->video_id = $data->video_id;
        $videoCategories->category_id = $data->category_id;

        if ($videoCategories->updateVideoCategory()) {
            http_response_code(200);
            echo json_encode(array("message" => "Video category updated."));
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "Unable to update Video category."));
        }
        break;

    case 'DELETE':
        // DELETE
        $data = json_decode(file_get_contents("php://input"));
        $videoCategories->video_id = $data->video_id;
        $videoCategories->category_id = $data->category_id;

        if ($videoCategories->checkVideoCategoryExists($videoCategories->video_id, $videoCategories->category_id)) {
            if ($videoCategories->deleteVideoCategory()) {
                http_response_code(200);
                echo json_encode(array("message" => "Video category deleted successfully."));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Unable to delete Video category."));
            }
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "Video category does not exist."));
        }
        break;

    default:
        // Invalid request method
        http_response_code(405);
        echo json_encode(array("message" => "Invalid request method."));
        break;
}

?>
