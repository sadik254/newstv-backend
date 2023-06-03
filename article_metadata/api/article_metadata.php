<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../../config/database.php';
include_once '../class/article_metadata.php';

$database = new Database();
$db = $database->getConnection();

$articleMetadata = new ArticleMetadata($db);

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
        // GET all metadata or single metadata
        if (!empty($_GET["metadata_id"])) {
            $articleMetadata->metadata_id = $_GET["metadata_id"];
            $articleMetadata->getSingleMetadata();
            if ($articleMetadata->metadata_id) {
                $metadata_arr = array(
                    "metadata_id" => $articleMetadata->metadata_id,
                    "metadata_content" => $articleMetadata->metadata_content,
                    "article_id" => $articleMetadata->article_id
                );
                http_response_code(200);
                echo json_encode($metadata_arr);
            } else {
                http_response_code(404);
                echo json_encode(array("message" => "Metadata not found."));
            }
        } else {
            $stmt = $articleMetadata->getMetadata();
            $metadata_arr = array();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                $metadata_item = array(
                    "metadata_id" => $metadata_id,
                    "metadata_content" => $metadata_content,
                    "article_id" => $article_id
                );
                array_push($metadata_arr, $metadata_item);
            }
            http_response_code(200);
            echo json_encode($metadata_arr);
        }
        break;

    case 'POST':
        // CREATE
        $data = json_decode(file_get_contents("php://input"));
        $articleMetadata->metadata_content = $data->metadata_content;
        $articleMetadata->article_id = $data->article_id;

        if ($articleMetadata->createMetadata()) {
            http_response_code(201);
            echo json_encode(array("message" => "Metadata was created."));
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "Unable to create Metadata."));
        }
        break;

    case 'PUT':
        // UPDATE
        $data = json_decode(file_get_contents("php://input"));
        $articleMetadata->metadata_id = $data->metadata_id;
        $articleMetadata->metadata_content = $data->metadata_content;
        $articleMetadata->article_id = $data->article_id;

        if ($articleMetadata->updateMetadata()) {
            http_response_code(200);
            echo json_encode(array("message" => "Metadata updated."));
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "Unable to update Metadata."));
        }
        break;

    case 'DELETE':
        // DELETE
        $data = json_decode(file_get_contents("php://input"));
        $articleMetadata->metadata_id = $data->metadata_id;

        if ($articleMetadata->deleteMetadata()) {
            http_response_code(200);
            echo json_encode(array("message" => "Metadata deleted successfully."));
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "Unable to delete Metadata."));
        }
        break;

    default:
        // Invalid request method
        http_response_code(405);
        echo json_encode(array("message" => "Invalid request method."));
        break;
}

?>
