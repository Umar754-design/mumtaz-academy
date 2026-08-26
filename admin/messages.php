<?php
$pageTitle = 'Contact Messages Management';
require_once 'header.php';

// Set PHP timezone to Asia/Kolkata
date_default_timezone_set('Asia/Kolkata');

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

// Mark as read/unread
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && empty($message)) {
    if ($_POST['action'] === 'toggle_read') {
        $messageId = (int)$_POST['message_id'];
        try {
            $stmt = $pdo->prepare("UPDATE contact_messages SET is_read = NOT is_read WHERE id = ?");
            $stmt->execute([$messageId]);
            $message = 'Message status updated successfully!';
            $messageType = 'success';
        } catch (PDOException $e) {
            $message = 'Error updating message status: ' . $e->getMessage();
            $messageType = 'error';
        }
    }
    
    // Delete message
    if ($_POST['action'] === 'delete') {
        $messageId = (int)$_POST['message_id'];
        try {
            $stmt = $pdo->prepare("DELETE FROM contact_messages WHERE id = ?");
            $stmt->execute([$messageId]);
            $message = 'Message deleted successfully!';
            $messageType = 'success';
        } catch (PDOException $e) {
            $message = 'Error deleting message: ' . $e->getMessage();
            $messageType = 'error';
        }
    }
    
    // Mark all as read
    if ($_POST['action'] === 'mark_all_read') {
        try {
            $stmt = $pdo->prepare("UPDATE contact_messages SET is_read = 1 WHERE is_read = 0");
            $stmt->execute();
            $message = 'All messages marked as read!';
            $messageType = 'success';
        } catch (PDOException $e) {
            $message = 'Error marking messages as read: ' . $e->getMessage();
            $messageType = 'error';
        }
    }
}

