<?php
require_once 'config.php';
require_once 'auth.php';

// Get database connection
$pdo = getDB();

// Get test ID from URL
$testId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Check if user is logged in
$user = getCurrentUser();
if (!$user) {
    header('Location: login.php');
    exit;
}

// Fetch test details
$stmt = $pdo->prepare("SELECT * FROM mcq_tests WHERE id = ? AND is_published = 1");
$stmt->execute([$testId]);
$test = $stmt->fetch();

if (!$test) {
    header('Location: mcq-tests.php');
    exit;
}

// Fetch questions for this test
$stmt = $pdo->prepare("SELECT * FROM mcq_questions WHERE test_id = ? ORDER BY id ASC");
$stmt->execute([$testId]);
$questions = $stmt->fetchAll();

// Check if user has already taken this test
$stmt = $pdo->prepare("SELECT * FROM mcq_attempts WHERE user_id = ? AND test_id = ?");
$stmt->execute([$user['id'], $testId]);
$previousAttempt = $stmt->fetch();

// Handle test submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_test'])) {
    $correctAnswers = 0;
    $totalQuestions = count($questions);
    
    foreach ($questions as $question) {
        $userAnswer = isset($_POST['question_' . $question['id']]) ? $_POST['question_' . $question['id']] : '';
        if ($userAnswer === $question['correct_answer']) {
            $correctAnswers++;
        }
    }
    
    $score = round(($correctAnswers / $totalQuestions) * 100);
    
    // Calculate time taken (simplified - in production, track start time)
    $timeTaken = $test['duration_minutes'] ?? 0;
    
    // Save attempt
    $stmt = $pdo->prepare("
        INSERT INTO mcq_attempts (user_id, test_id, score, total_questions, correct_answers, time_taken_minutes)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([$user['id'], $testId, $score, $totalQuestions, $correctAnswers, $timeTaken]);
    
    header('Location: dashboard.php');
    exit;
}
?>
<?php include 'header.php'; ?>
      <section class="ma-page-banner">
        <h1><?php echo htmlspecialchars($test['title']); ?></h1>
        <p><?php echo htmlspecialchars($test['category']); ?> • <?php echo ucfirst(htmlspecialchars($test['level'])); ?></p>
      </section>

      <section class="ma-mcq-test">
        <div class="ma-mcq-test-inner">
          <?php if ($previousAttempt): ?>
            <div class="ma-test-already-taken">
              <div class="ma-already-taken-icon">✓</div>
              <h2>You've already taken this test</h2>
              <p>Your previous score: <strong><?php echo $previousAttempt['score']; ?>%</strong></p>
              <p>You answered <?php echo $previousAttempt['correct_answers']; ?> out of <?php echo $previousAttempt['total_questions']; ?> questions correctly.</p>
              <div class="ma-already-taken-actions">
                <a href="dashboard.php" class="ma-btn-gold">View Dashboard</a>
                <a href="mcq-tests.php" class="ma-btn-outline">Browse More Tests</a>
              </div>
            </div>
          <?php else: ?>
            <div class="ma-test-info">
              <div class="ma-test-info-card">
                <div class="ma-test-info-item">
                  <span class="ma-info-icon">📝</span>
                  <div>
                    <span class="ma-info-label">Questions</span>
                    <span class="ma-info-value"><?php echo count($questions); ?></span>
                  </div>
                </div>
                <div class="ma-test-info-item">
                  <span class="ma-info-icon">⏱️</span>
                  <div>
                    <span class="ma-info-label">Duration</span>
                    <span class="ma-info-value"><?php echo $test['duration_minutes']; ?> mins</span>
                  </div>
                </div>
                <div class="ma-test-info-item">
                  <span class="ma-info-icon">🎯</span>
                  <div>
                    <span class="ma-info-label">Passing Score</span>
                    <span class="ma-info-value"><?php echo $test['passing_score']; ?>%</span>
                  </div>
                </div>
              </div>
            </div>

            <?php if (!empty($questions)): ?>
              <form method="POST" class="ma-test-form">
                <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                
                <div class="ma-questions-list">
                  <?php foreach ($questions as $index => $question): ?>
                    <div class="ma-question-card">
                      <div class="ma-question-header">
                        <span class="ma-question-number">Question <?php echo $index + 1; ?></span>
                      </div>
                      <div class="ma-question-text">
                        <?php echo nl2br(htmlspecialchars($question['question_text'])); ?>
                      </div>
                      <div class="ma-question-options">
                        <label class="ma-option-label">
                          <input type="radio" name="question_<?php echo $question['id']; ?>" value="A" required>
                          <span class="ma-option-text"><?php echo htmlspecialchars($question['option_a']); ?></span>
                        </label>
                        <label class="ma-option-label">
                          <input type="radio" name="question_<?php echo $question['id']; ?>" value="B">
                          <span class="ma-option-text"><?php echo htmlspecialchars($question['option_b']); ?></span>
                        </label>
                        <label class="ma-option-label">
                          <input type="radio" name="question_<?php echo $question['id']; ?>" value="C">
                          <span class="ma-option-text"><?php echo htmlspecialchars($question['option_c']); ?></span>
                        </label>
                        <label class="ma-option-label">
                          <input type="radio" name="question_<?php echo $question['id']; ?>" value="D">
                          <span class="ma-option-text"><?php echo htmlspecialchars($question['option_d']); ?></span>
                        </label>
                      </div>
                    </div>
                  <?php endforeach; ?>
                </div>

                <div class="ma-test-actions">
                  <button type="submit" name="submit_test" class="ma-btn-gold">Submit Test</button>
                  <a href="mcq-tests.php" class="ma-btn-outline">Cancel</a>
                </div>
              </form>
            <?php else: ?>
              <div class="ma-empty-state">
                <p>No questions available for this test.</p>
                <a href="mcq-tests.php" class="ma-btn-gold">Browse Other Tests</a>
              </div>
            <?php endif; ?>
          <?php endif; ?>
        </div>
      </section>
<?php include 'footer.php'; ?>
