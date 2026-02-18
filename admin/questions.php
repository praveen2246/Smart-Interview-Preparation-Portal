<?php
/**
 * Admin - Question Management
 * Add, Edit, Delete questions
 */

session_start();
$base_url = '..';
$page_title = 'Manage Questions - Admin';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

require_once '../config/Database.php';
require_once '../models/Question.php';

$database = new Database();
$connection = $database->connect();

$question_model = new Question($connection);

$action = isset($_GET['action']) ? htmlspecialchars($_GET['action']) : 'list';
$question_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$message = '';
$error = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = htmlspecialchars($_POST['action'] ?? '');
    
    if ($action === 'add') {
        $result = $question_model->addQuestion(
            $_POST['technology'],
            $_POST['topic'],
            $_POST['question_text'],
            $_POST['option_a'],
            $_POST['option_b'],
            $_POST['option_c'],
            $_POST['option_d'],
            $_POST['correct_answer'],
            $_POST['difficulty']
        );
        
        if ($result['success']) {
            $message = $result['message'];
            header('Location: questions.php');
            exit;
        } else {
            $error = $result['message'];
        }
    } elseif ($action === 'update') {
        $result = $question_model->updateQuestion(
            $_POST['id'],
            $_POST['technology'],
            $_POST['topic'],
            $_POST['question_text'],
            $_POST['option_a'],
            $_POST['option_b'],
            $_POST['option_c'],
            $_POST['option_d'],
            $_POST['correct_answer'],
            $_POST['difficulty']
        );
        
        if ($result['success']) {
            $message = $result['message'];
            header('Location: questions.php');
            exit;
        } else {
            $error = $result['message'];
        }
    } elseif ($action === 'delete') {
        $result = $question_model->deleteQuestion($_POST['id']);
        
        if ($result['success']) {
            $message = $result['message'];
            header('Location: questions.php');
            exit;
        } else {
            $error = $result['message'];
        }
    }
}

// Get filter values
$filter_technology = isset($_GET['technology']) ? htmlspecialchars($_GET['technology']) : '';
$filter_difficulty = isset($_GET['difficulty']) ? htmlspecialchars($_GET['difficulty']) : '';

// Get questions
$questions = $question_model->getAllQuestions(
    $filter_technology ?: null,
    $filter_difficulty ?: null
);

// Get question to edit
$edit_question = null;
if ($action === 'edit' && $question_id) {
    $all_questions = $question_model->getAllQuestions();
    foreach ($all_questions as $q) {
        if ($q['id'] === $question_id) {
            $edit_question = $q;
            break;
        }
    }
}

include '../views/header.php';
?>

<div class="mb-5">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h2 fw-bold">
                <i class="bi bi-question-circle"></i> Question Management
            </h1>
            <p class="text-muted">Add, edit, and delete interview questions</p>
        </div>
        <?php if ($action !== 'add' && $action !== 'edit'): ?>
            <a href="questions.php?action=add" class="btn btn-success">
                <i class="bi bi-plus-circle"></i> Add Question
            </a>
        <?php endif; ?>
    </div>
</div>

