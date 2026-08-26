<?php
require_once 'config.php';
require_once 'auth.php';

// Get database connection
$pdo = getDB();

// Check if user is logged in
if (!auth()->check()) {
    setFlashMessage('error', 'Please log in to access this course.');
    header('Location: login.php');
    exit;
}

$user = auth()->user();

$testId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch test details
try {
    $stmt = $pdo->prepare("SELECT * FROM mcq_tests WHERE id = ? AND is_published = 1");
    $stmt->execute([$testId]);
    $test = $stmt->fetch();
    
    if (!$test) {
        die('Test not found or not published.');
    }
    
    // Fetch questions
    $stmt = $pdo->prepare("SELECT * FROM mcq_questions WHERE test_id = ? ORDER BY id");
    $stmt->execute([$testId]);
    $questions = $stmt->fetchAll();
    
    if (empty($questions)) {
        die('No questions found for this test.');
    }
    
} catch (PDOException $e) {
    die('Error loading test: ' . $e->getMessage());
}

// Handle test submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_test'])) {
    $answers = $_POST['answers'] ?? [];
    $correctCount = 0;
    $results = [];
    
    foreach ($questions as $question) {
        $userAnswer = strtoupper($answers[$question['id']] ?? '');
        $correctAnswer = strtoupper($question['correct_answer']);
        $isCorrect = $userAnswer === $correctAnswer;
        
        if ($isCorrect) {
            $correctCount++;
        }
        
        $results[] = [
            'question' => $question,
            'user_answer' => $userAnswer,
            'correct_answer' => $correctAnswer,
            'is_correct' => $isCorrect
        ];
    }
    
    $score = round(($correctCount / count($questions)) * 100);
    $passed = $score >= $test['passing_score'];
    
    // Record attempt (without user_id for now, can be added later when user auth is implemented)
    try {
        $stmt = $pdo->prepare("
            INSERT INTO mcq_attempts (user_id, test_id, score, total_questions, correct_answers, time_taken_minutes, created_at)
            VALUES (?, ?, ?, ?, ?, ?, NOW())
        ");
        $stmt->execute([
            0, // user_id - 0 for guest users
            $testId,
            $score,
            count($questions),
            $correctCount,
            $_POST['time_taken'] ?? 0
        ]);
    } catch (PDOException $e) {
        // Continue even if recording fails
    }
    
    // Show results
    include 'header.php';
    ?>
    <section class="ma-page-banner">
        <h1>Test Results</h1>
        <p><?php echo htmlspecialchars($test['title']); ?></p>
    </section>
    
    <section style="padding: 40px 24px; max-width: 800px; margin: 0 auto;">
        <div style="background: #fff; border-radius: 12px; padding: 32px; box-shadow: 0 2px 12px rgba(0,0,0,0.08); text-align: center; margin-bottom: 32px;">
            <div style="font-size: 64px; font-weight: 800; color: <?php echo $passed ? '#1a5c3e' : '#c62828'; ?>; margin-bottom: 8px;">
                <?php echo $score; ?>%
            </div>
            <div style="font-size: 18px; font-weight: 600; color: #333; margin-bottom: 16px;">
                <?php echo $passed ? '🎉 Passed!' : '❌ Not Passed'; ?>
            </div>
            <div style="font-size: 14px; color: #666; margin-bottom: 24px;">
                You answered <?php echo $correctCount; ?> out of <?php echo count($questions); ?> questions correctly
            </div>
            <div style="display: flex; gap: 12px; justify-content: center;">
                <a href="mcq-tests.php" class="ma-enroll-btn" style="text-decoration: none; display: inline-block;">Back to Tests</a>
                <a href="take-mcq-test.php?id=<?php echo $testId; ?>" class="ma-filter-pill" style="text-decoration: none; display: inline-block; cursor: pointer;">Retake Test</a>
            </div>
        </div>
        
        <h2 style="font-size: 20px; font-weight: 700; margin-bottom: 20px;">Question Review</h2>
        
        <?php foreach ($results as $index => $result): ?>
            <div style="background: #fff; border-radius: 8px; padding: 24px; margin-bottom: 16px; border-left: 4px solid <?php echo $result['is_correct'] ? '#1a5c3e' : '#c62828'; ?>; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">
                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px;">
                    <span style="font-weight: 600; color: #333;">Question <?php echo $index + 1; ?></span>
                    <span style="background: <?php echo $result['is_correct'] ? '#e8f5e9' : '#ffebee'; ?>; color: <?php echo $result['is_correct'] ? '#1a5c3e' : '#c62828'; ?>; padding: 4px 10px; border-radius: 12px; font-size: 12px; font-weight: 600;">
                        <?php echo $result['is_correct'] ? 'Correct' : 'Incorrect'; ?>
                    </span>
                </div>
                <p style="font-size: 15px; color: #333; margin-bottom: 16px; line-height: 1.6;">
                    <?php echo htmlspecialchars($result['question']['question_text']); ?>
                </p>
                <div style="display: grid; gap: 8px; margin-bottom: 16px;">
                    <?php 
                    $options = ['A' => $result['question']['option_a'], 'B' => $result['question']['option_b'], 'C' => $result['question']['option_c'], 'D' => $result['question']['option_d']];
                    foreach ($options as $key => $value): 
                        $isSelected = $result['user_answer'] === $key;
                        $isCorrect = $result['correct_answer'] === $key;
                        $bgColor = $isCorrect ? '#e8f5e9' : ($isSelected ? '#ffebee' : '#f5f5f5');
                        $textColor = $isCorrect ? '#1a5c3e' : ($isSelected ? '#c62828' : '#333');
                    ?>
                        <div style="background: <?php echo $bgColor; ?>; padding: 10px 14px; border-radius: 6px; display: flex; align-items: center; gap: 10px;">
                            <span style="background: <?php echo $textColor; ?>; color: #fff; width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 600;"><?php echo $key; ?></span>
                            <span style="color: <?php echo $textColor; ?>; font-size: 14px;"><?php echo htmlspecialchars($value); ?></span>
                            <?php if ($isCorrect): ?>
                                <span style="color: #1a5c3e; font-size: 12px; margin-left: auto;">✓ Correct</span>
                            <?php elseif ($isSelected && !$isCorrect): ?>
                                <span style="color: #c62828; font-size: 12px; margin-left: auto;">✗ Your answer</span>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php if ($result['question']['explanation']): ?>
                    <div style="background: #e3f2fd; padding: 12px; border-radius: 6px; font-size: 13px; color: #1565c0; line-height: 1.5;">
                        <strong>Explanation:</strong> <?php echo htmlspecialchars($result['question']['explanation']); ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </section>
    <?php
    include 'footer.php';
    exit;
}

