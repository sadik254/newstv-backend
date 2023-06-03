<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../../config/database.php';
include_once '../class/videos.php';

$videosbase = new Database();
$db = $videosbase->getConnection();

$videos = new Videos($db);

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
        // GET all Videos or single video
        if (!empty($_GET["video_id"])) {
            $videos->video_id = $_GET["video_id"];
            $videos->getSingleVideo();
            if ($videos->video_url) {
                $videos_arr = array(
                    "video_id" =>  $videos->video_id,
                    "video_url" => $videos->video_url,
                    "video_title" => $videos->video_title,
                    "video_meta" => $videos->video_meta,
                    "created_at" => $videos->created_at,
                    "updated_at" => $videos->updated_at
                );
                http_response_code(200);
                echo json_encode($videos_arr);
            } else {
                http_response_code(404);
                echo json_encode(array("message" => "Video not found."));
            }
        } else {
            $stmt = $videos->getvideos();
            $videos_arr = array();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                $videos_item = array(
                    "video_id" => $video_id,
                    "video_url" => $video_url,
                    "video_title" => $video_title,
                    "video_meta" => $video_meta,
                    "created_at" => $created_at,
                    "updated_at" => $updated_at
                );
                array_push($videos_arr, $videos_item);
            }
            http_response_code(200);
            echo json_encode($videos_arr);
        }
        break;    
    
    case 'POST':
        // CREATE 
        $data = json_decode(file_get_contents("php://input"));
        $videos->video_url = $data->video_url;
        $videos->video_title = $data->video_title;
        $videos->video_meta = $data->video_meta;

        if ($videos->createVideo()) {
            http_response_code(201);
            echo json_encode(array("message" => "Video was created."));
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "Unable to create Video."));
        }
        break;
    case 'PUT':
        // UPDATE 
        $data = json_decode(file_get_contents("php://input"));
        $videos->video_url = $data->video_url;
        $videos->video_title = $data->video_title;
        $videos->video_meta = $data->video_meta;

        if ($videos->updateVideo()) {
            http_response_code(200);
            echo json_encode(array("message" => "Video updated."));
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "Unable to update Video."));
        }
        break;
    case 'DELETE':
        // DELETE 
        $data = json_decode(file_get_contents("php://input"));
        $video_id = $data->video_id;
        $videos->video_id = $video_id;

        // Check if the Article exists
        if ($videos->check_data_exists($video_id)) {
            // Data exists, delete it
            if ($videos->deleteVideo()) {
                http_response_code(200);
                echo json_encode(array("message" => "Video deleted successfully."));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Unable to delete Video."));
            }
        } else {
            // Video does not exist
            http_response_code(404);
            echo json_encode(array("message" => "Video does not exist."));
        }
        break;

    default:
        // Invalid request method
        http_response_code(405);
        echo json_encode(array("message" => "Invalid request method."));
        break;
}



?>