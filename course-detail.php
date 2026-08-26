<?php
require_once 'config.php';
require_once 'auth.php';

// Get database connection
$pdo = getDB();

// Get course ID from URL
$courseId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch course details
$stmt = $pdo->prepare("SELECT * FROM courses WHERE id = ? AND is_published = 1");
$stmt->execute([$courseId]);
$course = $stmt->fetch();

if (!$course) {
    header('Location: courses.php');
    exit;
}

// Check if user is logged in
$isLoggedIn = auth()->check();
$user = $isLoggedIn ? auth()->user() : null;

// Check if user is enrolled
$isEnrolled = false;
if ($user) {
    $stmt = $pdo->prepare("SELECT * FROM enrollments WHERE user_id = ? AND course_id = ?");
    $stmt->execute([$user['id'], $courseId]);
    $isEnrolled = $stmt->fetch() !== false;
}

// Redirect guests who try to access course content directly
// Guests can only see basic course info, not content sections
$contentAccessRequired = isset($_GET['view_content']) || isset($_POST['enroll']);
if ($contentAccessRequired && !$isLoggedIn) {
    setFlashMessage('error', 'Please log in to access this course.');
    header('Location: login.php');
    exit;
}

// Fetch related courses
$stmt = $pdo->prepare("SELECT * FROM courses WHERE category = ? AND id != ? AND is_published = 1 LIMIT 3");
$stmt->execute([$course['category'], $courseId]);
$relatedCourses = $stmt->fetchAll();

// Fetch live classes for this course
$stmt = $pdo->prepare("SELECT * FROM live_classes WHERE course_id = ? AND is_published = 1 AND scheduled_at > NOW() ORDER BY scheduled_at ASC LIMIT 5");
$stmt->execute([$courseId]);
$liveClasses = $stmt->fetchAll();

