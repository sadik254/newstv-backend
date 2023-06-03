<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../../config/database.php';
include_once '../class/article_comment.php';

$database = new Database();
$db = $database->getConnection();

$articleComment = new ArticleComment($db);

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
        // GET all comments or single comment
        if (!empty($_GET["comment_id"])) {
            $articleComment->comment_id = $_GET["comment_id"];
            $articleComment->getSingleComment();
            if ($articleComment->comment_id) {
                $comment_arr = array(
                    "comment_id" => $articleComment->comment_id,
                    "comment_content" => $articleComment->comment_content,
                    "article_id" => $articleComment->article_id,
                    "created_at" => $articleComment->created_at,
                    "updated_at" => $articleComment->updated_at
                );
                http_response_code(200);
                echo json_encode($comment_arr);
            } else {
                http_response_code(404);
                echo json_encode(array("message" => "Comment not found."));
            }
        } else {
            $stmt = $articleComment->getComments();
            $comments_arr = array();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                $comment_item = array(
                    "comment_id" => $comment_id,
                    "comment_content" => $comment_content,
                    "article_id" => $article_id,
                    "created_at" => $created_at,
                    "updated_at" => $updated_at
                );
                array_push($comments_arr, $comment_item);
            }
            http_response_code(200);
            echo json_encode($comments_arr);
        }
        break;

    case 'POST':
        // CREATE
        $data = json_decode(file_get_contents("php://input"));
        $articleComment->comment_content = $data->comment_content;
        $articleComment->article_id = $data->article_id;

        if ($articleComment->createComment()) {
            http_response_code(201);
            echo json_encode(array("message" => "Comment was created."));
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "Unable to create Comment."));
        }
        break;

    case 'PUT':
        // UPDATE
        $data = json_decode(file_get_contents("php://input"));
        $articleComment->comment_id = $data->comment_id;
        $articleComment->comment_content = $data->comment_content;
        $articleComment->article_id = $data->article_id;

        if ($articleComment->updateComment()) {
            http_response_code(200);
            echo json_encode(array("message" => "Comment updated."));
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "Unable to update Comment."));
        }
        break;

    case 'DELETE':
        // DELETE
        $data = json_decode(file_get_contents("php://input"));
        $articleComment->comment_id = $data->comment_id;

        if ($articleComment->deleteComment()) {
            http_response_code(200);
            echo json_encode(array("message" => "Comment deleted successfully."));
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "Unable to delete Comment."));
        }
        break;

    default:
        // Invalid request method
        http_response_code(405);
        echo json_encode(array("message" => "Invalid request method."));
        break;
}

?>
