<?php
/**
 * Submit Test - Process test answers and calculate score
 */

session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once 'config/Database.php';
require_once 'models/Question.php';
require_once 'models/Result.php';

$database = new Database();
$connection = $database->connect();

$question_model = new Question($connection);
$result_model = new Result($connection);

$user_id = $_SESSION['user_id'];
$technology = isset($_POST['technology']) ? htmlspecialchars($_POST['technology']) : '';
$test_duration = isset($_POST['test_duration']) ? (int)$_POST['test_duration'] : 600;
$answers = isset($_POST['answer']) ? $_POST['answer'] : array();
$answer_mappings = isset($_POST['answer_mapping']) ? $_POST['answer_mapping'] : array();

if (empty($technology) || empty($answers)) {
    header('Location: test.php');
    exit;
}

// Process answers
$correct_count = 0;
$wrong_count = 0;
$answers_data = array();

foreach ($answers as $question_id => $user_answer) {
    $question_id = (int)$question_id;
    $user_answer = htmlspecialchars($user_answer);
    
    $answer_info = $question_model->getCorrectAnswer($question_id);
    
    if ($answer_info) {
        // Get the answer mapping for this question to find the original letter
        $original_answer = $user_answer;
        if (isset($answer_mappings[$question_id])) {
            $mapping = json_decode($answer_mappings[$question_id], true);
            if (is_array($mapping)) {
                // Find the original letter based on display letter
                foreach ($mapping as $shuffled_data) {
                    if ($shuffled_data['display_letter'] === $user_answer) {
                        $original_answer = $shuffled_data['original_letter'];
                        break;
                    }
                }
            }
        }
        
        $is_correct = ($original_answer === $answer_info['correct_answer']);
        
        if ($is_correct) {
            $correct_count++;
        } else {
            $wrong_count++;
        }
        
        $answers_data[] = array(
            'question_id' => $question_id,
            'user_answer' => $user_answer,
            'is_correct' => $is_correct,
            'topic' => $answer_info['topic']
        );
    }
}

$total_questions = count($answers_data);

// Save result
$save_result = $result_model->saveResult(
    $user_id,
    $technology,
    $total_questions,
    $correct_count,
    $wrong_count,
    $test_duration,
    $answers_data
);

if ($save_result['success']) {
    // Redirect to results page
    header('Location: test_result.php?result_id=' . $save_result['result_id']);
    exit;
} else {
    // Error saving result
    $_SESSION['error'] = 'Failed to save test result. Please try again.';
    header('Location: dashboard.php');
    exit;
}
?>
