<?php
/**
 * Database Configuration and Connection Class
 * Handles all database operations using prepared statements for security
 */

class Database {
    
    // Database connection details
    private $host = 'localhost';
    private $db_name = 'sipp';
    private $db_user = 'root';
    private $db_pass = '';
    private $connection;
    
    /**
     * Connect to database
     */
    public function connect() {
        try {
            $this->connection = new mysqli(
                $this->host,
                $this->db_user,
                $this->db_pass,
                $this->db_name
            );
            
            // Check connection
            if ($this->connection->connect_error) {
                throw new Exception("Connection Error: " . $this->connection->connect_error);
            }
            
            // Set charset to utf8mb4
            $this->connection->set_charset("utf8mb4");
            
            return $this->connection;
        } catch (Exception $e) {
            die("Database Connection Error: " . $e->getMessage());
        }
    }
    
    /**
     * Get database connection
     */
    public function getConnection() {
        if (!$this->connection) {
            $this->connect();
        }
        return $this->connection;
    }
    
    /**
     * Execute prepared statement safely
     */
    public function prepare($query) {
        return $this->getConnection()->prepare($query);
    }
    
    /**
     * Escape string to prevent SQL injection
     */
    public function escape($string) {
        return $this->getConnection()->real_escape_string($string);
    }
    
    /**
     * Close database connection
     */
    public function closeConnection() {
        if ($this->connection) {
            $this->connection->close();
        }
    }
}

// Create global database instance
$db = new Database();
$connection = $db->connect();
?>
