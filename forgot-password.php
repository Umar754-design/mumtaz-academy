<?php
require_once 'config.php';
require_once 'auth.php';

// Get database connection
$pdo = getDB();

$error = '';
$success = '';

// Generate CSRF token
$csrfToken = generateCSRFToken();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $csrfTokenInput = $_POST['csrf_token'] ?? '';
    
    if (!verifyCSRFToken($csrfTokenInput)) {
        $error = 'Invalid request. Please try again.';
    } elseif (empty($email)) {
        $error = 'Please enter your email address.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        // Check if user exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user) {
            // Generate reset token
            $token = bin2hex(random_bytes(32));
            $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            // Delete any existing tokens for this user
            $stmt = $pdo->prepare("DELETE FROM password_reset_tokens WHERE user_id = ?");
            $stmt->execute([$user['id']]);
            
            // Insert new token
            $stmt = $pdo->prepare("INSERT INTO password_reset_tokens (user_id, token, expires_at) VALUES (?, ?, ?)");
            $stmt->execute([$user['id'], $token, $expiresAt]);
            
            // In production, send email with reset link
            // For now, show the link (for development)
            $resetLink = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/reset-password.php?token=" . $token;
            $success = "Password reset link has been sent to your email. (Development: $resetLink)";
        } else {
            // Don't reveal if email exists or not for security
            $success = "If an account exists with this email, a password reset link has been sent.";
        }
    }
}
?>
<?php include 'header.php'; ?>
      <section class="ma-page-banner">
        <h1>Forgot Password</h1>
        <p>Reset your password to regain access</p>
      </section>

      <section class="ma-auth-section">
        <div class="ma-auth-card">
          <div class="ma-auth-header">
            <h2>Reset Your Password</h2>
            <p>Enter your email address and we'll send you a link to reset your password.</p>
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

          <form method="POST" class="ma-auth-form">
            <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
            
            <div class="ma-form-group">
              <label for="email">Email Address</label>
              <input type="email" id="email" name="email" required placeholder="Enter your email">
            </div>

            <button type="submit" class="ma-btn-gold ma-btn-full">Send Reset Link</button>
          </form>

          <div class="ma-auth-footer">
            <p>Remember your password? <a href="login.php">Login here</a></p>
            <p>Don't have an account? <a href="register.php">Register here</a></p>
          </div>
        </div>
      </section>
<?php include 'footer.php'; ?>
