<?php
$pageTitle = 'Dashboard';
require_once 'header.php';

// Get database connection
$pdo = getDB();

// Fetch statistics
$stats = [
    'users' => 0,
    'courses' => 0,
    'teachers' => 0,
    'blog_posts' => 0,
    'live_classes' => 0,
    'enrollments' => 0,
    'mcq_tests' => 0,
    'messages' => 0
];

try {
    // Users count
    $stmt = $pdo->query("SELECT COUNT(*) FROM users");
    $stats['users'] = $stmt->fetchColumn();
    
    // Courses count
    $stmt = $pdo->query("SELECT COUNT(*) FROM courses");
    $stats['courses'] = $stmt->fetchColumn();
    
    // Teachers count
    $stmt = $pdo->query("SELECT COUNT(*) FROM teachers");
    $stats['teachers'] = $stmt->fetchColumn();
    
    // Blog posts count
    $stmt = $pdo->query("SELECT COUNT(*) FROM blog_posts");
    $stats['blog_posts'] = $stmt->fetchColumn();
    
    // Live classes count
    $stmt = $pdo->query("SELECT COUNT(*) FROM live_classes");
    $stats['live_classes'] = $stmt->fetchColumn();
    
    // Enrollments count
    $stmt = $pdo->query("SELECT COUNT(*) FROM enrollments");
    $stats['enrollments'] = $stmt->fetchColumn();
    
    // MCQ tests count
    $stmt = $pdo->query("SELECT COUNT(*) FROM mcq_tests");
    $stats['mcq_tests'] = $stmt->fetchColumn();
    
    // Messages count
    $stmt = $pdo->query("SELECT COUNT(*) FROM contact_messages WHERE is_read = 0");
    $stats['messages'] = $stmt->fetchColumn();
    
    // Recent enrollments
    $stmt = $pdo->query("
        SELECT e.*, u.name as user_name, c.title as course_title
        FROM enrollments e
        JOIN users u ON e.user_id = u.id
        JOIN courses c ON e.course_id = c.id
        ORDER BY e.enrolled_at DESC
        LIMIT 5
    ");
    $recentEnrollments = $stmt->fetchAll();
    
    // Recent messages
    $stmt = $pdo->query("
        SELECT * FROM contact_messages
        ORDER BY created_at DESC
        LIMIT 5
    ");
    $recentMessages = $stmt->fetchAll();
    
} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
}
?>

<div class="ma-admin-dashboard">
    <div class="ma-admin-stats-grid">
        <div class="ma-admin-stat-card">
            <div class="ma-admin-stat-icon">👥</div>
            <div class="ma-admin-stat-info">
                <div class="ma-admin-stat-number"><?php echo number_format($stats['users']); ?></div>
                <div class="ma-admin-stat-label">Total Users</div>
            </div>
            <a href="users.php" class="ma-admin-stat-link">View →</a>
        </div>
        
        <div class="ma-admin-stat-card">
            <div class="ma-admin-stat-icon">📚</div>
            <div class="ma-admin-stat-info">
                <div class="ma-admin-stat-number"><?php echo number_format($stats['courses']); ?></div>
                <div class="ma-admin-stat-label">Courses</div>
            </div>
            <a href="courses.php" class="ma-admin-stat-link">View →</a>
        </div>
        
        <div class="ma-admin-stat-card">
            <div class="ma-admin-stat-icon">👨‍🏫</div>
            <div class="ma-admin-stat-info">
                <div class="ma-admin-stat-number"><?php echo number_format($stats['teachers']); ?></div>
                <div class="ma-admin-stat-label">Teachers</div>
            </div>
            <a href="teachers.php" class="ma-admin-stat-link">View →</a>
        </div>
        
        <div class="ma-admin-stat-card">
            <div class="ma-admin-stat-icon">📝</div>
            <div class="ma-admin-stat-info">
                <div class="ma-admin-stat-number"><?php echo number_format($stats['blog_posts']); ?></div>
                <div class="ma-admin-stat-label">Blog Posts</div>
            </div>
            <a href="blog.php" class="ma-admin-stat-link">View →</a>
        </div>
        
        <div class="ma-admin-stat-card">
            <div class="ma-admin-stat-icon">📹</div>
            <div class="ma-admin-stat-info">
                <div class="ma-admin-stat-number"><?php echo number_format($stats['live_classes']); ?></div>
                <div class="ma-admin-stat-label">Live Classes</div>
            </div>
            <a href="live-classes.php" class="ma-admin-stat-link">View →</a>
        </div>
        
        <div class="ma-admin-stat-card">
            <div class="ma-admin-stat-icon">🎓</div>
            <div class="ma-admin-stat-info">
                <div class="ma-admin-stat-number"><?php echo number_format($stats['enrollments']); ?></div>
                <div class="ma-admin-stat-label">Enrollments</div>
            </div>
            <a href="#" class="ma-admin-stat-link">View →</a>
        </div>
        
        <div class="ma-admin-stat-card">
            <div class="ma-admin-stat-icon">📋</div>
            <div class="ma-admin-stat-info">
                <div class="ma-admin-stat-number"><?php echo number_format($stats['mcq_tests']); ?></div>
                <div class="ma-admin-stat-label">MCQ Tests</div>
            </div>
            <a href="mcq-tests.php" class="ma-admin-stat-link">View →</a>
        </div>
        
        <div class="ma-admin-stat-card">
            <div class="ma-admin-stat-icon">✉️</div>
            <div class="ma-admin-stat-info">
                <div class="ma-admin-stat-number"><?php echo number_format($stats['messages']); ?></div>
                <div class="ma-admin-stat-label">Unread Messages</div>
            </div>
            <a href="messages.php" class="ma-admin-stat-link">View →</a>
        </div>
    </div>
    
    <div class="ma-admin-dashboard-grid">
        <div class="ma-admin-card">
            <div class="ma-admin-card-header">
                <h3>Recent Enrollments</h3>
                <a href="users.php">View All</a>
            </div>
            <div class="ma-admin-card-body">
                <?php if (empty($recentEnrollments)): ?>
                    <p class="ma-admin-empty">No enrollments yet.</p>
                <?php else: ?>
                    <table class="ma-admin-table">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Course</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentEnrollments as $enrollment): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($enrollment['user_name']); ?></td>
                                    <td><?php echo htmlspecialchars($enrollment['course_title']); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($enrollment['enrolled_at'])); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="ma-admin-card">
            <div class="ma-admin-card-header">
                <h3>Recent Messages</h3>
                <a href="messages.php">View All</a>
            </div>
            <div class="ma-admin-card-body">
                <?php if (empty($recentMessages)): ?>
                    <p class="ma-admin-empty">No messages yet.</p>
                <?php else: ?>
                    <table class="ma-admin-table">
                        <thead>
                            <tr>
                                <th>From</th>
                                <th>Subject</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentMessages as $message): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($message['name']); ?></td>
                                    <td><?php echo htmlspecialchars(substr($message['subject'], 0, 30)); ?>...</td>
                                    <td><?php echo date('M d, Y', strtotime($message['created_at'])); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
    .ma-admin-dashboard {
        display: flex;
        flex-direction: column;
        gap: 32px;
    }
    .ma-admin-stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
    }
    .ma-admin-stat-card {
        background: #fff;
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        display: flex;
        align-items: center;
        gap: 16px;
        position: relative;
    }
    .ma-admin-stat-icon {
        font-size: 32px;
        width: 56px;
        height: 56px;
        border-radius: 12px;
        background: rgba(201,162,39,0.1);
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .ma-admin-stat-info {
        flex: 1;
    }
    .ma-admin-stat-number {
        font-size: 28px;
        font-weight: 800;
        color: #0b2b2b;
        margin-bottom: 4px;
    }
    .ma-admin-stat-label {
        font-size: 13px;
        color: #666;
        font-weight: 500;
    }
    .ma-admin-stat-link {
        position: absolute;
        top: 16px;
        right: 16px;
        font-size: 12px;
        color: #c9a227;
        text-decoration: none;
        font-weight: 600;
    }
    .ma-admin-stat-link:hover {
        text-decoration: underline;
    }
    .ma-admin-dashboard-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px;
    }
    .ma-admin-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        overflow: hidden;
    }
    .ma-admin-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 20px 24px;
        border-bottom: 1px solid #eee;
    }
    .ma-admin-card-header h3 {
        font-size: 16px;
        font-weight: 700;
        color: #111;
        margin: 0;
    }
    .ma-admin-card-header a {
        font-size: 13px;
        color: #c9a227;
        text-decoration: none;
        font-weight: 600;
    }
    .ma-admin-card-body {
        padding: 0;
    }
    .ma-admin-table {
        width: 100%;
        border-collapse: collapse;
    }
    .ma-admin-table th {
        background: #f8f8f8;
        padding: 12px 16px;
        text-align: left;
        font-size: 12px;
        font-weight: 600;
        color: #666;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .ma-admin-table td {
        padding: 14px 16px;
        border-bottom: 1px solid #eee;
        font-size: 13px;
        color: #333;
    }
    .ma-admin-table tr:last-child td {
        border-bottom: none;
    }
    .ma-admin-empty {
        padding: 40px 20px;
        text-align: center;
        color: #888;
        font-size: 14px;
    }
    @media (max-width: 1200px) {
        .ma-admin-stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        .ma-admin-dashboard-grid {
            grid-template-columns: 1fr;
        }
    }
    @media (max-width: 600px) {
        .ma-admin-stats-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<?php require_once 'footer.php'; ?>
