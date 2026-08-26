<?php
$pageTitle = 'Admin Management';
require_once 'header.php';

// Get database connection
$pdo = getDB();

// Handle form submissions
$message = '';
$messageType = '';

// Add/Edit admin
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    
    if ($action === 'add' || $action === 'edit') {
        $username = trim($_POST['username'] ?? '');
        $fullName = trim($_POST['full_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $isActive = isset($_POST['is_active']) ? 1 : 0;
        
        if (empty($username) || empty($fullName) || empty($email)) {
            $message = 'Username, full name, and email are required.';
            $messageType = 'error';
        } elseif ($action === 'add' && empty($password)) {
            $message = 'Password is required for new admins.';
            $messageType = 'error';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $message = 'Please enter a valid email address.';
            $messageType = 'error';
        } else {
            try {
                if ($action === 'add') {
                    // Check if username or email already exists
                    $stmt = $pdo->prepare("SELECT id FROM admins WHERE username = ? OR email = ?");
                    $stmt->execute([$username, $email]);
                    if ($stmt->fetch()) {
                        $message = 'Username or email already exists.';
                        $messageType = 'error';
                    } else {
                        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                        $stmt = $pdo->prepare("
                            INSERT INTO admins (username, password, full_name, email, is_active, created_at)
                            VALUES (?, ?, ?, ?, ?, NOW())
                        ");
                        $stmt->execute([$username, $hashedPassword, $fullName, $email, $isActive]);
                        $message = 'Admin added successfully!';
                        $messageType = 'success';
                    }
                } else {
                    $adminId = (int)$_POST['admin_id'];
                    $updateFields = "full_name = ?, email = ?, is_active = ?";
                    $params = [$fullName, $email, $isActive];
                    
                    if (!empty($password)) {
                        $updateFields .= ", password = ?";
                        $params[] = password_hash($password, PASSWORD_DEFAULT);
                    }
                    
                    $updateFields .= ", updated_at = NOW()";
                    $params[] = $adminId;
                    
                    $stmt = $pdo->prepare("UPDATE admins SET $updateFields WHERE id = ?");
                    $stmt->execute($params);
                    $message = 'Admin updated successfully!';
                    $messageType = 'success';
                }
            } catch (PDOException $e) {
                $message = 'Database error: ' . $e->getMessage();
                $messageType = 'error';
            }
        }
    }
    
    // Delete admin
    if ($action === 'delete') {
        $adminId = (int)$_POST['admin_id'];
        $currentAdmin = getCurrentAdmin();
        
        if ($adminId === $currentAdmin['id']) {
            $message = 'You cannot delete your own account.';
            $messageType = 'error';
        } else {
            try {
                $stmt = $pdo->prepare("DELETE FROM admins WHERE id = ?");
                $stmt->execute([$adminId]);
                $message = 'Admin deleted successfully!';
                $messageType = 'success';
            } catch (PDOException $e) {
                $message = 'Error deleting admin: ' . $e->getMessage();
                $messageType = 'error';
            }
        }
    }
}

// Fetch all admins
try {
    $stmt = $pdo->query("SELECT * FROM admins ORDER BY created_at DESC");
    $admins = $stmt->fetchAll();
} catch (PDOException $e) {
    $admins = [];
    $message = 'Error fetching admins: ' . $e->getMessage();
    $messageType = 'error';
}

// Fetch admin for editing
$editingAdmin = null;
if (isset($_GET['edit'])) {
    $editId = (int)$_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM admins WHERE id = ?");
    $stmt->execute([$editId]);
    $editingAdmin = $stmt->fetch();
}
?>

<div class="ma-admin-admins">
    <?php if ($message): ?>
        <div class="ma-admin-alert ma-admin-alert-<?php echo $messageType; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>
    
    <!-- Current Admin Info -->
    <?php $currentAdmin = getCurrentAdmin(); ?>
    <div class="ma-admin-current-info">
        <div class="ma-admin-current-info-content">
            <div class="ma-admin-current-info-icon">🔐</div>
            <div class="ma-admin-current-info-details">
                <div class="ma-admin-current-info-label">Logged in as</div>
                <div class="ma-admin-current-info-name"><?php echo htmlspecialchars($currentAdmin['name']); ?></div>
                <div class="ma-admin-current-info-username">@<?php echo htmlspecialchars($currentAdmin['username']); ?></div>
            </div>
        </div>
    </div>
    
    <div class="ma-admin-admins-header">
        <h2>Admin Management</h2>
        <button class="ma-admin-btn" onclick="showAddForm()">+ Add New Admin</button>
    </div>
    
    <div class="ma-admin-admins-content">
        <!-- Admins Table -->
        <div class="ma-admin-card">
            <div class="ma-admin-card-body">
                <?php if (empty($admins)): ?>
                    <p class="ma-admin-empty">No admins found.</p>
                <?php else: ?>
                    <table class="ma-admin-table">
                        <thead>
                            <tr>
                                <th>Username</th>
                                <th>Full Name</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Last Login</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($admins as $admin): 
                                $currentAdmin = getCurrentAdmin();
                                $isCurrentUser = $admin['id'] === $currentAdmin['id'];
                            ?>
                                <tr>
                                    <td>
                                        <strong><?php echo htmlspecialchars($admin['username']); ?></strong>
                                        <?php if ($isCurrentUser): ?>
                                            <span class="ma-admin-badge ma-admin-badge-info">(You)</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($admin['full_name']); ?></td>
                                    <td><?php echo htmlspecialchars($admin['email']); ?></td>
                                    <td>
                                        <span class="ma-admin-badge ma-admin-badge-<?php echo $admin['is_active'] ? 'success' : 'warning'; ?>">
                                            <?php echo $admin['is_active'] ? 'Active' : 'Inactive'; ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('M d, Y', strtotime($admin['created_at'])); ?></td>
                                    <td><?php echo $admin['last_login'] ? date('M d, Y H:i', strtotime($admin['last_login'])) : 'Never'; ?></td>
                                    <td>
                                        <div class="ma-admin-actions">
                                            <a href="?edit=<?php echo $admin['id']; ?>" class="ma-admin-action-btn ma-admin-action-edit">Edit</a>
                                            <?php if (!$isCurrentUser): ?>
                                                <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this admin?');">
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="admin_id" value="<?php echo $admin['id']; ?>">
                                                    <button type="submit" class="ma-admin-action-btn ma-admin-action-delete">Delete</button>
                                                </form>
                                            <?php endif; ?>
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
        <div class="ma-admin-card" id="adminForm" style="display: <?php echo $editingAdmin ? 'block' : 'none'; ?>;">
            <div class="ma-admin-card-header">
                <h3><?php echo $editingAdmin ? 'Edit Admin' : 'Add New Admin'; ?></h3>
                <button class="ma-admin-close-btn" onclick="hideForm()">×</button>
            </div>
            <div class="ma-admin-card-body">
                <form method="POST" class="ma-admin-form">
                    <input type="hidden" name="action" value="<?php echo $editingAdmin ? 'edit' : 'add'; ?>">
                    <?php if ($editingAdmin): ?>
                        <input type="hidden" name="admin_id" value="<?php echo $editingAdmin['id']; ?>">
                    <?php endif; ?>
                    
                    <div class="ma-admin-form-row">
                        <div class="ma-admin-form-group">
                            <label for="username">Username *</label>
                            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($editingAdmin['username'] ?? ''); ?>" required <?php echo $editingAdmin ? 'readonly' : ''; ?>>
                            <?php if ($editingAdmin): ?>
                                <small style="color: #888;">Username cannot be changed</small>
                            <?php endif; ?>
                        </div>
                        
                        <div class="ma-admin-form-group">
                            <label for="full_name">Full Name *</label>
                            <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($editingAdmin['full_name'] ?? ''); ?>" required>
                        </div>
                    </div>
                    
                    <div class="ma-admin-form-group">
                        <label for="email">Email Address *</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($editingAdmin['email'] ?? ''); ?>" required>
                    </div>
                    
                    <div class="ma-admin-form-group">
                        <label for="password">Password <?php echo $editingAdmin ? '(leave blank to keep current)' : '*'; ?></label>
                        <input type="password" id="password" name="password" <?php echo $editingAdmin ? '' : 'required'; ?> minlength="6">
                    </div>
                    
                    <div class="ma-admin-form-group">
                        <label class="ma-admin-checkbox">
                            <input type="checkbox" name="is_active" <?php echo ($editingAdmin['is_active'] ?? 0) ? 'checked' : ''; ?>>
                            <span>Active (can login to admin panel)</span>
                        </label>
                    </div>
                    
                    <div class="ma-admin-form-actions">
                        <button type="button" class="ma-admin-btn ma-admin-btn-secondary" onclick="hideForm()">Cancel</button>
                        <button type="submit" class="ma-admin-btn"><?php echo $editingAdmin ? 'Update Admin' : 'Add Admin'; ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .ma-admin-admins {
        display: flex;
        flex-direction: column;
        gap: 24px;
    }
    .ma-admin-current-info {
        background: linear-gradient(135deg, #0b2b2b 0%, #0d3333 100%);
        border-radius: 12px;
        padding: 24px;
        color: #fff;
    }
    .ma-admin-current-info-content {
        display: flex;
        align-items: center;
        gap: 16px;
    }
    .ma-admin-current-info-icon {
        font-size: 32px;
        width: 56px;
        height: 56px;
        background: rgba(201,162,39,0.2);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .ma-admin-current-info-details {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }
    .ma-admin-current-info-label {
        font-size: 12px;
        color: rgba(255,255,255,0.7);
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    .ma-admin-current-info-name {
        font-size: 18px;
        font-weight: 700;
        color: #fff;
    }
    .ma-admin-current-info-username {
        font-size: 13px;
        color: #c9a227;
    }
    .ma-admin-admins-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .ma-admin-admins-header h2 {
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
    .ma-admin-admins-content {
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
    .ma-admin-badge-info {
        background: #e3f2fd;
        color: #1565c0;
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
    .ma-admin-form-group input {
        padding: 12px 14px;
        border: 1.5px solid #ddd;
        border-radius: 8px;
        font-size: 14px;
        transition: border-color 0.2s;
    }
    .ma-admin-form-group input:focus {
        outline: none;
        border-color: #0b2b2b;
    }
    .ma-admin-form-group input[readonly] {
        background: #f5f5f5;
        cursor: not-allowed;
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
        .ma-admin-admins-header {
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
    document.getElementById('adminForm').style.display = 'block';
    document.querySelector('#adminForm h3').textContent = 'Add New Admin';
    document.querySelector('#adminForm input[name="action"]').value = 'add';
    document.querySelector('#adminForm form').reset();
    window.location.hash = 'form';
}

function hideForm() {
    document.getElementById('adminForm').style.display = 'none';
    window.location.hash = '';
}

// Show form if editing
<?php if ($editingAdmin): ?>
    document.getElementById('adminForm').style.display = 'block';
<?php endif; ?>

// Show form if hash is #form
if (window.location.hash === '#form') {
    document.getElementById('adminForm').style.display = 'block';
}
</script>

<?php require_once 'footer.php'; ?>
