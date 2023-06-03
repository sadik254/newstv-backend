<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../../config/database.php';
include_once '../class/article_categories.php';

$database = new Database();
$db = $database->getConnection();

$articleCategories = new ArticleCategories($db);

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
        // GET all article categories or single article category
        if (!empty($_GET["article_id"]) && !empty($_GET["category_id"])) {
            $articleCategories->article_id = $_GET["article_id"];
            $articleCategories->category_id = $_GET["category_id"];
            $articleCategoryExists = $articleCategories->getSingleArticleCategory();
            
            if ($articleCategoryExists) {
                $articleCategory = array(
                    "article_id" => $articleCategories->article_id,
                    "category_id" => $articleCategories->category_id
                );
                http_response_code(200);
                echo json_encode($articleCategory);
            } else {
                http_response_code(404);
                echo json_encode(array("message" => "Article category not found."));
            }
        } else {
            $stmt = $articleCategories->getArticleCategories();
            $articleCategoriesArr = array();
            
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                $articleCategory = array(
                    "article_id" => $article_id,
                    "category_id" => $category_id
                );
                array_push($articleCategoriesArr, $articleCategory);
            }
            
            http_response_code(200);
            echo json_encode($articleCategoriesArr);
        }
        break;

    case 'POST':
        // CREATE
        $data = json_decode(file_get_contents("php://input"));
        
        $articleCategories->article_id = $data->article_id;
        $articleCategories->category_id = $data->category_id;

        if ($articleCategories->createArticleCategory()) {
            http_response_code(201);
            echo json_encode(array("message" => "Article category was created."));
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "Unable to create Article category."));
        }
        break;

    case 'PUT':
        // UPDATE
        $data = json_decode(file_get_contents("php://input"));
        
        $articleCategories->article_id = $data->article_id;
        $articleCategories->category_id = $data->category_id;

        if ($articleCategories->updateArticleCategory()) {
            http_response_code(200);
            echo json_encode(array("message" => "Article category updated."));
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "Unable to update Article category."));
        }
        break;

    case 'DELETE':
        // DELETE
        $data = json_decode(file_get_contents("php://input"));
        
        $article_id = $data->article_id;
        $category_id = $data->category_id;
        
        if ($articleCategories->checkArticleCategoryExists($article_id, $category_id)) {
            if ($articleCategories->deleteArticleCategory()) {
                http_response_code(200);
                echo json_encode(array("message" => "Article category deleted successfully."));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Unable to delete Article category."));
            }
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "Article category does not exist."));
        }
        break;

    default:
        // Invalid request method
        http_response_code(405);
        echo json_encode(array("message" => "Invalid request method."));
        break;
}

?>
