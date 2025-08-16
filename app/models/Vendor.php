<?php
// app/models/Vendor.php
class Vendor {
    private $db;
    public function __construct($db) {
        $this->db = $db;
    }

    public function getAll() {
        $stmt = $this->db->query('SELECT * FROM vendor ORDER BY created_at DESC');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->db->prepare('SELECT * FROM vendor WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $stmt = $this->db->prepare('INSERT INTO vendor (name, location, discount, offering) VALUES (?, ?, ?, ?)');
        return $stmt->execute([
            $data['name'],
            $data['location'],
            $data['discount'],
            $data['offering']
        ]);
    }

    public function update($id, $data) {
        $stmt = $this->db->prepare('UPDATE vendor SET name = ?, location = ?, discount = ?, offering = ? WHERE id = ?');
        return $stmt->execute([
            $data['name'],
            $data['location'],
            $data['discount'],
            $data['offering'],
            $id
        ]);
    }

    public function delete($id) {
        $stmt = $this->db->prepare('DELETE FROM vendor WHERE id = ?');
        return $stmt->execute([$id]);
    }
}