// Fetch study materials for this course
$stmt = $pdo->prepare("SELECT * FROM study_materials WHERE course_id = ? ORDER BY created_at DESC LIMIT 10");
$stmt->execute([$courseId]);
$studyMaterials = $stmt->fetchAll();
?>
<?php include 'header.php'; ?>
      <section class="ma-page-banner">
        <h1><?php echo htmlspecialchars($course['title']); ?></h1>
        <p><?php echo htmlspecialchars($course['category']); ?> Course</p>
      </section>

      <section class="ma-course-detail">
        <div class="ma-course-detail-inner">
          <div class="ma-course-main">
            <div class="ma-course-hero">
              <div class="ma-course-hero-img" style="background: linear-gradient(135deg, #0d3b2e 0%, #1a5c3e 100%);">
                <?php if ($course['image_url']): ?>
                  <img src="<?php echo htmlspecialchars($course['image_url']); ?>" alt="<?php echo htmlspecialchars($course['title']); ?>">
                <?php else: ?>
                  <div class="ma-course-hero-icon">📚</div>
                <?php endif; ?>
              </div>
              <div class="ma-course-hero-info">
                <span class="ma-course-level"><?php echo ucfirst(htmlspecialchars($course['level'])); ?></span>
                <span class="ma-course-category"><?php echo htmlspecialchars($course['category']); ?></span>
                <?php if ($course['is_free']): ?>
                  <span class="ma-free-badge">Free Course</span>
                <?php endif; ?>
              </div>
            </div>

            <div class="ma-course-content">
              <h2>Course Description</h2>
              <p><?php echo nl2br(htmlspecialchars($course['description'])); ?></p>

              <?php if (!empty($liveClasses)): ?>
                <h2>Upcoming Live Classes</h2>
                <?php if ($isLoggedIn && $isEnrolled): ?>
                  <div class="ma-live-classes-list">
                    <?php foreach ($liveClasses as $class): ?>
                      <div class="ma-live-class-item">
                        <div class="ma-live-class-date">
                          <div class="ma-date-day"><?php echo date('d', strtotime($class['scheduled_at'])); ?></div>
                          <div class="ma-date-month"><?php echo date('M', strtotime($class['scheduled_at'])); ?></div>
                        </div>
                        <div class="ma-live-class-info">
                          <h3><?php echo htmlspecialchars($class['title']); ?></h3>
                          <p><?php echo htmlspecialchars($class['description']); ?></p>
                          <div class="ma-live-class-meta">
                            <span>🕐 <?php echo date('h:i A', strtotime($class['scheduled_at'])); ?></span>
                            <span>⏱️ <?php echo $class['duration_minutes']; ?> mins</span>
                            <span>👨‍🏫 <?php echo htmlspecialchars($class['instructor']); ?></span>
                          </div>
                        </div>
                        <a href="<?php echo htmlspecialchars($class['meeting_link']); ?>" class="ma-btn-gold" target="_blank">Join Class</a>
                      </div>
                    <?php endforeach; ?>
                  </div>
                <?php elseif (!$isLoggedIn): ?>
                  <div class="ma-login-prompt">
                    <p>Please log in to view live class schedules and join classes.</p>
                    <a href="login.php" class="ma-btn-gold">Login to Access</a>
                  </div>
                <?php else: ?>
                  <div class="ma-enroll-prompt">
                    <p>Please enroll in this course to access live classes.</p>
                    <form method="POST" action="enroll.php">
                      <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
                      <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                      <button type="submit" class="ma-btn-gold">Enroll Now</button>
                    </form>
                  </div>
                <?php endif; ?>
              <?php endif; ?>

              <?php if (!empty($studyMaterials)): ?>
                <h2>Study Materials</h2>
                <?php if ($isLoggedIn && $isEnrolled): ?>
                  <div class="ma-materials-list">
                    <?php foreach ($studyMaterials as $material): ?>
                      <div class="ma-material-item">
                        <div class="ma-material-icon">
                          <?php
                          $icon = '📄';
                          if ($material['file_type'] === 'pdf') $icon = '📕';
                          elseif ($material['file_type'] === 'video') $icon = '🎬';
                          elseif ($material['file_type'] === 'audio') $icon = '🎵';
                          echo $icon;
                          ?>
                        </div>
                        <div class="ma-material-info">
                          <h3><?php echo htmlspecialchars($material['title']); ?></h3>
                          <p><?php echo htmlspecialchars($material['description']); ?></p>
                          <span class="ma-material-meta"><?php echo htmlspecialchars($material['file_size']); ?> • <?php echo $material['download_count']; ?> downloads</span>
                        </div>
                        <a href="<?php echo htmlspecialchars($material['file_url']); ?>" class="ma-btn-outline" target="_blank">Download</a>
                      </div>
                    <?php endforeach; ?>
                  </div>
                <?php elseif (!$isLoggedIn): ?>
                  <div class="ma-login-prompt">
                    <p>Please log in to view and download study materials.</p>
                    <a href="login.php" class="ma-btn-gold">Login to Access</a>
                  </div>
                <?php else: ?>
                  <div class="ma-enroll-prompt">
                    <p>Please enroll in this course to access study materials.</p>
                    <form method="POST" action="enroll.php">
                      <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
                      <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                      <button type="submit" class="ma-btn-gold">Enroll Now</button>
                    </form>
                  </div>
                <?php endif; ?>
              <?php endif; ?>
            </div>
          </div>

          <div class="ma-course-sidebar">
            <div class="ma-sidebar-card">
              <h3>Course Info</h3>
              <ul class="ma-sidebar-info">
                <li>
                  <span>Category</span>
                  <span><?php echo htmlspecialchars($course['category']); ?></span>
                </li>
                <li>
                  <span>Level</span>
                  <span><?php echo ucfirst(htmlspecialchars($course['level'])); ?></span>
                </li>
                <li>
                  <span>Price</span>
                  <span><?php echo $course['is_free'] ? 'Free' : 'Paid'; ?></span>
                </li>
              </ul>

              <?php if ($isLoggedIn): ?>
                <?php if ($isEnrolled): ?>
                  <button class="ma-btn-gold" disabled>You're Enrolled ✓</button>
                <?php else: ?>
                  <form method="POST" action="enroll.php">
                    <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                    <button type="submit" class="ma-btn-gold">Enroll Now</button>
                  </form>
                <?php endif; ?>
              <?php else: ?>
                <a href="login.php" class="ma-btn-gold">Login to Enroll</a>
              <?php endif; ?>
            </div>

            <?php if (!empty($relatedCourses)): ?>
              <div class="ma-sidebar-card">
                <h3>Related Courses</h3>
                <div class="ma-related-courses">
                  <?php foreach ($relatedCourses as $related): ?>
                    <a href="course-detail.php?id=<?php echo $related['id']; ?>" class="ma-related-course">
                      <div class="ma-related-course-img" style="background: linear-gradient(135deg, #1a4a2a 0%, #2d6a4a 100%);">
                        <span>📚</span>
                      </div>
                      <div class="ma-related-course-info">
                        <h4><?php echo htmlspecialchars($related['title']); ?></h4>
                        <span><?php echo htmlspecialchars($related['category']); ?></span>
                      </div>
                    </a>
                  <?php endforeach; ?>
                </div>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </section>
<?php include 'footer.php'; ?>
