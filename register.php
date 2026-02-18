<?php
/**
 * User Registration Page
 */

session_start();
$base_url = '';
$page_title = 'Register - SIPP';

require_once 'config/Database.php';
require_once 'models/User.php';
require_once 'controllers/AuthController.php';

// Get database connection
$database = new Database();
$connection = $database->connect();

$user = new User($connection);
$auth = new AuthController($user, null);

$error = '';
$success = '';

// Handle registration
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $full_name = trim($_POST['full_name'] ?? '');
    
    $result = $auth->registerUser($username, $email, $password, $confirm_password, $full_name);
    
    if ($result['success']) {
        $success = $result['message'];
        echo '<script>
            setTimeout(() => {
                window.location.href = "login.php";
            }, 2000);
        </script>';
    } else {
        $error = $result['message'];
    }
}

include 'views/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body p-5">
                <h2 class="card-title text-center mb-4">Create Account</h2>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle"></i> <?php echo htmlspecialchars($success); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <form method="POST" id="registerForm">
                    <div class="mb-3">
                        <label for="full_name" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="full_name" name="full_name" required 
                               value="<?php echo htmlspecialchars($_POST['full_name'] ?? ''); ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required 
                               placeholder="3-50 characters, alphanumeric and underscore"
                               value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
                        <small class="text-muted">Letters, numbers, and underscore only</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="email" name="email" required 
                               value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required 
                               placeholder="Minimum 6 characters">
                        <small class="text-muted">At least 6 characters for security</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirm Password</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100 py-2">
                        <i class="bi bi-person-plus"></i> Register
                    </button>
                </form>
                
                <div class="text-center mt-4">
                    <p class="text-muted">Already have an account? 
                        <a href="login.php" class="text-primary fw-bold">Sign In</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'views/footer.php'; ?>
