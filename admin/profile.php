<?php
/**
 * Admin Profile Page
 */

session_start();
$base_url = '..';
$page_title = 'Profile - Admin';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

require_once '../config/Database.php';
require_once '../models/Admin.php';

$database = new Database();
$connection = $database->connect();

$admin_model = new Admin($connection);

$admin_id = $_SESSION['admin_id'];
$admin = $admin_model->getAdminById($admin_id);

include '../views/header.php';
?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="bi bi-gear"></i> Admin Profile
                </h5>
            </div>
            <div class="card-body">
                <div class="row mb-5">
                    <div class="col-md-4 text-center mb-4">
                        <div class="avatar-placeholder" style="width: 120px; height: 120px; background: linear-gradient(135deg, #0d6efd, #0b5ed7); border-radius: 50%; margin: 0 auto; display: flex; align-items: center; justify-content: center;">
                            <i class="bi bi-shield-check text-white" style="font-size: 3rem;"></i>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <h4 class="fw-bold mb-1"><?php echo htmlspecialchars($admin['full_name']); ?></h4>
                        <p class="text-muted mb-3">Administrator</p>
                        
                        <div class="mb-2">
                            <p class="text-muted small mb-1">Email</p>
                            <p><?php echo htmlspecialchars($admin['email']); ?></p>
                        </div>
                        
                        <div class="mb-2">
                            <p class="text-muted small mb-1">Username</p>
                            <p>@<?php echo htmlspecialchars($admin['username']); ?></p>
                        </div>
                    </div>
                </div>
                
                <!-- Permissions -->
                <h6 class="mb-3 fw-bold">Admin Permissions</h6>
                <div class="mb-4">
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success"></i> 
                            <strong>Add/Edit/Delete Questions</strong>
                            <p class="text-muted small mb-0">Manage interview questions for all technologies</p>
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success"></i> 
                            <strong>Filter Questions</strong>
                            <p class="text-muted small mb-0">Filter by technology and difficulty level</p>
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success"></i> 
                            <strong>View User Performance</strong>
                            <p class="text-muted small mb-0">Access detailed user analytics and test results</p>
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success"></i> 
                            <strong>System Dashboard</strong>
                            <p class="text-muted small mb-0">View system statistics and overview</p>
                        </li>
                    </ul>
                </div>
                
                <!-- Action Buttons -->
                <div class="row g-2">
                    <div class="col-md-6">
                        <a href="dashboard.php" class="btn btn-primary w-100">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                    </div>
                    <div class="col-md-6">
                        <a href="questions.php" class="btn btn-info w-100">
                            <i class="bi bi-question-circle"></i> Manage Questions
                        </a>
                    </div>
                    <div class="col-md-6">
                        <a href="users.php" class="btn btn-warning w-100">
                            <i class="bi bi-people"></i> View Users
                        </a>
                    </div>
                    <div class="col-md-6">
                        <a href="../logout.php" class="btn btn-danger w-100">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../views/footer.php'; ?>
