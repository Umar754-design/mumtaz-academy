<?php
$pageTitle = 'MCQ Tests Management';
require_once 'header.php';

// Set PHP timezone to Asia/Kolkata
date_default_timezone_set('Asia/Kolkata');

// Get database connection
$pdo = getDB();

// Handle form submissions
$message = '';
$messageType = '';

// Verify CSRF token for POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrfToken = $_POST['csrf_token'] ?? '';
    if (!verifyCSRFToken($csrfToken)) {
        $message = 'Invalid request. Please try again.';
        $messageType = 'error';
    }
}

// Add/Edit MCQ test
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && empty($message)) {
    $action = $_POST['action'];
    
    if ($action === 'add' || $action === 'edit') {
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $category = trim($_POST['category'] ?? '');
        $level = trim($_POST['level'] ?? 'beginner');
        $durationMinutes = (int)($_POST['duration_minutes'] ?? 30);
        $passingScore = (int)($_POST['passing_score'] ?? 60);
        $isPublished = isset($_POST['is_published']) ? 1 : 0;
        
        // Handle questions
        $questions = [];
        if (isset($_POST['questions'])) {
            foreach ($_POST['questions'] as $q) {
                if (!empty($q['question_text']) && !empty($q['correct_answer'])) {
                    $questions[] = [
                        'question_text' => trim($q['question_text']),
                        'option_a' => trim($q['option_a'] ?? ''),
                        'option_b' => trim($q['option_b'] ?? ''),
                        'option_c' => trim($q['option_c'] ?? ''),
                        'option_d' => trim($q['option_d'] ?? ''),
                        'correct_answer' => strtoupper(trim($q['correct_answer'])),
                        'explanation' => trim($q['explanation'] ?? '')
                    ];
                }
            }
        }
        
        if (empty($title)) {
            $message = 'Title is required.';
            $messageType = 'error';
        } elseif (empty($questions)) {
            $message = 'At least one question is required.';
            $messageType = 'error';
        } else {
            try {
                $pdo->beginTransaction();
                
                if ($action === 'add') {
                    $stmt = $pdo->prepare("
                        INSERT INTO mcq_tests (title, description, category, level, duration_minutes, total_questions, passing_score, is_published, created_at)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
                    ");
                    $stmt->execute([$title, $description, $category, $level, $durationMinutes, count($questions), $passingScore, $isPublished]);
                    $testId = $pdo->lastInsertId();
                    
                    // Insert questions
                    $questionStmt = $pdo->prepare("
                        INSERT INTO mcq_questions (test_id, question_text, option_a, option_b, option_c, option_d, correct_answer, explanation)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                    ");
                    foreach ($questions as $q) {
                        $questionStmt->execute([$testId, $q['question_text'], $q['option_a'], $q['option_b'], $q['option_c'], $q['option_d'], $q['correct_answer'], $q['explanation']]);
                    }
                    
                    $message = 'MCQ test added successfully!';
                    $messageType = 'success';
                } else {
                    $testId = (int)$_POST['test_id'];
                    
                    // Update test
                    $stmt = $pdo->prepare("
                        UPDATE mcq_tests 
                        SET title = ?, description = ?, category = ?, level = ?, duration_minutes = ?, total_questions = ?, passing_score = ?, is_published = ?
                        WHERE id = ?
                    ");
                    $stmt->execute([$title, $description, $category, $level, $durationMinutes, count($questions), $passingScore, $isPublished, $testId]);
                    
                    // Delete existing questions
                    $pdo->prepare("DELETE FROM mcq_questions WHERE test_id = ?")->execute([$testId]);
                    
                    // Insert new questions
                    $questionStmt = $pdo->prepare("
                        INSERT INTO mcq_questions (test_id, question_text, option_a, option_b, option_c, option_d, correct_answer, explanation)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                    ");
                    foreach ($questions as $q) {
                        $questionStmt->execute([$testId, $q['question_text'], $q['option_a'], $q['option_b'], $q['option_c'], $q['option_d'], $q['correct_answer'], $q['explanation']]);
                    }
                    
                    $message = 'MCQ test updated successfully!';
                    $messageType = 'success';
                }
                
                $pdo->commit();
            } catch (PDOException $e) {
                $pdo->rollBack();
                $message = 'Error saving MCQ test: ' . $e->getMessage();
                $messageType = 'error';
            }
        }
    }
    
    // Delete MCQ test
    if ($action === 'delete') {
        $testId = (int)$_POST['test_id'];
        try {
            $stmt = $pdo->prepare("DELETE FROM mcq_tests WHERE id = ?");
            $stmt->execute([$testId]);
            $message = 'MCQ test deleted successfully!';
            $messageType = 'success';
        } catch (PDOException $e) {
            $message = 'Error deleting MCQ test: ' . $e->getMessage();
            $messageType = 'error';
        }
    }
    
    // Toggle publish status
    if ($action === 'toggle_publish') {
        $testId = (int)$_POST['test_id'];
        try {
            $stmt = $pdo->prepare("UPDATE mcq_tests SET is_published = NOT is_published WHERE id = ?");
            $stmt->execute([$testId]);
            $message = 'Publish status updated successfully!';
            $messageType = 'success';
        } catch (PDOException $e) {
            $message = 'Error updating publish status: ' . $e->getMessage();
            $messageType = 'error';
        }
    }
}

// Fetch all MCQ tests
try {
    $stmt = $pdo->query("
        SELECT t.*, 
               (SELECT COUNT(*) FROM mcq_attempts WHERE test_id = t.id) as attempt_count,
               (SELECT AVG(score) FROM mcq_attempts WHERE test_id = t.id) as avg_score
        FROM mcq_tests t 
        ORDER BY t.created_at DESC
    ");
    $tests = $stmt->fetchAll();
} catch (PDOException $e) {
    $tests = [];
    $message = 'Error fetching MCQ tests: ' . $e->getMessage();
    $messageType = 'error';
}

// Handle edit mode
$editingTest = null;
$editingQuestions = [];
if (isset($_GET['edit'])) {
    $editId = (int)$_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM mcq_tests WHERE id = ?");
    $stmt->execute([$editId]);
    $editingTest = $stmt->fetch();
    
    if ($editingTest) {
        $stmt = $pdo->prepare("SELECT * FROM mcq_questions WHERE test_id = ? ORDER BY id");
        $stmt->execute([$editId]);
        $editingQuestions = $stmt->fetchAll();
    }
}
?>

<div class="ma-admin-mcq-tests">
    <?php if ($message): ?>
        <div class="ma-admin-alert ma-admin-alert-<?php echo $messageType; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>
    
    <div class="ma-admin-mcq-tests-header">
        <h2>MCQ Tests Management</h2>
        <button class="ma-admin-btn" onclick="showAddForm()">+ Add New Test</button>
    </div>
    
    <div class="ma-admin-mcq-tests-content">
        <!-- MCQ Tests Table -->
        <div class="ma-admin-card">
            <div class="ma-admin-card-body">
                <?php if (empty($tests)): ?>
                    <p class="ma-admin-empty">No MCQ tests found.</p>
                <?php else: ?>
                    <table class="ma-admin-table">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Level</th>
                                <th>Questions</th>
                                <th>Duration</th>
                                <th>Attempts</th>
                                <th>Avg Score</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($tests as $test): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo htmlspecialchars($test['title']); ?></strong>
                                        <?php if ($test['description']): ?>
                                            <div class="ma-admin-subtext"><?php echo htmlspecialchars(substr($test['description'], 0, 40)); ?>...</div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="ma-admin-badge ma-admin-badge-info">
                                            <?php echo htmlspecialchars($test['category'] ?: 'General'); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="ma-admin-badge ma-admin-badge-secondary">
                                            <?php echo htmlspecialchars(ucfirst($test['level'])); ?>
                                        </span>
                                    </td>
                                    <td><?php echo $test['total_questions']; ?></td>
                                    <td><?php echo $test['duration_minutes']; ?> min</td>
                                    <td><?php echo $test['attempt_count']; ?></td>
                                    <td><?php echo $test['avg_score'] ? round($test['avg_score'], 1) . '%' : '-'; ?></td>
                                    <td>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                            <input type="hidden" name="action" value="toggle_publish">
                                            <input type="hidden" name="test_id" value="<?php echo $test['id']; ?>">
                                            <button type="submit" class="ma-admin-badge ma-admin-badge-<?php echo $test['is_published'] ? 'success' : 'warning'; ?>">
                                                <?php echo $test['is_published'] ? 'Published' : 'Draft'; ?>
                                            </button>
                                        </form>
                                    </td>
                                    <td>
                                        <div class="ma-admin-actions">
                                            <a href="?edit=<?php echo $test['id']; ?>" class="ma-admin-action-btn ma-admin-action-edit">Edit</a>
                                            <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this MCQ test?');">
                                                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="test_id" value="<?php echo $test['id']; ?>">
                                                <button type="submit" class="ma-admin-action-btn ma-admin-action-delete">Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Add/Edit Form Modal -->
    <div id="testFormModal" class="ma-admin-modal" style="display: <?php echo $editingTest ? 'block' : 'none'; ?>;">
        <div class="ma-admin-modal-content ma-admin-modal-large">
            <div class="ma-admin-modal-header">
                <h3><?php echo $editingTest ? 'Edit MCQ Test' : 'Add New MCQ Test'; ?></h3>
                <button class="ma-admin-close-btn" onclick="hideForm()">×</button>
            </div>
            <div class="ma-admin-modal-body">
                <form method="POST" id="mcqForm">
                    <input type="hidden" name="action" value="<?php echo $editingTest ? 'edit' : 'add'; ?>">
                    <?php if ($editingTest): ?>
                        <input type="hidden" name="test_id" value="<?php echo $editingTest['id']; ?>">
                    <?php endif; ?>
                    
                    <!-- Test Details -->
                    <div class="ma-admin-form-section">
                        <h4>Test Details</h4>
                        <div class="ma-admin-form-row">
                            <div class="ma-admin-form-group">
                                <label for="title">Title *</label>
                                <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($editingTest['title'] ?? ''); ?>" required placeholder="Test title">
                            </div>
                            
                            <div class="ma-admin-form-group">
                                <label for="category">Category</label>
                                <input type="text" id="category" name="category" value="<?php echo htmlspecialchars($editingTest['category'] ?? ''); ?>" placeholder="e.g., Quran, Arabic, Fiqh">
                            </div>
                        </div>
                        
                        <div class="ma-admin-form-row">
                            <div class="ma-admin-form-group">
                                <label for="level">Level</label>
                                <select id="level" name="level">
                                    <option value="beginner" <?php echo ($editingTest['level'] ?? 'beginner') == 'beginner' ? 'selected' : ''; ?>>Beginner</option>
                                    <option value="intermediate" <?php echo ($editingTest['level'] ?? 'beginner') == 'intermediate' ? 'selected' : ''; ?>>Intermediate</option>
                                    <option value="advanced" <?php echo ($editingTest['level'] ?? 'beginner') == 'advanced' ? 'selected' : ''; ?>>Advanced</option>
                                </select>
                            </div>
                            
                            <div class="ma-admin-form-group">
                                <label for="duration_minutes">Duration (Minutes)</label>
                                <input type="number" id="duration_minutes" name="duration_minutes" value="<?php echo htmlspecialchars($editingTest['duration_minutes'] ?? '30'); ?>" min="5" step="5">
                            </div>
                        </div>
                        
                        <div class="ma-admin-form-row">
                            <div class="ma-admin-form-group">
                                <label for="passing_score">Passing Score (%)</label>
                                <input type="number" id="passing_score" name="passing_score" value="<?php echo htmlspecialchars($editingTest['passing_score'] ?? '60'); ?>" min="1" max="100">
                            </div>
                            
                            <div class="ma-admin-form-group">
                                <label class="ma-admin-checkbox">
                                    <input type="checkbox" name="is_published" <?php echo ($editingTest['is_published'] ?? 0) ? 'checked' : ''; ?>>
                                    <span>Published (visible to students)</span>
                                </label>
                            </div>
                        </div>
                        
                        <div class="ma-admin-form-group">
                            <label for="description">Description</label>
                            <textarea id="description" name="description" rows="2" placeholder="Brief description of the test"><?php echo htmlspecialchars($editingTest['description'] ?? ''); ?></textarea>
                        </div>
                    </div>
                    
                    <!-- Questions Section -->
                    <div class="ma-admin-form-section">
                        <h4>Questions</h4>
                        <div id="questionsContainer">
                            <?php 
                            $questionsToEdit = $editingQuestions ?: [['question_text' => '', 'option_a' => '', 'option_b' => '', 'option_c' => '', 'option_d' => '', 'correct_answer' => 'A', 'explanation' => '']];
                            foreach ($questionsToEdit as $index => $q): 
                            ?>
                                <div class="ma-admin-question-card" data-index="<?php echo $index; ?>">
                                    <div class="ma-admin-question-header">
                                        <span>Question <?php echo $index + 1; ?></span>
                                        <button type="button" class="ma-admin-btn-remove" onclick="removeQuestion(<?php echo $index; ?>)">Remove</button>
                                    </div>
                                    <div class="ma-admin-form-group">
                                        <label>Question Text *</label>
                                        <textarea name="questions[<?php echo $index; ?>][question_text]" rows="2" required placeholder="Enter the question"><?php echo htmlspecialchars($q['question_text']); ?></textarea>
                                    </div>
                                    <div class="ma-admin-form-row">
                                        <div class="ma-admin-form-group">
                                            <label>Option A *</label>
                                            <input type="text" name="questions[<?php echo $index; ?>][option_a]" value="<?php echo htmlspecialchars($q['option_a']); ?>" required>
                                        </div>
                                        <div class="ma-admin-form-group">
                                            <label>Option B *</label>
                                            <input type="text" name="questions[<?php echo $index; ?>][option_b]" value="<?php echo htmlspecialchars($q['option_b']); ?>" required>
                                        </div>
                                    </div>
                                    <div class="ma-admin-form-row">
                                        <div class="ma-admin-form-group">
                                            <label>Option C *</label>
                                            <input type="text" name="questions[<?php echo $index; ?>][option_c]" value="<?php echo htmlspecialchars($q['option_c']); ?>" required>
                                        </div>
                                        <div class="ma-admin-form-group">
                                            <label>Option D *</label>
                                            <input type="text" name="questions[<?php echo $index; ?>][option_d]" value="<?php echo htmlspecialchars($q['option_d']); ?>" required>
                                        </div>
                                    </div>
                                    <div class="ma-admin-form-row">
                                        <div class="ma-admin-form-group">
                                            <label>Correct Answer *</label>
                                            <select name="questions[<?php echo $index; ?>][correct_answer]" required>
                                                <option value="A" <?php echo $q['correct_answer'] == 'A' ? 'selected' : ''; ?>>A</option>
                                                <option value="B" <?php echo $q['correct_answer'] == 'B' ? 'selected' : ''; ?>>B</option>
                                                <option value="C" <?php echo $q['correct_answer'] == 'C' ? 'selected' : ''; ?>>C</option>
                                                <option value="D" <?php echo $q['correct_answer'] == 'D' ? 'selected' : ''; ?>>D</option>
                                            </select>
                                        </div>
                                        <div class="ma-admin-form-group">
                                            <label>Explanation</label>
                                            <input type="text" name="questions[<?php echo $index; ?>][explanation]" value="<?php echo htmlspecialchars($q['explanation']); ?>" placeholder="Why this answer is correct">
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <button type="button" class="ma-admin-btn ma-admin-btn-secondary" onclick="addQuestion()">+ Add Question</button>
                    </div>
                    
                    <div class="ma-admin-form-actions">
                        <button type="button" class="ma-admin-btn ma-admin-btn-secondary" onclick="hideForm()">Cancel</button>
                        <button type="submit" class="ma-admin-btn"><?php echo $editingTest ? 'Update Test' : 'Add Test'; ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
let questionIndex = <?php echo count($editingQuestions); ?>;

function showAddForm() {
    document.getElementById('testFormModal').style.display = 'block';
    questionIndex = 1;
}

function hideForm() {
    document.getElementById('testFormModal').style.display = 'none';
    window.location.href = 'mcq-tests.php';
}

function addQuestion() {
    const container = document.getElementById('questionsContainer');
    const questionCard = document.createElement('div');
    questionCard.className = 'ma-admin-question-card';
    questionCard.dataset.index = questionIndex;
    
    questionCard.innerHTML = `
        <div class="ma-admin-question-header">
            <span>Question ${questionIndex + 1}</span>
            <button type="button" class="ma-admin-btn-remove" onclick="removeQuestion(${questionIndex})">Remove</button>
        </div>
        <div class="ma-admin-form-group">
            <label>Question Text *</label>
            <textarea name="questions[${questionIndex}][question_text]" rows="2" required placeholder="Enter the question"></textarea>
        </div>
        <div class="ma-admin-form-row">
            <div class="ma-admin-form-group">
                <label>Option A *</label>
                <input type="text" name="questions[${questionIndex}][option_a]" required>
            </div>
            <div class="ma-admin-form-group">
                <label>Option B *</label>
                <input type="text" name="questions[${questionIndex}][option_b]" required>
            </div>
        </div>
        <div class="ma-admin-form-row">
            <div class="ma-admin-form-group">
                <label>Option C *</label>
                <input type="text" name="questions[${questionIndex}][option_c]" required>
            </div>
            <div class="ma-admin-form-group">
                <label>Option D *</label>
                <input type="text" name="questions[${questionIndex}][option_d]" required>
            </div>
        </div>
        <div class="ma-admin-form-row">
            <div class="ma-admin-form-group">
                <label>Correct Answer *</label>
                <select name="questions[${questionIndex}][correct_answer]" required>
                    <option value="A">A</option>
                    <option value="B">B</option>
                    <option value="C">C</option>
                    <option value="D">D</option>
                </select>
            </div>
            <div class="ma-admin-form-group">
                <label>Explanation</label>
                <input type="text" name="questions[${questionIndex}][explanation]" placeholder="Why this answer is correct">
            </div>
        </div>
    `;
    
    container.appendChild(questionCard);
    questionIndex++;
}

function removeQuestion(index) {
    const card = document.querySelector(`.ma-admin-question-card[data-index="${index}"]`);
    if (card) {
        card.remove();
    }
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('testFormModal');
    if (event.target == modal) {
        hideForm();
    }
}
</script>

<style>
    .ma-admin-mcq-tests {
        display: flex;
        flex-direction: column;
        gap: 24px;
    }
    .ma-admin-mcq-tests-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .ma-admin-mcq-tests-header h2 {
        font-size: 24px;
        font-weight: 700;
        color: #111;
        margin: 0;
    }
    .ma-admin-btn {
        background: #0b2b2b;
        color: #fff;
        border: none;
        padding: 12px 24px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s;
    }
    .ma-admin-btn:hover {
        background: #143a3a;
    }
    .ma-admin-btn-secondary {
        background: #f0f0f0;
        color: #333;
    }
    .ma-admin-btn-secondary:hover {
        background: #e0e0e0;
    }
    .ma-admin-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        overflow: hidden;
    }
    .ma-admin-card-body {
        padding: 24px;
    }
    .ma-admin-table {
        width: 100%;
        border-collapse: collapse;
    }
    .ma-admin-table th {
        background: #f8f8f8;
        padding: 14px 16px;
        text-align: left;
        font-size: 12px;
        font-weight: 600;
        color: #666;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .ma-admin-table td {
        padding: 16px;
        border-bottom: 1px solid #f0f0f0;
        vertical-align: middle;
    }
    .ma-admin-table tr:last-child td {
        border-bottom: none;
    }
    .ma-admin-subtext {
        font-size: 12px;
        color: #888;
        margin-top: 4px;
    }
    .ma-admin-empty {
        text-align: center;
        padding: 40px;
        color: #888;
    }
    .ma-admin-form-section {
        margin-bottom: 32px;
        padding-bottom: 24px;
        border-bottom: 1px solid #f0f0f0;
    }
    .ma-admin-form-section:last-child {
        border-bottom: none;
    }
    .ma-admin-form-section h4 {
        font-size: 16px;
        font-weight: 700;
        color: #111;
        margin-bottom: 16px;
    }
    .ma-admin-form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }
    .ma-admin-form-group {
        margin-bottom: 16px;
    }
    .ma-admin-form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        font-size: 13px;
        color: #333;
    }
    .ma-admin-form-group input,
    .ma-admin-form-group select,
    .ma-admin-form-group textarea {
        width: 100%;
        padding: 12px;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 14px;
        box-sizing: border-box;
    }
    .ma-admin-form-group input:focus,
    .ma-admin-form-group select:focus,
    .ma-admin-form-group textarea:focus {
        outline: none;
        border-color: #0b2b2b;
    }
    .ma-admin-checkbox {
        display: flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
    }
    .ma-admin-checkbox input {
        width: auto;
    }
    .ma-admin-form-actions {
        display: flex;
        gap: 12px;
        justify-content: flex-end;
        margin-top: 24px;
    }
    .ma-admin-actions {
        display: flex;
        gap: 8px;
    }
    .ma-admin-action-btn {
        padding: 6px 12px;
        border: none;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
    }
    .ma-admin-action-edit {
        background: #e3f2fd;
        color: #1976d2;
    }
    .ma-admin-action-edit:hover {
        background: #bbdefb;
    }
    .ma-admin-action-delete {
        background: #ffebee;
        color: #c62828;
    }
    .ma-admin-action-delete:hover {
        background: #ffcdd2;
    }
    .ma-admin-badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
    }
    .ma-admin-badge-success {
        background: #e8f5e9;
        color: #1a5c3e;
    }
    .ma-admin-badge-warning {
        background: #fff8e1;
        color: #b7791f;
    }
    .ma-admin-badge-info {
        background: #e3f2fd;
        color: #1565c0;
    }
    .ma-admin-badge-secondary {
        background: #f5f5f5;
        color: #666;
    }
    .ma-admin-alert {
        padding: 16px;
        border-radius: 8px;
        margin-bottom: 24px;
        font-weight: 600;
    }
    .ma-admin-alert-success {
        background: #e8f5e9;
        color: #1a5c3e;
        border: 1px solid #c8e6c9;
    }
    .ma-admin-alert-error {
        background: #ffebee;
        color: #c62828;
        border: 1px solid #ffcdd2;
    }
    .ma-admin-modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 1000;
    }
    .ma-admin-modal-content {
        background: #fff;
        border-radius: 12px;
        width: 90%;
        max-width: 800px;
        max-height: 90vh;
        overflow-y: auto;
    }
    .ma-admin-modal-large {
        max-width: 1000px;
    }
    .ma-admin-modal-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 20px 24px;
        border-bottom: 1px solid #f0f0f0;
    }
    .ma-admin-modal-header h3 {
        margin: 0;
        font-size: 18px;
        font-weight: 700;
        color: #111;
    }
    .ma-admin-close-btn {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        transition: background 0.2s;
        background: none;
        border: none;
        font-size: 24px;
        cursor: pointer;
    }
    .ma-admin-close-btn:hover {
        background: #f0f0f0;
        color: #333;
    }
    .ma-admin-modal-body {
        padding: 24px;
    }
    .ma-admin-question-card {
        background: #f8f8f8;
        border-radius: 8px;
        padding: 16px;
        margin-bottom: 16px;
    }
    .ma-admin-question-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 12px;
        font-weight: 600;
        color: #333;
    }
    .ma-admin-btn-remove {
        background: #ffebee;
        color: #c62828;
        border: none;
        padding: 4px 12px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
    }
    .ma-admin-btn-remove:hover {
        background: #ffcdd2;
    }
</style>

<?php require_once 'footer.php'; ?>
