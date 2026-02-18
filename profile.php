<?php
/**
 * User Profile Page
 */

session_start();
$base_url = '';
$page_title = 'Profile - SIPP';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once 'config/Database.php';
require_once 'models/User.php';
require_once 'models/Result.php';

$database = new Database();
$connection = $database->connect();

$user_model = new User($connection);
$result_model = new Result($connection);

$user_id = $_SESSION['user_id'];
$user = $user_model->getUserById($user_id);
$stats = $result_model->getDashboardStats($user_id);

include 'views/header.php';
?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="bi bi-person-circle"></i> User Profile
                </h5>
            </div>
            <div class="card-body">
                <div class="row mb-5">
                    <div class="col-md-4 text-center mb-4">
                        <div class="avatar-placeholder" style="width: 120px; height: 120px; background: linear-gradient(135deg, #0d6efd, #0b5ed7); border-radius: 50%; margin: 0 auto; display: flex; align-items: center; justify-content: center;">
                            <i class="bi bi-person-fill text-white" style="font-size: 3rem;"></i>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <h4 class="fw-bold mb-1"><?php echo htmlspecialchars($user['full_name']); ?></h4>
                        <p class="text-muted mb-3">@<?php echo htmlspecialchars($user['username']); ?></p>
                        
                        <div class="mb-2">
                            <p class="text-muted small mb-1">Email</p>
                            <p><?php echo htmlspecialchars($user['email']); ?></p>
                        </div>
                        
                        <div class="mb-2">
                            <p class="text-muted small mb-1">Member Since</p>
                            <p><?php echo date('F d, Y', strtotime($user['created_at'])); ?></p>
                        </div>
                    </div>
                </div>
                
                <!-- Statistics -->
                <h6 class="mb-3 fw-bold">Performance Statistics</h6>
                <div class="row g-3 mb-5">
                    <div class="col-md-6">
                        <div class="card stat-card">
                            <div class="stat-number"><?php echo $stats['total_tests']; ?></div>
                            <div class="stat-label">Tests Taken</div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card stat-card success">
                            <div class="stat-number"><?php echo $stats['average_accuracy']; ?>%</div>
                            <div class="stat-label">Average Accuracy</div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card stat-card info">
                            <div class="stat-number"><?php echo $stats['best_score']; ?></div>
                            <div class="stat-label">Best Score</div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card stat-card warning">
                            <div class="stat-number"><?php echo $stats['average_score']; ?></div>
                            <div class="stat-label">Average Score</div>
                        </div>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="row g-2">
                    <div class="col-md-6">
                        <a href="dashboard.php" class="btn btn-primary w-100">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                    </div>
                    <div class="col-md-6">
                        <a href="results.php" class="btn btn-info w-100">
                            <i class="bi bi-history"></i> View Results
                        </a>
                    </div>
                    <div class="col-md-6">
                        <a href="test.php" class="btn btn-success w-100">
                            <i class="bi bi-pencil-square"></i> Take Test
                        </a>
                    </div>
                    <div class="col-md-6">
                        <a href="logout.php" class="btn btn-danger w-100">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'views/footer.php'; ?>
