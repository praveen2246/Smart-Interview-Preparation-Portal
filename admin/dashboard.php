<?php
/**
 * Admin Dashboard
 * Overview of system statistics
 */

session_start();
$base_url = '..';
$page_title = 'Admin Dashboard - SIPP';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

require_once '../config/Database.php';
require_once '../models/Admin.php';
require_once '../models/Question.php';
require_once '../models/Result.php';
require_once '../models/User.php';

$database = new Database();
$connection = $database->connect();

$admin_model = new Admin($connection);
$question_model = new Question($connection);
$result_model = new Result($connection);

// Get statistics
$php_count = $question_model->getQuestionCount('PHP');
$java_count = $question_model->getQuestionCount('Java');
$react_count = $question_model->getQuestionCount('React');
$total_questions = $php_count + $java_count + $react_count;

// Get all results for overview
$all_results = $result_model->getAllResults(5, 0);

$admin = $admin_model->getAdminById($_SESSION['admin_id']);

include '../views/header.php';
?>

<!-- Page Title -->
<div class="mb-5">
    <h1 class="h2 fw-bold">
        <i class="bi bi-speedometer2"></i> Admin Dashboard
    </h1>
    <p class="text-muted">System overview and management tools</p>
</div>

<!-- Statistics Cards -->
<div class="row g-4 mb-5">
    <div class="col-md-6 col-lg-3">
        <div class="card stat-card">
            <div class="stat-number"><?php echo $total_questions; ?></div>
            <div class="stat-label">
                <i class="bi bi-question-circle"></i> Total Questions
            </div>
        </div>
    </div>
    
    <div class="col-md-6 col-lg-3">
        <div class="card stat-card success">
            <div class="stat-number"><?php echo $php_count; ?></div>
            <div class="stat-label">
                <i class="bi bi-file-earmark-code"></i> PHP Questions
            </div>
        </div>
    </div>
    
    <div class="col-md-6 col-lg-3">
        <div class="card stat-card info">
            <div class="stat-number"><?php echo $java_count; ?></div>
            <div class="stat-label">
                <i class="bi bi-cup"></i> Java Questions
            </div>
        </div>
    </div>
    
    <div class="col-md-6 col-lg-3">
        <div class="card stat-card warning">
            <div class="stat-number"><?php echo $react_count; ?></div>
            <div class="stat-label">
                <i class="bi bi-lightning-charge"></i> React Questions
            </div>
        </div>
    </div>
</div>

<!-- Management Sections -->
<div class="row g-4 mb-5">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-question-circle"></i> Question Management
                </h5>
            </div>
            <div class="card-body">
                <p class="text-muted mb-3">Manage interview questions for all technologies</p>
                <a href="questions.php" class="btn btn-primary">
                    <i class="bi bi-pencil-square"></i> Manage Questions
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-people"></i> User Management
                </h5>
            </div>
            <div class="card-body">
                <p class="text-muted mb-3">View user details and performance analytics</p>
                <a href="users.php" class="btn btn-info">
                    <i class="bi bi-people"></i> View Users
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Recent Test Results -->
<?php if (!empty($all_results)): ?>
<div class="row g-4 mb-5">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-clock-history"></i> Recent Test Results
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Technology</th>
                                <th>Score</th>
                                <th>Accuracy</th>
                                <th>Correct/Wrong</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($all_results as $result): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($result['full_name']); ?></td>
                                    <td>
                                        <span class="badge bg-primary"><?php echo htmlspecialchars($result['technology']); ?></span>
                                    </td>
                                    <td><strong><?php echo (int)$result['score']; ?>/100</strong></td>
                                    <td>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar" role="progressbar" 
                                                 style="width: <?php echo (float)$result['accuracy_percentage']; ?>%" 
                                                 aria-valuenow="<?php echo (float)$result['accuracy_percentage']; ?>" 
                                                 aria-valuemin="0" aria-valuemax="100">
                                                <?php echo (float)$result['accuracy_percentage']; ?>%
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-success"><?php echo $result['correct_answers']; ?></span>
                                        <span class="badge bg-danger"><?php echo $result['wrong_answers']; ?></span>
                                    </td>
                                    <td><small><?php echo date('M d, Y H:i', strtotime($result['test_date'])); ?></small></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="text-center mt-3">
                    <a href="users.php" class="btn btn-sm btn-outline-primary">View All Results</a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php include '../views/footer.php'; ?>
