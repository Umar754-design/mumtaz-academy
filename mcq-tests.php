<?php 
require_once 'config.php';

// Get database connection
$pdo = getDB();

// Fetch all published MCQ tests
try {
    $stmt = $pdo->query("
        SELECT t.*, 
               (SELECT COUNT(*) FROM mcq_attempts WHERE test_id = t.id) as attempt_count
        FROM mcq_tests t 
        WHERE t.is_published = 1
        ORDER BY t.created_at DESC
    ");
    $tests = $stmt->fetchAll();
} catch (PDOException $e) {
    $tests = [];
}

include 'header.php'; 
?>
      <section class="ma-page-banner">
        <h1>MCQ Tests</h1>
        <p>Assess your Islamic knowledge with interactive quizzes</p>
      </section>

      <section style="background: #f7f7f4; padding: 28px 24px; border-bottom: 1px solid #eee;">
        <div style="max-width: 900px; margin: 0 auto; display: flex; justify-content: center; gap: 48px; flex-wrap: wrap;">
          <div style="text-align: center;">
            <div style="font-size: 26px; font-weight: 800; color: #c9a227;">30+</div>
            <div style="font-size: 12.5px; color: #666; margin-top: 2px;">Practice Tests</div>
          </div>
          <div style="text-align: center;">
            <div style="font-size: 26px; font-weight: 800; color: #c9a227;">500+</div>
            <div style="font-size: 12.5px; color: #666; margin-top: 2px;">Questions</div>
          </div>
          <div style="text-align: center;">
            <div style="font-size: 26px; font-weight: 800; color: #c9a227;">19K+</div>
            <div style="font-size: 12.5px; color: #666; margin-top: 2px;">Attempts</div>
          </div>
          <div style="text-align: center;">
            <div style="font-size: 26px; font-weight: 800; color: #c9a227;">Free</div>
            <div style="font-size: 12.5px; color: #666; margin-top: 2px;">No Sign-up Required</div>
          </div>
        </div>
      </section>

      <section style="padding: 50px 24px; max-width: 1100px; margin: 0 auto;">
        <?php if (empty($tests)): ?>
          <p style="text-align: center; color: #888; padding: 40px;">No MCQ tests available yet.</p>
        <?php else: ?>
          <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 24px;">
            <?php foreach ($tests as $test): 
              // Color based on category
              $categoryColors = [
                'Quran' => '#1a5c3e',
                'Arabic' => '#1b3a6a',
                'Islamic Studies' => '#5c3500',
                'Fiqh' => '#2d1a5c',
                'General' => '#0b2b2b'
              ];
              $bgColor = $categoryColors[$test['category']] ?? '#0b2b2b';
              
              // Level badge color
              $levelColors = [
                'beginner' => '#1a5c3e',
                'intermediate' => '#3b4a8c',
                'advanced' => '#7a2020'
              ];
              $levelColor = $levelColors[$test['level']] ?? '#666';
            ?>
              <div style="background: #fff; border-radius: 14px; overflow: hidden; box-shadow: 0 2px 12px rgba(0,0,0,0.06); border: 1.5px solid #eee;">
                <div style="background: <?php echo $bgColor; ?>; padding: 20px 22px; display: flex; justify-content: space-between; align-items: flex-start;">
                  <div>
                    <div style="font-size: 11px; color: rgba(255,255,255,0.5); font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;"><?php echo htmlspecialchars($test['category'] ?: 'General'); ?></div>
                    <h3 style="font-size: 16px; font-weight: 700; color: #fff; line-height: 1.3;"><?php echo htmlspecialchars($test['title']); ?></h3>
                  </div>
                  <span style="background: <?php echo $levelColor; ?>; color: #fff; font-size: 10.5px; font-weight: 600; padding: 3px 10px; border-radius: 20px; flex-shrink: 0; margin-left: 8px;"><?php echo htmlspecialchars(ucfirst($test['level'])); ?></span>
                </div>
                <div style="padding: 18px 22px;">
                  <?php if ($test['description']): ?>
                    <p style="font-size: 13px; color: #666; margin-bottom: 12px; line-height: 1.5;"><?php echo htmlspecialchars(substr($test['description'], 0, 100)); ?>...</p>
                  <?php endif; ?>
                  <div style="display: flex; gap: 20px; margin-bottom: 16px;">
                    <div style="font-size: 12px; color: #666;"><span style="margin-right: 4px;">❓</span><?php echo $test['total_questions']; ?> Questions</div>
                    <div style="font-size: 12px; color: #666;"><span style="margin-right: 4px;">⏱️</span><?php echo $test['duration_minutes']; ?> min</div>
                    <div style="font-size: 12px; color: #666;"><span style="margin-right: 4px;">👥</span><?php echo $test['attempt_count']; ?> attempts</div>
                  </div>
                  <a href="take-mcq-test.php?id=<?php echo $test['id']; ?>" class="ma-enroll-btn" style="width: 100%; text-align: center; cursor: pointer; border: none; padding: 10px; border-radius: 8px; font-family: inherit; font-size: 14px; text-decoration: none; display: block;">Start Test →</a>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </section>
<?php include 'footer.php'; ?>
