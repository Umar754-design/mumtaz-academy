<?php
$pageTitle = 'Study Materials Management';
require_once 'header.php';

// Set PHP timezone to Asia/Kolkata
date_default_timezone_set('Asia/Kolkata');

// Get database connection
$pdo = getDB();

// Handle form submissions
$message = '';
$messageType = '';

// Add/Edit study material
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    
    if ($action === 'add' || $action === 'edit') {
        $courseId = (int)($_POST['course_id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $category = trim($_POST['category'] ?? '');
        $isFeatured = isset($_POST['is_featured']) ? 1 : 0;
        $fileUrl = '';
        $fileType = '';
        $fileSize = '';
        
        // Handle file upload
        if (isset($_FILES['material_file']) && $_FILES['material_file']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = '../uploads/materials/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $file = $_FILES['material_file'];
            $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $allowedExtensions = ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx', 'zip', 'rar'];
            $allowedMimeTypes = [
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.ms-powerpoint',
                'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'application/zip',
                'application/x-rar-compressed',
                'application/vnd.rar'
            ];
            $maxFileSize = 10 * 1024 * 1024; // 10MB for documents
            
            // Validate file size
            if ($file['size'] > $maxFileSize) {
                $message = 'File size must be less than 10MB.';
                $messageType = 'error';
            }
            // Validate file extension
            elseif (!in_array($fileExtension, $allowedExtensions)) {
                $message = 'Invalid file type. Only PDF, DOC, DOCX, PPT, PPTX, XLS, XLSX, ZIP, and RAR are allowed.';
                $messageType = 'error';
            }
            // Validate MIME type
            elseif (!in_array($file['type'], $allowedMimeTypes)) {
                $message = 'Invalid file type. Please upload a valid document.';
                $messageType = 'error';
            }
            else {
                $fileName = uniqid('material_', true) . '_' . time() . '.' . $fileExtension;
                $uploadPath = $uploadDir . $fileName;
                
                if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
                    $fileUrl = 'uploads/materials/' . $fileName;
                    $fileType = $fileExtension;
                    $fileSize = round($file['size'] / 1024, 2) . ' KB';
                } else {
                    $message = 'Failed to upload file. Please try again.';
                    $messageType = 'error';
                }
            }
        }
        
        // If editing and no new file uploaded, keep existing file
        if ($action === 'edit' && empty($fileUrl)) {
            $materialId = (int)$_POST['material_id'];
            $stmt = $pdo->prepare("SELECT file_url, file_type, file_size FROM study_materials WHERE id = ?");
            $stmt->execute([$materialId]);
            $existingMaterial = $stmt->fetch();
            if ($existingMaterial) {
                $fileUrl = $existingMaterial['file_url'];
                $fileType = $existingMaterial['file_type'];
                $fileSize = $existingMaterial['file_size'];
            }
        }
        
        if (empty($title) || empty($courseId) || empty($fileUrl)) {
            $message = 'Title, course, and file are required.';
            $messageType = 'error';
        } else {
            try {
                if ($action === 'add') {
                    $stmt = $pdo->prepare("
                        INSERT INTO study_materials (course_id, title, description, file_url, file_type, file_size, category, is_featured, created_at)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
                    ");
                    $stmt->execute([$courseId, $title, $description, $fileUrl, $fileType, $fileSize, $category, $isFeatured]);
                    $message = 'Study material added successfully!';
                    $messageType = 'success';
                } else {
                    $materialId = (int)$_POST['material_id'];
                    $stmt = $pdo->prepare("
                        UPDATE study_materials 
                        SET course_id = ?, title = ?, description = ?, file_url = ?, file_type = ?, file_size = ?, category = ?, is_featured = ?
                        WHERE id = ?
                    ");
                    $stmt->execute([$courseId, $title, $description, $fileUrl, $fileType, $fileSize, $category, $isFeatured, $materialId]);
                    $message = 'Study material updated successfully!';
                    $messageType = 'success';
                }
            } catch (PDOException $e) {
                $message = 'Error saving study material: ' . $e->getMessage();
                $messageType = 'error';
            }
        }
    }
    
    // Delete study material
    if ($action === 'delete') {
        $materialId = (int)$_POST['material_id'];
        try {
            // Get file info before deleting
            $stmt = $pdo->prepare("SELECT file_url FROM study_materials WHERE id = ?");
            $stmt->execute([$materialId]);
            $material = $stmt->fetch();
            
            // Delete from database
            $stmt = $pdo->prepare("DELETE FROM study_materials WHERE id = ?");
            $stmt->execute([$materialId]);
            
            // Delete file if exists
            if ($material && $material['file_url']) {
                $filePath = '../' . $material['file_url'];
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }
            
            $message = 'Study material deleted successfully!';
            $messageType = 'success';
        } catch (PDOException $e) {
            $message = 'Error deleting study material: ' . $e->getMessage();
            $messageType = 'error';
        }
    }
    
    // Toggle featured status
    if ($action === 'toggle_featured') {
        $materialId = (int)$_POST['material_id'];
        try {
            $stmt = $pdo->prepare("UPDATE study_materials SET is_featured = NOT is_featured WHERE id = ?");
            $stmt->execute([$materialId]);
            $message = 'Featured status updated successfully!';
            $messageType = 'success';
        } catch (PDOException $e) {
            $message = 'Error updating featured status: ' . $e->getMessage();
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

// Fetch all study materials with course name
try {
    $stmt = $pdo->query("
        SELECT sm.*, c.title as course_title 
        FROM study_materials sm 
        LEFT JOIN courses c ON sm.course_id = c.id 
        ORDER BY sm.created_at DESC
    ");
    $materials = $stmt->fetchAll();
} catch (PDOException $e) {
    $materials = [];
    $message = 'Error fetching study materials: ' . $e->getMessage();
    $messageType = 'error';
}

// Handle edit mode
$editingMaterial = null;
if (isset($_GET['edit'])) {
    $editId = (int)$_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM study_materials WHERE id = ?");
    $stmt->execute([$editId]);
    $editingMaterial = $stmt->fetch();
}
?>

<div class="ma-admin-materials">
    <?php if ($message): ?>
        <div class="ma-admin-alert ma-admin-alert-<?php echo $messageType; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>
    
    <div class="ma-admin-materials-header">
        <h2>Study Materials Management</h2>
        <button class="ma-admin-btn" onclick="showAddForm()">+ Add New Material</button>
    </div>
    
    <div class="ma-admin-materials-content">
        <!-- Study Materials Table -->
        <div class="ma-admin-card">
            <div class="ma-admin-card-body">
                <?php if (empty($materials)): ?>
                    <p class="ma-admin-empty">No study materials found.</p>
                <?php else: ?>
                    <table class="ma-admin-table">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Course</th>
                                <th>Category</th>
                                <th>File Type</th>
                                <th>File Size</th>
                                <th>Downloads</th>
                                <th>Featured</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($materials as $material): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo htmlspecialchars($material['title']); ?></strong>
                                        <?php if ($material['description']): ?>
                                            <div class="ma-admin-subtext"><?php echo htmlspecialchars(substr($material['description'], 0, 40)); ?>...</div>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($material['course_title'] ?? 'Unknown'); ?></td>
                                    <td>
                                        <span class="ma-admin-badge ma-admin-badge-info">
                                            <?php echo htmlspecialchars($material['category'] ?: 'General'); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="ma-admin-file-type">
                                            <?php echo strtoupper(htmlspecialchars($material['file_type'] ?: 'FILE')); ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($material['file_size'] ?: '-'); ?></td>
                                    <td><?php echo number_format($material['download_count']); ?></td>
                                    <td>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="action" value="toggle_featured">
                                            <input type="hidden" name="material_id" value="<?php echo $material['id']; ?>">
                                            <button type="submit" class="ma-admin-badge ma-admin-badge-<?php echo $material['is_featured'] ? 'success' : 'secondary'; ?>">
                                                <?php echo $material['is_featured'] ? '⭐ Featured' : '☆ Normal'; ?>
                                            </button>
                                        </form>
                                    </td>
                                    <td>
                                        <div class="ma-admin-actions">
                                            <a href="?edit=<?php echo $material['id']; ?>" class="ma-admin-action-btn ma-admin-action-edit">Edit</a>
                                            <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this study material?');">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="material_id" value="<?php echo $material['id']; ?>">
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
    </div>
    
    <!-- Add/Edit Form Modal -->
    <div id="materialFormModal" class="ma-admin-modal" style="display: <?php echo $editingMaterial ? 'block' : 'none'; ?>;">
        <div class="ma-admin-modal-content">
            <div class="ma-admin-modal-header">
                <h3><?php echo $editingMaterial ? 'Edit Study Material' : 'Add New Study Material'; ?></h3>
                <button class="ma-admin-close-btn" onclick="hideForm()">×</button>
            </div>
            <div class="ma-admin-modal-body">
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="<?php echo $editingMaterial ? 'edit' : 'add'; ?>">
                    <?php if ($editingMaterial): ?>
                        <input type="hidden" name="material_id" value="<?php echo $editingMaterial['id']; ?>">
                    <?php endif; ?>
                    
                    <div class="ma-admin-form-row">
                        <div class="ma-admin-form-group">
                            <label for="title">Title *</label>
                            <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($editingMaterial['title'] ?? ''); ?>" required placeholder="Material title">
                        </div>
                        
                        <div class="ma-admin-form-group">
                            <label for="course_id">Course *</label>
                            <select id="course_id" name="course_id" required>
                                <option value="">Select Course</option>
                                <?php foreach ($courses as $course): ?>
                                    <option value="<?php echo $course['id']; ?>" <?php echo ($editingMaterial['course_id'] ?? '') == $course['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($course['title']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="ma-admin-form-row">
                        <div class="ma-admin-form-group">
                            <label for="category">Category</label>
                            <input type="text" id="category" name="category" value="<?php echo htmlspecialchars($editingMaterial['category'] ?? ''); ?>" placeholder="e.g., Notes, Slides, Reference">
                        </div>
                        
                        <div class="ma-admin-form-group">
                            <label for="material_file">File *</label>
                            <input type="file" id="material_file" name="material_file" <?php echo $editingMaterial ? '' : 'required'; ?>>
                            <?php if ($editingMaterial && $editingMaterial['file_url']): ?>
                                <small>Current: <?php echo htmlspecialchars($editingMaterial['file_url']); ?></small>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="ma-admin-form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" rows="3" placeholder="Brief description of the study material"><?php echo htmlspecialchars($editingMaterial['description'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="ma-admin-form-group">
                        <label class="ma-admin-checkbox">
                            <input type="checkbox" name="is_featured" <?php echo ($editingMaterial['is_featured'] ?? 0) ? 'checked' : ''; ?>>
                            <span>Featured (show prominently)</span>
                        </label>
                    </div>
                    
                    <div class="ma-admin-form-actions">
                        <button type="button" class="ma-admin-btn ma-admin-btn-secondary" onclick="hideForm()">Cancel</button>
                        <button type="submit" class="ma-admin-btn"><?php echo $editingMaterial ? 'Update Material' : 'Add Material'; ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function showAddForm() {
    document.getElementById('materialFormModal').style.display = 'block';
}

function hideForm() {
    document.getElementById('materialFormModal').style.display = 'none';
    window.location.href = 'materials.php';
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('materialFormModal');
    if (event.target == modal) {
        hideForm();
    }
}
</script>

<style>
    .ma-admin-materials {
        display: flex;
        flex-direction: column;
        gap: 24px;
    }
    .ma-admin-materials-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .ma-admin-materials-header h2 {
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
        background: #f0f0f0;
        color: #333;
    }
    .ma-admin-btn-secondary:hover {
        background: #e0e0e0;
    }
    .ma-admin-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        overflow: hidden;
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
        border-bottom: 1px solid #f0f0f0;
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
    .ma-admin-empty {
        text-align: center;
        padding: 40px;
        color: #888;
    }
    .ma-admin-form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }
    .ma-admin-form-group {
        margin-bottom: 16px;
    }
    .ma-admin-form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        font-size: 13px;
        color: #333;
    }
    .ma-admin-form-group input,
    .ma-admin-form-group select,
    .ma-admin-form-group textarea {
        width: 100%;
        padding: 12px;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 14px;
        box-sizing: border-box;
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
        gap: 8px;
        cursor: pointer;
    }
    .ma-admin-checkbox input {
        width: auto;
    }
    .ma-admin-form-actions {
        display: flex;
        gap: 12px;
        justify-content: flex-end;
        margin-top: 24px;
    }
    .ma-admin-actions {
        display: flex;
        gap: 8px;
    }
    .ma-admin-action-btn {
        padding: 6px 12px;
        border: none;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
    }
    .ma-admin-action-edit {
        background: #e3f2fd;
        color: #1976d2;
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
    .ma-admin-badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 12px;
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
    .ma-admin-badge-info {
        background: #e3f2fd;
        color: #1565c0;
    }
    .ma-admin-badge-secondary {
        background: #f5f5f5;
        color: #666;
    }
    .ma-admin-file-type {
        display: inline-block;
        padding: 4px 8px;
        background: #f5f5f5;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 700;
        color: #666;
    }
    .ma-admin-alert {
        padding: 16px;
        border-radius: 8px;
        margin-bottom: 24px;
        font-weight: 600;
    }
    .ma-admin-alert-success {
        background: #e8f5e9;
        color: #1a5c3e;
        border: 1px solid #c8e6c9;
    }
    .ma-admin-alert-error {
        background: #ffebee;
        color: #c62828;
        border: 1px solid #ffcdd2;
    }
    .ma-admin-modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 1000;
    }
    .ma-admin-modal-content {
        background: #fff;
        border-radius: 12px;
        width: 90%;
        max-width: 600px;
        max-height: 90vh;
        overflow-y: auto;
    }
    .ma-admin-modal-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 20px 24px;
        border-bottom: 1px solid #f0f0f0;
    }
    .ma-admin-modal-header h3 {
        margin: 0;
        font-size: 18px;
        font-weight: 700;
        color: #111;
    }
    .ma-admin-close-btn {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        transition: background 0.2s;
        background: none;
        border: none;
        font-size: 24px;
        cursor: pointer;
    }
    .ma-admin-close-btn:hover {
        background: #f0f0f0;
        color: #333;
    }
    .ma-admin-modal-body {
        padding: 24px;
    }
</style>

<?php require_once 'footer.php'; ?>
