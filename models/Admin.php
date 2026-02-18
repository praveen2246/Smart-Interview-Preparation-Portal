<?php
/**
 * Admin Model Class
 * Handles admin authentication and operations
 */

class Admin {
    private $connection;
    private $table = 'admin';
    
    public $id;
    public $username;
    public $email;
    public $password;
    public $full_name;
    
    public function __construct($connection) {
        $this->connection = $connection;
    }
    
    /**
     * Admin login
     */
    public function login($username, $password) {
        // Validate inputs
        if (empty($username) || empty($password)) {
            return array('success' => false, 'message' => 'Username and password required');
        }
        
        // Prepare statement
        $stmt = $this->connection->prepare("SELECT id, username, email, password, full_name FROM $this->table WHERE username = ?");
        
        if (!$stmt) {
            return array('success' => false, 'message' => 'Database error');
        }
        
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            $stmt->close();
            return array('success' => false, 'message' => 'Invalid username or password');
        }
        
        $admin = $result->fetch_assoc();
        $stmt->close();
        
        // Verify password
        if (!password_verify($password, $admin['password'])) {
            return array('success' => false, 'message' => 'Invalid username or password');
        }
        
        // Start session and store admin data
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_username'] = $admin['username'];
        $_SESSION['admin_email'] = $admin['email'];
        $_SESSION['admin_full_name'] = $admin['full_name'];
        $_SESSION['user_type'] = 'admin';
        
        return array('success' => true, 'message' => 'Login successful', 'admin' => $admin);
    }
    
    /**
     * Get admin by ID
     */
    public function getAdminById($id) {
        $stmt = $this->connection->prepare("SELECT id, username, email, full_name FROM $this->table WHERE id = ?");
        
        if (!$stmt) {
            return false;
        }
        
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $admin = $result->fetch_assoc();
            $stmt->close();
            return $admin;
        }
        
        $stmt->close();
        return false;
    }
    
    /**
     * Check if admin exists (for initial setup)
     */
    public function adminExists() {
        $stmt = $this->connection->prepare("SELECT COUNT(*) as count FROM $this->table");
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        return $row['count'] > 0;
    }
    
    /**
     * Create default admin account (one-time setup)
     */
    public function createDefaultAdmin($username, $password, $email, $full_name) {
        if ($this->adminExists()) {
            return array('success' => false, 'message' => 'Admin account already exists');
        }
        
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        
        // Prepare statement
        $stmt = $this->connection->prepare("INSERT INTO $this->table (username, password, email, full_name) VALUES (?, ?, ?, ?)");
        
        if (!$stmt) {
            return array('success' => false, 'message' => 'Database error: ' . $this->connection->error);
        }
        
        $stmt->bind_param("ssss", $username, $hashed_password, $email, $full_name);
        
        if ($stmt->execute()) {
            $stmt->close();
            return array('success' => true, 'message' => 'Admin account created successfully');
        } else {
            $stmt->close();
            return array('success' => false, 'message' => 'Failed to create admin account: ' . $this->connection->error);
        }
    }
}
?>
