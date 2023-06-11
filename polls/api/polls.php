<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../../config/database.php';
include_once '../class/polls.php';

$database = new Database();
$db = $database->getConnection();

$poll = new Poll($db);

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
        // GET all polls or single poll
        if (!empty($_GET["id"])) {
            $poll->id = $_GET["id"];
            $poll->getSinglePoll();
            if ($poll->id) {
                $poll_arr = array(
                    "id" => $poll->id,
                    "question" => $poll->question,
                    "total_votes" => $poll->total_votes,
                    "publication_date" => $poll->publication_date,
                    "created_at" => $poll->created_at,
                    "updated_at" => $poll->updated_at
                );
                http_response_code(200);
                echo json_encode($poll_arr);
            } else {
                http_response_code(404);
                echo json_encode(array("message" => "Poll not found."));
            }
        } else {
            $stmt = $poll->getPolls();
            $polls_arr = array();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                $poll_item = array(
                    "id" => $id,
                    "question" => $question,
                    "total_votes" => $total_votes,
                    "publication_date" => $publication_date,
                    "created_at" => $created_at,
                    "updated_at" => $updated_at
                );
                array_push($polls_arr, $poll_item);
            }
            http_response_code(200);
            echo json_encode($polls_arr);
        }
        break;

    case 'POST':
        // CREATE
        $data = json_decode(file_get_contents("php://input"));
        $poll->question = $data->question;
        $poll->publication_date = $data->publication_date;

        if ($poll->createPoll()) {
            http_response_code(201);
            echo json_encode(array("message" => "Poll was created."));
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "Unable to create Poll."));
        }
        break;

    case 'PUT':
        // UPDATE
        $data = json_decode(file_get_contents("php://input"));
        $poll->id = $data->id;
        $poll->question = $data->question;
        $poll->publication_date = $data->publication_date;

        if ($poll->updatePoll()) {
            http_response_code(200);
            echo json_encode(array("message" => "Poll updated."));
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "Unable to update Poll."));
        }
        break;

    case 'DELETE':
        // DELETE
        $data = json_decode(file_get_contents("php://input"));
        $poll->id = $data->id;

        if ($poll->deletePoll()) {
            http_response_code(200);
            echo json_encode(array("message" => "Poll deleted successfully."));
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "Unable to delete Poll."));
        }
        break;

    default:
        // Invalid request method
        http_response_code(405);
        echo json_encode(array("message" => "Invalid request method."));
        break;
}

?>
