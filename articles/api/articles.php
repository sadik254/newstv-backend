    <?php

    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

    include_once '../../config/database.php';
    include_once '../class/articles.php';

    $articlesbase = new Database();
    $db = $articlesbase->getConnection();

    $articles = new Articles($db);

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
            // GET all articles or single articles
            if (!empty($_GET["article_id"])) {
                $articles->article_id = $_GET["article_id"];
                $articles->getSingleArticle();
                if ($articles->title) {
                    $articles_arr = array(
                        "article_id" =>  $articles->article_id,
                        "category_id" => $articles->category_id,
                        "title" => $articles->title,
                        "content" => $articles->content,
                        "user_id" => $articles->user_id,
                        "publication_date" => $articles->publication_date,
                        "status" => $articles->status,
                        "created_at" => $articles->created_at,
                        "updated_at" => $articles->updated_at
                    );
                    http_response_code(200);
                    echo json_encode($articles_arr);
                } else {
                    http_response_code(404);
                    echo json_encode(array("message" => "Video not found."));
                }
            } else {
                $stmt = $articles->getArticles();
                $articles_arr = array();
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    extract($row);
                    $articles_item = array(
                        "article_id" => $article_id,
                        "category_id" => $category_id,
                        "title" => $title,
                        "content" => $content,
                        "user_id" => $user_id,
                        "publication_date" => $publication_date,
                        "status" => $status,
                        "created_at" => $created_at,
                        "updated_at" => $updated_at
                    );
                    array_push($articles_arr, $articles_item);
                }
                http_response_code(200);
                echo json_encode($articles_arr);
            }
            break;    
        
        case 'POST':
            // CREATE 
            $data = json_decode(file_get_contents("php://input"));
            $articles->category_id = $data->category_id;
            $articles->title = $data->title;
            $articles->content = $data->content;
            $articles->user_id = $data->user_id;
            $articles->publication_date = $data->publication_date;
            $articles->status = $data->status;

            if ($articles->createArticles()) {
                http_response_code(201);
                echo json_encode(array("message" => "Article was created."));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Unable to create article."));
            }
            break;
        case 'PUT':
            // UPDATE 
            $data = json_decode(file_get_contents("php://input"));
            $articles->article_id = $data->article_id;
            $articles->category_id = $data->category_id;
            $articles->title = $data->title;
            $articles->content = $data->content;
            $articles->user_id = $data->user_id;
            $articles->publication_date = $data->publication_date;
            $articles->status = $data->status;

            if ($articles->updateArticles()) {
                http_response_code(200);
                echo json_encode(array("message" => "Article updated."));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Unable to update Article."));
            }
            break;
        case 'DELETE':
            // DELETE 
            $data = json_decode(file_get_contents("php://input"));
            $article_id = $data->article_id;
            $articles->article_id = $article_id;

            // Check if the Article exists
            if ($articles->check_data_exists($article_id)) {
                // Data exists, delete it
                if ($articles->deleteArticles()) {
                    http_response_code(200);
                    echo json_encode(array("message" => "Article deleted successfully."));
                } else {
                    http_response_code(503);
                    echo json_encode(array("message" => "Unable to delete Article."));
                }
            } else {
                // Article does not exist
                http_response_code(404);
                echo json_encode(array("message" => "Article does not exist."));
            }
            break;

        default:
            // Invalid request method
            http_response_code(405);
            echo json_encode(array("message" => "Invalid request method."));
            break;
    }



    ?>