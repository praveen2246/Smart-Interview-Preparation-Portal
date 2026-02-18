<?php
/**
 * User Dashboard
 * Displays performance metrics and performance charts
 */

session_start();
$base_url = '';
$page_title = 'Dashboard - SIPP';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once 'config/Database.php';
require_once 'models/Result.php';
require_once 'models/User.php';

$database = new Database();
$connection = $database->connect();

$result_model = new Result($connection);
$user_model = new User($connection);

$user_id = $_SESSION['user_id'];

// Get dashboard statistics
$stats = $result_model->getDashboardStats($user_id);

// Get performance data for chart
$performance_data = $result_model->getPerformanceData($user_id);

// Get weak topics
$weak_topics = $result_model->getWeakTopics($user_id);

// Prepare data for chart
$chart_labels = array();
$chart_accuracy = array();
$chart_scores = array();

foreach ($performance_data as $data) {
    $chart_labels[] = date('M d, Y', strtotime($data['test_date']));
    $chart_accuracy[] = (float)$data['accuracy_percentage'];
    $chart_scores[] = (int)$data['score'];
}

$user = $user_model->getUserById($user_id);

include 'views/header.php';
?>

<!-- Page Title -->
<div class="mb-5">
    <h1 class="h2 fw-bold">
        <i class="bi bi-speedometer2"></i> Welcome, <?php echo htmlspecialchars($user['full_name']); ?>
    </h1>
    <p class="text-muted">Track your interview preparation progress</p>
</div>

<!-- Statistics Cards -->
<div class="row g-4 mb-5">
    <div class="col-md-6 col-lg-3">
        <div class="card stat-card">
            <div class="stat-number"><?php echo $stats['total_tests']; ?></div>
            <div class="stat-label">
                <i class="bi bi-pencil-square"></i> Tests Taken
            </div>
        </div>
    </div>
    
    <div class="col-md-6 col-lg-3">
        <div class="card stat-card success">
            <div class="stat-number"><?php echo $stats['average_accuracy']; ?>%</div>
            <div class="stat-label">
                <i class="bi bi-target"></i> Average Accuracy
            </div>
        </div>
    </div>
    
    <div class="col-md-6 col-lg-3">
        <div class="card stat-card info">
            <div class="stat-number"><?php echo $stats['best_score']; ?></div>
            <div class="stat-label">
                <i class="bi bi-award"></i> Best Score
            </div>
        </div>
    </div>
    
    <div class="col-md-6 col-lg-3">
        <div class="card stat-card warning">
            <div class="stat-number"><?php echo $stats['average_score']; ?></div>
            <div class="stat-label">
                <i class="bi bi-graph-up"></i> Average Score
            </div>
        </div>
    </div>
</div>

<!-- Performance Chart -->
<?php if (!empty($performance_data)): ?>
<div class="row g-4 mb-5">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-graph-up"></i> Performance Over Time
                </h5>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="performanceChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-info-circle"></i> Statistics
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <p class="text-muted mb-1">Total Tests</p>
                    <p class="h5 mb-0"><?php echo $stats['total_tests']; ?></p>
                </div>
                <hr>
                <div class="mb-3">
                    <p class="text-muted mb-1">Best Accuracy</p>
                    <p class="h5 mb-0">
                        <?php 
                        $best_accuracy = 0;
                        foreach ($performance_data as $data) {
                            if ($data['accuracy_percentage'] > $best_accuracy) {
                                $best_accuracy = $data['accuracy_percentage'];
                            }
                        }
                        echo $best_accuracy . '%';
                        ?>
                    </p>
                </div>
                <hr>
                <div class="mb-3">
                    <p class="text-muted mb-1">Worst Accuracy</p>
                    <p class="h5 mb-0">
                        <?php 
                        $worst_accuracy = 100;
                        foreach ($performance_data as $data) {
                            if ($data['accuracy_percentage'] < $worst_accuracy) {
                                $worst_accuracy = $data['accuracy_percentage'];
                            }
                        }
                        echo $worst_accuracy . '%';
                        ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
<?php else: ?>
<div class="alert alert-info" role="alert">
    <i class="bi bi-info-circle"></i> No test results yet. <a href="test.php" class="alert-link">Take your first test!</a>
</div>
<?php endif; ?>

<!-- Weak Topics Section -->
<?php if (!empty($weak_topics)): ?>
<div class="row g-4 mb-5">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-exclamation-triangle"></i> Areas Needing Improvement
                </h5>
            </div>
            <div class="card-body">
                <?php foreach ($weak_topics as $topic): ?>
                    <div class="weak-topic-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong><?php echo htmlspecialchars($topic['topic']); ?></strong>
                                <p class="mb-0 text-muted small">
                                    <span class="badge bg-danger"><?php echo $topic['wrong_count']; ?> Wrong</span>
                                    <span class="badge bg-success"><?php echo $topic['correct_count']; ?> Correct</span>
                                </p>
                            </div>
                            <div class="text-end">
                                <strong class="text-warning">Action Required</strong>
                                <p class="small text-muted mb-0">Last attempted: <?php echo date('M d, Y', strtotime($topic['last_attempted'])); ?></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
                
                <div class="alert alert-warning mt-3 mb-0">
                    <i class="bi bi-lightbulb"></i> <strong>Recommendation:</strong> Focus on these areas to improve your performance. 
                    Take more practice tests on these topics to strengthen your knowledge.
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Action Buttons -->
<div class="row g-4 mb-5">
    <div class="col-md-6">
        <div class="card text-center py-4">
            <div class="card-body">
                <i class="bi bi-pencil-square text-primary" style="font-size: 3rem;"></i>
                <h5 class="mt-3">Take a Test</h5>
                <p class="text-muted mb-3">Challenge yourself with a 10-minute mock test</p>
                <a href="test.php" class="btn btn-primary">
                    <i class="bi bi-play-circle"></i> Start Test
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card text-center py-4">
            <div class="card-body">
                <i class="bi bi-clock-history text-info" style="font-size: 3rem;"></i>
                <h5 class="mt-3">View History</h5>
                <p class="text-muted mb-3">Review your past test results and performance</p>
                <a href="results.php" class="btn btn-info">
                    <i class="bi bi-history"></i> View Results
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Performance Chart Script -->
<?php if (!empty($performance_data)): ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('performanceChart').getContext('2d');
        
        const chartLabels = <?php echo json_encode($chart_labels); ?>;
        const chartAccuracy = <?php echo json_encode($chart_accuracy); ?>;
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartLabels,
                datasets: [{
                    label: 'Accuracy Percentage (%)',
                    data: chartAccuracy,
                    borderColor: '#0d6efd',
                    backgroundColor: 'rgba(13, 110, 253, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 5,
                    pointBackgroundColor: '#0d6efd',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            callback: function(value) {
                                return value + '%';
                            }
                        }
                    }
                }
            }
        });
    });
</script>
<?php endif; ?>

<?php include 'views/footer.php'; ?>
