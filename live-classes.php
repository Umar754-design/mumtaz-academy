<?php 
require_once 'config.php';

// Set PHP timezone to Asia/Kolkata
date_default_timezone_set('Asia/Kolkata');

// Get database connection
$pdo = getDB();

// Fetch all published live classes from database
try {
    $stmt = $pdo->query("
        SELECT lc.*, c.title as course_title 
        FROM live_classes lc 
        LEFT JOIN courses c ON lc.course_id = c.id 
        WHERE lc.is_published = 1
        ORDER BY lc.scheduled_at ASC
        LIMIT 10
    ");
    $liveClasses = $stmt->fetchAll();
} catch (PDOException $e) {
    $liveClasses = [];
}

// Current server timestamp (available even when no classes exist)
include 'header.php'; ?>
      <section class="ma-page-banner">
        <h1>Live Classes</h1>
        <p>Join interactive live sessions with our expert scholars</p>
      </section>

      <section class="ma-live-section">
        <div class="ma-live-info">
          <div class="ma-live-stat">
            <div class="ma-live-num">100+</div>
            <div class="ma-live-label">Live Classes</div>
          </div>
          <div class="ma-live-stat">
            <div class="ma-live-num">50+</div>
            <div class="ma-live-label">Expert Teachers</div>
          </div>
          <div class="ma-live-stat">
            <div class="ma-live-num">5000+</div>
            <div class="ma-live-label">Students</div>
          </div>
        </div>

        <div class="ma-live-schedule">
          <h2>Upcoming Classes</h2>
          <?php if (empty($liveClasses)): ?>
            <div class="ma-live-empty">
              <p>No upcoming live classes scheduled yet.</p>
              <p>Check back soon for new sessions!</p>
            </div>
          <?php else: ?>
            <?php foreach ($liveClasses as $class): 
              // Convert UTC database time to Asia/Kolkata for display
              $scheduledDateTime = new DateTime($class['scheduled_at'], new DateTimeZone('UTC'));
              $scheduledDateTime->setTimezone(new DateTimeZone('Asia/Kolkata'));
              $scheduledTimestamp = $scheduledDateTime->getTimestamp();
              $endTimeTimestamp = $scheduledTimestamp + (60 * 60); // 1 hour after start
              
              $currentDateTime = new DateTime('now', new DateTimeZone('Asia/Kolkata'));
              $currentTimestamp = $currentDateTime->getTimestamp();
              
              $dayName = $scheduledDateTime->format('D');
              $dayNumber = $scheduledDateTime->format('j');
              $time = $scheduledDateTime->format('g:i A'); // 12-hour format with AM/PM
              $endTime = (new DateTime('@' . $endTimeTimestamp))->setTimezone(new DateTimeZone('Asia/Kolkata'))->format('g:i A'); // 12-hour format
            ?>
              <div class="ma-live-card" 
                   data-scheduled="<?php echo $scheduledTimestamp; ?>" 
                   data-end="<?php echo $endTimeTimestamp; ?>"
                   data-meeting-link="<?php echo htmlspecialchars($class['meeting_link'] ?: ''); ?>"
                   id="class-<?php echo $class['id']; ?>">
                <div class="ma-live-time">
                  <span class="ma-live-day"><?php echo $dayName; ?></span>
                  <span class="ma-live-date"><?php echo $dayNumber; ?></span>
                </div>
                <div class="ma-live-details">
                  <h3><?php echo htmlspecialchars($class['title']); ?></h3>
                  <p><?php echo htmlspecialchars($class['description'] ?: 'Join this live session with our expert instructor'); ?></p>
                  <?php if ($class['course_title']): ?>
                    <p class="ma-live-course">Course: <?php echo htmlspecialchars($class['course_title']); ?></p>
                  <?php endif; ?>
                  <div class="ma-live-meta">
                    <span>🕐 <?php echo $time; ?> - <?php echo $endTime; ?></span>
                    <?php if ($class['instructor']): ?>
                      <span>👨‍🏫 <?php echo htmlspecialchars($class['instructor']); ?></span>
                    <?php endif; ?>
                  </div>
                  <div class="ma-live-countdown" id="countdown-<?php echo $class['id']; ?>">
                    <span class="ma-countdown-label">Starting in:</span>
                    <span class="ma-countdown-timer">--:--:--</span>
                  </div>
                </div>
                <div class="ma-live-status" id="status-<?php echo $class['id']; ?>">
                  <span class="ma-live-badge ma-badge-upcoming">🟡 Upcoming</span>
                </div>
                <div class="ma-live-action" id="action-<?php echo $class['id']; ?>">
                  <!-- Join button will be added by JavaScript -->
                </div>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>

        <div class="ma-live-cta">
          <h2>Don't Miss Our Live Sessions</h2>
          <p>Register now to get notified about upcoming classes</p>
          <a href="register.php" class="ma-btn-gold">Register for Free</a>
        </div>
      </section>

<script>
try {
    // Get current server timestamp in Asia/Kolkata timezone
    const serverTimestamp = <?php echo isset($currentTimestamp) ? $currentTimestamp : 'Math.floor(Date.now() / 1000)'; ?>;
    const pageLoadTime = Date.now();

    // Update live class statuses every 30 seconds
    setInterval(updateClassStatuses, 30000);

    // Initial update
    updateClassStatuses();

    function updateClassStatuses() {
        const classCards = document.querySelectorAll('.ma-live-card');
        if (!classCards.length) return;
        
        // Calculate current time based on elapsed time since page load
        const timeElapsed = Math.floor((Date.now() - pageLoadTime) / 1000);
        const currentTime = serverTimestamp + timeElapsed;
        
        classCards.forEach(card => {
            const scheduledTime = parseInt(card.dataset.scheduled);
            const endTime = parseInt(card.dataset.end);
            const meetingLink = card.dataset.meetingLink;
            const classId = card.id.replace('class-', '');
            
            const statusContainer = document.getElementById(`status-${classId}`);
            const actionContainer = document.getElementById(`action-${classId}`);
            const countdownContainer = document.getElementById(`countdown-${classId}`);
            const countdownTimer = countdownContainer?.querySelector('.ma-countdown-timer');
            
            if (!statusContainer || !countdownTimer) return;
            
            if (currentTime < scheduledTime) {
                // Upcoming
                statusContainer.innerHTML = '<span class="ma-live-badge ma-badge-upcoming">🟡 Upcoming</span>';
                if (actionContainer) actionContainer.innerHTML = '';
                
                // Update countdown
                const timeDiff = scheduledTime - currentTime;
                const hours = Math.floor(timeDiff / 3600);
                const minutes = Math.floor((timeDiff % 3600) / 60);
                const seconds = timeDiff % 60;
                countdownTimer.textContent = `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
                countdownContainer.style.display = 'flex';
                
            } else if (currentTime >= scheduledTime && currentTime < endTime) {
                // Live Now
                statusContainer.innerHTML = '<span class="ma-live-badge ma-badge-live">🟢 Live Now</span>';
                
                if (actionContainer) {
                    if (meetingLink) {
                        actionContainer.innerHTML = `<a href="${meetingLink}" target="_blank" class="ma-btn-gold">Join Class</a>`;
                    } else {
                        actionContainer.innerHTML = '<span class="ma-btn-gold ma-btn-disabled">Link Not Available</span>';
                    }
                }
                
                // Show remaining time
                const timeDiff = endTime - currentTime;
                const hours = Math.floor(timeDiff / 3600);
                const minutes = Math.floor((timeDiff % 3600) / 60);
                const seconds = timeDiff % 60;
                const countdownLabel = countdownContainer.querySelector('.ma-countdown-label');
                if (countdownLabel) countdownLabel.textContent = 'Ends in:';
                countdownTimer.textContent = `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
                countdownContainer.style.display = 'flex';
                
            } else {
                // Ended
                statusContainer.innerHTML = '<span class="ma-live-badge ma-badge-ended">🔴 Class Ended</span>';
                if (actionContainer) actionContainer.innerHTML = '<span class="ma-btn-gold ma-btn-disabled">Recording will be uploaded soon</span>';
                countdownContainer.style.display = 'none';
            }
        });
    }

    // Update countdown every second
    setInterval(() => {
        const classCards = document.querySelectorAll('.ma-live-card');
        const timeElapsed = Math.floor((Date.now() - pageLoadTime) / 1000);
        const currentTime = serverTimestamp + timeElapsed;
        
        classCards.forEach(card => {
            const scheduledTime = parseInt(card.dataset.scheduled);
            const endTime = parseInt(card.dataset.end);
            const classId = card.id.replace('class-', '');
            const countdownContainer = document.getElementById(`countdown-${classId}`);
            
            if (!countdownContainer || countdownContainer.style.display === 'none') return;
            
            const countdownTimer = countdownContainer.querySelector('.ma-countdown-timer');
            const countdownLabel = countdownContainer.querySelector('.ma-countdown-label');
            
            if (!countdownTimer) return;
            
            if (currentTime < scheduledTime) {
                const timeDiff = scheduledTime - currentTime;
                const hours = Math.floor(timeDiff / 3600);
                const minutes = Math.floor((timeDiff % 3600) / 60);
                const seconds = timeDiff % 60;
                countdownTimer.textContent = `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
            } else if (currentTime >= scheduledTime && currentTime < endTime) {
                const timeDiff = endTime - currentTime;
                const hours = Math.floor(timeDiff / 3600);
                const minutes = Math.floor((timeDiff % 3600) / 60);
                const seconds = timeDiff % 60;
                if (countdownLabel) countdownLabel.textContent = 'Ends in:';
                countdownTimer.textContent = `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
            }
        });
    }, 1000);
} catch (error) {
    console.error('Live classes script error:', error);
}
</script>

<?php include 'footer.php'; ?>
