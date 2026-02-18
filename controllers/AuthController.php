<?php
/**
 * Auth Controller
 * Handles user registration, login, and logout
 */

class AuthController {
    private $user;
    private $admin;
    
    public function __construct($user, $admin) {
        $this->user = $user;
        $this->admin = $admin;
    }
    
    /**
     * Handle user registration
     */
    public function registerUser($username, $email, $password, $confirm_password, $full_name) {
        // Validate passwords match
        if ($password !== $confirm_password) {
            return array('success' => false, 'message' => 'Passwords do not match');
        }
        
        // Register user using model
        return $this->user->register($username, $email, $password, $full_name);
    }
    
    /**
     * Handle user login
     */
    public function loginUser($username, $password) {
        return $this->user->login($username, $password);
    }
    
    /**
     * Handle admin login
     */
    public function loginAdmin($username, $password) {
        return $this->admin->login($username, $password);
    }
    
    /**
     * Logout user
     */
    public function logout() {
        session_start();
        session_destroy();
        return true;
    }
    
    /**
     * Check if user is logged in
     */
    public static function isUserLoggedIn() {
        return isset($_SESSION['user_id']) && isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'user';
    }
    
    /**
     * Check if admin is logged in
     */
    public static function isAdminLoggedIn() {
        return isset($_SESSION['admin_id']) && isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin';
    }
    
    /**
     * Get logged in user ID
     */
    public static function getUserId() {
        return isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    }
    
    /**
     * Get logged in admin ID
     */
    public static function getAdminId() {
        return isset($_SESSION['admin_id']) ? $_SESSION['admin_id'] : null;
    }
}
?>
