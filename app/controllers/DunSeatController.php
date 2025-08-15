<?php
require_once ROOT_PATH . '/app/models/DunSeat.php';

class DunSeatController
{
    private PDO $conn;
    public function __construct(PDO $db_connection)
    {
        $this->conn = $db_connection;
    }
    public function create(DunSeat $seat): bool
    {
        $sql = "INSERT INTO sabah_dun_seats (code, seat) VALUES (:code, :seat)";
        try {
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([':code' => $seat->code, ':seat' => $seat->seat]);
        } catch (PDOException $e) {
            return false;
        }
    }
    public function getByCode(string $code): ?DunSeat
    {
        $sql = "SELECT code, seat FROM sabah_dun_seats WHERE code = :code";
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':code' => $code]);
            $stmt->setFetchMode(PDO::FETCH_CLASS, 'DunSeat');
            $result = $stmt->fetch();
            return $result ?: null;
        } catch (PDOException $e) {
            return null;
        }
    }
    public function getAll(): array
    {
        $sql = "SELECT code, seat FROM sabah_dun_seats ORDER BY code ASC";
        $seats_array = [];
        try {
            $stmt = $this->conn->query($sql);
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $seats_array[] = new DunSeat($row['code'], $row['seat']);
            }
        } catch (PDOException $e) {}
        return $seats_array;
    }
    public function update(DunSeat $seat, string $originalCode): bool
    {
        $sql = "UPDATE sabah_dun_seats SET code = :code, seat = :seat WHERE code = :originalCode";
        try {
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([':code' => $seat->code, ':seat' => $seat->seat, ':originalCode' => $originalCode]);
        } catch (PDOException $e) {
            return false;
        }
    }
    public function delete(string $code): bool
    {
        $sql = "DELETE FROM sabah_dun_seats WHERE code = :code";
        try {
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([':code' => $code]);
        } catch (PDOException $e) {
            return false;
        }
    }
}
