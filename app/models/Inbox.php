<?php
// app/models/Inbox.php
class Inbox {
    private $db;
    public function __construct($db) {
        $this->db = $db;
    }

    public function getMessagesByUserId($user_id) {
        $stmt = $this->db->prepare('SELECT * FROM inbox WHERE user_id = ? ORDER BY created_at DESC');
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getMessageById($id, $user_id) {
        $stmt = $this->db->prepare('SELECT * FROM inbox WHERE id = ? AND user_id = ?');
        $stmt->execute([$id, $user_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function markAsRead($id, $user_id) {
        $stmt = $this->db->prepare('UPDATE inbox SET is_read = 1 WHERE id = ? AND user_id = ?');
        return $stmt->execute([$id, $user_id]);
    }

    public function deleteMessage($id, $user_id) {
        $stmt = $this->db->prepare('DELETE FROM inbox WHERE id = ? AND user_id = ?');
        return $stmt->execute([$id, $user_id]);
    }
}
