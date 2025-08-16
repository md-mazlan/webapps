<?php
// app/controllers/ContentController.php
require_once ROOT_PATH . '/app/models/Content.php';
require_once ROOT_PATH . '/php/database.php';

class ContentController {
    private $contentModel;

    public function __construct() {
        $db = (new Database())->connect();
        $this->contentModel = new Content($db);
    }

    public function create($data) {
        $this->contentModel->content_type = $data['content_type'] ?? null;
        $this->contentModel->title = $data['title'] ?? null;
        $this->contentModel->details = $data['details'] ?? [];
        return $this->contentModel->create();
    }

    public function getAll($limit = 9, $offset = 0, $type = null) {
        return $this->contentModel->getAll($limit, $offset, $type);
    }

    public function getTotalCount($type = null) {
        return $this->contentModel->getTotalCount($type);
    }

    public function getById($id, $current_user_id = null) {
        return $this->contentModel->getById($id, $current_user_id);
    }

    public function update($id, $data) {
        $this->contentModel->title = $data['title'] ?? null;
        $this->contentModel->details = $data['details'] ?? [];
        return $this->contentModel->update($id);
    }

    public function delete($id) {
        return $this->contentModel->delete($id);
    }

    public function addLike($content_id, $user_id) {
        return $this->contentModel->addLike($content_id, $user_id);
    }

    public function removeLike($content_id, $user_id) {
        return $this->contentModel->removeLike($content_id, $user_id);
    }

    public function addComment($content_id, $user_id, $comment_text) {
        return $this->contentModel->addComment($content_id, $user_id, $comment_text);
    }

    public function hasUserLiked($content_id, $user_id) {
        return $this->contentModel->hasUserLiked($content_id, $user_id);
    }
}
