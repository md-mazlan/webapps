
<?php
// app/controllers/NewsController.php
require_once ROOT_PATH . '/app/models/News.php';

class NewsController {
    private $db;
    public function __construct($db) {
        $this->db = $db;
    }

    public function getAll() {
        $stmt = $this->db->query('SELECT * FROM news ORDER BY published_at DESC, id DESC');
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $newsList = [];
        foreach ($rows as $row) {
            $newsList[] = new News(
                $row['id'] ?? null,
                $row['title'] ?? null,
                $row['body'] ?? null,
                $row['image_url'] ?? null,
                $row['published_at'] ?? null,
                $row['created_at'] ?? null,
                $row['updated_at'] ?? null
            );
        }
        return $newsList;
    }

    public function getById($id) {
        $stmt = $this->db->prepare('SELECT * FROM news WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return new News(
                $row['id'] ?? null,
                $row['title'] ?? null,
                $row['body'] ?? null,
                $row['image_url'] ?? null,
                $row['published_at'] ?? null,
                $row['created_at'] ?? null,
                $row['updated_at'] ?? null
            );
        }
        return null;
    }

    public function create($data) {
        $stmt = $this->db->prepare('INSERT INTO news (title, body, image_url, published_at) VALUES (?, ?, ?, ?)');
        return $stmt->execute([
            $data['title'],
            $data['body'],
            $data['image_url'],
            $data['published_at']
        ]);
    }

    public function update($id, $data) {
        $stmt = $this->db->prepare('UPDATE news SET title = ?, body = ?, image_url = ?, published_at = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?');
        return $stmt->execute([
            $data['title'],
            $data['body'],
            $data['image_url'],
            $data['published_at'],
            $id
        ]);
    }

    public function delete($id) {
        $stmt = $this->db->prepare('DELETE FROM news WHERE id = ?');
        return $stmt->execute([$id]);
    }
}
