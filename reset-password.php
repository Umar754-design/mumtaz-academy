<?php
require_once 'config.php';
require_once 'auth.php';

// Get database connection
$pdo = getDB();

$error = '';
$success = '';

// Generate CSRF token
$csrfToken = generateCSRFToken();

$token = $_GET['token'] ?? '';

if (empty($token)) {
    header('Location: forgot-password.php');
    exit;
}

// Validate token
$stmt = $pdo->prepare("
    SELECT prt.*, u.id as user_id
    FROM password_reset_tokens prt
    JOIN users u ON prt.user_id = u.id
    WHERE prt.token = ? AND prt.expires_at > NOW() AND prt.used_at IS NULL
");
$stmt->execute([$token]);
$resetToken = $stmt->fetch();

if (!$resetToken) {
    $error = 'Invalid or expired reset token. Please request a new password reset.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $resetToken) {
    $password = trim($_POST['password'] ?? '');
    $confirmPassword = trim($_POST['confirm_password'] ?? '');
    $csrfTokenInput = $_POST['csrf_token'] ?? '';
    
    if (!verifyCSRFToken($csrfTokenInput)) {
        $error = 'Invalid request. Please try again.';
    } elseif (empty($password)) {
        $error = 'Please enter a new password.';
    } elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters long.';
    } elseif ($password !== $confirmPassword) {
        $error = 'Passwords do not match.';
    } else {
        // Update password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password = ?, updated_at = NOW() WHERE id = ?");
        $stmt->execute([$hashedPassword, $resetToken['user_id']]);
        
        // Mark token as used
        $stmt = $pdo->prepare("UPDATE password_reset_tokens SET used_at = NOW() WHERE token = ?");
        $stmt->execute([$token]);
        
        $success = 'Password has been reset successfully. You can now login with your new password.';
        
        // Redirect to login after 3 seconds
        header("refresh:3;url=login.php");
    }
}
?>
<?php include 'header.php'; ?>
      <section class="ma-page-banner">
        <h1>Reset Password</h1>
        <p>Create a new password for your account</p>
      </section>

      <section class="ma-auth-section">
        <div class="ma-auth-card">
          <div class="ma-auth-header">
            <h2>Set New Password</h2>
            <p>Please enter your new password below.</p>
          </div>

          <?php if ($error): ?>
            <div class="ma-alert ma-alert-error">
              <span class="ma-alert-icon">⚠</span>
              <span><?php echo htmlspecialchars($error); ?></span>
            </div>
          <?php endif; ?>

          <?php if ($success): ?>
            <div class="ma-alert ma-alert-success">
              <span class="ma-alert-icon">✓</span>
              <span><?php echo htmlspecialchars($success); ?></span>
            </div>
          <?php endif; ?>

          <?php if (!$success && $resetToken): ?>
            <form method="POST" class="ma-auth-form">
              <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
              
              <div class="ma-form-group">
                <label for="password">New Password</label>
                <input type="password" id="password" name="password" required placeholder="Enter new password (min 8 characters)" minlength="8">
              </div>

              <div class="ma-form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required placeholder="Confirm new password" minlength="8">
              </div>

              <button type="submit" class="ma-btn-gold ma-btn-full">Reset Password</button>
            </form>
          <?php endif; ?>

          <div class="ma-auth-footer">
            <p>Remember your password? <a href="login.php">Login here</a></p>
          </div>
        </div>
      </section>
<?php include 'footer.php'; ?>
