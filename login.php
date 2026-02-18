<?php
/**
 * User Login Page
 */

session_start();
$base_url = '';
$page_title = 'Login - SIPP';

require_once 'config/Database.php';
require_once 'models/User.php';
require_once 'controllers/AuthController.php';

// Get database connection
$database = new Database();
$connection = $database->connect();

$user = new User($connection);
$auth = new AuthController($user, null);

$error = '';

// Handle login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    $result = $auth->loginUser($username, $password);
    
    if ($result['success']) {
        header('Location: dashboard.php');
        exit;
    } else {
        $error = $result['message'];
    }
}

include 'views/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card">
            <div class="card-body p-5">
                <h2 class="card-title text-center mb-4">User Login</h2>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <form method="POST">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required 
                               value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"
                               autofocus>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100 py-2">
                        <i class="bi bi-box-arrow-in-right"></i> Sign In
                    </button>
                </form>
                
                <div class="text-center mt-4">
                    <p class="text-muted mb-3">Don't have an account? 
                        <a href="register.php" class="text-primary fw-bold">Create one</a>
                    </p>
                    <a href="admin/login.php" class="text-secondary text-decoration-none small">Admin Login</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'views/footer.php'; ?>
