<?php
/**
 * Test Result Page
 * Displays detailed test results and analysis
 */

session_start();
$base_url = '';
$page_title = 'Test Result - SIPP';

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
$result_id = isset($_GET['result_id']) ? (int)$_GET['result_id'] : 0;

if (!$result_id) {
    header('Location: dashboard.php');
    exit;
}

// Get test result
$test_result = $result_model->getResultById($result_id, $user_id);

if (!$test_result) {
    header('Location: dashboard.php');
    exit;
}

// Get test answers
$answers = $result_model->getTestAnswers($result_id);

// Get weak topics
$weak_topics = $result_model->getWeakTopics($user_id, $test_result['technology']);

include 'views/header.php';
?>

<!-- Result Score Display -->
<div class="result-score">
    <div class="result-score-big"><?php echo (int)$test_result['score']; ?>/100</div>
    <div class="result-score-label">Your Score</div>
</div>

<!-- Result Statistics -->
<div class="row g-4 mb-5">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body text-center">
                <h6 class="text-muted mb-2">Accuracy</h6>
                <div class="h4 text-success mb-0"><?php echo (float)$test_result['accuracy_percentage']; ?>%</div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-body text-center">
                <h6 class="text-muted mb-2">Technology</h6>
                <div class="h4 mb-0">
                    <span class="badge bg-primary"><?php echo htmlspecialchars($test_result['technology']); ?></span>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <h6 class="text-muted mb-2">Total Questions</h6>
                <div class="h4 mb-0"><?php echo $test_result['total_questions']; ?></div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <h6 class="text-muted mb-2">Correct Answers</h6>
                <div class="h4 text-success mb-0"><?php echo $test_result['correct_answers']; ?></div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <h6 class="text-muted mb-2">Wrong Answers</h6>
                <div class="h4 text-danger mb-0"><?php echo $test_result['wrong_answers']; ?></div>
            </div>
        </div>
    </div>
    
    <div class="col-12">
        <div class="card">
            <div class="card-body text-center">
                <h6 class="text-muted mb-2">Time Taken</h6>
                <div class="h4 mb-0">
                    <?php 
                    $duration = $test_result['test_duration_seconds'];
                    $minutes = floor($duration / 60);
                    $seconds = $duration % 60;
                    echo sprintf("%02d:%02d", $minutes, $seconds);
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Detailed Answers -->
<div class="card mb-5">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="bi bi-list-check"></i> Detailed Answers
        </h5>
    </div>
    <div class="card-body">
        <?php foreach ($answers as $index => $answer): ?>
            <div class="question-card" style="border-left: 4px solid <?php echo $answer['is_correct'] ? '#198754' : '#dc3545'; ?>;">
                <span class="question-number">Question <?php echo $index + 1; ?></span>
                
                <h6 class="mt-3 mb-3"><?php echo htmlspecialchars($answer['question_text']); ?></h6>
                
                <div class="mb-3">
                    <p class="text-muted small"><strong>Your Answer:</strong></p>
                    <div class="alert <?php echo $answer['is_correct'] ? 'alert-success' : 'alert-danger'; ?> mb-2">
                        <strong><?php echo $answer['user_answer']; ?>.</strong> 
                        <?php 
                        $option_key = 'option_' . strtolower($answer['user_answer']);
                        echo htmlspecialchars($answer[$option_key]);
                        ?>
                        <?php if ($answer['is_correct']): ?>
                            <span class="ms-2"><i class="bi bi-check-circle"></i> Correct</span>
                        <?php else: ?>
                            <span class="ms-2"><i class="bi bi-x-circle"></i> Incorrect</span>
                        <?php endif; ?>
                    </div>
                </div>
                
                <?php if (!$answer['is_correct']): ?>
                    <div class="mb-3">
                        <p class="text-muted small"><strong>Correct Answer:</strong></p>
                        <div class="alert alert-success mb-0">
                            <strong><?php echo $answer['correct_answer']; ?>.</strong> 
                            <?php 
                            $option_key = 'option_' . strtolower($answer['correct_answer']);
                            echo htmlspecialchars($answer[$option_key]);
                            ?>
                            <span class="ms-2"><i class="bi bi-check-circle"></i> Correct</span>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Weak Topics Alert -->
<?php if (!empty($weak_topics)): ?>
    <div class="alert alert-warning" role="alert">
        <h5 class="alert-heading">
            <i class="bi bi-exclamation-triangle"></i> Areas Needing Improvement
        </h5>
        <p>Based on your recent tests, you should focus on these topics:</p>
        <ul class="mb-0">
            <?php foreach ($weak_topics as $topic): ?>
                <li><?php echo htmlspecialchars($topic['topic']); ?> (<?php echo $topic['wrong_count']; ?> incorrect attempts)</li>
            <?php endforeach; ?>
        </ul>
        <hr>
        <p class="mb-0"><strong>Tip:</strong> Take more practice tests on these topics to improve your performance.</p>
    </div>
<?php endif; ?>

<!-- Action Buttons -->
<div class="row g-3 mt-4 mb-5">
    <div class="col-md-4">
        <a href="dashboard.php" class="btn btn-primary w-100">
            <i class="bi bi-speedometer2"></i> View Dashboard
        </a>
    </div>
    <div class="col-md-4">
        <a href="test.php" class="btn btn-success w-100">
            <i class="bi bi-pencil-square"></i> Take Another Test
        </a>
    </div>
    <div class="col-md-4">
        <a href="results.php" class="btn btn-info w-100">
            <i class="bi bi-history"></i> View All Results
        </a>
    </div>
</div>

<?php include 'views/footer.php'; ?>
