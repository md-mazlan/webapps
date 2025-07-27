<?php

/**
 * File: /app/models/StateManager.php
 * Manages all database CRUD operations for the `states` table using PDO.
 */

// Use ROOT_PATH for reliable file inclusion.
require_once ROOT_PATH . '/app/models/State.php';

class StateManager
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

    /**
     * READ: Fetches a single state by its ID.
     * @param int $id The ID of the state.
     * @return State|null The state object or null if not found.
     */
    public function getById(int $id): ?State
    {
        $sql = "SELECT id, name FROM states WHERE id = :id";
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':id' => $id]);
            // Fetch the result directly into a new instance of the State class.
            $stmt->setFetchMode(PDO::FETCH_CLASS, 'State');
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
        $sql = "SELECT id, name FROM states ORDER BY name ASC";
        $states_array = []; // Initialize an empty array to hold the objects
        try {
            $stmt = $this->conn->query($sql);
            // Loop through each row of the result set
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                // Create a new State object for each row and add it to the array
                $states_array[] = new State($row['id'], $row['name']);
            }
        } catch (PDOException $e) {
            // Production-ready: return an empty array on error to prevent crashes.
            return [];
        }
        // Return the manually constructed array of State objects
        return $states_array;
    }

    /**
     * UPDATE: Updates an existing state in the database.
     * @param State $state The state object with updated data.
     * @return bool True on success, false on failure.
     */
    public function update(State $state): bool
    {
        $sql = "UPDATE states SET name = :name WHERE id = :id";
        try {
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([
                ':name' => $state->name,
                ':id' => $state->id
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * DELETE: Deletes a state from the database.
     * @param int $id The ID of the state to delete.
     * @return bool True on success, false on failure.
     */
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
