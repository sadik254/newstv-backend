<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../../config/database.php';
include_once '../class/users.php';

$usersbase = new Database();
$db = $usersbase->getConnection();

$users = new Users($db);

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
        // GET all users or single users
        if (!empty($_GET["user_id"])) {
            $users->user_id = $_GET["user_id"];
            $users->getSingleUser();
            if ($users->username) {
                $users_arr = array(
                    "user_id" =>  $users->user_id,
                    "username" => $users->username,
                    "email" => $users->email,
                    "role" => $users->role,
                    "created_at" => $users->created_at,
                    "updated_at" => $users->updated_at
                );
                http_response_code(200);
                echo json_encode($users_arr);
            } else {
                http_response_code(404);
                echo json_encode(array("message" => "User not found."));
            }
        } else {
            $stmt = $users->getUsers();
            $users_arr = array();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                $users_item = array(
                    "user_id" => $user_id,
                    "username" => $username,
                    "email" => $email,
                    "role" => $role,
                    "created_at" => $created_at,
                    "updated_at" => $updated_at
                );
                array_push($users_arr, $users_item);
            }
            http_response_code(200);
            echo json_encode($users_arr);
        }
        break;    
    
    case 'POST':
        // CREATE 
        $data = json_decode(file_get_contents("php://input"));
        $users->username = $data->username;
        $users->email = $data->email;
        $users->password = $data->password;
        $users->role = $data->role;

        if ($users->createUsers()) {
            http_response_code(201);
            echo json_encode(array("message" => "User created."));
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "Unable to create User."));
        }
        break;
    case 'PUT':
        // UPDATE 
        $data = json_decode(file_get_contents("php://input"));
        $users->user_id = $data->user_id;
        $users->username = $data->username;
        $users->email = $data->email;
        $users->password = $data->password;
        $users->role = $data->role;

        if ($users->updateUsers()) {
            http_response_code(200);
            echo json_encode(array("message" => "User Data updated."));
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "Unable to update User Data."));
        }
        break;
    case 'DELETE':
        // DELETE
        $data = json_decode(file_get_contents("php://input"));
        $user_id = $data->user_id;
        $users->user_id = $user_id;

        // Check if the user exists
        if ($users->check_data_exists($user_id)) {
            // User exists, delete it
            if ($users->deleteUsers()) {
                http_response_code(200);
                echo json_encode(array("message" => "User deleted successfully."));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Unable to delete User."));
            }
        } else {
            // Data does not exist
            http_response_code(404);
            echo json_encode(array("message" => "User does not exist."));
        }
        break;

    default:
        // Invalid request method
        http_response_code(405);
        echo json_encode(array("message" => "Invalid request method."));
        break;
}



?>