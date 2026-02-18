<?php
/**
 * Admin - Users Management
 * View user performance and statistics
 */

session_start();
$base_url = '..';
$page_title = 'Users - Admin';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

require_once '../config/Database.php';
require_once '../models/Result.php';
require_once '../models/User.php';

$database = new Database();
$connection = $database->connect();

$result_model = new Result($connection);

// Get all results
$all_results = $result_model->getAllResults(100, 0);

// Group results by user
$user_stats = array();
foreach ($all_results as $result) {
    $user_id = $result['user_id'];
    if (!isset($user_stats[$user_id])) {
        $user_stats[$user_id] = array(
            'user_id' => $user_id,
            'full_name' => $result['full_name'],
            'username' => $result['username'],
            'total_tests' => 0,
            'average_accuracy' => 0,
            'best_score' => 0,
            'total_accuracy' => 0
        );
    }
    
    $user_stats[$user_id]['total_tests']++;
    $user_stats[$user_id]['total_accuracy'] += (float)$result['accuracy_percentage'];
    
    if ((int)$result['score'] > $user_stats[$user_id]['best_score']) {
        $user_stats[$user_id]['best_score'] = (int)$result['score'];
    }
}

// Calculate average accuracy
foreach ($user_stats as &$stat) {
    if ($stat['total_tests'] > 0) {
        $stat['average_accuracy'] = round($stat['total_accuracy'] / $stat['total_tests'], 2);
    }
}

include '../views/header.php';
?>

<div class="mb-5">
    <h1 class="h2 fw-bold">
        <i class="bi bi-people"></i> User Management
    </h1>
    <p class="text-muted">View user performance and analytics</p>
</div>

<?php if (empty($user_stats)): ?>
    <div class="alert alert-info">
        <i class="bi bi-info-circle"></i> No test results yet from users.
    </div>
<?php else: ?>
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Username</th>
                            <th>Tests Taken</th>
                            <th>Average Accuracy</th>
                            <th>Best Score</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($user_stats as $stat): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($stat['full_name']); ?></td>
                                <td>
                                    <code>@<?php echo htmlspecialchars($stat['username']); ?></code>
                                </td>
                                <td><?php echo $stat['total_tests']; ?></td>
                                <td>
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar" role="progressbar" 
                                             style="width: <?php echo $stat['average_accuracy']; ?>%" 
                                             aria-valuenow="<?php echo $stat['average_accuracy']; ?>" 
                                             aria-valuemin="0" aria-valuemax="100">
                                            <?php echo $stat['average_accuracy']; ?>%
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-success"><?php echo $stat['best_score']; ?>/100</span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-primary" 
                                            onclick="viewUserDetails(<?php echo $stat['user_id']; ?>, '<?php echo htmlspecialchars($stat['full_name']); ?>')">
                                        <i class="bi bi-eye"></i> View
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- User Details Modal -->
<div class="modal fade" id="userModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">User Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="userModalBody">
                <p class="text-center text-muted"><span class="spinner-border"></span> Loading...</p>
            </div>
        </div>
    </div>
</div>

<script>
    function viewUserDetails(userId, fullName) {
        const modal = new bootstrap.Modal(document.getElementById('userModal'));
        const modalBody = document.getElementById('userModalBody');
        
        // In a real application, you would fetch user details via AJAX
        // For now, showing basic info
        modalBody.innerHTML = `
            <div class="card mb-3">
                <div class="card-body">
                    <h5>User: ${fullName}</h5>
                    <p class="text-muted">User ID: ${userId}</p>
                    <p class="text-muted">To view detailed analytics for this user, use the user dashboard feature.</p>
                </div>
            </div>
        `;
        
        modal.show();
    }
</script>

<?php include '../views/footer.php'; ?>
