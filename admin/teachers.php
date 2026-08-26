<?php
$pageTitle = 'Teachers Management';
require_once 'header.php';

// Get database connection
$pdo = getDB();

// Handle form submissions
$message = '';
$messageType = '';

// Verify CSRF token for POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrfToken = $_POST['csrf_token'] ?? '';
    if (!verifyCSRFToken($csrfToken)) {
        $message = 'Invalid request. Please try again.';
        $messageType = 'error';
    }
}

// Add/Edit teacher
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && empty($message)) {
    $action = $_POST['action'];
    
    if ($action === 'add' || $action === 'edit') {
        $name = trim($_POST['name'] ?? '');
        $specialization = trim($_POST['specialization'] ?? '');
        $bio = trim($_POST['bio'] ?? '');
        $qualifications = trim($_POST['qualifications'] ?? '');
        $experienceYears = (int)($_POST['experience_years'] ?? 0);
        $imageUrl = trim($_POST['image_url'] ?? '');
        $isActive = isset($_POST['is_active']) ? 1 : 0;
        
        // Handle file upload
        $uploadedImagePath = '';
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = '../uploads/teachers/';
            
            // Create directory if it doesn't exist
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $file = $_FILES['profile_image'];
            $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $allowedMimeTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
            $maxFileSize = 5 * 1024 * 1024; // 5MB
            
            // Validate file size
            if ($file['size'] > $maxFileSize) {
                $message = 'File size must be less than 5MB.';
                $messageType = 'error';
            }
            // Validate file extension
            elseif (!in_array($fileExtension, $allowedExtensions)) {
                $message = 'Invalid file type. Only JPG, PNG, GIF, and WebP are allowed.';
                $messageType = 'error';
            }
            // Validate MIME type
            elseif (!in_array($file['type'], $allowedMimeTypes)) {
                $message = 'Invalid file type. Please upload a valid image.';
                $messageType = 'error';
            }
            else {
                $fileName = uniqid('teacher_', true) . '_' . time() . '.' . $fileExtension;
                $uploadPath = $uploadDir . $fileName;
                
                if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
                    $uploadedImagePath = 'uploads/teachers/' . $fileName;
                } else {
                    $message = 'Failed to upload file. Please try again.';
                    $messageType = 'error';
                }
            }
        }
        
        // Use uploaded image if available, otherwise use URL or existing image
        if ($uploadedImagePath) {
            $imageUrl = $uploadedImagePath;
        } elseif (!$imageUrl && $editingTeacher && $editingTeacher['image_url']) {
            $imageUrl = $editingTeacher['image_url'];
        }
        
        // Generate slug from name
        $slug = strtolower(preg_replace('/[^a-z0-9]+/', '-', $name));
        
        if (empty($name)) {
            $message = 'Teacher name is required.';
            $messageType = 'error';
        } else {
            try {
                if ($action === 'add') {
                    $stmt = $pdo->prepare("
                        INSERT INTO teachers (name, slug, specialization, bio, qualifications, experience_years, image_url, is_active, created_at)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
                    ");
                    $stmt->execute([$name, $slug, $specialization, $bio, $qualifications, $experienceYears, $imageUrl, $isActive]);
                    $message = 'Teacher added successfully!';
                    $messageType = 'success';
                } else {
                    $teacherId = (int)$_POST['teacher_id'];
                    $stmt = $pdo->prepare("
                        UPDATE teachers 
                        SET name = ?, slug = ?, specialization = ?, bio = ?, qualifications = ?, experience_years = ?, image_url = ?, is_active = ?, updated_at = NOW()
                        WHERE id = ?
                    ");
                    $stmt->execute([$name, $slug, $specialization, $bio, $qualifications, $experienceYears, $imageUrl, $isActive, $teacherId]);
                    $message = 'Teacher updated successfully!';
                    $messageType = 'success';
                }
            } catch (PDOException $e) {
                $message = 'Database error: ' . $e->getMessage();
                $messageType = 'error';
            }
        }
    }
    
    // Delete teacher
    if ($action === 'delete') {
        $teacherId = (int)$_POST['teacher_id'];
        try {
            $stmt = $pdo->prepare("DELETE FROM teachers WHERE id = ?");
            $stmt->execute([$teacherId]);
            $message = 'Teacher deleted successfully!';
            $messageType = 'success';
        } catch (PDOException $e) {
            $message = 'Error deleting teacher: ' . $e->getMessage();
            $messageType = 'error';
        }
    }
    
    // Toggle active status
    if ($action === 'toggle_status') {
        $teacherId = (int)$_POST['teacher_id'];
        $status = (int)$_POST['status'];
        try {
            $stmt = $pdo->prepare("UPDATE teachers SET is_active = ? WHERE id = ?");
            $stmt->execute([$status, $teacherId]);
            $message = 'Teacher status updated successfully!';
            $messageType = 'success';
        } catch (PDOException $e) {
            $message = 'Error updating teacher status: ' . $e->getMessage();
            $messageType = 'error';
        }
    }
}

