<?php

/**
 * Database Class
 *
 * Handles the database connection using PDO.
 * This ensures a single, reusable connection point.
 */
class Database
{
    // Database credentials
    // IMPORTANT: Replace with your actual database details
    private $host = 'localhost';
    private $db_name = 'app'; // The database you created with users.sql
    private $username = 'root';      // Your database username
    private $password = '';      // Your database password
    private $conn;

    /**
     * Establishes the database connection.
     *
     * @return PDO|null Returns the PDO connection object on success, or null on failure.
     */
    public function connect()
    {
        $this->conn = null;

        try {
            // Data Source Name (DSN) for the PDO connection
            $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->db_name . ';charset=utf8mb4';

            // Create a new PDO instance
            $this->conn = new PDO($dsn, $this->username, $this->password);

            // Set PDO attributes for error handling and fetch mode
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $this->conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        } catch (PDOException $e) {
            // In a real application, you would log this error, not echo it
            echo 'Connection Error: ' . $e->getMessage();
        }

        return $this->conn;
    }
}