<?php if ($message): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle"></i> <?php echo htmlspecialchars($message); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if ($action === 'add' || $action === 'edit'): ?>
    <!-- Form for Add/Edit Question -->
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <?php echo ($action === 'add') ? 'Add New Question' : 'Edit Question'; ?>
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <input type="hidden" name="action" value="<?php echo $action; ?>">
                        <?php if ($action === 'edit'): ?>
                            <input type="hidden" name="id" value="<?php echo $edit_question['id']; ?>">
                        <?php endif; ?>
                        
                        <div class="mb-3">
                            <label for="technology" class="form-label">Technology *</label>
                            <select class="form-select" name="technology" id="technology" required>
                                <option value="">-- Select Technology --</option>
                                <option value="PHP" <?php echo (isset($edit_question) && $edit_question['technology'] === 'PHP') ? 'selected' : ''; ?>>PHP</option>
                                <option value="Java" <?php echo (isset($edit_question) && $edit_question['technology'] === 'Java') ? 'selected' : ''; ?>>Java</option>
                                <option value="React" <?php echo (isset($edit_question) && $edit_question['technology'] === 'React') ? 'selected' : ''; ?>>React</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="topic" class="form-label">Topic *</label>
                            <input type="text" class="form-control" name="topic" id="topic" required 
                                   value="<?php echo isset($edit_question) ? htmlspecialchars($edit_question['topic']) : ''; ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="difficulty" class="form-label">Difficulty *</label>
                            <select class="form-select" name="difficulty" id="difficulty" required>
                                <option value="">-- Select Difficulty --</option>
                                <option value="easy" <?php echo (isset($edit_question) && $edit_question['difficulty'] === 'easy') ? 'selected' : ''; ?>>Easy</option>
                                <option value="medium" <?php echo (isset($edit_question) && $edit_question['difficulty'] === 'medium') ? 'selected' : ''; ?>>Medium</option>
                                <option value="hard" <?php echo (isset($edit_question) && $edit_question['difficulty'] === 'hard') ? 'selected' : ''; ?>>Hard</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="question_text" class="form-label">Question *</label>
                            <textarea class="form-control" name="question_text" id="question_text" rows="4" required><?php echo isset($edit_question) ? htmlspecialchars($edit_question['question_text']) : ''; ?></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="option_a" class="form-label">Option A *</label>
                                <input type="text" class="form-control" name="option_a" id="option_a" required 
                                       value="<?php echo isset($edit_question) ? htmlspecialchars($edit_question['option_a']) : ''; ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="option_b" class="form-label">Option B *</label>
                                <input type="text" class="form-control" name="option_b" id="option_b" required 
                                       value="<?php echo isset($edit_question) ? htmlspecialchars($edit_question['option_b']) : ''; ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="option_c" class="form-label">Option C *</label>
                                <input type="text" class="form-control" name="option_c" id="option_c" required 
                                       value="<?php echo isset($edit_question) ? htmlspecialchars($edit_question['option_c']) : ''; ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="option_d" class="form-label">Option D *</label>
                                <input type="text" class="form-control" name="option_d" id="option_d" required 
                                       value="<?php echo isset($edit_question) ? htmlspecialchars($edit_question['option_d']) : ''; ?>">
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label for="correct_answer" class="form-label">Correct Answer *</label>
                            <select class="form-select" name="correct_answer" id="correct_answer" required>
                                <option value="">-- Select Correct Answer --</option>
                                <option value="A" <?php echo (isset($edit_question) && $edit_question['correct_answer'] === 'A') ? 'selected' : ''; ?>>A</option>
                                <option value="B" <?php echo (isset($edit_question) && $edit_question['correct_answer'] === 'B') ? 'selected' : ''; ?>>B</option>
                                <option value="C" <?php echo (isset($edit_question) && $edit_question['correct_answer'] === 'C') ? 'selected' : ''; ?>>C</option>
                                <option value="D" <?php echo (isset($edit_question) && $edit_question['correct_answer'] === 'D') ? 'selected' : ''; ?>>D</option>
                            </select>
                        </div>
                        
                        <div class="d-grid gap-2 d-sm-flex">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> <?php echo ($action === 'add') ? 'Add Question' : 'Update Question'; ?>
                            </button>
                            <a href="questions.php" class="btn btn-outline-danger">
                                <i class="bi bi-x-circle"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

<?php else: ?>
    <!-- Questions List -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Filter Questions</h5>
        </div>
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label for="filter_tech" class="form-label">Technology</label>
                    <select class="form-select" name="technology" id="filter_tech">
                        <option value="">-- All --</option>
                        <option value="PHP" <?php echo $filter_technology === 'PHP' ? 'selected' : ''; ?>>PHP</option>
                        <option value="Java" <?php echo $filter_technology === 'Java' ? 'selected' : ''; ?>>Java</option>
                        <option value="React" <?php echo $filter_technology === 'React' ? 'selected' : ''; ?>>React</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="filter_diff" class="form-label">Difficulty</label>
                    <select class="form-select" name="difficulty" id="filter_diff">
                        <option value="">-- All --</option>
                        <option value="easy" <?php echo $filter_difficulty === 'easy' ? 'selected' : ''; ?>>Easy</option>
                        <option value="medium" <?php echo $filter_difficulty === 'medium' ? 'selected' : ''; ?>>Medium</option>
                        <option value="hard" <?php echo $filter_difficulty === 'hard' ? 'selected' : ''; ?>>Hard</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Filter
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <?php if (empty($questions)): ?>
        <div class="alert alert-info">
            <i class="bi bi-info-circle"></i> No questions found. <a href="questions.php?action=add" class="alert-link">Add a question</a>
        </div>
    <?php else: ?>
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Question</th>
                                <th>Technology</th>
                                <th>Topic</th>
                                <th>Difficulty</th>
                                <th>Answer</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($questions as $q): ?>
                                <tr>
                                    <td><?php echo $q['id']; ?></td>
                                    <td>
                                        <small><?php echo htmlspecialchars(substr($q['question_text'], 0, 50)) . '...'; ?></small>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary"><?php echo htmlspecialchars($q['technology']); ?></span>
                                    </td>
                                    <td><?php echo htmlspecialchars($q['topic']); ?></td>
                                    <td>
                                        <span class="badge bg-secondary"><?php echo htmlspecialchars($q['difficulty']); ?></span>
                                    </td>
                                    <td><strong><?php echo htmlspecialchars($q['correct_answer']); ?></strong></td>
                                    <td>
                                        <a href="questions.php?action=edit&id=<?php echo $q['id']; ?>" class="btn btn-sm btn-warning">
                                            <i class="bi bi-pencil"></i> Edit
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger" 
                                                onclick="confirmDelete(<?php echo $q['id']; ?>)">
                                            <i class="bi bi-trash"></i> Delete
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
<?php endif; ?>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this question? This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" style="display: inline;">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" id="deleteId" value="">
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function confirmDelete(id) {
        document.getElementById('deleteId').value = id;
        new bootstrap.Modal(document.getElementById('deleteModal')).show();
    }
</script>

<?php include '../views/footer.php'; ?>
