<?php
$pageTitle = 'Live Classes Management';
require_once 'header.php';

// Set PHP timezone to Asia/Kolkata
date_default_timezone_set('Asia/Kolkata');

// Get database connection
$pdo = getDB();

// Handle form submissions
$message = '';
$messageType = '';

// Add/Edit live class
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    
    if ($action === 'add' || $action === 'edit') {
        $courseId = (int)($_POST['course_id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $instructor = trim($_POST['instructor'] ?? '');
        $meetingLink = trim($_POST['meeting_link'] ?? '');
        $meetingId = trim($_POST['meeting_id'] ?? '');
        $meetingPassword = trim($_POST['meeting_password'] ?? '');
        $scheduledAt = trim($_POST['scheduled_at'] ?? '');
        $durationMinutes = (int)($_POST['duration_minutes'] ?? 60);
        $maxStudents = (int)($_POST['max_students'] ?? 100);
        $isPublished = isset($_POST['is_published']) ? 1 : 0;
        
        // Convert datetime-local to timestamp and store as UTC
        if ($scheduledAt) {
            $dt = DateTime::createFromFormat('Y-m-d\TH:i', $scheduledAt, new DateTimeZone('Asia/Kolkata'));
            if ($dt) {
                // Convert to UTC for storage
                $dt->setTimezone(new DateTimeZone('UTC'));
                $scheduledAt = $dt->format('Y-m-d H:i:s');
            }
        }
        
        if (empty($title) || empty($courseId) || empty($scheduledAt)) {
            $message = 'Title, course, and scheduled date are required.';
            $messageType = 'error';
        } else {
            try {
                if ($action === 'add') {
                    $stmt = $pdo->prepare("
                        INSERT INTO live_classes (course_id, title, description, instructor, meeting_link, meeting_id, meeting_password, scheduled_at, duration_minutes, max_students, is_published, created_at)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
                    ");
                    $stmt->execute([$courseId, $title, $description, $instructor, $meetingLink, $meetingId, $meetingPassword, $scheduledAt, $durationMinutes, $maxStudents, $isPublished]);
                    $message = 'Live class added successfully!';
                    $messageType = 'success';
                } else {
                    $classId = (int)$_POST['class_id'];
                    $stmt = $pdo->prepare("
                        UPDATE live_classes 
                        SET course_id = ?, title = ?, description = ?, instructor = ?, meeting_link = ?, meeting_id = ?, meeting_password = ?, scheduled_at = ?, duration_minutes = ?, max_students = ?, is_published = ?
                        WHERE id = ?
                    ");
                    $stmt->execute([$courseId, $title, $description, $instructor, $meetingLink, $meetingId, $meetingPassword, $scheduledAt, $durationMinutes, $maxStudents, $isPublished, $classId]);
                    $message = 'Live class updated successfully!';
                    $messageType = 'success';
                }
            } catch (PDOException $e) {
                $message = 'Database error: ' . $e->getMessage();
                $messageType = 'error';
            }
        }
    }
    
    // Delete live class
    if ($action === 'delete') {
        $classId = (int)$_POST['class_id'];
        try {
            $stmt = $pdo->prepare("DELETE FROM live_classes WHERE id = ?");
            $stmt->execute([$classId]);
            $message = 'Live class deleted successfully!';
            $messageType = 'success';
        } catch (PDOException $e) {
            $message = 'Error deleting live class: ' . $e->getMessage();
            $messageType = 'error';
        }
    }
    
    // Toggle publish status
    if ($action === 'toggle_publish') {
        $classId = (int)$_POST['class_id'];
        $status = (int)$_POST['status'];
        try {
            $stmt = $pdo->prepare("UPDATE live_classes SET is_published = ? WHERE id = ?");
            $stmt->execute([$status, $classId]);
            $message = 'Live class status updated successfully!';
            $messageType = 'success';
        } catch (PDOException $e) {
            $message = 'Error updating live class status: ' . $e->getMessage();
            $messageType = 'error';
        }
    }
}

// Fetch all courses for dropdown
try {
    $stmt = $pdo->query("SELECT id, title FROM courses WHERE is_published = 1 ORDER BY title");
    $courses = $stmt->fetchAll();
} catch (PDOException $e) {
    $courses = [];
}

// Fetch all live classes with course name
try {
    $stmt = $pdo->query("
        SELECT lc.*, c.title as course_title 
        FROM live_classes lc 
        LEFT JOIN courses c ON lc.course_id = c.id 
        ORDER BY lc.scheduled_at DESC
    ");
    $liveClasses = $stmt->fetchAll();
} catch (PDOException $e) {
    $liveClasses = [];
    $message = 'Error fetching live classes: ' . $e->getMessage();
    $messageType = 'error';
}

// Fetch live class for editing
$editingClass = null;
if (isset($_GET['edit'])) {
    $editId = (int)$_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM live_classes WHERE id = ?");
    $stmt->execute([$editId]);
    $editingClass = $stmt->fetch();
}
?>

<div class="ma-admin-live-classes">
    <?php if ($message): ?>
        <div class="ma-admin-alert ma-admin-alert-<?php echo $messageType; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>
    
    <div class="ma-admin-live-classes-header">
        <h2>Live Classes Management</h2>
        <button class="ma-admin-btn" onclick="showAddForm()">+ Add New Live Class</button>
    </div>
    
    <div class="ma-admin-live-classes-content">
        <!-- Live Classes Table -->
        <div class="ma-admin-card">
            <div class="ma-admin-card-body">
                <?php if (empty($liveClasses)): ?>
                    <p class="ma-admin-empty">No live classes found.</p>
                <?php else: ?>
                    <table class="ma-admin-table">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Course</th>
                                <th>Instructor</th>
                                <th>Scheduled</th>
                                <th>Duration</th>
                                <th>Max Students</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($liveClasses as $class): 
                                $isPast = strtotime($class['scheduled_at']) < time();
                            ?>
                                <tr>
                                    <td>
                                        <strong><?php echo htmlspecialchars($class['title']); ?></strong>
                                        <?php if ($class['description']): ?>
                                            <div class="ma-admin-subtext"><?php echo htmlspecialchars(substr($class['description'], 0, 40)); ?>...</div>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($class['course_title'] ?? 'Unknown'); ?></td>
                                    <td><?php echo htmlspecialchars($class['instructor'] ?: '-'); ?></td>
                                    <td>
                                        <div class="ma-admin-date">
                                            <?php echo date('M d, Y', strtotime($class['scheduled_at'])); ?>
                                            <span class="ma-admin-time"><?php echo date('g:i A', strtotime($class['scheduled_at'])); ?></span>
                                        </div>
                                        <?php if ($isPast): ?>
                                            <span class="ma-admin-badge ma-admin-badge-warning">Past</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo $class['duration_minutes']; ?> min</td>
                                    <td><?php echo number_format($class['max_students']); ?></td>
                                    <td>
                                        <span class="ma-admin-badge ma-admin-badge-<?php echo $class['is_published'] ? 'success' : 'warning'; ?>">
                                            <?php echo $class['is_published'] ? 'Published' : 'Draft'; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="ma-admin-actions">
                                            <a href="?edit=<?php echo $class['id']; ?>" class="ma-admin-action-btn ma-admin-action-edit">Edit</a>
                                            <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this live class?');">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="class_id" value="<?php echo $class['id']; ?>">
                                                <button type="submit" class="ma-admin-action-btn ma-admin-action-delete">Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Add/Edit Form -->
        <div class="ma-admin-card" id="classForm" style="display: <?php echo $editingClass ? 'block' : 'none'; ?>;">
            <div class="ma-admin-card-header">
                <h3><?php echo $editingClass ? 'Edit Live Class' : 'Add New Live Class'; ?></h3>
                <button class="ma-admin-close-btn" onclick="hideForm()">×</button>
            </div>
            <div class="ma-admin-card-body">
                <form method="POST" class="ma-admin-form">
                    <input type="hidden" name="action" value="<?php echo $editingClass ? 'edit' : 'add'; ?>">
                    <?php if ($editingClass): ?>
                        <input type="hidden" name="class_id" value="<?php echo $editingClass['id']; ?>">
                    <?php endif; ?>
                    
                    <div class="ma-admin-form-row">
                        <div class="ma-admin-form-group">
                            <label for="title">Class Title *</label>
                            <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($editingClass['title'] ?? ''); ?>" required>
                        </div>
                        
                        <div class="ma-admin-form-group">
                            <label for="course_id">Course *</label>
                            <select id="course_id" name="course_id" required>
                                <option value="">Select Course</option>
                                <?php foreach ($courses as $course): ?>
                                    <option value="<?php echo $course['id']; ?>" <?php echo ($editingClass['course_id'] ?? '') == $course['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($course['title']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="ma-admin-form-row">
                        <div class="ma-admin-form-group">
                            <label for="instructor">Instructor</label>
                            <input type="text" id="instructor" name="instructor" value="<?php echo htmlspecialchars($editingClass['instructor'] ?? ''); ?>" placeholder="Teacher name">
                        </div>
                        
                        <div class="ma-admin-form-group">
                            <label for="scheduled_at">Scheduled Date & Time *</label>
                            <?php 
                            $scheduledValue = '';
                            if ($editingClass && $editingClass['scheduled_at']) {
                                $dt = new DateTime($editingClass['scheduled_at']);
                                $scheduledValue = $dt->format('Y-m-d\TH:i');
                            }
                            ?>
                            <input type="datetime-local" id="scheduled_at" name="scheduled_at" value="<?php echo htmlspecialchars($scheduledValue); ?>" required>
                        </div>
                    </div>
                    
                    <div class="ma-admin-form-row">
                        <div class="ma-admin-form-group">
                            <label for="duration_minutes">Duration (Minutes)</label>
                            <input type="number" id="duration_minutes" name="duration_minutes" value="<?php echo htmlspecialchars($editingClass['duration_minutes'] ?? '60'); ?>" min="15" step="5" placeholder="60">
                        </div>
                        
                        <div class="ma-admin-form-group">
                            <label for="max_students">Max Students</label>
                            <input type="number" id="max_students" name="max_students" value="<?php echo htmlspecialchars($editingClass['max_students'] ?? '100'); ?>" min="1" placeholder="100">
                        </div>
                    </div>
                    
                    <div class="ma-admin-form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" rows="3" placeholder="Brief description of the live class"><?php echo htmlspecialchars($editingClass['description'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="ma-admin-form-group">
                        <label for="meeting_link">Meeting Link (Zoom/Google Meet)</label>
                        <input type="url" id="meeting_link" name="meeting_link" value="<?php echo htmlspecialchars($editingClass['meeting_link'] ?? ''); ?>" placeholder="https://zoom.us/j/...">
                    </div>
                    
                    <div class="ma-admin-form-row">
                        <div class="ma-admin-form-group">
                            <label for="meeting_id">Meeting ID</label>
                            <input type="text" id="meeting_id" name="meeting_id" value="<?php echo htmlspecialchars($editingClass['meeting_id'] ?? ''); ?>" placeholder="123-456-789">
                        </div>
                        
                        <div class="ma-admin-form-group">
                            <label for="meeting_password">Meeting Password</label>
                            <input type="text" id="meeting_password" name="meeting_password" value="<?php echo htmlspecialchars($editingClass['meeting_password'] ?? ''); ?>" placeholder="Optional password">
                        </div>
                    </div>
                    
                    <div class="ma-admin-form-group">
                        <label class="ma-admin-checkbox">
                            <input type="checkbox" name="is_published" <?php echo ($editingClass['is_published'] ?? 0) ? 'checked' : ''; ?>>
                            <span>Published (visible to students)</span>
                        </label>
                    </div>
                    
                    <div class="ma-admin-form-actions">
                        <button type="button" class="ma-admin-btn ma-admin-btn-secondary" onclick="hideForm()">Cancel</button>
                        <button type="submit" class="ma-admin-btn"><?php echo $editingClass ? 'Update Live Class' : 'Add Live Class'; ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .ma-admin-live-classes {
        display: flex;
        flex-direction: column;
        gap: 24px;
    }
    .ma-admin-live-classes-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .ma-admin-live-classes-header h2 {
        font-size: 24px;
        font-weight: 700;
        color: #111;
        margin: 0;
    }
    .ma-admin-btn {
        background: #0b2b2b;
        color: #fff;
        border: none;
        padding: 12px 24px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s;
    }
    .ma-admin-btn:hover {
        background: #143a3a;
    }
    .ma-admin-btn-secondary {
        background: #fff;
        color: #0b2b2b;
        border: 1.5px solid #ddd;
    }
    .ma-admin-btn-secondary:hover {
        background: #f8f8f8;
    }
    .ma-admin-live-classes-content {
        display: flex;
        flex-direction: column;
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
    .ma-admin-close-btn {
        background: none;
        border: none;
        font-size: 24px;
        color: #999;
        cursor: pointer;
        padding: 0;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        transition: background 0.2s;
    }
    .ma-admin-close-btn:hover {
        background: #f0f0f0;
        color: #333;
    }
    .ma-admin-card-body {
        padding: 24px;
    }
    .ma-admin-table {
        width: 100%;
        border-collapse: collapse;
    }
    .ma-admin-table th {
        background: #f8f8f8;
        padding: 14px 16px;
        text-align: left;
        font-size: 12px;
        font-weight: 600;
        color: #666;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .ma-admin-table td {
        padding: 16px;
        border-bottom: 1px solid #eee;
        font-size: 13px;
        color: #333;
        vertical-align: middle;
    }
    .ma-admin-table tr:last-child td {
        border-bottom: none;
    }
    .ma-admin-subtext {
        font-size: 11px;
        color: #888;
        margin-top: 2px;
    }
    .ma-admin-date {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }
    .ma-admin-time {
        font-size: 11px;
        color: #666;
    }
    .ma-admin-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
    }
    .ma-admin-badge-success {
        background: #e8f5e9;
        color: #1a5c3e;
    }
    .ma-admin-badge-warning {
        background: #fff8e1;
        color: #b7791f;
    }
    .ma-admin-actions {
        display: flex;
        gap: 8px;
    }
    .ma-admin-action-btn {
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        text-decoration: none;
        cursor: pointer;
        border: none;
        transition: all 0.2s;
    }
    .ma-admin-action-edit {
        background: #e3f2fd;
        color: #1565c0;
    }
    .ma-admin-action-edit:hover {
        background: #bbdefb;
    }
    .ma-admin-action-delete {
        background: #ffebee;
        color: #c62828;
    }
    .ma-admin-action-delete:hover {
        background: #ffcdd2;
    }
    .ma-admin-form {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }
    .ma-admin-form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }
    .ma-admin-form-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }
    .ma-admin-form-group label {
        font-size: 13px;
        font-weight: 600;
        color: #444;
    }
    .ma-admin-form-group input,
    .ma-admin-form-group select,
    .ma-admin-form-group textarea {
        padding: 12px 14px;
        border: 1.5px solid #ddd;
        border-radius: 8px;
        font-size: 14px;
        transition: border-color 0.2s;
    }
    .ma-admin-form-group input:focus,
    .ma-admin-form-group select:focus,
    .ma-admin-form-group textarea:focus {
        outline: none;
        border-color: #0b2b2b;
    }
    .ma-admin-form-group textarea {
        resize: vertical;
        font-family: inherit;
    }
    .ma-admin-checkbox {
        display: flex;
        align-items: center;
        gap: 10px;
        cursor: pointer;
        font-size: 14px;
        color: #444;
    }
    .ma-admin-checkbox input[type="checkbox"] {
        width: 18px;
        height: 18px;
        cursor: pointer;
        accent-color: #c9a227;
    }
    .ma-admin-form-actions {
        display: flex;
        gap: 12px;
        justify-content: flex-end;
        margin-top: 8px;
    }
    .ma-admin-alert {
        padding: 14px 18px;
        border-radius: 8px;
        font-size: 14px;
        margin-bottom: 24px;
    }
    .ma-admin-alert-success {
        background: #e8f5e9;
        border: 1px solid #c8e6c9;
        color: #1a5c3e;
    }
    .ma-admin-alert-error {
        background: #ffebee;
        border: 1px solid #ffcdd2;
        color: #c62828;
    }
    .ma-admin-empty {
        padding: 60px 20px;
        text-align: center;
        color: #888;
        font-size: 14px;
    }
    @media (max-width: 768px) {
        .ma-admin-form-row {
            grid-template-columns: 1fr;
        }
        .ma-admin-live-classes-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 16px;
        }
        .ma-admin-actions {
            flex-direction: column;
        }
    }
</style>

<script>
function showAddForm() {
    document.getElementById('classForm').style.display = 'block';
    document.querySelector('#classForm h3').textContent = 'Add New Live Class';
    document.querySelector('#classForm input[name="action"]').value = 'add';
    document.querySelector('#classForm form').reset();
    window.location.hash = 'form';
}

function hideForm() {
    document.getElementById('classForm').style.display = 'none';
    window.location.hash = '';
}

// Show form if editing
<?php if ($editingClass): ?>
    document.getElementById('classForm').style.display = 'block';
<?php endif; ?>

// Show form if hash is #form
if (window.location.hash === '#form') {
    document.getElementById('classForm').style.display = 'block';
}
</script>

<?php require_once 'footer.php'; ?>
