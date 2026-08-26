<?php
$pageTitle = 'Courses Management';
require_once 'header.php';

// Get database connection
$pdo = getDB();

// Handle form submissions
$message = '';
$messageType = '';

// Add/Edit course
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    
    if ($action === 'add' || $action === 'edit') {
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $category = trim($_POST['category'] ?? '');
        $level = trim($_POST['level'] ?? '');
        $price = floatval($_POST['price'] ?? 0);
        $isPublished = isset($_POST['is_published']) ? 1 : 0;
        
        // Generate slug from title
        $slug = strtolower(preg_replace('/[^a-z0-9]+/', '-', $title));
        
        if (empty($title) || empty($description) || empty($category)) {
            $message = 'Title, description, and category are required.';
            $messageType = 'error';
        } else {
            try {
                if ($action === 'add') {
                    $stmt = $pdo->prepare("
                        INSERT INTO courses (title, slug, description, category, level, price, is_published, created_at)
                        VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
                    ");
                    $stmt->execute([$title, $slug, $description, $category, $level, $price, $isPublished]);
                    $message = 'Course added successfully!';
                    $messageType = 'success';
                } else {
                    $courseId = (int)$_POST['course_id'];
                    $stmt = $pdo->prepare("
                        UPDATE courses 
                        SET title = ?, slug = ?, description = ?, category = ?, level = ?, price = ?, is_published = ?, updated_at = NOW()
                        WHERE id = ?
                    ");
                    $stmt->execute([$title, $slug, $description, $category, $level, $price, $isPublished, $courseId]);
                    $message = 'Course updated successfully!';
                    $messageType = 'success';
                }
            } catch (PDOException $e) {
                $message = 'Database error: ' . $e->getMessage();
                $messageType = 'error';
            }
        }
    }
    
    // Delete course
    if ($action === 'delete') {
        $courseId = (int)$_POST['course_id'];
        try {
            $stmt = $pdo->prepare("DELETE FROM courses WHERE id = ?");
            $stmt->execute([$courseId]);
            $message = 'Course deleted successfully!';
            $messageType = 'success';
        } catch (PDOException $e) {
            $message = 'Cannot delete course. It may have enrollments or related data.';
            $messageType = 'error';
        }
    }
}

// Fetch all courses
try {
    $stmt = $pdo->query("
        SELECT c.*, 
               (SELECT COUNT(*) FROM enrollments e WHERE e.course_id = c.id) as enrollment_count
        FROM courses c
        ORDER BY c.created_at DESC
    ");
    $courses = $stmt->fetchAll();
} catch (PDOException $e) {
    $courses = [];
    $message = 'Error fetching courses: ' . $e->getMessage();
    $messageType = 'error';
}

// Fetch course for editing
$editingCourse = null;
if (isset($_GET['edit'])) {
    $editId = (int)$_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM courses WHERE id = ?");
    $stmt->execute([$editId]);
    $editingCourse = $stmt->fetch();
}
?>

<div class="ma-admin-courses">
    <?php if ($message): ?>
        <div class="ma-admin-alert ma-admin-alert-<?php echo $messageType; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>
    
    <div class="ma-admin-courses-header">
        <h2>Courses Management</h2>
        <button class="ma-admin-btn" onclick="showAddForm()">+ Add New Course</button>
    </div>
    
    <div class="ma-admin-courses-content">
        <!-- Courses Table -->
        <div class="ma-admin-card">
            <div class="ma-admin-card-body">
                <?php if (empty($courses)): ?>
                    <p class="ma-admin-empty">No courses found.</p>
                <?php else: ?>
                    <table class="ma-admin-table">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Level</th>
                                <th>Price</th>
                                <th>Enrollments</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($courses as $course): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo htmlspecialchars($course['title']); ?></strong>
                                        <div class="ma-admin-subtext"><?php echo htmlspecialchars(substr($course['description'], 0, 50)); ?>...</div>
                                    </td>
                                    <td><?php echo htmlspecialchars($course['category']); ?></td>
                                    <td><?php echo htmlspecialchars($course['level'] ?: '-'); ?></td>
                                    <td>
                                        <span class="ma-admin-price">
                                            <?php if ($course['price'] > 0): ?>
                                                ₹<?php echo number_format($course['price'], 2); ?>
                                            <?php else: ?>
                                                <span class="ma-admin-free">Free</span>
                                            <?php endif; ?>
                                        </span>
                                    </td>
                                    <td><?php echo number_format($course['enrollment_count']); ?></td>
                                    <td>
                                        <span class="ma-admin-badge ma-admin-badge-<?php echo $course['is_published'] ? 'success' : 'warning'; ?>">
                                            <?php echo $course['is_published'] ? 'Published' : 'Draft'; ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('M d, Y', strtotime($course['created_at'])); ?></td>
                                    <td>
                                        <div class="ma-admin-actions">
                                            <a href="?edit=<?php echo $course['id']; ?>" class="ma-admin-action-btn ma-admin-action-edit">Edit</a>
                                            <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this course?');">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
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
        <div class="ma-admin-card" id="courseForm" style="display: <?php echo $editingCourse ? 'block' : 'none'; ?>;">
            <div class="ma-admin-card-header">
                <h3><?php echo $editingCourse ? 'Edit Course' : 'Add New Course'; ?></h3>
                <button class="ma-admin-close-btn" onclick="hideForm()">×</button>
            </div>
            <div class="ma-admin-card-body">
                <form method="POST" class="ma-admin-form">
                    <input type="hidden" name="action" value="<?php echo $editingCourse ? 'edit' : 'add'; ?>">
                    <?php if ($editingCourse): ?>
                        <input type="hidden" name="course_id" value="<?php echo $editingCourse['id']; ?>">
                    <?php endif; ?>
                    
                    <div class="ma-admin-form-row">
                        <div class="ma-admin-form-group">
                            <label for="title">Course Title *</label>
                            <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($editingCourse['title'] ?? ''); ?>" required>
                        </div>
                        
                        <div class="ma-admin-form-group">
                            <label for="category">Category *</label>
                            <select id="category" name="category" required>
                                <option value="">Select Category</option>
                                <option value="Quran" <?php echo ($editingCourse['category'] ?? '') === 'Quran' ? 'selected' : ''; ?>>Quran</option>
                                <option value="Arabic" <?php echo ($editingCourse['category'] ?? '') === 'Arabic' ? 'selected' : ''; ?>>Arabic</option>
                                <option value="Islamic Studies" <?php echo ($editingCourse['category'] ?? '') === 'Islamic Studies' ? 'selected' : ''; ?>>Islamic Studies</option>
                                <option value="Hadith" <?php echo ($editingCourse['category'] ?? '') === 'Hadith' ? 'selected' : ''; ?>>Hadith</option>
                                <option value="Fiqh" <?php echo ($editingCourse['category'] ?? '') === 'Fiqh' ? 'selected' : ''; ?>>Fiqh</option>
                                <option value="Seerah" <?php echo ($editingCourse['category'] ?? '') === 'Seerah' ? 'selected' : ''; ?>>Seerah</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="ma-admin-form-group">
                        <label for="level">Level</label>
                        <select id="level" name="level">
                            <option value="">Select Level</option>
                            <option value="Beginner" <?php echo ($editingCourse['level'] ?? '') === 'Beginner' ? 'selected' : ''; ?>>Beginner</option>
                            <option value="Intermediate" <?php echo ($editingCourse['level'] ?? '') === 'Intermediate' ? 'selected' : ''; ?>>Intermediate</option>
                            <option value="Advanced" <?php echo ($editingCourse['level'] ?? '') === 'Advanced' ? 'selected' : ''; ?>>Advanced</option>
                        </select>
                    </div>
                    
                    <div class="ma-admin-form-group">
                        <label for="price">Price (₹)</label>
                        <input type="number" id="price" name="price" value="<?php echo htmlspecialchars($editingCourse['price'] ?? '0'); ?>" placeholder="0.00" step="0.01" min="0">
                        <small style="color: #888;">Enter 0 for free course</small>
                    </div>
                    
                    <div class="ma-admin-form-group">
                        <label for="description">Description *</label>
                        <textarea id="description" name="description" rows="6" required><?php echo htmlspecialchars($editingCourse['description'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="ma-admin-form-group">
                        <label class="ma-admin-checkbox">
                            <input type="checkbox" name="is_published" <?php echo ($editingCourse['is_published'] ?? 0) ? 'checked' : ''; ?>>
                            <span>Published (visible to users)</span>
                        </label>
                    </div>
                    
                    <div class="ma-admin-form-actions">
                        <button type="button" class="ma-admin-btn ma-admin-btn-secondary" onclick="hideForm()">Cancel</button>
                        <button type="submit" class="ma-admin-btn"><?php echo $editingCourse ? 'Update Course' : 'Add Course'; ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .ma-admin-courses {
        display: flex;
        flex-direction: column;
        gap: 24px;
    }
    .ma-admin-courses-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .ma-admin-courses-header h2 {
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
    .ma-admin-courses-content {
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
        font-size: 12px;
        color: #888;
        margin-top: 4px;
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
    .ma-admin-price {
        font-weight: 700;
        color: #0b2b2b;
        font-size: 14px;
    }
    .ma-admin-free {
        background: #e8f5e9;
        color: #1a5c3e;
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
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
        font-family: inherit;
        transition: border-color 0.2s;
    }
    .ma-admin-form-group input:focus,
    .ma-admin-form-group select:focus,
    .ma-admin-form-group textarea:focus {
        outline: none;
        border-color: #0b2b2b;
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
        .ma-admin-courses-header {
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
    document.getElementById('courseForm').style.display = 'block';
    document.querySelector('#courseForm h3').textContent = 'Add New Course';
    document.querySelector('#courseForm input[name="action"]').value = 'add';
    document.querySelector('#courseForm form').reset();
    window.location.hash = 'form';
}

function hideForm() {
    document.getElementById('courseForm').style.display = 'none';
    window.location.hash = '';
}

// Show form if editing
<?php if ($editingCourse): ?>
    document.getElementById('courseForm').style.display = 'block';
<?php endif; ?>

// Show form if hash is #form
if (window.location.hash === '#form') {
    document.getElementById('courseForm').style.display = 'block';
}
</script>

<?php require_once 'footer.php'; ?>
