<?php
/**
 * Results History Page
 * Displays all test results for the user
 */

session_start();
$base_url = '';
$page_title = 'Results - SIPP';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once 'config/Database.php';
require_once 'models/Result.php';

$database = new Database();
$connection = $database->connect();

$result_model = new Result($connection);

$user_id = $_SESSION['user_id'];

// Get all results
$results = $result_model->getUserResults($user_id);

include 'views/header.php';
?>

<div class="mb-5">
    <h1 class="h2 fw-bold">
        <i class="bi bi-clock-history"></i> Test Results History
    </h1>
    <p class="text-muted">View all your test attempts and performance metrics</p>
</div>

<?php if (empty($results)): ?>
    <div class="alert alert-info" role="alert">
        <i class="bi bi-info-circle"></i> No test results yet. 
        <a href="test.php" class="alert-link">Take your first test!</a>
    </div>
<?php else: ?>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th><i class="bi bi-hash"></i> #</th>
                    <th><i class="bi bi-lightning-fill"></i> Technology</th>
                    <th><i class="bi bi-percent"></i> Score</th>
                    <th><i class="bi bi-target"></i> Accuracy</th>
                    <th><i class="bi bi-check-circle"></i> Correct</th>
                    <th><i class="bi bi-x-circle"></i> Wrong</th>
                    <th><i class="bi bi-hourglass"></i> Time</th>
                    <th><i class="bi bi-calendar-event"></i> Date</th>
                    <th><i class="bi bi-eye"></i> Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($results as $index => $result): ?>
                    <tr>
                        <td><?php echo $index + 1; ?></td>
                        <td>
                            <span class="badge bg-primary"><?php echo htmlspecialchars($result['technology']); ?></span>
                        </td>
                        <td>
                            <strong><?php echo (int)$result['score']; ?>/100</strong>
                        </td>
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
                        <td><span class="badge bg-success"><?php echo $result['correct_answers']; ?></span></td>
                        <td><span class="badge bg-danger"><?php echo $result['wrong_answers']; ?></span></td>
                        <td>
                            <?php 
                            $duration = $result['test_duration_seconds'];
                            $minutes = floor($duration / 60);
                            $seconds = $duration % 60;
                            echo sprintf("%02d:%02d", $minutes, $seconds);
                            ?>
                        </td>
                        <td>
                            <small><?php echo date('M d, Y H:i', strtotime($result['test_date'])); ?></small>
                        </td>
                        <td>
                            <a href="test_result.php?result_id=<?php echo $result['id']; ?>" class="btn btn-sm btn-primary">
                                <i class="bi bi-eye"></i> View
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <!-- Statistics Summary -->
    <div class="row g-4 mt-5">
        <div class="col-md-4">
            <div class="card stat-card">
                <div class="stat-number"><?php echo count($results); ?></div>
                <div class="stat-label">
                    <i class="bi bi-pencil-square"></i> Total Tests
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card stat-card success">
                <div class="stat-number">
                    <?php 
                    $total_accuracy = 0;
                    foreach ($results as $result) {
                        $total_accuracy += (float)$result['accuracy_percentage'];
                    }
                    $avg_accuracy = count($results) > 0 ? round($total_accuracy / count($results), 2) : 0;
                    echo $avg_accuracy . '%';
                    ?>
                </div>
                <div class="stat-label">
                    <i class="bi bi-target"></i> Average Accuracy
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card stat-card info">
                <div class="stat-number">
                    <?php 
                    $best_score = max(array_map(function($r) { return (int)$r['score']; }, $results));
                    echo $best_score;
                    ?>
                </div>
                <div class="stat-label">
                    <i class="bi bi-award"></i> Best Score
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- Action Buttons -->
<div class="row g-3 mt-5 mb-5">
    <div class="col-md-4">
        <a href="dashboard.php" class="btn btn-secondary w-100">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>
    </div>
    <div class="col-md-4">
        <a href="test.php" class="btn btn-success w-100">
            <i class="bi bi-pencil-square"></i> Take Test
        </a>
    </div>
    <div class="col-md-4">
        <a href="profile.php" class="btn btn-primary w-100">
            <i class="bi bi-person-circle"></i> Profile
        </a>
    </div>
</div>

<?php include 'views/footer.php'; ?>
