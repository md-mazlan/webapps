<?php

/**
 * Content Class
 *
 * Handles all CRUD operations for content and manages user interactions.
 * The getAll() method is now enhanced to support pagination and filtering.
 */
class Content
{
    private $conn;

    // Table names
    private $content_table = 'content';
    private $articles_table = 'articles';
    private $events_table = 'events';
    private $galleries_table = 'galleries';
    private $videos_table = 'videos';
    private $gallery_images_table = 'gallery_images';
    private $likes_table = 'likes';
    private $comments_table = 'comments';

    // Properties
    public $id;
    public $content_type;
    public $title;
    public $details;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // --- Core Content Functions ---
    public function create()
    {
        if (empty($this->content_type) || empty($this->title)) {
            return false;
        }
        $this->conn->beginTransaction();
        try {
            $query = "INSERT INTO " . $this->content_table . " (content_type, title) VALUES (:content_type, :title)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':content_type', $this->content_type);
            $stmt->bindParam(':title', $this->title);
            $stmt->execute();
            $content_id = $this->conn->lastInsertId();
            switch ($this->content_type) {
                case 'article':
                    $this->createArticle($content_id);
                    break;
                case 'event':
                    $this->createEvent($content_id);
                    break;
                case 'gallery':
                    $this->createGallery($content_id);
                    break;
                case 'video':
                    $this->createVideo($content_id);
                    break;
                default:
                    throw new Exception("Invalid content type.");
            }
            $this->conn->commit();
            return $content_id;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    /**
     * Retrieves a paginated list of all content with necessary details.
     *
     * @param int $limit The number of items to retrieve per page.
     * @param int $offset The starting point for the retrieval.
     * @param string|null $type The content type to filter by.
     * @return array An array of content items.
     */
    public function getAll($limit = 9, $offset = 0, $type = null)
    {
        $whereClause = '';
        if ($type) {
            $whereClause = "WHERE c.content_type = :content_type";
        }

        $query = "
            SELECT 
                c.id, 
                c.content_type, 
                c.title, 
                c.created_at,
                a.body as article_body,
                e.event_date,
                e.banner_url,
                (SELECT image_src FROM gallery_images gi WHERE gi.gallery_id = g.id ORDER BY gi.sort_order ASC LIMIT 1) as gallery_thumbnail,
                v.embedded_src,
                (SELECT COUNT(*) FROM likes l WHERE l.content_id = c.id) as like_count,
                (SELECT COUNT(*) FROM comments co WHERE co.content_id = c.id) as comment_count
            FROM 
                content c
            LEFT JOIN
                articles a ON c.id = a.content_id AND c.content_type = 'article'
            LEFT JOIN 
                events e ON c.id = e.content_id AND c.content_type = 'event'
            LEFT JOIN 
                galleries g ON c.id = g.content_id AND c.content_type = 'gallery'
            LEFT JOIN 
                videos v ON c.id = v.content_id AND c.content_type = 'video'
            {$whereClause}
            ORDER BY 
                c.created_at DESC
            LIMIT :limit OFFSET :offset
        ";
        $stmt = $this->conn->prepare($query);

        if ($type) {
            $stmt->bindParam(':content_type', $type);
        }
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        // For gallery, fetch all images for each gallery item
        if ($type === 'gallery') {
            foreach ($results as &$row) {
                if ($row['content_type'] === 'gallery') {
                    // Get gallery id
                    $galleryIdQuery = "SELECT id FROM galleries WHERE content_id = :content_id LIMIT 1";
                    $galleryIdStmt = $this->conn->prepare($galleryIdQuery);
                    $galleryIdStmt->bindParam(':content_id', $row['id'], PDO::PARAM_INT);
                    $galleryIdStmt->execute();
                    $galleryRow = $galleryIdStmt->fetch(PDO::FETCH_ASSOC);
                    $gallery_id = $galleryRow ? $galleryRow['id'] : null;
                    if ($gallery_id) {
                        $imagesQuery = "SELECT image_src, title FROM gallery_images WHERE gallery_id = :gallery_id ORDER BY sort_order, id";
                        $imagesStmt = $this->conn->prepare($imagesQuery);
                        $imagesStmt->bindParam(':gallery_id', $gallery_id, PDO::PARAM_INT);
                        $imagesStmt->execute();
                        $row['images'] = $imagesStmt->fetchAll(PDO::FETCH_ASSOC);
                    } else {
                        $row['images'] = [];
                    }
                }
            }
        }
        return $results;
    }

    /**
     * Gets the total number of content items for pagination, with an optional filter.
     * @param string|null $type The content type to filter by.
     * @return int The total count of content items.
     */
    public function getTotalCount($type = null)
    {
        $query = "SELECT COUNT(*) as count FROM " . $this->content_table;
        if ($type) {
            $query .= " WHERE content_type = :content_type";
        }
        $stmt = $this->conn->prepare($query);
        if ($type) {
            $stmt->bindParam(':content_type', $type);
        }
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($result['count'] ?? 0);
    }

    public function getById($id, $current_user_id = null)
    {
        $contentData = $this->getBaseContent($id);
        if (!$contentData) {
            return false;
        }
        $details = $this->getContentDetails($id, $contentData['content_type']);
        $contentData['like_count'] = $this->getLikeCount($id);
        $contentData['comments'] = $this->getComments($id);
        $contentData['user_has_liked'] = $current_user_id ? $this->hasUserLiked($id, $current_user_id) : false;
        return array_merge($contentData, $details ?: []);
    }

    public function update($id)
    {
        if (empty($id) || empty($this->title)) {
            return false;
        }
        $this->conn->beginTransaction();
        try {
            $query = "UPDATE " . $this->content_table . " SET title = :title WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':title', $this->title);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $contentType = $this->getBaseContent($id)['content_type'];
            switch ($contentType) {
                case 'article':
                    $this->updateArticle($id);
                    break;
                case 'event':
                    $this->updateEvent($id);
                    break;
                case 'gallery':
                    $this->updateGallery($id);
                    break;
                case 'video':
                    $this->updateVideo($id);
                    break;
            }
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    public function delete($id)
    {
        if (empty($id)) {
            return false;
        }
        $query = "DELETE FROM " . $this->content_table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function addLike($content_id, $user_id)
    {
        $query = "INSERT INTO " . $this->likes_table . " (content_id, user_id) VALUES (:content_id, :user_id)";
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':content_id', $content_id, PDO::PARAM_INT);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }
    public function removeLike($content_id, $user_id)
    {
        $query = "DELETE FROM " . $this->likes_table . " WHERE content_id = :content_id AND user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':content_id', $content_id, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        return $stmt->execute();
    }
    public function addComment($content_id, $user_id, $comment_text)
    {
        $query = "INSERT INTO " . $this->comments_table . " (content_id, user_id, comment) VALUES (:content_id, :user_id, :comment)";
        $stmt = $this->conn->prepare($query);
        $comment_text = htmlspecialchars(strip_tags($comment_text));
        $stmt->bindParam(':content_id', $content_id, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':comment', $comment_text);
        return $stmt->execute();
    }
    public function hasUserLiked($content_id, $user_id)
    {
        $query = "SELECT id FROM " . $this->likes_table . " WHERE content_id = :content_id AND user_id = :user_id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':content_id', $content_id, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    private function getBaseContent($id)
    {
        $query = "SELECT * FROM " . $this->content_table . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    private function getContentDetails($id, $contentType)
    {
        $detailsQuery = '';
        switch ($contentType) {
            case 'article':
                $detailsQuery = "SELECT * FROM " . $this->articles_table . " WHERE content_id = :id";
                break;
            case 'event':
                $detailsQuery = "SELECT * FROM " . $this->events_table . " WHERE content_id = :id";
                break;
            case 'gallery':
                $detailsQuery = "SELECT * FROM " . $this->galleries_table . " WHERE content_id = :id";
                break;
            case 'video':
                $detailsQuery = "SELECT * FROM " . $this->videos_table . " WHERE content_id = :id";
                break;
        }
        if (!$detailsQuery) return [];
        $detailsStmt = $this->conn->prepare($detailsQuery);
        $detailsStmt->bindParam(':id', $id, PDO::PARAM_INT);
        $detailsStmt->execute();
        $details = $detailsStmt->fetch(PDO::FETCH_ASSOC);
        if ($contentType === 'gallery' && $details) {
            $galleryId = $details['id'];
            $imagesQuery = "SELECT image_src, title FROM " . $this->gallery_images_table . " WHERE gallery_id = :gallery_id ORDER BY sort_order";
            $imagesStmt = $this->conn->prepare($imagesQuery);
            $imagesStmt->bindParam(':gallery_id', $galleryId, PDO::PARAM_INT);
            $imagesStmt->execute();
            $details['images'] = $imagesStmt->fetchAll(PDO::FETCH_ASSOC);
        }
        return $details;
    }
    private function getLikeCount($content_id)
    {
        $query = "SELECT COUNT(*) as count FROM " . $this->likes_table . " WHERE content_id = :content_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':content_id', $content_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] ?? 0;
    }
    private function getComments($content_id)
    {
        $query = "SELECT c.comment, c.created_at, u.username FROM " . $this->comments_table . " c JOIN users u ON c.user_id = u.id WHERE c.content_id = :content_id ORDER BY c.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':content_id', $content_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    private function createArticle($content_id)
    {
        $query = "INSERT INTO " . $this->articles_table . " (content_id, author, body) VALUES (:content_id, :author, :body)";
        $stmt = $this->conn->prepare($query);
        $author = htmlspecialchars(strip_tags($this->details['author'] ?? ''));
        $body = htmlspecialchars(strip_tags($this->details['body'] ?? ''));
        $stmt->bindParam(':content_id', $content_id);
        $stmt->bindParam(':author', $author);
        $stmt->bindParam(':body', $body);
        if (!$stmt->execute()) {
            throw new Exception("Failed to create article details.");
        }
    }
    private function createEvent($content_id)
    {
        $banner_url = '';
        if (isset($this->details['banner_file']) && $this->details['banner_file']['error'] === UPLOAD_ERR_OK) {
            $banner_url = $this->handleFileUpload($this->details['banner_file']);
            if ($banner_url === false) {
                throw new Exception("Failed to upload event banner.");
            }
        }
        $query = "INSERT INTO " . $this->events_table . " (content_id, description, event_date, banner_url) VALUES (:content_id, :description, :event_date, :banner_url)";
        $stmt = $this->conn->prepare($query);
        $description = htmlspecialchars(strip_tags($this->details['description'] ?? ''));
        $event_date = htmlspecialchars(strip_tags($this->details['event_date']));
        $stmt->bindParam(':content_id', $content_id);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':event_date', $event_date);
        $stmt->bindParam(':banner_url', $banner_url);
        if (!$stmt->execute()) {
            throw new Exception("Failed to create event details.");
        }
    }
    private function createGallery($content_id)
    {
        $query = "INSERT INTO " . $this->galleries_table . " (content_id, description) VALUES (:content_id, :description)";
        $stmt = $this->conn->prepare($query);
        $description = htmlspecialchars(strip_tags($this->details['description'] ?? ''));
        $stmt->bindParam(':content_id', $content_id);
        $stmt->bindParam(':description', $description);
        if (!$stmt->execute()) {
            throw new Exception("Failed to create gallery details.");
        }
    }
    private function createVideo($content_id)
    {
        $query = "INSERT INTO " . $this->videos_table . " (content_id, uploaded_src, uploaded_description, embedded_src, embedded_description) VALUES (:content_id, :uploaded_src, :uploaded_description, :embedded_src, :embedded_description)";
        $stmt = $this->conn->prepare($query);
        $uploaded_src = htmlspecialchars(strip_tags($this->details['uploaded_src'] ?? ''));
        $uploaded_description = htmlspecialchars(strip_tags($this->details['uploaded_description'] ?? ''));
        $embedded_src = htmlspecialchars(strip_tags($this->details['embedded_src'] ?? ''));
        $embedded_description = htmlspecialchars(strip_tags($this->details['embedded_description'] ?? ''));
        $stmt->bindParam(':content_id', $content_id);
        $stmt->bindParam(':uploaded_src', $uploaded_src);
        $stmt->bindParam(':uploaded_description', $uploaded_description);
        $stmt->bindParam(':embedded_src', $embedded_src);
        $stmt->bindParam(':embedded_description', $embedded_description);
        if (!$stmt->execute()) {
            throw new Exception("Failed to create video details.");
        }
    }
    private function handleFileUpload($file, $uploadDir = 'uploads/')
    {
        if (!isset($file['error']) || is_array($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
            return false;
        }
        if ($file['size'] > 5 * 1024 * 1024) {
            return false;
        }
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($finfo->file($file['tmp_name']), $allowedTypes)) {
            return false;
        }
        $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $uniqueName = bin2hex(random_bytes(8)) . '_' . time() . '.' . $fileExtension;
        $destination = $uploadDir . $uniqueName;
        if (!is_dir($uploadDir)) {
            if (!mkdir($uploadDir, 0755, true)) {
                return false;
            }
        }
        if (move_uploaded_file($file['tmp_name'], $destination)) {
            return $destination;
        }
        return false;
    }

    private function updateArticle($content_id)
    {
        $query = "UPDATE " . $this->articles_table . " SET author = :author, body = :body WHERE content_id = :content_id";
        $stmt = $this->conn->prepare($query);
        $author = htmlspecialchars(strip_tags($this->details['author'] ?? ''));
        $body = htmlspecialchars(strip_tags($this->details['body'] ?? ''));
        $stmt->bindParam(':author', $author);
        $stmt->bindParam(':body', $body);
        $stmt->bindParam(':content_id', $content_id, PDO::PARAM_INT);
        if (!$stmt->execute()) {
            throw new Exception("Failed to update article details.");
        }
    }
    private function updateEvent($content_id)
    {
        $query = "UPDATE " . $this->events_table . " SET description = :description, event_date = :event_date WHERE content_id = :content_id";
        $stmt = $this->conn->prepare($query);
        $description = htmlspecialchars(strip_tags($this->details['description'] ?? ''));
        $event_date = htmlspecialchars(strip_tags($this->details['event_date']));
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':event_date', $event_date);
        $stmt->bindParam(':content_id', $content_id, PDO::PARAM_INT);
        if (!$stmt->execute()) {
            throw new Exception("Failed to update event details.");
        }
    }
    private function updateGallery($content_id)
    {
        $query = "UPDATE " . $this->galleries_table . " SET description = :description WHERE content_id = :content_id";
        $stmt = $this->conn->prepare($query);
        $description = htmlspecialchars(strip_tags($this->details['description'] ?? ''));
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':content_id', $content_id, PDO::PARAM_INT);
        if (!$stmt->execute()) {
            throw new Exception("Failed to update gallery details.");
        }
    }
    private function updateVideo($content_id)
    {
        $query = "UPDATE " . $this->videos_table . " SET uploaded_src = :uploaded_src, uploaded_description = :uploaded_description, embedded_src = :embedded_src, embedded_description = :embedded_description WHERE content_id = :content_id";
        $stmt = $this->conn->prepare($query);
        $uploaded_src = htmlspecialchars(strip_tags($this->details['uploaded_src'] ?? ''));
        $uploaded_description = htmlspecialchars(strip_tags($this->details['uploaded_description'] ?? ''));
        $embedded_src = htmlspecialchars(strip_tags($this->details['embedded_src'] ?? ''));
        $embedded_description = htmlspecialchars(strip_tags($this->details['embedded_description'] ?? ''));
        $stmt->bindParam(':uploaded_src', $uploaded_src);
        $stmt->bindParam(':uploaded_description', $uploaded_description);
        $stmt->bindParam(':embedded_src', $embedded_src);
        $stmt->bindParam(':embedded_description', $embedded_description);
        $stmt->bindParam(':content_id', $content_id, PDO::PARAM_INT);
        if (!$stmt->execute()) {
            throw new Exception("Failed to update video details.");
        }
    }
}
