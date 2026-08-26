<?php
$pageTitle = 'Users Management';
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

// Delete user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete' && empty($message)) {
    $userId = (int)$_POST['user_id'];
    try {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $message = 'User deleted successfully!';
        $messageType = 'success';
    } catch (PDOException $e) {
        $message = 'Cannot delete user. They may have enrollments or related data.';
        $messageType = 'error';
    }
}

// Toggle user active status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'toggle_status' && empty($message)) {
    $userId = (int)$_POST['user_id'];
    $isActive = (int)$_POST['is_active'];
    try {
        $newStatus = $isActive ? 0 : 1;
        $stmt = $pdo->prepare("UPDATE users SET is_active = ? WHERE id = ?");
        $stmt->execute([$newStatus, $userId]);
        $message = 'User status updated successfully!';
        $messageType = 'success';
    } catch (PDOException $e) {
        $message = 'Error updating user status: ' . $e->getMessage();
        $messageType = 'error';
    }
}

// Get filter and search
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$statusFilter = isset($_GET['status']) ? $_GET['status'] : '';

// Fetch users with filters
try {
    $sql = "SELECT u.*, 
                   (SELECT COUNT(*) FROM enrollments e WHERE e.user_id = u.id) as enrollment_count,
                   (SELECT COUNT(*) FROM mcq_attempts ma WHERE ma.user_id = u.id) as test_attempts
            FROM users u 
            WHERE 1=1";
    $params = [];
    
    if ($search) {
        $sql .= " AND (u.full_name LIKE ? OR u.email LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }
    
    if ($statusFilter !== '') {
        $sql .= " AND u.is_active = ?";
        $params[] = (int)$statusFilter;
    }
    
    $sql .= " ORDER BY u.created_at DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $users = $stmt->fetchAll();
    
    // Get user statistics
    $totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    $activeUsers = $pdo->query("SELECT COUNT(*) FROM users WHERE is_active = 1")->fetchColumn();
    $inactiveUsers = $pdo->query("SELECT COUNT(*) FROM users WHERE is_active = 0")->fetchColumn();
    
} catch (PDOException $e) {
    $users = [];
    $totalUsers = 0;
    $activeUsers = 0;
    $inactiveUsers = 0;
    $message = 'Error fetching users: ' . $e->getMessage();
    $messageType = 'error';
}
?>

<div class="ma-admin-users">
    <?php if ($message): ?>
        <div class="ma-admin-alert ma-admin-alert-<?php echo $messageType; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>
    
    <div class="ma-admin-users-header">
        <h2>Users Management</h2>
        <div class="ma-admin-users-stats">
            <div class="ma-admin-stat-pill">
                <span class="ma-admin-stat-label">Total</span>
                <span class="ma-admin-stat-value"><?php echo number_format($totalUsers); ?></span>
            </div>
            <div class="ma-admin-stat-pill ma-admin-stat-pill-success">
                <span class="ma-admin-stat-label">Active</span>
                <span class="ma-admin-stat-value"><?php echo number_format($activeUsers); ?></span>
            </div>
            <div class="ma-admin-stat-pill ma-admin-stat-pill-warning">
                <span class="ma-admin-stat-label">Inactive</span>
                <span class="ma-admin-stat-value"><?php echo number_format($inactiveUsers); ?></span>
            </div>
        </div>
    </div>
    
    <div class="ma-admin-users-content">
        <!-- Filters -->
        <div class="ma-admin-card">
            <div class="ma-admin-card-body">
                <form method="GET" class="ma-admin-filter-form">
                    <div class="ma-admin-filter-row">
                        <div class="ma-admin-filter-group">
                            <label for="search">Search Users</label>
                            <input type="text" id="search" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search by name or email...">
                        </div>
                        
                        <div class="ma-admin-filter-group">
                            <label for="status">Status</label>
                            <select id="status" name="status">
                                <option value="">All Status</option>
                                <option value="1" <?php echo $statusFilter === '1' ? 'selected' : ''; ?>>Active</option>
                                <option value="0" <?php echo $statusFilter === '0' ? 'selected' : ''; ?>>Inactive</option>
                            </select>
                        </div>
                        
                        <div class="ma-admin-filter-actions">
                            <button type="submit" class="ma-admin-btn">Filter</button>
                            <a href="users.php" class="ma-admin-btn ma-admin-btn-secondary">Clear</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Users Table -->
        <div class="ma-admin-card">
            <div class="ma-admin-card-body">
                <?php if (empty($users)): ?>
                    <p class="ma-admin-empty">No users found.</p>
                <?php else: ?>
                    <table class="ma-admin-table">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Enrollments</th>
                                <th>Tests</th>
                                <th>Status</th>
                                <th>Joined</th>
                                <th>Last Login</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo htmlspecialchars($user['full_name']); ?></strong>
                                    </td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td><?php echo $user['phone'] ? htmlspecialchars($user['phone']) : '-'; ?></td>
                                    <td><?php echo number_format($user['enrollment_count']); ?></td>
                                    <td><?php echo number_format($user['test_attempts']); ?></td>
                                    <td>
                                        <span class="ma-admin-badge ma-admin-badge-<?php echo $user['is_active'] ? 'success' : 'warning'; ?>">
                                            <?php echo $user['is_active'] ? 'Active' : 'Inactive'; ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                                    <td><?php echo $user['last_login'] ? date('M d, Y', strtotime($user['last_login'])) : 'Never'; ?></td>
                                    <td>
                                        <div class="ma-admin-actions">
                                            <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to <?php echo $user['is_active'] ? 'deactivate' : 'activate'; ?> this user?');">
                                                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                                <input type="hidden" name="action" value="toggle_status">
                                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                <input type="hidden" name="is_active" value="<?php echo $user['is_active']; ?>">
                                                <button type="submit" class="ma-admin-action-btn ma-admin-action-<?php echo $user['is_active'] ? 'deactivate' : 'activate'; ?>">
                                                    <?php echo $user['is_active'] ? 'Deactivate' : 'Activate'; ?>
                                                </button>
                                            </form>
                                            <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this user? This action cannot be undone.');">
                                                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
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
</div>

<style>
    .ma-admin-users {
        display: flex;
        flex-direction: column;
        gap: 24px;
    }
    .ma-admin-users-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 16px;
    }
    .ma-admin-users-header h2 {
        font-size: 24px;
        font-weight: 700;
        color: #111;
        margin: 0;
    }
    .ma-admin-users-stats {
        display: flex;
        gap: 12px;
    }
    .ma-admin-stat-pill {
        background: #fff;
        padding: 12px 20px;
        border-radius: 8px;
        border: 1px solid #eee;
        display: flex;
        flex-direction: column;
        align-items: center;
        min-width: 80px;
    }
    .ma-admin-stat-pill-success {
        border-color: #c8e6c9;
        background: #e8f5e9;
    }
    .ma-admin-stat-pill-warning {
        border-color: #ffe0b2;
        background: #fff8e1;
    }
    .ma-admin-stat-label {
        font-size: 11px;
        color: #666;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .ma-admin-stat-value {
        font-size: 20px;
        font-weight: 800;
        color: #111;
    }
    .ma-admin-users-content {
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
    .ma-admin-card-body {
        padding: 24px;
    }
    .ma-admin-filter-form {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }
    .ma-admin-filter-row {
        display: flex;
        gap: 16px;
        align-items: flex-end;
        flex-wrap: wrap;
    }
    .ma-admin-filter-group {
        flex: 1;
        min-width: 200px;
    }
    .ma-admin-filter-group label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: #444;
        margin-bottom: 8px;
    }
    .ma-admin-filter-group input,
    .ma-admin-filter-group select {
        width: 100%;
        padding: 12px 14px;
        border: 1.5px solid #ddd;
        border-radius: 8px;
        font-size: 14px;
        transition: border-color 0.2s;
    }
    .ma-admin-filter-group input:focus,
    .ma-admin-filter-group select:focus {
        outline: none;
        border-color: #0b2b2b;
    }
    .ma-admin-filter-actions {
        display: flex;
        gap: 8px;
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
        text-decoration: none;
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
    .ma-admin-actions {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }
    .ma-admin-action-btn {
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        border: none;
        transition: all 0.2s;
    }
    .ma-admin-action-activate {
        background: #e8f5e9;
        color: #1a5c3e;
    }
    .ma-admin-action-activate:hover {
        background: #c8e6c9;
    }
    .ma-admin-action-deactivate {
        background: #fff8e1;
        color: #b7791f;
    }
    .ma-admin-action-deactivate:hover {
        background: #ffe0b2;
    }
    .ma-admin-action-delete {
        background: #ffebee;
        color: #c62828;
    }
    .ma-admin-action-delete:hover {
        background: #ffcdd2;
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
        .ma-admin-users-header {
            flex-direction: column;
            align-items: flex-start;
        }
        .ma-admin-filter-row {
            flex-direction: column;
            align-items: stretch;
        }
        .ma-admin-filter-group {
            min-width: auto;
        }
        .ma-admin-filter-actions {
            flex-direction: column;
        }
        .ma-admin-actions {
            flex-direction: column;
        }
        .ma-admin-table {
            font-size: 12px;
        }
        .ma-admin-table th,
        .ma-admin-table td {
            padding: 10px 8px;
        }
    }
</style>

<?php require_once 'footer.php'; ?>
