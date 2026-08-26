<?php
require_once 'config.php';
require_once 'auth.php';

// Redirect if already logged in
if (auth()->check()) {
    redirect('index.php');
}

$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = sanitizeInput($_POST['full_name'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $agreeTerms = isset($_POST['agree_terms']);
    
    // Verify CSRF token
    $csrfToken = $_POST['csrf_token'] ?? '';
    if (!verifyCSRFToken($csrfToken)) {
        $error = 'Invalid request. Please try again.';
    } elseif (!$agreeTerms) {
        $error = 'You must agree to the Terms of Service and Privacy Policy.';
    } else {
        $result = auth()->register($fullName, $email, $password);
        
        if ($result['success']) {
            setFlashMessage('success', 'Registration successful! Please login to continue.');
            redirect('login.php');
        } else {
            $error = $result['message'];
        }
    }
}

$csrfToken = generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1" />
    <title>Register - Mumtaz Academy</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Amiri:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css?v=20260723">
  </head>
  <body>
    <div class="ma-auth-page">
      <div class="ma-auth-left">
        <div class="ma-auth-left-inner">
          <a href="index.php" class="ma-logo ma-auth-logo" aria-label="Mumtaz Academy Home">
            <div class="ma-logo-icon">
              <svg aria-hidden="true" width="44" height="44" viewBox="0 0 36 36" fill="none">
                <path d="M18 2 L34 12 L34 30 L18 34 L2 30 L2 12 Z" fill="#c9a227" opacity="0.2"/>
                <path d="M18 4 L32 13 L32 29 L18 32 L4 29 L4 13 Z" stroke="#c9a227" stroke-width="1.5" fill="none"/>
                <rect x="14" y="16" width="8" height="12" rx="4" fill="#c9a227"/>
                <path d="M10 16 Q18 8 26 16" fill="none" stroke="#c9a227" stroke-width="1.5"/>
                <path d="M6 18 Q18 6 30 18" fill="none" stroke="#c9a227" stroke-width="1" opacity="0.6"/>
                <circle cx="18" cy="4" r="1.5" fill="#c9a227"/>
              </svg>
            </div>
            <div class="ma-logo-text">
              <span class="ma-logo-title" style="font-size: 18px;">MUMTAZ</span>
              <span class="ma-logo-sub" style="font-size: 11px;">ACADEMY</span>
            </div>
          </a>

          <div class="ma-auth-quote-block">
            <p class="ma-arabic" style="font-size: 32px; margin-bottom: 10px;">
              وَقُل رَّبِّ زِدۡنِي عِلۡمًا
            </p>
            <p style="color: rgba(255,255,255,0.65); font-size: 13px; font-style: italic; margin-bottom: 4px;">
              "My Lord, increase me in knowledge."
            </p>
            <p style="color: rgba(255,255,255,0.35); font-size: 12px;">(Surah Taha : 114)</p>
          </div>

          <div class="ma-auth-features">
            <div class="ma-auth-feature-item"><span>📖</span><span>50+ Free Courses</span></div>
            <div class="ma-auth-feature-item"><span>🎓</span><span>Verified Certificates</span></div>
            <div class="ma-auth-feature-item"><span>📡</span><span>100+ Live Classes</span></div>
            <div class="ma-auth-feature-item"><span>👨‍🏫</span><span>Expert Scholars</span></div>
          </div>

          <div class="ma-auth-arch" aria-hidden="true"></div>
        </div>
      </div>

      <div class="ma-auth-right">
        <div class="ma-auth-form-wrap">
          <a href="index.php" class="ma-logo ma-auth-logo-mobile" aria-label="Mumtaz Academy Home">
            <div class="ma-logo-icon">
              <svg aria-hidden="true" width="32" height="32" viewBox="0 0 36 36" fill="none">
                <path d="M18 4 L32 13 L32 29 L18 32 L4 29 L4 13 Z" stroke="#c9a227" stroke-width="1.5" fill="none"/>
                <rect x="14" y="16" width="8" height="12" rx="4" fill="#c9a227"/>
                <path d="M10 16 Q18 8 26 16" fill="none" stroke="#c9a227" stroke-width="1.5"/>
              </svg>
            </div>
            <div class="ma-logo-text">
              <span class="ma-logo-title">MUMTAZ</span>
              <span class="ma-logo-sub">ACADEMY</span>
            </div>
          </a>

          <h1 class="ma-auth-heading">Create Account</h1>
          <p class="ma-auth-sub">Join Mumtaz Academy and start learning for free</p>

          <?php if ($error): ?>
          <div style="background: #fee; border: 1px solid #fcc; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; color: #c33; font-size: 14px;">
            <?php echo htmlspecialchars($error); ?>
          </div>
          <?php endif; ?>

          <?php if ($success): ?>
          <div style="background: #efe; border: 1px solid #cfc; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; color: #3c3; font-size: 14px;">
            <?php echo htmlspecialchars($success); ?>
          </div>
          <?php endif; ?>

          <form class="ma-auth-form" method="POST" action="">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
            
            <div class="ma-form-group">
              <label for="reg-name">Full Name</label>
              <input id="reg-name" name="full_name" type="text" placeholder="e.g. Ahmad Ali" required value="<?php echo htmlspecialchars($_POST['full_name'] ?? ''); ?>" />
            </div>

            <div class="ma-form-group">
              <label for="reg-email">Email Address</label>
              <input id="reg-email" name="email" type="email" placeholder="you@email.com" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" />
            </div>

            <div class="ma-form-group">
              <label for="reg-pass">Password</label>
              <input id="reg-pass" name="password" type="password" placeholder="Create a strong password (min 8 characters)" required />
            </div>

            <label class="ma-checkbox-label">
              <input type="checkbox" name="agree_terms" required <?php echo isset($_POST['agree_terms']) ? 'checked' : ''; ?> />
              <span class="ma-checkmark"></span>
              I agree to the <a href="privacy-policy.php" style="color: #c9a227;">Terms of Service</a> and <a href="privacy-policy.php" style="color: #c9a227;">Privacy Policy</a>
            </label>

            <button type="submit" class="ma-auth-submit">Create Account</button>
          </form>

          <div class="ma-auth-divider"><span>or continue with</span></div>

          <div class="ma-auth-social-btns">
            <button class="ma-social-login-btn" type="button">
              <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
              Google
            </button>
            <button class="ma-social-login-btn" type="button">
              <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="#1877F2"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
              Facebook
            </button>
          </div>

          <p class="ma-auth-switch">
            Already have an account? <a href="login.php" class="ma-auth-link">Sign in →</a>
          </p>
        </div>
      </div>
    </div>
  </body>
</html>