include 'header.php';
?>
<style>
    .mcq-test-container {
        max-width: 800px;
        margin: 0 auto;
        padding: 40px 24px;
    }
    .mcq-timer {
        background: #0b2b2b;
        color: #fff;
        padding: 16px 24px;
        border-radius: 8px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 32px;
    }
    .mcq-timer-display {
        font-size: 24px;
        font-weight: 700;
    }
    .mcq-question-card {
        background: #fff;
        border-radius: 12px;
        padding: 24px;
        margin-bottom: 24px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06);
    }
    .mcq-question-number {
        font-size: 14px;
        color: #c9a227;
        font-weight: 600;
        margin-bottom: 8px;
    }
    .mcq-question-text {
        font-size: 16px;
        color: #333;
        line-height: 1.6;
        margin-bottom: 20px;
    }
    .mcq-option {
        background: #f5f5f5;
        padding: 14px 18px;
        border-radius: 8px;
        margin-bottom: 12px;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .mcq-option:hover {
        background: #e8e8e8;
    }
    .mcq-option.selected {
        background: #e3f2fd;
        border: 2px solid #1976d2;
    }
    .mcq-option-radio {
        width: 20px;
        height: 20px;
        accent-color: #1976d2;
    }
    .mcq-submit-btn {
        background: #0b2b2b;
        color: #fff;
        border: none;
        padding: 16px 32px;
        border-radius: 8px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        width: 100%;
        margin-top: 24px;
    }
    .mcq-submit-btn:hover {
        background: #143a3a;
    }
</style>

<section class="ma-page-banner">
    <h1><?php echo htmlspecialchars($test['title']); ?></h1>
    <p><?php echo htmlspecialchars($test['description'] ?: 'Test your knowledge'); ?></p>
</section>

<div class="mcq-test-container">
    <div class="mcq-timer">
        <div>
            <div style="font-size: 12px; color: rgba(255,255,255,0.7); margin-bottom: 4px;">Time Remaining</div>
            <div class="mcq-timer-display" id="timerDisplay"><?php echo $test['duration_minutes']; ?>:00</div>
        </div>
        <div>
            <div style="font-size: 12px; color: rgba(255,255,255,0.7); margin-bottom: 4px;">Progress</div>
            <div style="font-size: 16px; font-weight: 600;"><span id="answeredCount">0</span> / <?php echo count($questions); ?></div>
        </div>
    </div>
    
    <form method="POST" id="mcqForm">
        <input type="hidden" name="time_taken" id="timeTaken" value="0">
        <input type="hidden" name="submit_test" value="1">
        
        <?php foreach ($questions as $index => $question): ?>
            <div class="mcq-question-card" id="question-<?php echo $question['id']; ?>">
                <div class="mcq-question-number">Question <?php echo $index + 1; ?> of <?php echo count($questions); ?></div>
                <p class="mcq-question-text"><?php echo htmlspecialchars($question['question_text']); ?></p>
                
                <div class="mcq-option" onclick="selectOption(<?php echo $question['id']; ?>, 'A')">
                    <input type="radio" name="answers[<?php echo $question['id']; ?>]" value="A" class="mcq-option-radio" id="opt-<?php echo $question['id']; ?>-A">
                    <label for="opt-<?php echo $question['id']; ?>-A" style="cursor: pointer; flex: 1;">A) <?php echo htmlspecialchars($question['option_a']); ?></label>
                </div>
                
                <div class="mcq-option" onclick="selectOption(<?php echo $question['id']; ?>, 'B')">
                    <input type="radio" name="answers[<?php echo $question['id']; ?>]" value="B" class="mcq-option-radio" id="opt-<?php echo $question['id']; ?>-B">
                    <label for="opt-<?php echo $question['id']; ?>-B" style="cursor: pointer; flex: 1;">B) <?php echo htmlspecialchars($question['option_b']); ?></label>
                </div>
                
                <div class="mcq-option" onclick="selectOption(<?php echo $question['id']; ?>, 'C')">
                    <input type="radio" name="answers[<?php echo $question['id']; ?>]" value="C" class="mcq-option-radio" id="opt-<?php echo $question['id']; ?>-C">
                    <label for="opt-<?php echo $question['id']; ?>-C" style="cursor: pointer; flex: 1;">C) <?php echo htmlspecialchars($question['option_c']); ?></label>
                </div>
                
                <div class="mcq-option" onclick="selectOption(<?php echo $question['id']; ?>, 'D')">
                    <input type="radio" name="answers[<?php echo $question['id']; ?>]" value="D" class="mcq-option-radio" id="opt-<?php echo $question['id']; ?>-D">
                    <label for="opt-<?php echo $question['id']; ?>-D" style="cursor: pointer; flex: 1;">D) <?php echo htmlspecialchars($question['option_d']); ?></label>
                </div>
            </div>
        <?php endforeach; ?>
        
        <button type="submit" class="mcq-submit-btn">Submit Test</button>
    </form>
