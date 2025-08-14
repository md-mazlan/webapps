<?php

/**
 * File: /app/models/StateManager.php
 * Manages all database CRUD operations for the `states` table using PDO.
 */

// Use ROOT_PATH for reliable file inclusion.
require_once ROOT_PATH . '/app/models/DunSeat.php';

class DunSeatManager
{
    /**
     * @var PDO The active PDO database connection.
     */
    private PDO $conn;

    /**
     * Constructor that requires an existing PDO database connection.
     * This connection should come from your Database class's connect() method.
     * @param PDO $db_connection The active PDO connection object.
     */
    public function __construct(PDO $db_connection)
    {
        $this->conn = $db_connection;
    }

    /**
     * CREATE: Adds a new state to the database.
     * @param State $state The state object to create.
     * @return bool True on success, false on failure.
     */
    public function create(State $state): bool
    {
        $sql = "INSERT INTO states (name) VALUES (:name)";
        try {
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([':name' => $state->name]);
        } catch (PDOException $e) {
            // In a real application, you might log this error.
            // error_log('State creation failed: ' . $e->getMessage());
            return false;
        }
    }

    // Fetch a single DUN seat by code
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

    /**
     * READ ALL: Fetches all states from the database, sorted by name.
     * @return array An array of State objects.
     */
    public function getAll(): array
    {
        $sql = "SELECT code, seat FROM sabah_dun_seats ORDER BY seat ASC";
        $states_array = []; // Initialize an empty array to hold the objects
        try {
            $stmt = $this->conn->query($sql);
            // Loop through each row of the result set
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                // Create a new State object for each row and add it to the array
                $states_array[] = new DunSeat($row['code'], $row['seat']);
            }
        } catch (PDOException $e) {
            // Production-ready: return an empty array on error to prevent crashes.
            return [];
        }
        // Return the manually constructed array of State objects
        return $states_array;
    }

    // Update a DUN seat by code
    public function update(DunSeat $seat, string $originalCode): bool
    {
        $sql = "UPDATE sabah_dun_seats SET code = :code, seat = :seat WHERE code = :originalCode";
        try {
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([
                ':code' => $seat->code,
                ':seat' => $seat->seat,
                ':originalCode' => $originalCode
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    // Delete a DUN seat by code
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
