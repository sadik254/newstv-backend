<?php
require_once '../class/articleapi.php';
require_once '../../config/database.php';

$db = new Database();
$articleAPI = new ArticleAPI($db);

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        if (!empty($_GET['article_id'])) {
            $articleId = intval($_GET['article_id']);
            $article = $articleAPI->getArticle($articleId);
            echo json_encode($article);
        } else {
            // Handle error: article ID not provided
        }
        break;
    case 'POST':
        $articleData = json_decode(file_get_contents('php://input'), true);
        $articleId = $articleAPI->createArticle($articleData);
        echo json_encode(array('article_id' => $articleId));
        break;
    case 'PUT':
        if (!empty($_GET['article_id'])) {
            $articleId = intval($_GET['article_id']);
            $articleData = json_decode(file_get_contents('php://input'), true);
            $success = $articleAPI->updateArticle($articleId, $articleData);
            echo json_encode(array('success' => $success));
        } else {
            // Handle error: article ID not provided
        }
        break;
    case 'DELETE':
        if (!empty($_GET['article_id'])) {
            $articleId = intval($_GET['article_id']);
            $success = $articleAPI->deleteArticle($articleId);
            echo json_encode(array('success' => $success));
        } else {
            // Handle error: article ID not provided
        }
        break;
    default:
        // Handle error: unsupported HTTP method
        break;
}
?>
