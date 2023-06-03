<?php
// include database and object files
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
include_once '../../config/database.php';
include_once '../class/users.php';
// get database connection
$database = new Database();
$db = $database->getConnection();

// get input data from request body
$data = json_decode(file_get_contents("php://input"));

// prepare user object
$user = new Users($db);

// set user properties
$user->username = $data->username;
$user->password = $data->password;

// login user
if ($user->login()) {
    // create response array
    $response = array(
        "status" => true,
        "message" => "Successfully Login!",
        "user_id" => $user->user_id,
        "username" => $user->username
    );
} else {
    // create response array
    $response = array(
        "status" => false,
        "message" => "Invalid Username or Password!",
    );
}

// make response JSON format
echo json_encode($response);
?>