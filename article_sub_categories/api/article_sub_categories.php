<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../../config/database.php';
include_once '../class/ArticleSubCategory.php';

$database = new Database();
$db = $database->getConnection();

$articleSubCategory = new ArticleSubCategory($db);

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
        // GET all article-subcategory relationships or single relationship
        if (!empty($_GET["article_id"]) && !empty($_GET["sub_category_id"])) {
            $articleSubCategory->article_id = $_GET["article_id"];
            $articleSubCategory->sub_category_id = $_GET["sub_category_id"];
            $articleSubCategory->getSingleArticleSubCategory($articleId, $subCategoryId);
            if ($articleSubCategory->article_id && $articleSubCategory->sub_category_id) {
                $articleSubCategory_arr = array(
                    "article_id" => $articleSubCategory->article_id,
                    "sub_category_id" => $articleSubCategory->sub_category_id
                );
                http_response_code(200);
                echo json_encode($articleSubCategory_arr);
            } else {
                http_response_code(404);
                echo json_encode(array("message" => "Article-subcategory relationship not found."));
            }
        } else {
            $stmt = $articleSubCategory->getArticleSubCategories();
            $articleSubCategories_arr = array();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                $articleSubCategory_item = array(
                    "article_id" => $article_id,
                    "sub_category_id" => $sub_category_id
                );
                array_push($articleSubCategories_arr, $articleSubCategory_item);
            }
            http_response_code(200);
            echo json_encode($articleSubCategories_arr);
        }
        break;

    case 'POST':
        // CREATE article-subcategory relationship
        $data = json_decode(file_get_contents("php://input"));
        $articleSubCategory->article_id = $data->article_id;
        $articleSubCategory->sub_category_id = $data->sub_category_id;

        if ($articleSubCategory->createArticleSubCategory()) {
            http_response_code(201);
            echo json_encode(array("message" => "Article-subcategory relationship was created."));
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "Unable to create article-subcategory relationship."));
        }
        break;

    case 'DELETE':
        // DELETE article-subcategory relationship
        $data = json_decode(file_get_contents("php://input"));
        $articleSubCategory->article_id = $data->article_id;
        $articleSubCategory->sub_category_id = $data->sub_category_id;

        if ($articleSubCategory->deleteArticleSubCategory()) {
            http_response_code(200);
            echo json_encode(array("message" => "Article-subcategory relationship deleted successfully."));
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "Unable to delete article-subcategory relationship."));
        }
        break;

    default:
        // Invalid request method
        http_response_code(405);
        echo json_encode(array("message" => "Invalid request method."));
        break;
}
?>
