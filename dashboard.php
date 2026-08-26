<?php
require_once 'config.php';
require_once 'auth.php';

// Get database connection
$pdo = getDB();

// Check if user is logged in
if (!auth()->check()) {
    header('Location: login.php');
    exit;
}

$user = auth()->user();

// Fetch user enrollments with course details
$stmt = $pdo->prepare("
    SELECT e.*, c.title, c.category, c.level, c.image_url, c.id as course_id
    FROM enrollments e
    JOIN courses c ON e.course_id = c.id
    WHERE e.user_id = ?
    ORDER BY e.enrolled_at DESC
");
$stmt->execute([$user['id']]);
$enrollments = $stmt->fetchAll();

// Fetch user MCQ attempts
$stmt = $pdo->prepare("
    SELECT ma.*, mt.title, mt.category
    FROM mcq_attempts ma
    JOIN mcq_tests mt ON ma.test_id = mt.id
    WHERE ma.user_id = ?
    ORDER BY ma.completed_at DESC
    LIMIT 10
");
$stmt->execute([$user['id']]);
$attempts = $stmt->fetchAll();

// Fetch upcoming live classes for enrolled courses
$courseIds = array_column($enrollments, 'course_id');
$upcomingClasses = [];
if (!empty($courseIds)) {
    $placeholders = str_repeat('?,', count($courseIds) - 1) . '?';
    $stmt = $pdo->prepare("
        SELECT lc.*, c.title as course_title
        FROM live_classes lc
        JOIN courses c ON lc.course_id = c.id
        WHERE lc.course_id IN ($placeholders)
        AND lc.is_published = 1
        AND lc.scheduled_at > NOW()
        ORDER BY lc.scheduled_at ASC
        LIMIT 5
    ");
    $stmt->execute($courseIds);
    $upcomingClasses = $stmt->fetchAll();
}
?>
<?php include 'header.php'; ?>
      <section class="ma-page-banner">
        <h1>My Dashboard</h1>
        <p>Welcome back, <?php echo htmlspecialchars($user['name']); ?></p>
      </section>

      <section class="ma-dashboard">
        <div class="ma-dashboard-inner">
          <div class="ma-dashboard-main">
            <div class="ma-dashboard-stats">
              <div class="ma-stat-card">
                <div class="ma-stat-icon">📚</div>
                <div class="ma-stat-num"><?php echo count($enrollments); ?></div>
                <div class="ma-stat-label">Enrolled Courses</div>
              </div>
              <div class="ma-stat-card">
                <div class="ma-stat-icon">📝</div>
                <div class="ma-stat-num"><?php echo count($attempts); ?></div>
                <div class="ma-stat-label">Tests Taken</div>
              </div>
              <div class="ma-stat-card">
                <div class="ma-stat-icon">🎬</div>
                <div class="ma-stat-num"><?php echo count($upcomingClasses); ?></div>
                <div class="ma-stat-label">Upcoming Classes</div>
              </div>
            </div>

            <?php if (!empty($upcomingClasses)): ?>
              <div class="ma-dashboard-section">
                <h2>Upcoming Live Classes</h2>
                <div class="ma-upcoming-classes">
                  <?php foreach ($upcomingClasses as $class): ?>
                    <div class="ma-class-card">
                      <div class="ma-class-date">
                        <div class="ma-date-day"><?php echo date('d', strtotime($class['scheduled_at'])); ?></div>
                        <div class="ma-date-month"><?php echo date('M', strtotime($class['scheduled_at'])); ?></div>
                      </div>
                      <div class="ma-class-info">
                        <h3><?php echo htmlspecialchars($class['title']); ?></h3>
                        <p><?php echo htmlspecialchars($class['course_title']); ?></p>
                        <div class="ma-class-meta">
                          <span>🕐 <?php echo date('h:i A', strtotime($class['scheduled_at'])); ?></span>
                          <span>⏱️ <?php echo $class['duration_minutes']; ?> mins</span>
                        </div>
                      </div>
                      <a href="<?php echo htmlspecialchars($class['meeting_link']); ?>" class="ma-btn-gold" target="_blank">Join Class</a>
                    </div>
                  <?php endforeach; ?>
                </div>
              </div>
            <?php endif; ?>

            <div class="ma-dashboard-section">
              <h2>My Courses</h2>
              <?php if (!empty($enrollments)): ?>
                <div class="ma-enrolled-courses">
                  <?php foreach ($enrollments as $enrollment): ?>
                    <a href="course-detail.php?id=<?php echo $enrollment['course_id']; ?>" class="ma-enrolled-course-card">
                      <div class="ma-enrolled-course-img" style="background: linear-gradient(135deg, #0d3b2e 0%, #1a5c3e 100%);">
                        <?php if ($enrollment['image_url']): ?>
                          <img src="<?php echo htmlspecialchars($enrollment['image_url']); ?>" alt="<?php echo htmlspecialchars($enrollment['title']); ?>">
                        <?php else: ?>
                          <span>📚</span>
                        <?php endif; ?>
                      </div>
                      <div class="ma-enrolled-course-info">
                        <h3><?php echo htmlspecialchars($enrollment['title']); ?></h3>
                        <span><?php echo htmlspecialchars($enrollment['category']); ?></span>
                        <div class="ma-progress-bar">
                          <div class="ma-progress-fill" style="width: <?php echo $enrollment['progress']; ?>%;"></div>
                        </div>
                        <span class="ma-progress-text"><?php echo $enrollment['progress']; ?>% Complete</span>
                      </div>
                    </a>
                  <?php endforeach; ?>
                </div>
              <?php else: ?>
                <div class="ma-empty-state">
                  <p>You haven't enrolled in any courses yet.</p>
                  <a href="courses.php" class="ma-btn-gold">Browse Courses</a>
                </div>
              <?php endif; ?>
            </div>

            <div class="ma-dashboard-section">
              <h2>Recent Test Results</h2>
              <?php if (!empty($attempts)): ?>
                <div class="ma-test-results">
                  <?php foreach ($attempts as $attempt): ?>
                    <div class="ma-test-result-card">
                      <div class="ma-test-result-info">
                        <h3><?php echo htmlspecialchars($attempt['title']); ?></h3>
                        <span><?php echo htmlspecialchars($attempt['category']); ?></span>
                        <span><?php echo date('M j, Y', strtotime($attempt['completed_at'])); ?></span>
                      </div>
                      <div class="ma-test-result-score">
                        <div class="ma-score-circle">
                          <span><?php echo $attempt['score']; ?>%</span>
                        </div>
                        <span><?php echo $attempt['correct_answers']; ?>/<?php echo $attempt['total_questions']; ?> Correct</span>
                      </div>
                    </div>
                  <?php endforeach; ?>
                </div>
              <?php else: ?>
                <div class="ma-empty-state">
                  <p>You haven't taken any tests yet.</p>
                  <a href="mcq-tests.php" class="ma-btn-gold">Take a Test</a>
                </div>
              <?php endif; ?>
            </div>
          </div>

          <div class="ma-dashboard-sidebar">
            <div class="ma-sidebar-card">
              <h3>Profile</h3>
              <div class="ma-user-profile">
                <div class="ma-user-avatar">
                  <span><?php echo strtoupper(substr(htmlspecialchars($user['name']), 0, 2)); ?></span>
                </div>
                <div class="ma-user-details">
                  <h4><?php echo htmlspecialchars($user['name']); ?></h4>
                  <p><?php echo htmlspecialchars($user['email']); ?></p>
                </div>
              </div>
              <div class="ma-user-actions">
                <a href="#" class="ma-btn-outline">Edit Profile</a>
                <a href="logout.php" class="ma-btn-outline" style="color: #dc2626; border-color: #dc2626;">Logout</a>
              </div>
            </div>

            <div class="ma-sidebar-card">
              <h3>Quick Links</h3>
              <ul class="ma-quick-links">
                <li><a href="courses.php">Browse Courses</a></li>
                <li><a href="live-classes.php">Live Classes</a></li>
                <li><a href="mcq-tests.php">MCQ Tests</a></li>
                <li><a href="study-materials.php">Study Materials</a></li>
                <li><a href="student-support.php">Student Support</a></li>
              </ul>
            </div>
          </div>
        </div>
      </section>
<?php include 'footer.php'; ?>
