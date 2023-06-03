<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../../config/database.php';
include_once '../class/article_tags.php';

$database = new Database();
$db = $database->getConnection();

$articleTags = new ArticleTags($db);

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
        // GET all article tags or single article tag
        if (!empty($_GET["article_tag_id"])) {
            $articleTags->article_tag_id = $_GET["article_tag_id"];
            $articleTags->getSingleArticleTag();
            if ($articleTags->article_id) {
                $articleTags_arr = array(
                    "article_tag_id" =>  $articleTags->article_tag_id,
                    "article_id" => $articleTags->article_id,
                    "tag_id" => $articleTags->tag_id,
                    "created_at" => $articleTags->created_at,
                    "updated_at" => $articleTags->updated_at
                );
                http_response_code(200);
                echo json_encode($articleTags_arr);
            } else {
                http_response_code(404);
                echo json_encode(array("message" => "Article tag not found."));
            }
        } else {
            $stmt = $articleTags->getArticleTags();
            $articleTags_arr = array();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                $articleTags_item = array(
                    "article_tag_id" => $article_tag_id,
                    "article_id" => $article_id,
                    "tag_id" => $tag_id,
                    "created_at" => $created_at,
                    "updated_at" => $updated_at
                );
                array_push($articleTags_arr, $articleTags_item);
            }
            http_response_code(200);
            echo json_encode($articleTags_arr);
        }
        break;

    case 'POST':
        // CREATE 
        $data = json_decode(file_get_contents("php://input"));
        $articleTags->article_id = $data->article_id;
        $articleTags->tag_id = $data->tag_id;

        if ($articleTags->createArticleTag()) {
            http_response_code(201);
            echo json_encode(array("message" => "Article tag was created."));
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "Unable to create Article tag."));
        }
        break;

    case 'PUT':
        // UPDATE 
        $data = json_decode(file_get_contents("php://input"));
        $articleTags->article_tag_id = $data->article_tag_id;
        $articleTags->article_id = $data->article_id;
        $articleTags->tag_id = $data->tag_id;

        if ($articleTags->updateArticleTag()) {
            http_response_code(200);
            echo json_encode(array("message" => "Article tag updated."));
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "Unable to update Article tag."));
        }
        break;

    case 'DELETE':
        // DELETE 
        $data = json_decode(file_get_contents("php://input"));
        $article_tag_id = $data->article_tag_id;
        $articleTags->article_tag_id = $article_tag_id;

        // Check if the Article tag exists
        if ($articleTags->checkArticleTagExists($article_tag_id)) {
            // Article tag exists, delete it
            if ($articleTags->deleteArticleTag()) {
                http_response_code(200);
                echo json_encode(array("message" => "Article tag deleted successfully."));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Unable to delete Article tag."));
            }
        } else {
            // Article tag does not exist
            http_response_code(404);
            echo json_encode(array("message" => "Article tag does not exist."));
        }
        break;

    default:
        // Invalid request method
        http_response_code(405);
        echo json_encode(array("message" => "Invalid request method."));
        break;
}
?>
