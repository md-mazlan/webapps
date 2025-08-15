<?php
require_once ROOT_PATH . '/app/models/State.php';

class StateController
{
    private PDO $conn;
    public function __construct(PDO $db_connection)
    {
        $this->conn = $db_connection;
    }
    public function create(State $state): bool
    {
        $sql = "INSERT INTO states (name) VALUES (:name)";
        try {
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([':name' => $state->name]);
        } catch (PDOException $e) {
            return false;
        }
    }
    public function getById(int $id): ?State
    {
        $sql = "SELECT id, name FROM states WHERE id = :id";
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':id' => $id]);
            $stmt->setFetchMode(PDO::FETCH_CLASS, 'State');
            $result = $stmt->fetch();
            return $result ?: null;
        } catch (PDOException $e) {
            return null;
        }
    }
    public function getAll(): array
    {
        $sql = "SELECT id, name FROM states ORDER BY name ASC";
        $states_array = [];
        try {
            $stmt = $this->conn->query($sql);
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $states_array[] = new State($row['id'], $row['name']);
            }
        } catch (PDOException $e) {}
        return $states_array;
    }
    public function update(State $state): bool
    {
        $sql = "UPDATE states SET name = :name WHERE id = :id";
        try {
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([':name' => $state->name, ':id' => $state->id]);
        } catch (PDOException $e) {
            return false;
        }
    }
    public function delete(int $id): bool
    {
        $sql = "DELETE FROM states WHERE id = :id";
        try {
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            return false;
        }
    }
}
