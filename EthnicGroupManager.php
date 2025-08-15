<?php

/**
 * File: /app/models/EthnicGroupManager.php
 * Manages all database operations for the `sabah_ethnic_groups` table using PDO.
 */

// It's good practice to require the model this manager is responsible for.
require_once ROOT_PATH . '/app/models/EthnicGroup.php';

class EthnicGroupManager
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
     * CREATE: Adds a new ethnic group to the database.
     * @param EthnicGroup $ethnicGroup The ethnic group object to create.
     * @return bool True on success, false on failure.
     */
    public function create(EthnicGroup $ethnicGroup): bool
    {
        $sql = "INSERT INTO sabah_ethnic_groups (name, category) VALUES (:name, :category)";
        try {
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([
                ':name' => $ethnicGroup->name,
                ':category' => $ethnicGroup->category
            ]);
        } catch (PDOException $e) {
            // Optional: Log the error message, e.g., error_log($e->getMessage());
            return false;
        }
    }

    /**
     * READ: Fetches a single ethnic group by its ID.
     * @param int $id The ID of the ethnic group.
     * @return EthnicGroup|null The ethnic group object or null if not found.
     */
    public function getById(int $id): ?EthnicGroup
    {
        $sql = "SELECT id, name, category FROM sabah_ethnic_groups WHERE id = :id";
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':id' => $id]);
            // Fetch the result directly into a new instance of the EthnicGroup class.
            $stmt->setFetchMode(PDO::FETCH_CLASS, 'EthnicGroup');
            $result = $stmt->fetch();
            return $result ?: null;
        } catch (PDOException $e) {
            return null;
        }
    }

    /**
     * READ ALL: Fetches all ethnic groups, with an option to group by category.
     * @param bool $grouped If true, returns a nested array grouped by category.
     * @return array An array of EthnicGroup objects or a grouped array.
     */
    public function getAll(bool $grouped = false): array
    {
        $sql = "SELECT id, name, category FROM sabah_ethnic_groups ORDER BY category, name";
        $groups = [];
        try {
            $stmt = $this->conn->query($sql);
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $ethnicObject = new EthnicGroup($row['id'], $row['name'], $row['category']);
                if ($grouped) {
                    $groups[$row['category']][] = $ethnicObject;
                } else {
                    $groups[] = $ethnicObject;
                }
            }
        } catch (PDOException $e) {
            // Return an empty array on error
            return [];
        }
        return $groups;
    }

    /**
     * UPDATE: Updates an existing ethnic group in the database.
     * @param EthnicGroup $ethnicGroup The ethnic group object with updated data.
     * @return bool True on success, false on failure.
     */
    public function update(EthnicGroup $ethnicGroup): bool
    {
        $sql = "UPDATE sabah_ethnic_groups SET name = :name, category = :category WHERE id = :id";
        try {
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([
                ':name' => $ethnicGroup->name,
                ':category' => $ethnicGroup->category,
                ':id' => $ethnicGroup->id
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * DELETE: Deletes an ethnic group from the database.
     * @param int $id The ID of the ethnic group to delete.
     * @return bool True on success, false on failure.
     */
    public function delete(int $id): bool
    {
        $sql = "DELETE FROM sabah_ethnic_groups WHERE id = :id";
        try {
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            return false;
        }
    }
}