</div>

<script>
let timeRemaining = <?php echo $test['duration_minutes'] * 60; ?>;
let timeElapsed = 0;
let timerInterval;

function startTimer() {
    timerInterval = setInterval(() => {
        timeElapsed++;
        timeRemaining--;
        
        const minutes = Math.floor(timeRemaining / 60);
        const seconds = timeRemaining % 60;
        document.getElementById('timerDisplay').textContent = 
            String(minutes).padStart(2, '0') + ':' + String(seconds).padStart(2, '0');
        document.getElementById('timeTaken').value = Math.floor(timeElapsed / 60);
        
        if (timeRemaining <= 0) {
            clearInterval(timerInterval);
            alert('Time is up! Submitting your test.');
            document.getElementById('mcqForm').submit();
        }
    }, 1000);
}

function selectOption(questionId, option) {
    const radio = document.getElementById('opt-' + questionId + '-' + option);
    radio.checked = true;
    
    // Update visual selection
    const options = document.querySelectorAll(`#question-${questionId} .mcq-option`);
    options.forEach(opt => opt.classList.remove('selected'));
    event.currentTarget.classList.add('selected');
    
    updateProgress();
}

function updateProgress() {
    const answered = document.querySelectorAll('input[type="radio"]:checked').length;
    document.getElementById('answeredCount').textContent = answered;
}

// Start timer when page loads
window.onload = function() {
    startTimer();
};
</script>

<?php include 'footer.php'; ?>
