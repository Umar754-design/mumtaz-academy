<?php 
require_once 'config.php';

// Get database connection
$pdo = getDB();

// Get filter category
$filterCategory = isset($_GET['category']) ? sanitizeInput($_GET['category']) : '';

// Fetch courses from database
try {
    if ($filterCategory) {
        $stmt = $pdo->prepare("SELECT * FROM courses WHERE category = ? AND is_published = 1 ORDER BY created_at DESC");
        $stmt->execute([$filterCategory]);
    } else {
        $stmt = $pdo->query("SELECT * FROM courses WHERE is_published = 1 ORDER BY created_at DESC");
    }
    $courses = $stmt->fetchAll();
    
    // Get unique categories for filter
    $stmt = $pdo->query("SELECT DISTINCT category FROM courses WHERE is_published = 1 ORDER BY category");
    $categories = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    // Debug: Show all courses (including unpublished) to check
    $debugStmt = $pdo->query("SELECT * FROM courses ORDER BY created_at DESC");
    $allCourses = $debugStmt->fetchAll();
} catch (PDOException $e) {
    $courses = [];
    $categories = [];
    $allCourses = [];
    error_log("Courses page error: " . $e->getMessage());
}

include 'header.php'; 
?>
      <section class="ma-page-banner">
        <h1>Our Courses</h1>
        <p>Explore our free Islamic education courses</p>
      </section>

      <section class="ma-courses-page">
        <div class="ma-courses-filter">
          <button class="ma-filter-pill <?php echo $filterCategory === '' ? 'active' : ''; ?>" onclick="filterCourses('')">All</button>
          <?php foreach ($categories as $category): ?>
            <button class="ma-filter-pill <?php echo $filterCategory === $category ? 'active' : ''; ?>" onclick="filterCourses('<?php echo htmlspecialchars($category); ?>')">
              <?php echo htmlspecialchars($category); ?>
            </button>
          <?php endforeach; ?>
        </div>

        <div class="ma-courses-grid">
          <?php if (empty($courses)): ?>
            <div class="ma-empty-courses">
              <p>No courses available at the moment.</p>
            </div>
          <?php else: ?>
            <?php foreach ($courses as $course): 
              // Generate gradient based on category
              $gradients = [
                'Quran' => 'linear-gradient(135deg, #0d3b2e 0%, #1a5c3e 100%)',
                'Arabic' => 'linear-gradient(135deg, #1b3a3a 0%, #0d5050 100%)',
                'Islamic Studies' => 'linear-gradient(135deg, #2d1a00 0%, #5c3500 100%)',
                'Hadith' => 'linear-gradient(135deg, #3a1a5c 0%, #5c2d8a 100%)',
                'Fiqh' => 'linear-gradient(135deg, #4a1a1a 0%, #6a2d2d 100%)',
                'Seerah' => 'linear-gradient(135deg, #1a4a2a 0%, #2d6a4a 100%)',
              ];
              $gradient = $gradients[$course['category']] ?? 'linear-gradient(135deg, #0b2b2b 0%, #0d3333 100%)';
              
              // Get icon based on category
              $icons = [
                'Quran' => '📚',
                'Arabic' => 'ع',
                'Islamic Studies' => '🕌',
                'Hadith' => '📖',
                'Fiqh' => '⚖️',
                'Seerah' => '✍️',
              ];
              $icon = $icons[$course['category']] ?? '📚';
              
              // Get Arabic text based on category
              $arabicTexts = [
                'Quran' => 'ٱلۡقُرۡءَانُ',
                'Arabic' => 'اَلۡعَرَبِيَّة',
              ];
              $arabicText = $arabicTexts[$course['category']] ?? '';
            ?>
            <div class="ma-course-card">
              <div class="ma-course-img" style="background: <?php echo $gradient; ?>;">
                <?php if ($arabicText): ?>
                  <div class="ma-course-img-text"><?php echo $arabicText; ?></div>
                <?php endif; ?>
                <div class="ma-course-img-icon"><?php echo $icon; ?></div>
              </div>
              <div class="ma-course-body">
                <h3><?php echo htmlspecialchars($course['title']); ?></h3>
                <p><?php echo htmlspecialchars(substr($course['description'], 0, 100)); ?>...</p>
                <div class="ma-course-footer">
                  <?php if ($course['level']): ?>
                    <span class="ma-pill"><?php echo htmlspecialchars($course['level']); ?></span>
                  <?php endif; ?>
                  <?php if ($course['price'] > 0): ?>
                    <span class="ma-pill ma-pill-price">₹<?php echo number_format($course['price'], 2); ?></span>
                  <?php else: ?>
                    <span class="ma-pill ma-pill-free">Free</span>
                  <?php endif; ?>
                  <a href="course-detail.php?id=<?php echo $course['id']; ?>" class="ma-enroll-btn">View Details →</a>
                </div>
              </div>
            </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </section>

<script>
function filterCourses(category) {
  const url = new URL(window.location);
  if (category) {
    url.searchParams.set('category', category);
  } else {
    url.searchParams.delete('category');
  }
  window.location.href = url.toString();
}
</script>

<?php include 'footer.php'; ?>
