<?php
/**
 * Test Page
 * Users select technology and take a mock test with timer
 */

session_start();
$base_url = '';
$page_title = 'Take Test - SIPP';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once 'config/Database.php';
require_once 'models/Question.php';

$database = new Database();
$connection = $database->connect();

$question_model = new Question($connection);

$technology = isset($_GET['technology']) ? htmlspecialchars($_GET['technology']) : '';
$start_test = isset($_GET['start']) ? true : false;

include 'views/header.php';
?>

<?php if (!$start_test): ?>
    <!-- Technology Selection -->
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-pencil-square"></i> Select Technology
                    </h5>
                </div>
                <div class="card-body p-5">
                    <p class="text-muted mb-4">Choose a technology to test your knowledge:</p>
                    
                    <div class="row g-4">
                        <div class="col-md-4">
                            <a href="test.php?technology=PHP&start=1" class="text-decoration-none">
                                <div class="card text-center h-100 cursor-pointer" style="cursor: pointer; transition: all 0.3s;">
                                    <div class="card-body py-5">
                                        <h4 class="mb-3">
                                            <i class="bi bi-file-earmark-code text-primary" style="font-size: 2.5rem;"></i>
                                        </h4>
                                        <h6 class="fw-bold">PHP</h6>
                                        <p class="text-muted small">
                                            <?php 
                                            $php_count = $question_model->getQuestionCount('PHP');
                                            echo $php_count . ' Questions';
                                            ?>
                                        </p>
                                    </div>
                                </div>
                            </a>
                        </div>
                        
                        <div class="col-md-4">
                            <a href="test.php?technology=Java&start=1" class="text-decoration-none">
                                <div class="card text-center h-100 cursor-pointer" style="cursor: pointer; transition: all 0.3s;">
                                    <div class="card-body py-5">
                                        <h4 class="mb-3">
                                            <i class="bi bi-cup text-warning" style="font-size: 2.5rem;"></i>
                                        </h4>
                                        <h6 class="fw-bold">Java</h6>
                                        <p class="text-muted small">
                                            <?php 
                                            $java_count = $question_model->getQuestionCount('Java');
                                            echo $java_count . ' Questions';
                                            ?>
                                        </p>
                                    </div>
                                </div>
                            </a>
                        </div>
                        
                        <div class="col-md-4">
                            <a href="test.php?technology=React&start=1" class="text-decoration-none">
                                <div class="card text-center h-100 cursor-pointer" style="cursor: pointer; transition: all 0.3s;">
                                    <div class="card-body py-5">
                                        <h4 class="mb-3">
                                            <i class="bi bi-lightning-charge text-info" style="font-size: 2.5rem;"></i>
                                        </h4>
                                        <h6 class="fw-bold">React</h6>
                                        <p class="text-muted small">
                                            <?php 
                                            $react_count = $question_model->getQuestionCount('React');
                                            echo $react_count . ' Questions';
                                            ?>
                                        </p>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php else: ?>
    <!-- Test Instructions -->
    <div class="row justify-content-center mb-4">
        <div class="col-lg-10">
            <div class="alert alert-info" role="alert">
                <i class="bi bi-info-circle"></i>
                <strong>Test Instructions:</strong><br>
                • You have <strong>10 minutes</strong> to complete the test<br>
                • <strong>10 random questions</strong> from <?php echo htmlspecialchars($technology); ?><br>
                • Select one answer for each question<br>
                • You cannot go back and change answers<br>
                • Your test will auto-submit when time is up
            </div>
        </div>
    </div>
    
    <!-- Test Form -->
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <form id="testForm" method="POST" action="submit_test.php">
                <!-- Timer Display -->
                <div class="card mb-4 bg-light">
                    <div class="card-body text-center">
                        <p class="text-muted mb-2">Time Remaining</p>
                        <div class="timer" id="timer" style="font-size: 2.5rem; color: #0d6efd;">10:00</div>
                    </div>
                </div>
                
                <!-- Questions -->
                <input type="hidden" name="technology" value="<?php echo htmlspecialchars($technology); ?>">
                <input type="hidden" name="test_start_time" id="test_start_time" value="<?php echo time(); ?>">
                <input type="hidden" id="test_duration" name="test_duration" value="0">
                
                <?php
                // Get random questions
                $questions = $question_model->getRandomQuestions($technology, null, 10);
                
                if (empty($questions)):
                ?>
                    <div class="alert alert-warning" role="alert">
                        <i class="bi bi-exclamation-triangle"></i> No questions available for this technology yet.
                        <a href="dashboard.php" class="alert-link">Back to Dashboard</a>
                    </div>
                    
                <?php else: ?>
                    <div id="questionsContainer">
                        <?php foreach ($questions as $index => $q): ?>
                            <div class="question-card">
                                <span class="question-number">Question <?php echo $index + 1; ?> of <?php echo count($questions); ?></span>
                                
                                <h5 class="mt-3 mb-4"><?php echo htmlspecialchars($q['question_text']); ?></h5>
                                <p class="text-muted small mb-3">Topic: <strong><?php echo htmlspecialchars($q['topic']); ?></strong> | Difficulty: <span class="badge bg-secondary"><?php echo htmlspecialchars($q['difficulty']); ?></span></p>
                                
                                <div class="options">
                                    <?php
                                    $options = [
                                        'A' => $q['option_a'],
                                        'B' => $q['option_b'],
                                        'C' => $q['option_c'],
                                        'D' => $q['option_d']
                                    ];
                                    
                                    // Shuffle the options randomly
                                    $shuffled_keys = array_keys($options);
                                    shuffle($shuffled_keys);
                                    $shuffled_options = [];
                                    
                                    foreach ($shuffled_keys as $display_position => $original_letter) {
                                        $shuffled_options[$display_position] = [
                                            'original_letter' => $original_letter,
                                            'text' => $options[$original_letter],
                                            'display_letter' => chr(65 + $display_position) // A, B, C, D
                                        ];
                                    }
                                    
                                    // Store the mapping as JSON for correct answer verification
                                    $answer_mapping = json_encode($shuffled_options);
                                    ?>
                                    <input type="hidden" name="answer_mapping[<?php echo $q['id']; ?>]" 
                                           value="<?php echo htmlspecialchars($answer_mapping); ?>">
                                    
                                    <?php
                                    foreach ($shuffled_options as $display_pos => $opt):
                                        $display_letter = $opt['display_letter'];
                                    ?>
                                        <label class="option">
                                            <input type="radio" name="answer[<?php echo $q['id']; ?>]" 
                                                   value="<?php echo $display_letter; ?>" required>
                                            <strong><?php echo $display_letter; ?>.</strong> <?php echo htmlspecialchars($opt['text']); ?>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="d-grid gap-2 d-sm-flex justify-content-center mb-5">
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="bi bi-check-circle"></i> Submit Test
                        </button>
                        <a href="dashboard.php" class="btn btn-outline-danger btn-lg">
                            <i class="bi bi-x-circle"></i> Cancel
                        </a>
                    </div>
                <?php endif; ?>
            </form>
        </div>
    </div>
    
    <!-- Timer Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const duration = 10 * 60; // 10 minutes in seconds
            let remaining = duration;
            const timerElement = document.getElementById('timer');
            const durationInput = document.getElementById('test_duration');
            const testForm = document.getElementById('testForm');
            
            function updateTimer() {
                const minutes = Math.floor(remaining / 60);
                const seconds = remaining % 60;
                timerElement.textContent = String(minutes).padStart(2, '0') + ':' + String(seconds).padStart(2, '0');
                
                // Change color
                if (remaining <= 60) {
                    timerElement.classList.remove('warning');
                    timerElement.classList.add('danger');
                } else if (remaining <= 300) {
                    timerElement.classList.add('warning');
                }
            }
            
            const interval = setInterval(() => {
                remaining--;
                updateTimer();
                
                if (remaining <= 0) {
                    clearInterval(interval);
                    // Auto submit
                    durationInput.value = duration - remaining;
                    testForm.submit();
                }
            }, 1000);
            
            // Update duration on form submit
            testForm.addEventListener('submit', function(e) {
                const elapsedTime = duration - remaining;
                durationInput.value = elapsedTime;
            });
            
            // Disable leaving page warning
            window.addEventListener('beforeunload', function(e) {
                const answer = Object.keys(document.forms['testForm'].elements)
                    .some(key => document.forms['testForm'].elements[key].checked);
                
                if (answer) {
                    e.preventDefault();
                    e.returnValue = '';
                    return '';
                }
            });
        });
    </script>

<?php endif; ?>

<?php include 'views/footer.php'; ?>