// Fetch all contact messages
try {
    $stmt = $pdo->query("
        SELECT * FROM contact_messages 
        ORDER BY is_read ASC, created_at DESC
    ");
    $messages = $stmt->fetchAll();
} catch (PDOException $e) {
    $messages = [];
    $message = 'Error fetching messages: ' . $e->getMessage();
    $messageType = 'error';
}

// Count unread messages
try {
    $stmt = $pdo->query("SELECT COUNT(*) as unread_count FROM contact_messages WHERE is_read = 0");
    $unreadCount = $stmt->fetch()['unread_count'];
} catch (PDOException $e) {
    $unreadCount = 0;
}

// Handle view message
$viewingMessage = null;
if (isset($_GET['view'])) {
    $viewId = (int)$_GET['view'];
    $stmt = $pdo->prepare("SELECT * FROM contact_messages WHERE id = ?");
    $stmt->execute([$viewId]);
    $viewingMessage = $stmt->fetch();
    
    if ($viewingMessage && !$viewingMessage['is_read']) {
        // Mark as read when viewing
        $stmt = $pdo->prepare("UPDATE contact_messages SET is_read = 1 WHERE id = ?");
        $stmt->execute([$viewId]);
    }
}
?>

<div class="ma-admin-messages">
    <?php if ($message): ?>
        <div class="ma-admin-alert ma-admin-alert-<?php echo $messageType; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>
    
    <div class="ma-admin-messages-header">
        <h2>Contact Messages</h2>
        <div style="display: flex; gap: 12px; align-items: center;">
            <span style="background: <?php echo $unreadCount > 0 ? '#ffebee' : '#e8f5e9'; ?>; color: <?php echo $unreadCount > 0 ? '#c62828' : '#1a5c3e'; ?>; padding: 6px 12px; border-radius: 20px; font-size: 13px; font-weight: 600;">
                <?php echo $unreadCount; ?> unread
            </span>
            <?php if ($unreadCount > 0): ?>
                <form method="POST" style="display: inline;">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                    <input type="hidden" name="action" value="mark_all_read">
                    <button type="submit" class="ma-admin-btn ma-admin-btn-secondary" style="padding: 8px 16px; font-size: 13px;">Mark All Read</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
    
    <?php if ($viewingMessage): ?>
        <!-- View Message Modal -->
        <div class="ma-admin-modal" style="display: block;">
            <div class="ma-admin-modal-content">
                <div class="ma-admin-modal-header">
                    <h3>Message Details</h3>
                    <button class="ma-admin-close-btn" onclick="closeView()">×</button>
                </div>
                <div class="ma-admin-modal-body">
                    <div style="margin-bottom: 20px;">
                        <div style="font-size: 12px; color: #999; margin-bottom: 4px;">From</div>
                        <div style="font-size: 16px; font-weight: 600; color: #333;">
                            <?php echo htmlspecialchars($viewingMessage['name']); ?>
                            <span style="font-weight: 400; color: #666; margin-left: 8px;">&lt;<?php echo htmlspecialchars($viewingMessage['email']); ?>&gt;</span>
                        </div>
                    </div>
                    
                    <div style="margin-bottom: 20px;">
                        <div style="font-size: 12px; color: #999; margin-bottom: 4px;">Subject</div>
                        <div style="font-size: 15px; font-weight: 600; color: #333;">
                            <?php echo htmlspecialchars($viewingMessage['subject']); ?>
                        </div>
                    </div>
                    
                    <div style="margin-bottom: 20px;">
                        <div style="font-size: 12px; color: #999; margin-bottom: 4px;">Message</div>
                        <div style="background: #f5f5f5; padding: 16px; border-radius: 8px; line-height: 1.6; color: #333;">
                            <?php echo nl2br(htmlspecialchars($viewingMessage['message'])); ?>
                        </div>
                    </div>
                    
                    <div style="display: flex; gap: 20px; font-size: 12px; color: #999; margin-bottom: 24px;">
                        <div>IP: <?php echo htmlspecialchars($viewingMessage['ip_address'] ?: 'Unknown'); ?></div>
                        <div>Date: <?php echo date('M d, Y g:i A', strtotime($viewingMessage['created_at'])); ?></div>
                    </div>
                    
                    <div style="display: flex; gap: 12px;">
                        <a href="mailto:<?php echo htmlspecialchars($viewingMessage['email']); ?>?subject=Re: <?php echo htmlspecialchars($viewingMessage['subject']); ?>" class="ma-admin-btn" style="text-decoration: none; display: inline-block;">Reply via Email</a>
                        <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this message?');">
                            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="message_id" value="<?php echo $viewingMessage['id']; ?>">
                            <button type="submit" class="ma-admin-btn ma-admin-btn-secondary" style="background: #ffebee; color: #c62828;">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
    
    <div class="ma-admin-messages-content">
        <div class="ma-admin-card">
            <div class="ma-admin-card-body">
                <?php if (empty($messages)): ?>
                    <p class="ma-admin-empty">No contact messages found.</p>
                <?php else: ?>
                    <table class="ma-admin-table">
                        <thead>
                            <tr>
                                <th>Status</th>
                                <th>From</th>
                                <th>Subject</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($messages as $msg): ?>
                                <tr class="<?php echo $msg['is_read'] ? '' : 'ma-admin-unread-row'; ?>">
                                    <td>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                            <input type="hidden" name="action" value="toggle_read">
                                            <input type="hidden" name="message_id" value="<?php echo $msg['id']; ?>">
                                            <button type="submit" class="ma-admin-badge ma-admin-badge-<?php echo $msg['is_read'] ? 'secondary' : 'success'; ?>">
                                                <?php echo $msg['is_read'] ? 'Read' : 'New'; ?>
                                            </button>
                                        </form>
                                    </td>
                                    <td>
                                        <div style="font-weight: 600; color: #333;"><?php echo htmlspecialchars($msg['name']); ?></div>
                                        <div style="font-size: 12px; color: #999;"><?php echo htmlspecialchars($msg['email']); ?></div>
                                    </td>
                                    <td>
                                        <div style="max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                            <?php echo htmlspecialchars($msg['subject']); ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div style="font-size: 13px; color: #666;">
                                            <?php echo date('M d, Y', strtotime($msg['created_at'])); ?>
                                        </div>
                                        <div style="font-size: 11px; color: #999;">
                                            <?php echo date('g:i A', strtotime($msg['created_at'])); ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="ma-admin-actions">
                                            <a href="?view=<?php echo $msg['id']; ?>" class="ma-admin-action-btn ma-admin-action-edit">View</a>
                                            <a href="mailto:<?php echo htmlspecialchars($msg['email']); ?>?subject=Re: <?php echo htmlspecialchars($msg['subject']); ?>" class="ma-admin-action-btn" style="background: #e3f2fd; color: #1976d2;">Reply</a>
                                            <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this message?');">
                                                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="message_id" value="<?php echo $msg['id']; ?>">
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

<script>
function closeView() {
    window.location.href = 'messages.php';
}
</script>

<style>
    .ma-admin-messages {
        display: flex;
        flex-direction: column;
        gap: 24px;
    }
    .ma-admin-messages-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .ma-admin-messages-header h2 {
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
    .ma-admin-unread-row {
        background: #f8f9ff;
    }
    .ma-admin-unread-row td {
        font-weight: 600;
    }
    .ma-admin-empty {
        text-align: center;
        padding: 40px;
        color: #888;
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
