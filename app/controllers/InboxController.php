<?php
// app/controllers/InboxController.php
require_once ROOT_PATH . '/app/models/Inbox.php';
require_once ROOT_PATH . '/php/database.php';

class InboxController {
    private $inboxModel;

    public function __construct() {
        $db = (new Database())->connect();
        $this->inboxModel = new Inbox($db);
    }

    public function getUserMessages($user_id) {
        return $this->inboxModel->getMessagesByUserId($user_id);
    }

    public function getMessage($id, $user_id) {
        return $this->inboxModel->getMessageById($id, $user_id);
    }

    public function markMessageAsRead($id, $user_id) {
        return $this->inboxModel->markAsRead($id, $user_id);
    }

    public function deleteMessage($id, $user_id) {
        return $this->inboxModel->deleteMessage($id, $user_id);
    }
}
