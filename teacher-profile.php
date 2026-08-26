<?php
require_once 'config.php';
require_once 'auth.php';

// Get database connection
$pdo = getDB();

// Get teacher ID from URL
$teacherId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch teacher details
$stmt = $pdo->prepare("SELECT * FROM teachers WHERE id = ? AND is_active = 1");
$stmt->execute([$teacherId]);
$teacher = $stmt->fetch();

if (!$teacher) {
    header('Location: teachers.php');
    exit;
}

// Fetch teacher subjects
$stmt = $pdo->prepare("SELECT * FROM teacher_subjects WHERE teacher_id = ?");
$stmt->execute([$teacherId]);
$subjects = $stmt->fetchAll();

// Fetch courses taught by this teacher
$stmt = $pdo->prepare("SELECT * FROM courses WHERE id IN (SELECT course_id FROM live_classes WHERE instructor = ?) AND is_published = 1 LIMIT 6");
$stmt->execute([$teacher['name']]);
$courses = $stmt->fetchAll();
?>
<?php include 'header.php'; ?>
      <section class="ma-page-banner">
        <h1><?php echo htmlspecialchars($teacher['name']); ?></h1>
        <p><?php echo htmlspecialchars($teacher['specialization']); ?></p>
      </section>

      <section class="ma-teacher-profile">
        <div class="ma-teacher-profile-inner">
          <div class="ma-teacher-main">
            <div class="ma-teacher-hero">
              <div class="ma-teacher-avatar">
                <?php if ($teacher['image_url']): ?>
                  <img src="<?php echo htmlspecialchars($teacher['image_url']); ?>" alt="<?php echo htmlspecialchars($teacher['name']); ?>">
                <?php else: ?>
                  <span><?php echo strtoupper(substr(htmlspecialchars($teacher['name']), 0, 2)); ?></span>
                <?php endif; ?>
              </div>
              <div class="ma-teacher-hero-info">
                <h2><?php echo htmlspecialchars($teacher['name']); ?></h2>
                <p class="ma-teacher-specialization"><?php echo htmlspecialchars($teacher['specialization']); ?></p>
                <div class="ma-teacher-stats">
                  <div class="ma-teacher-stat">
                    <span class="ma-stat-number"><?php echo $teacher['experience_years']; ?>+</span>
                    <span class="ma-stat-label">Years Experience</span>
                  </div>
                  <div class="ma-teacher-stat">
                    <span class="ma-stat-number"><?php echo $teacher['student_count']; ?>+</span>
                    <span class="ma-stat-label">Students Taught</span>
                  </div>
                </div>
              </div>
            </div>

            <div class="ma-teacher-content">
              <h2>About</h2>
              <p><?php echo nl2br(htmlspecialchars($teacher['bio'])); ?></p>

              <h2>Qualifications</h2>
              <p><?php echo nl2br(htmlspecialchars($teacher['qualifications'])); ?></p>

              <?php if (!empty($subjects)): ?>
                <h2>Subjects Taught</h2>
                <div class="ma-subjects-list">
                  <?php foreach ($subjects as $subject): ?>
                    <span class="ma-subject-tag"><?php echo htmlspecialchars($subject['subject_name']); ?></span>
                  <?php endforeach; ?>
                </div>
              <?php endif; ?>

              <?php if (!empty($courses)): ?>
                <h2>Courses</h2>
                <div class="ma-teacher-courses">
                  <?php foreach ($courses as $course): ?>
                    <a href="course-detail.php?id=<?php echo $course['id']; ?>" class="ma-teacher-course-card">
                      <div class="ma-teacher-course-img" style="background: linear-gradient(135deg, #0d3b2e 0%, #1a5c3e 100%);">
                        <span>📚</span>
                      </div>
                      <div class="ma-teacher-course-info">
                        <h3><?php echo htmlspecialchars($course['title']); ?></h3>
                        <span><?php echo htmlspecialchars($course['category']); ?></span>
                      </div>
                    </a>
                  <?php endforeach; ?>
                </div>
              <?php endif; ?>
            </div>
          </div>

          <div class="ma-teacher-sidebar">
            <div class="ma-sidebar-card">
              <h3>Contact Teacher</h3>
              <p>Have questions about courses or want to connect with this teacher?</p>
              <a href="contact.php" class="ma-btn-gold">Send Message</a>
            </div>

            <div class="ma-sidebar-card">
              <h3>Quick Info</h3>
              <ul class="ma-sidebar-info">
                <li>
                  <span>Experience</span>
                  <span><?php echo $teacher['experience_years']; ?>+ Years</span>
                </li>
                <li>
                  <span>Students</span>
                  <span><?php echo $teacher['student_count']; ?>+</span>
                </li>
                <li>
                  <span>Status</span>
                  <span>Active</span>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </section>
<?php include 'footer.php'; ?>
