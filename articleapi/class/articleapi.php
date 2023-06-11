<?php

class ArticleAPI {
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db->getConnection();
    }

    public function getArticle($articleId) {
        $query = "
            SELECT articles.article_id, articles.title, articles.content, articles.publication_date,
                   GROUP_CONCAT(DISTINCT categories.name) AS category_names, users.username,
                   GROUP_CONCAT(DISTINCT images.file_name) AS file_names, GROUP_CONCAT(DISTINCT images.file_path) AS file_paths, 
                   GROUP_CONCAT(DISTINCT tags.name) AS tag_names,
                   GROUP_CONCAT(DISTINCT article_metadata.metadata_content) AS metadata_contents,
                   GROUP_CONCAT(DISTINCT sub_categories.sub_category_name) AS sub_category_names
            FROM articles
            LEFT JOIN article_categories ON articles.article_id = article_categories.article_id
            LEFT JOIN categories ON article_categories.category_id = categories.category_id
            LEFT JOIN users ON articles.user_id = users.user_id
            LEFT JOIN images ON articles.article_id = images.article_id
            LEFT JOIN article_tags ON articles.article_id = article_tags.article_id
            LEFT JOIN tags ON article_tags.tag_id = tags.tag_id
            LEFT JOIN article_metadata ON articles.article_id = article_metadata.article_id
            LEFT JOIN article_sub_categories ON articles.article_id = article_sub_categories.article_id
            LEFT JOIN sub_categories ON article_sub_categories.sub_category_id = sub_categories.id
            WHERE articles.article_id = :articleId
            GROUP BY articles.article_id
        ";
    
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(":articleId", $articleId, PDO::PARAM_INT);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
    
        $article = array(
            "article_id" => $results[0]['article_id'],
            "title" => $results[0]['title'],
            "content" => $results[0]['content'],
            "publication_date" => $results[0]['publication_date'],
            "categories" => explode(",", $results[0]['category_names']),
            "user" => $results[0]['username'],
            "images" => array_map(function($file_name, $file_path) {
                return array(
                    "file_name" => $file_name,
                    "file_path" => $file_path
                );
            }, explode(",", $results[0]['file_names']), explode(",", $results[0]['file_paths'])),
            "tags" => explode(",", $results[0]['tag_names']),
            "metadata" => explode(",", $results[0]['metadata_contents']),
            "sub_categories" => explode(",", $results[0]['sub_category_names'])
        );
    
        return $article;
    }
    
    
    public function createArticle($articleData) {
        $insertArticleQuery = "
            INSERT INTO articles (title, content, user_id, publication_date, status)
            VALUES (:title, :content, :user_id, :publication_date, :status)
        ";
    
        $stmt = $this->conn->prepare($insertArticleQuery);
        $stmt->bindValue(":title", $articleData['title'], PDO::PARAM_STR);
        $stmt->bindValue(":content", $articleData['content'], PDO::PARAM_STR);
        $stmt->bindValue(":user_id", $articleData['user_id'], PDO::PARAM_INT);
        $stmt->bindValue(":publication_date", $articleData['publication_date'], PDO::PARAM_STR);
        $stmt->bindValue(":status", $articleData['status'], PDO::PARAM_STR);
        $stmt->execute();
        $articleId = $this->conn->lastInsertId();
        $stmt->closeCursor();
    
        if (!empty($articleData['images'])) {
            $insertImageQuery = "
                INSERT INTO images (article_id, file_name, file_path)
                VALUES (:article_id, :file_name, :file_path)
            ";
    
            $stmt = $this->conn->prepare($insertImageQuery);
            foreach ($articleData['images'] as $image) {
                $stmt->bindValue(":article_id", $articleId, PDO::PARAM_INT);
                $stmt->bindValue(":file_name", $image['file_name'], PDO::PARAM_STR);
                $stmt->bindValue(":file_path", $image['file_path'], PDO::PARAM_STR);
                $stmt->execute();
            }
            $stmt->closeCursor();
        }
    
        if (!empty($articleData['tags'])) {
            $insertTagQuery = "
                INSERT INTO article_tags (article_id, tag_id)
                VALUES (:article_id, :tag_id)
            ";
    
            $stmt = $this->conn->prepare($insertTagQuery);
            foreach ($articleData['tags'] as $tag) {
                $stmt->bindValue(":article_id", $articleId, PDO::PARAM_INT);
                $stmt->bindValue(":tag_id", $tag['tag_id'], PDO::PARAM_INT);
                $stmt->execute();
            }
            $stmt->closeCursor();
        }        
    
        if (!empty($articleData['categories'])) {
            $insertCategoryQuery = "
                INSERT INTO article_categories (article_id, category_id)
                VALUES (:article_id, :category_id)
            ";
    
            $stmt = $this->conn->prepare($insertCategoryQuery);
            foreach ($articleData['categories'] as $category) {
                $stmt->bindValue(":article_id", $articleId, PDO::PARAM_INT);
                $stmt->bindValue(":category_id", $category['category_id'], PDO::PARAM_INT);
                $stmt->execute();
            }
            $stmt->closeCursor();
        }
    
        if (!empty($articleData['metadata'])) {
            $insertMetadataQuery = "
                INSERT INTO article_metadata (metadata_content, article_id)
                VALUES (:metadata_content, :article_id)
            ";
    
            $stmt = $this->conn->prepare($insertMetadataQuery);
            foreach ($articleData['metadata'] as $metadata) {
                $stmt->bindValue(":metadata_content", $metadata, PDO::PARAM_STR);
                $stmt->bindValue(":article_id", $articleId, PDO::PARAM_INT);
                $stmt->execute();
            }
            $stmt->closeCursor();
        }
    
        if (!empty($articleData['sub_categories'])) {
            $insertSubCategoryQuery = "
                INSERT INTO article_sub_categories (article_id, sub_category_id)
                VALUES (:article_id, :sub_category_id)
            ";
    
            $stmt = $this->conn->prepare($insertSubCategoryQuery);
            foreach ($articleData['sub_categories'] as $subCategory) {
                $stmt->bindValue(":article_id", $articleId, PDO::PARAM_INT);
                $stmt->bindValue(":sub_category_id", $subCategory['sub_category_id'], PDO::PARAM_INT);
                $stmt->execute();
            }
            $stmt->closeCursor();
        }
    
        return $articleId;
    }
    
    

    public function updateArticle($articleId, $articleData) {
        $updateArticleQuery = "
        UPDATE articles
        SET title = :title, content = :content, user_id = :user_id, publication_date = :publication_date
        WHERE article_id = :article_id
        ";
        
        $stmt = $this->conn->prepare($updateArticleQuery);
        $stmt->bindValue(":title", $articleData['title'], PDO::PARAM_STR);
        $stmt->bindValue(":content", $articleData['content'], PDO::PARAM_STR);
        $stmt->bindValue(":user_id", $articleData['user_id'], PDO::PARAM_INT);
        $stmt->bindValue(":publication_date", $articleData['publication_date'], PDO::PARAM_STR);
        $stmt->bindValue(":article_id", $articleId, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->closeCursor();
    
        $deleteImagesQuery = "DELETE FROM images WHERE article_id = :article_id";
        $stmt = $this->conn->prepare($deleteImagesQuery);
        $stmt->bindValue(":article_id", $articleId, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->closeCursor();
    
        if (!empty($articleData['images'])) {
            $insertImageQuery = "
            INSERT INTO images (article_id, file_name, file_path)
            VALUES (:article_id, :file_name, :file_path)
            ";
            
            $stmt = $this->conn->prepare($insertImageQuery);
            $stmt->bindParam(":article_id", $articleId, PDO::PARAM_INT);
            $file_name = '';
            $file_path = '';
            $stmt->bindParam(":file_name", $file_name, PDO::PARAM_STR);
            $stmt->bindParam(":file_path", $file_path, PDO::PARAM_STR);
            foreach ($articleData['images'] as $image) {
                $file_name = $image['file_name'];
                $file_path = $image['file_path'];
                $stmt->execute();
            }
            $stmt->closeCursor();
        }
    
        $deleteTagsQuery = "DELETE FROM article_tags WHERE article_id = :article_id";
        $stmt = $this->conn->prepare($deleteTagsQuery);
        $stmt->bindValue(":article_id", $articleId, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->closeCursor();
    
        if (!empty($articleData['tags'])) {
            $insertTagQuery = "
            INSERT INTO article_tags (article_id, tag_id)
            VALUES (:article_id, :tag_id)
            ";
            
            $stmt = $this->conn->prepare($insertTagQuery);
            $stmt->bindParam(":article_id", $articleId, PDO::PARAM_INT);
            $tag_id = 0;
            $stmt->bindParam(":tag_id", $tag_id, PDO::PARAM_INT);
            foreach ($articleData['tags'] as $tag) {
                $tag_id = $tag['tag_id'];
                $stmt->execute();
            }
            $stmt->closeCursor();
        }
        $deleteCategoriesQuery = "DELETE FROM article_categories WHERE article_id = :article_id";
        $stmt = $this->conn->prepare($deleteCategoriesQuery);
        $stmt->bindValue(":article_id", $articleId, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->closeCursor();

        if (!empty($articleData['categories'])) {
            $insertCategoryQuery = "
            INSERT INTO article_categories (article_id, category_id)
            VALUES (:article_id, :category_id)
            ";
            
            $stmt = $this->conn->prepare($insertCategoryQuery);
            foreach ($articleData['categories'] as $category) {
                $stmt->bindValue(":article_id", $articleId, PDO::PARAM_INT);
                $stmt->bindValue(":category_id", $category['category_id'], PDO::PARAM_INT);
                $stmt->execute();
            }
            $stmt->closeCursor();
        }
    
        $deleteMetadataQuery = "DELETE FROM article_metadata WHERE article_id = :article_id";
        $stmt = $this->conn->prepare($deleteMetadataQuery);
        $stmt->bindValue(":article_id", $articleId, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->closeCursor();
    
        if (!empty($articleData['metadata'])) {
            $insertMetadataQuery = "
            INSERT INTO article_metadata (metadata_content, article_id)
            VALUES (:metadata_content, :article_id)
            ";
            
            $stmt = $this->conn->prepare($insertMetadataQuery);
            $stmt->bindParam(":article_id", $articleId, PDO::PARAM_INT);
            $metadata_content = '';
            $stmt->bindParam(":metadata_content", $metadata_content, PDO::PARAM_STR);
            foreach ($articleData['metadata'] as $metadata) {
                $metadata_content = $metadata;
                $stmt->execute();
            }
            $stmt->closeCursor();
        }

        $deleteSubCategoryQuery = "DELETE FROM article_sub_categories WHERE article_id = :article_id";
        $stmt = $this->conn->prepare($deleteSubCategoryQuery);
        $stmt->bindValue(":article_id", $articleId, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->closeCursor();

        if (!empty($articleData['sub_categories'])) {
            $insertSubCategoryQuery = "
                INSERT INTO article_sub_categories (article_id, sub_category_id)
                VALUES (:article_id, :sub_category_id)
            ";
    
            $stmt = $this->conn->prepare($insertSubCategoryQuery);
            foreach ($articleData['sub_categories'] as $subCategory) {
                $stmt->bindValue(":article_id", $articleId, PDO::PARAM_INT);
                $stmt->bindValue(":sub_category_id", $subCategory['sub_category_id'], PDO::PARAM_INT);
                $stmt->execute();
            }
            $stmt->closeCursor();
        }
    
        return true;
    }
    

    public function deleteArticle($articleId) {
        // Delete associated rows from other tables
        $deleteMetadataQuery = "DELETE FROM article_metadata WHERE article_id = :article_id";
        $stmt = $this->conn->prepare($deleteMetadataQuery);
        $stmt->bindValue(":article_id", $articleId, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->closeCursor();
    
        $deleteImagesQuery = "DELETE FROM images WHERE article_id = :article_id";
        $stmt = $this->conn->prepare($deleteImagesQuery);
        $stmt->bindValue(":article_id", $articleId, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->closeCursor();
    
        $deleteTagsQuery = "DELETE FROM article_tags WHERE article_id = :article_id";
        $stmt = $this->conn->prepare($deleteTagsQuery);
        $stmt->bindValue(":article_id", $articleId, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->closeCursor();

        $deleteArticleCategory = "DELETE FROM article_categories WHERE article_id = :article_id";
        $stmt = $this->conn->prepare($deleteArticleCategory);
        $stmt->bindValue(":article_id", $articleId, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->closeCursor();

        $deleteArticleSubCategory = "DELETE FROM article_sub_categories WHERE article_id = :article_id";
        $stmt = $this->conn->prepare($deleteArticleSubCategory);
        $stmt->bindValue(":article_id", $articleId, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->closeCursor();

        // Now delete the row from the articles table
        $deleteArticleQuery = "DELETE FROM articles WHERE article_id = :article_id";
        $stmt = $this->conn->prepare($deleteArticleQuery);
        $stmt->bindValue(":article_id", $articleId, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->closeCursor();
    
        return true;
    }
    
    
}

?>
