<?php
header("Access-Control-Allow-Origin: *");
// header("Content-Type: application/json; charset=UTF-8");
header("Content-Type: multipart/form-data");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once('../../config/globalconfig.php');

// Set the file upload directory
// $upload_dir = '/path/to/upload/folder';
$upload_dir = ArticleConfig::$upload_dir;

// Allow only images to be uploaded
$allowed_extensions = array('png', 'jpg', 'jpeg');

// Check if a file was sent with the request
if (empty($_FILES['file'])) {
  http_response_code(400);
  echo json_encode(array('message' => 'No file part in the request'));
  exit;
}

// Get the file and its details
$file = $_FILES['file'];
$name = $file['name'];
$size = $file['size'];
$tmp_name = $file['tmp_name'];
$error = $file['error'];

// Validate the file
if ($error == UPLOAD_ERR_OK) {
  // Check if the file has an allowed extension
  $extension = strtolower(pathinfo($name, PATHINFO_EXTENSION));
  if (!in_array($extension, $allowed_extensions)) {
    http_response_code(400);
    echo json_encode(array('message' => 'Allowed file types are png, jpg, and jpeg'));
    exit;
  }

  // Save the file to the specified upload directory
  $new_name = uniqid() . '.' . $extension;
  $destination = $upload_dir . '/' . $new_name;
  move_uploaded_file($tmp_name, $destination);
  $dest = ArticleConfig::$dest;

  // Return success message
  http_response_code(200);
  
  echo json_encode(array($dest.$new_name));
  exit;
}

// Return error message
http_response_code(400);
echo json_encode(array('message' => 'An error occurred while uploading the file'));
exit;