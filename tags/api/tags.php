<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../../config/database.php';
include_once '../class/tags.php';

$tagsbase = new Database();
$db = $tagsbase->getConnection();

$tags = new Tags($db);

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
        // GET all tags or single tag
        if (!empty($_GET["tag_id"])) {
            $tags->tag_id = $_GET["tag_id"];
            $tags->getSingleTag();
            if ($tags->name) {
                $tags_arr = array(
                    "tag_id" =>  $tags->tag_id,
                    "name" => $tags->name,
                    "created_at" => $tags->created_at,
                    "updated_at" => $tags->updated_at
                );
                http_response_code(200);
                echo json_encode($tags_arr);
            } else {
                http_response_code(404);
                echo json_encode(array("message" => "Tag not found."));
            }
        } else {
            $stmt = $tags->getTags();
            $tags_arr = array();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                $tags_item = array(
                    "tag_id" => $tag_id,
                    "name" => $name,
                    "created_at" => $created_at,
                    "updated_at" => $updated_at
                );
                array_push($tags_arr, $tags_item);
            }
            http_response_code(200);
            echo json_encode($tags_arr);
        }
        break;

    case 'POST':
        // CREATE 
        $data = json_decode(file_get_contents("php://input"));
        $tags->name = $data->name;

        if ($tags->createTag()) {
            http_response_code(201);
            echo json_encode(array("message" => "Tag was created."));
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "Unable to create Tag."));
        }
        break;
        
    case 'PUT':
        // UPDATE 
        $data = json_decode(file_get_contents("php://input"));
        $tags->name = $data->name;

        if ($tags->updateTag()) {
            http_response_code(200);
            echo json_encode(array("message" => "Tag updated."));
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "Unable to update Tag."));
        }
        break;

    case 'DELETE':
        // DELETE 
        $data = json_decode(file_get_contents("php://input"));
        $tag_id = $data->tag_id;
        $tags->tag_id = $tag_id;

        // Check if the Tag exists
        if ($tags->checkTagExists($tag_id)) {
            // Tag exists, delete it
            if ($tags->deleteTag()) {
                http_response_code(200);
                echo json_encode(array("message" => "Tag deleted successfully."));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Unable to delete Tag."));
            }
        } else {
            // Tag does not exist
            http_response_code(404);
            echo json_encode(array("message" => "Tag does not exist."));
        }
        break;

    default:
        // Invalid request method
        http_response_code(405);
        echo json_encode(array("message" => "Invalid request method."));
        break;
}
?>