// Fetch all teachers with student count
try {
    $stmt = $pdo->query("
        SELECT t.*, 
               (SELECT COUNT(*) FROM live_classes lc WHERE lc.instructor = t.name) as class_count
        FROM teachers t 
        ORDER BY t.created_at DESC
    ");
    $teachers = $stmt->fetchAll();
} catch (PDOException $e) {
    $teachers = [];
    $message = 'Error fetching teachers: ' . $e->getMessage();
    $messageType = 'error';
}

// Fetch teacher for editing
$editingTeacher = null;
if (isset($_GET['edit'])) {
    $editId = (int)$_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM teachers WHERE id = ?");
    $stmt->execute([$editId]);
    $editingTeacher = $stmt->fetch();
}
?>

<div class="ma-admin-teachers">
    <?php if ($message): ?>
        <div class="ma-admin-alert ma-admin-alert-<?php echo $messageType; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>
    
    <div class="ma-admin-teachers-header">
        <h2>Teachers Management</h2>
        <button class="ma-admin-btn" onclick="showAddForm()">+ Add New Teacher</button>
    </div>
    
    <div class="ma-admin-teachers-content">
        <!-- Teachers Table -->
        <div class="ma-admin-card">
            <div class="ma-admin-card-body">
                <?php if (empty($teachers)): ?>
                    <p class="ma-admin-empty">No teachers found.</p>
                <?php else: ?>
                    <table class="ma-admin-table">
                        <thead>
                            <tr>
                                <th>Teacher</th>
                                <th>Specialization</th>
                                <th>Experience</th>
                                <th>Classes</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($teachers as $teacher): ?>
                                <tr>
                                    <td>
                                        <div class="ma-admin-teacher-info">
                                            <?php if ($teacher['image_url']): ?>
                                                <img src="<?php echo htmlspecialchars($teacher['image_url']); ?>" alt="<?php echo htmlspecialchars($teacher['name']); ?>" class="ma-admin-teacher-avatar">
                                            <?php else: ?>
                                                <div class="ma-admin-teacher-avatar-placeholder">
                                                    <?php echo strtoupper(substr($teacher['name'], 0, 2)); ?>
                                                </div>
                                            <?php endif; ?>
                                            <div>
                                                <strong><?php echo htmlspecialchars($teacher['name']); ?></strong>
                                                <?php if ($teacher['qualifications']): ?>
                                                    <div class="ma-admin-subtext"><?php echo htmlspecialchars(substr($teacher['qualifications'], 0, 30)); ?>...</div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?php echo htmlspecialchars($teacher['specialization'] ?: '-'); ?></td>
                                    <td><?php echo $teacher['experience_years'] ? $teacher['experience_years'] . ' years' : '-'; ?></td>
                                    <td><?php echo number_format($teacher['class_count']); ?></td>
                                    <td>
                                        <span class="ma-admin-badge ma-admin-badge-<?php echo $teacher['is_active'] ? 'success' : 'warning'; ?>">
                                            <?php echo $teacher['is_active'] ? 'Active' : 'Inactive'; ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('M d, Y', strtotime($teacher['created_at'])); ?></td>
                                    <td>
                                        <div class="ma-admin-actions">
                                            <a href="?edit=<?php echo $teacher['id']; ?>" class="ma-admin-action-btn ma-admin-action-edit">Edit</a>
                                            <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this teacher?');">
                                                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="teacher_id" value="<?php echo $teacher['id']; ?>">
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
        <div class="ma-admin-card" id="teacherForm" style="display: <?php echo $editingTeacher ? 'block' : 'none'; ?>;">
            <div class="ma-admin-card-header">
                <h3><?php echo $editingTeacher ? 'Edit Teacher' : 'Add New Teacher'; ?></h3>
                <button class="ma-admin-close-btn" onclick="hideForm()">×</button>
            </div>
            <div class="ma-admin-card-body">
                <form method="POST" class="ma-admin-form" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                    <input type="hidden" name="action" value="<?php echo $editingTeacher ? 'edit' : 'add'; ?>">
                    <?php if ($editingTeacher): ?>
                        <input type="hidden" name="teacher_id" value="<?php echo $editingTeacher['id']; ?>">
                    <?php endif; ?>
                    
                    <div class="ma-admin-form-row">
                        <div class="ma-admin-form-group">
                            <label for="name">Teacher Name *</label>
                            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($editingTeacher['name'] ?? ''); ?>" required>
                        </div>
                        
                        <div class="ma-admin-form-group">
                            <label for="specialization">Specialization</label>
                            <input type="text" id="specialization" name="specialization" value="<?php echo htmlspecialchars($editingTeacher['specialization'] ?? ''); ?>" placeholder="e.g., Quranic Studies, Arabic">
                        </div>
                    </div>
                    
                    <div class="ma-admin-form-row">
                        <div class="ma-admin-form-group">
                            <label for="experience_years">Experience (Years)</label>
                            <input type="number" id="experience_years" name="experience_years" value="<?php echo htmlspecialchars($editingTeacher['experience_years'] ?? ''); ?>" min="0" placeholder="0">
                        </div>
                        
                        <div class="ma-admin-form-group">
                            <label for="profile_image">Profile Image</label>
                            <input type="file" id="profile_image" name="profile_image" accept="image/*">
                            <?php if ($editingTeacher && $editingTeacher['image_url']): ?>
                                <small style="color: #888;">Current: <?php echo htmlspecialchars($editingTeacher['image_url']); ?></small>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="ma-admin-form-group">
                        <label for="image_url">Or Image URL</label>
                        <input type="url" id="image_url" name="image_url" value="<?php echo htmlspecialchars($editingTeacher['image_url'] ?? ''); ?>" placeholder="https://example.com/image.jpg">
                        <small style="color: #888;">Upload image or provide URL</small>
                    </div>
                    
                    <div class="ma-admin-form-group">
                        <label for="qualifications">Qualifications</label>
                        <input type="text" id="qualifications" name="qualifications" value="<?php echo htmlspecialchars($editingTeacher['qualifications'] ?? ''); ?>" placeholder="e.g., PhD in Islamic Studies, Al-Azhar University">
                    </div>
                    
                    <div class="ma-admin-form-group">
                        <label for="bio">Bio</label>
                        <textarea id="bio" name="bio" rows="4" placeholder="Brief biography of the teacher"><?php echo htmlspecialchars($editingTeacher['bio'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="ma-admin-form-group">
                        <label class="ma-admin-checkbox">
                            <input type="checkbox" name="is_active" <?php echo ($editingTeacher['is_active'] ?? 1) ? 'checked' : ''; ?>>
                            <span>Active (visible to users)</span>
                        </label>
                    </div>
                    
                    <div class="ma-admin-form-actions">
                        <button type="button" class="ma-admin-btn ma-admin-btn-secondary" onclick="hideForm()">Cancel</button>
                        <button type="submit" class="ma-admin-btn"><?php echo $editingTeacher ? 'Update Teacher' : 'Add Teacher'; ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .ma-admin-teachers {
        display: flex;
        flex-direction: column;
        gap: 24px;
    }
    .ma-admin-teachers-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .ma-admin-teachers-header h2 {
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
    .ma-admin-teachers-content {
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
    .ma-admin-teacher-info {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .ma-admin-teacher-avatar {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #e0e0e0;
    }
    .ma-admin-teacher-avatar-placeholder {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: linear-gradient(135deg, #c9a227 0%, #b8911f 100%);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        font-weight: 700;
    }
    .ma-admin-subtext {
        font-size: 11px;
        color: #888;
        margin-top: 2px;
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
    .ma-admin-form-group textarea {
        padding: 12px 14px;
        border: 1.5px solid #ddd;
        border-radius: 8px;
        font-size: 14px;
        transition: border-color 0.2s;
    }
    .ma-admin-form-group input:focus,
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
        .ma-admin-teachers-header {
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
    document.getElementById('teacherForm').style.display = 'block';
    document.querySelector('#teacherForm h3').textContent = 'Add New Teacher';
    document.querySelector('#teacherForm input[name="action"]').value = 'add';
    document.querySelector('#teacherForm form').reset();
    window.location.hash = 'form';
}

function hideForm() {
    document.getElementById('teacherForm').style.display = 'none';
    window.location.hash = '';
}

// Show form if editing
<?php if ($editingTeacher): ?>
    document.getElementById('teacherForm').style.display = 'block';
<?php endif; ?>

// Show form if hash is #form
if (window.location.hash === '#form') {
    document.getElementById('teacherForm').style.display = 'block';
}
</script>

<?php require_once 'footer.php'; ?>
