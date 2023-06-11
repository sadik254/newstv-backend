<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../../config/database.php';
include_once '../class/poll_option.php';

$database = new Database();
$db = $database->getConnection();

$pollOption = new PollOption($db);

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
        // GET all options by poll or single option
        if (!empty($_GET["poll_id"])) {
            $pollOption->poll_id = $_GET["poll_id"];
            $stmt = $pollOption->getOptionsByPoll();

            $options_arr = array();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                $option_item = array(
                    "id" => $id,
                    "poll_id" => $poll_id,
                    "option_text" => $option_text,
                    "vote_count" => $vote_count,
                    "percentage" => $percentage,
                    "created_at" => $created_at,
                    "updated_at" => $updated_at
                );
                array_push($options_arr, $option_item);
            }

            http_response_code(200);
            echo json_encode($options_arr);
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Missing poll_id parameter."));
        }
        break;

    case 'POST':
        // CREATE
        $data = json_decode(file_get_contents("php://input"));
        $pollOption->poll_id = $data->poll_id;
        $pollOption->option_text = $data->option_text;

        if ($pollOption->createOption()) {
            http_response_code(201);
            echo json_encode(array("message" => "Option was created."));
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "Unable to create Option."));
        }
        break;

    case 'PUT':
        // UPDATE
        $data = json_decode(file_get_contents("php://input"));
        $pollOption->id = $data->id;
        $pollOption->option_text = $data->option_text;
        $pollOption->vote_count = $data->vote_count;
        $pollOption->percentage = $data->percentage;

        if ($pollOption->updateOption()) {
            http_response_code(200);
            echo json_encode(array("message" => "Option updated."));
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "Unable to update Option."));
        }
        break;

    case 'DELETE':
        // DELETE
        $data = json_decode(file_get_contents("php://input"));
        $pollOption->id = $data->id;

        if ($pollOption->deleteOption()) {
            http_response_code(200);
            echo json_encode(array("message" => "Option deleted successfully."));
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "Unable to delete Option."));
        }
        break;

    default:
        // Invalid request method
        http_response_code(405);
        echo json_encode(array("message" => "Invalid request method."));
        break;
}
