<?php
/**
 * User Model Class
 * Handles user registration, login, and authentication
 */

class User {
    private $connection;
    private $table = 'users';
    
    public $id;
    public $username;
    public $email;
    public $password;
    public $full_name;
    public $created_at;
    
    public function __construct($connection) {
        $this->connection = $connection;
    }
    
    /**
     * Register a new user
     */
    public function register($username, $email, $password, $full_name) {
        // Validate inputs
        if (!$this->validateUsername($username)) {
            return array('success' => false, 'message' => 'Username must be 3-50 characters');
        }
        
        if (!$this->validateEmail($email)) {
            return array('success' => false, 'message' => 'Invalid email format');
        }
        
        if (!$this->validatePassword($password)) {
            return array('success' => false, 'message' => 'Password must be at least 6 characters');
        }
        
        // Check if username already exists
        if ($this->userExists($username)) {
            return array('success' => false, 'message' => 'Username already taken');
        }
        
        // Check if email already exists
        if ($this->emailExists($email)) {
            return array('success' => false, 'message' => 'Email already registered');
        }
        
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        
        // Prepare statement
        $stmt = $this->connection->prepare("INSERT INTO $this->table (username, email, password, full_name) VALUES (?, ?, ?, ?)");
        
        if (!$stmt) {
            return array('success' => false, 'message' => 'Database error: ' . $this->connection->error);
        }
        
        // Bind parameters
        $stmt->bind_param("ssss", $username, $email, $hashed_password, $full_name);
        
        // Execute
        if ($stmt->execute()) {
            $stmt->close();
            return array('success' => true, 'message' => 'Registration successful');
        } else {
            $stmt->close();
            return array('success' => false, 'message' => 'Registration failed: ' . $this->connection->error);
        }
    }
    
    /**
     * Login user
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
        
        $user = $result->fetch_assoc();
        $stmt->close();
        
        // Verify password
        if (!password_verify($password, $user['password'])) {
            return array('success' => false, 'message' => 'Invalid username or password');
        }
        
        // Start session and store user data
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['user_type'] = 'user';
        
        return array('success' => true, 'message' => 'Login successful', 'user' => $user);
    }
    
    /**
     * Get user by ID
     */
    public function getUserById($id) {
        $stmt = $this->connection->prepare("SELECT id, username, email, full_name, created_at FROM $this->table WHERE id = ?");
        
        if (!$stmt) {
            return false;
        }
        
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            $stmt->close();
            return $user;
        }
        
        $stmt->close();
        return false;
    }
    
    /**
     * Check if username exists
     */
    private function userExists($username) {
        $stmt = $this->connection->prepare("SELECT id FROM $this->table WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $exists = $result->num_rows > 0;
        $stmt->close();
        return $exists;
    }
    
    /**
     * Check if email exists
     */
    private function emailExists($email) {
        $stmt = $this->connection->prepare("SELECT id FROM $this->table WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $exists = $result->num_rows > 0;
        $stmt->close();
        return $exists;
    }
    
    /**
     * Validate username
     */
    private function validateUsername($username) {
        return strlen($username) >= 3 && strlen($username) <= 50 && preg_match('/^[a-zA-Z0-9_]+$/', $username);
    }
    
    /**
     * Validate email
     */
    private function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    /**
     * Validate password
     */
    private function validatePassword($password) {
        return strlen($password) >= 6;
    }
}
?>
