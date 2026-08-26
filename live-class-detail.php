<?php
require_once 'config.php';
require_once 'auth.php';

// Get database connection
$pdo = getDB();

// Get class ID from URL
$classId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch live class details with course info
$stmt = $pdo->prepare("
    SELECT lc.*, c.title as course_title, c.category, c.id as course_id
    FROM live_classes lc
    JOIN courses c ON lc.course_id = c.id
    WHERE lc.id = ? AND lc.is_published = 1
");
$stmt->execute([$classId]);
$liveClass = $stmt->fetch();

if (!$liveClass) {
    header('Location: live-classes.php');
    exit;
}

// Check if user is enrolled in the course
$user = getCurrentUser();
$isEnrolled = false;
if ($user) {
    $stmt = $pdo->prepare("SELECT * FROM enrollments WHERE user_id = ? AND course_id = ?");
    $stmt->execute([$user['id'], $liveClass['course_id']]);
    $isEnrolled = $stmt->fetch() !== false;
}

// Check if class has already passed
$isPast = strtotime($liveClass['scheduled_at']) < time();
?>
<?php include 'header.php'; ?>
      <section class="ma-page-banner">
        <h1><?php echo htmlspecialchars($liveClass['title']); ?></h1>
        <p><?php echo htmlspecialchars($liveClass['course_title']); ?></p>
      </section>

      <section class="ma-live-class-detail">
        <div class="ma-live-class-detail-inner">
          <div class="ma-live-class-main">
            <div class="ma-live-class-hero">
              <div class="ma-live-class-status">
                <?php if ($isPast): ?>
                  <span class="ma-status-badge ma-status-past">Past Class</span>
                <?php else: ?>
                  <span class="ma-status-badge ma-status-upcoming">Upcoming</span>
                <?php endif; ?>
              </div>
              <div class="ma-live-class-datetime">
                <div class="ma-datetime-card">
                  <div class="ma-datetime-icon">📅</div>
                  <div class="ma-datetime-info">
                    <span class="ma-datetime-label">Date</span>
                    <span class="ma-datetime-value"><?php echo date('F j, Y', strtotime($liveClass['scheduled_at'])); ?></span>
                  </div>
                </div>
                <div class="ma-datetime-card">
                  <div class="ma-datetime-icon">🕐</div>
                  <div class="ma-datetime-info">
                    <span class="ma-datetime-label">Time</span>
                    <span class="ma-datetime-value"><?php echo date('h:i A', strtotime($liveClass['scheduled_at'])); ?></span>
                  </div>
                </div>
                <div class="ma-datetime-card">
                  <div class="ma-datetime-icon">⏱️</div>
                  <div class="ma-datetime-info">
                    <span class="ma-datetime-label">Duration</span>
                    <span class="ma-datetime-value"><?php echo $liveClass['duration_minutes']; ?> minutes</span>
                  </div>
                </div>
              </div>
            </div>

            <div class="ma-live-class-content">
              <h2>About This Class</h2>
              <p><?php echo nl2br(htmlspecialchars($liveClass['description'])); ?></p>

              <h2>Class Details</h2>
              <div class="ma-class-details">
                <div class="ma-class-detail-item">
                  <span class="ma-detail-label">Instructor</span>
                  <span class="ma-detail-value"><?php echo htmlspecialchars($liveClass['instructor']); ?></span>
                </div>
                <div class="ma-class-detail-item">
                  <span class="ma-detail-label">Course</span>
                  <span class="ma-detail-value"><?php echo htmlspecialchars($liveClass['course_title']); ?></span>
                </div>
                <div class="ma-class-detail-item">
                  <span class="ma-detail-label">Category</span>
                  <span class="ma-detail-value"><?php echo htmlspecialchars($liveClass['category']); ?></span>
                </div>
                <div class="ma-class-detail-item">
                  <span class="ma-detail-label">Max Students</span>
                  <span class="ma-detail-value"><?php echo $liveClass['max_students']; ?> students</span>
                </div>
              </div>

              <?php if (!$isPast && $isEnrolled): ?>
                <h2>Join the Class</h2>
                <div class="ma-join-class">
                  <div class="ma-join-info">
                    <h3>Meeting Details</h3>
                    <?php if ($liveClass['meeting_id']): ?>
                      <p><strong>Meeting ID:</strong> <?php echo htmlspecialchars($liveClass['meeting_id']); ?></p>
                    <?php endif; ?>
                    <?php if ($liveClass['meeting_password']): ?>
                      <p><strong>Password:</strong> <?php echo htmlspecialchars($liveClass['meeting_password']); ?></p>
                    <?php endif; ?>
                  </div>
                  <a href="<?php echo htmlspecialchars($liveClass['meeting_link']); ?>" class="ma-btn-gold" target="_blank">
                    <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="5,3 19,12 5,21"/></svg>
                    Join Live Class
                  </a>
                </div>
              <?php elseif (!$isPast && !$isEnrolled): ?>
                <div class="ma-enroll-notice">
                  <h3>Enrollment Required</h3>
                  <p>You must be enrolled in this course to join the live class.</p>
                  <?php if ($user): ?>
                    <a href="course-detail.php?id=<?php echo $liveClass['course_id']; ?>" class="ma-btn-gold">View Course</a>
                  <?php else: ?>
                    <a href="login.php" class="ma-btn-gold">Login to Enroll</a>
                  <?php endif; ?>
                </div>
              <?php else: ?>
                <div class="ma-class-past-notice">
                  <h3>This class has already ended</h3>
                  <p>Check the course page for recordings or upcoming classes.</p>
                  <a href="course-detail.php?id=<?php echo $liveClass['course_id']; ?>" class="ma-btn-outline">View Course</a>
                </div>
              <?php endif; ?>
            </div>
          </div>

          <div class="ma-live-class-sidebar">
            <div class="ma-sidebar-card">
              <h3>Course Information</h3>
              <div class="ma-course-mini">
                <div class="ma-course-mini-img" style="background: linear-gradient(135deg, #0d3b2e 0%, #1a5c3e 100%);">
                  <span>📚</span>
                </div>
                <div class="ma-course-mini-info">
                  <h4><?php echo htmlspecialchars($liveClass['course_title']); ?></h4>
                  <span><?php echo htmlspecialchars($liveClass['category']); ?></span>
                </div>
              </div>
              <a href="course-detail.php?id=<?php echo $liveClass['course_id']; ?>" class="ma-btn-outline">View Course Details</a>
            </div>

            <div class="ma-sidebar-card">
              <h3>Need Help?</h3>
              <p>Having trouble joining the class or have questions?</p>
              <a href="student-support.php" class="ma-btn-outline">Get Support</a>
            </div>
          </div>
        </div>
      </section>
<?php include 'footer.php'; ?>
