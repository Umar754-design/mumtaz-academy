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
    $loginType = $_POST['login_type'] ?? 'student';
    $email = sanitizeInput($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $rememberMe = isset($_POST['remember']);
    
    // Verify CSRF token
    $csrfToken = $_POST['csrf_token'] ?? '';
    if (!verifyCSRFToken($csrfToken)) {
        $error = 'Invalid request. Please try again.';
    } else {
        // Handle different login types
        if ($loginType === 'admin') {
            // Redirect to admin login
            header('Location: admin/login.php');
            exit;
        } elseif ($loginType === 'teacher') {
            // Teacher login - teachers are managed by admins through the admin panel
            $error = 'Teacher login is not available. Please contact the academy administrator for teacher account access.';
        } else {
            // Student login (default)
            $result = auth()->login($email, $password, $rememberMe);
            
            if ($result['success']) {
                setFlashMessage('success', 'Welcome back, ' . $result['user']['name'] . '!');
                redirect('dashboard.php');
            } else {
                $error = $result['message'];
            }
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
    <title>Login - Mumtaz Academy</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Amiri:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css?v=20260723">
    <style>
      .ma-auth-page {
        min-height: 100vh;
        display: flex;
        background: linear-gradient(135deg, #0b2b2b 0%, #0d3333 50%, #0b2b2b 100%);
      }
      .ma-auth-left {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 40px;
        position: relative;
        overflow: hidden;
      }
      .ma-auth-left-inner {
        max-width: 480px;
        width: 100%;
        z-index: 2;
      }
      .ma-auth-logo {
        display: flex;
        align-items: center;
        gap: 12px;
        text-decoration: none;
        margin-bottom: 40px;
      }
      .ma-auth-logo-mobile {
        display: none;
      }
      .ma-auth-quote-block {
        background: rgba(201,162,39,0.1);
        border: 1px solid rgba(201,162,39,0.2);
        border-radius: 16px;
        padding: 32px;
        margin-bottom: 32px;
        text-align: center;
      }
      .ma-auth-features {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
        margin-bottom: 32px;
      }
      .ma-auth-feature-item {
        display: flex;
        align-items: center;
        gap: 10px;
        color: rgba(255,255,255,0.85);
        font-size: 14px;
      }
      .ma-auth-feature-item span:first-child {
        font-size: 20px;
      }
      .ma-auth-arch {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 200px;
        background: linear-gradient(to top, rgba(201,162,39,0.1), transparent);
        pointer-events: none;
      }
      .ma-auth-right {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 40px;
        background: #fff;
      }
      .ma-auth-form-wrap {
        max-width: 420px;
        width: 100%;
      }
      .ma-auth-heading {
        font-size: 32px;
        font-weight: 800;
        color: #0b2b2b;
        margin-bottom: 8px;
        font-family: 'Inter', sans-serif;
      }
      .ma-auth-sub {
        font-size: 15px;
        color: #666;
        margin-bottom: 32px;
      }
      .ma-auth-form {
        display: flex;
        flex-direction: column;
        gap: 20px;
      }
      .ma-form-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
      }
      .ma-form-group label {
        font-size: 13px;
        font-weight: 600;
        color: #444;
      }
      .ma-input-icon-wrap {
        position: relative;
        display: flex;
        align-items: center;
      }
      .ma-input-icon {
        position: absolute;
        left: 14px;
        color: #999;
        pointer-events: none;
      }
      .ma-input-icon-wrap input {
        width: 100%;
        padding: 14px 14px 14px 44px;
        border: 1.5px solid #e0e0e0;
        border-radius: 10px;
        font-size: 14px;
        transition: all 0.2s;
        font-family: 'Inter', sans-serif;
      }
      .ma-input-icon-wrap input:focus {
        outline: none;
        border-color: #c9a227;
        box-shadow: 0 0 0 3px rgba(201,162,39,0.1);
      }
      .ma-auth-forgot {
        font-size: 13px;
        color: #c9a227;
        text-decoration: none;
        font-weight: 500;
      }
      .ma-auth-forgot:hover {
        text-decoration: underline;
      }
      .ma-checkbox-label {
        display: flex;
        align-items: center;
        gap: 10px;
        cursor: pointer;
        font-size: 14px;
        color: #555;
      }
      .ma-checkbox-label input[type="checkbox"] {
        width: 18px;
        height: 18px;
        cursor: pointer;
        accent-color: #c9a227;
      }
      .ma-auth-submit {
        background: linear-gradient(135deg, #c9a227 0%, #b8911f 100%);
        color: #fff;
        border: none;
        padding: 16px;
        border-radius: 10px;
        font-size: 15px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s;
        font-family: 'Inter', sans-serif;
      }
      .ma-auth-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(201,162,39,0.3);
      }
      .ma-auth-divider {
        display: flex;
        align-items: center;
        gap: 16px;
        margin: 24px 0;
        color: #999;
        font-size: 13px;
      }
      .ma-auth-divider::before,
      .ma-auth-divider::after {
        content: '';
        flex: 1;
        height: 1px;
        background: #eee;
      }
      .ma-auth-social-btns {
        display: flex;
        gap: 12px;
      }
      .ma-social-login-btn {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        padding: 12px;
        border: 1.5px solid #e0e0e0;
        border-radius: 10px;
        background: #fff;
        font-size: 14px;
        font-weight: 600;
        color: #333;
        cursor: pointer;
        transition: all 0.2s;
        font-family: 'Inter', sans-serif;
      }
      .ma-social-login-btn:hover {
        background: #f8f8f8;
        border-color: #d0d0d0;
      }
      .ma-auth-switch {
        text-align: center;
        margin-top: 24px;
        font-size: 14px;
        color: #666;
      }
      .ma-auth-link {
        color: #c9a227;
        text-decoration: none;
        font-weight: 600;
      }
      .ma-auth-link:hover {
        text-decoration: underline;
      }
      .ma-login-type-selector {
        display: flex;
        gap: 12px;
        margin-bottom: 24px;
      }
      .ma-login-type-btn {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
        padding: 16px 12px;
        border: 2px solid #e0e0e0;
        border-radius: 12px;
        background: #fff;
        cursor: pointer;
        transition: all 0.2s;
      }
      .ma-login-type-btn:hover {
        border-color: #c9a227;
        background: rgba(201,162,39,0.05);
      }
      .ma-login-type-active {
        border-color: #c9a227;
        background: rgba(201,162,39,0.1);
      }
      .ma-login-type-icon {
        font-size: 24px;
      }
      .ma-login-type-label {
        font-size: 13px;
        font-weight: 600;
        color: #333;
      }
      @media (max-width: 900px) {
        .ma-auth-left {
          display: none;
        }
        .ma-auth-right {
          flex: 1;
        }
        .ma-auth-logo-mobile {
          display: flex;
          margin-bottom: 32px;
        }
        .ma-auth-form-wrap {
          max-width: 100%;
        }
      }
      @media (max-width: 600px) {
        .ma-auth-right {
          padding: 24px;
        }
        .ma-auth-heading {
          font-size: 26px;
        }
        .ma-auth-social-btns {
          flex-direction: column;
        }
        .ma-auth-features {
          grid-template-columns: 1fr;
        }
      }
    </style>
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

          <h1 class="ma-auth-heading">Welcome Back</h1>
          <p class="ma-auth-sub">Select your login type to continue</p>

          <!-- Login Type Selector -->
          <div class="ma-login-type-selector">
            <button type="button" class="ma-login-type-btn ma-login-type-active" data-type="student" onclick="selectLoginType('student')">
              <span class="ma-login-type-icon">🎓</span>
              <span class="ma-login-type-label">Student</span>
            </button>
            <button type="button" class="ma-login-type-btn" data-type="teacher" onclick="selectLoginType('teacher')">
              <span class="ma-login-type-icon">👨‍🏫</span>
              <span class="ma-login-type-label">Teacher</span>
            </button>
            <button type="button" class="ma-login-type-btn" data-type="admin" onclick="selectLoginType('admin')">
              <span class="ma-login-type-icon">🔐</span>
              <span class="ma-login-type-label">Admin</span>
            </button>
          </div>

          <input type="hidden" name="login_type" id="login_type" value="student">

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
              <label for="login-email">Email Address</label>
              <div class="ma-input-icon-wrap">
                <svg aria-hidden="true" class="ma-input-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                <input id="login-email" name="email" type="email" placeholder="you@email.com" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" />
              </div>
            </div>

            <div class="ma-form-group">
              <div style="Display: flex; justify-content: space-between; align-items: center;">
                <label for="login-pass">Password</label>
                <a href="#" class="ma-auth-forgot">Forgot password?</a>
              </div>
              <div class="ma-input-icon-wrap">
                <svg aria-hidden="true" class="ma-input-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                <input id="login-pass" name="password" type="password" placeholder="Enter your password" required />
              </div>
            </div>

            <label class="ma-checkbox-label">
              <input type="checkbox" name="remember" />
              <span class="ma-checkmark"></span>
              Remember me for 30 days
            </label>

            <button type="submit" class="ma-auth-submit">Sign In</button>
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
            Don't have an account? <a href="register.php" class="ma-auth-link">Create a free account →</a>
          </p>
        </div>
      </div>
    </div>

    <script>
      function selectLoginType(type) {
        // Update hidden input
        document.getElementById('login_type').value = type;
        
        // Update button styles
        document.querySelectorAll('.ma-login-type-btn').forEach(btn => {
          btn.classList.remove('ma-login-type-active');
          if (btn.dataset.type === type) {
            btn.classList.add('ma-login-type-active');
          }
        });
        
        // Update heading and subtext based on type
        const heading = document.querySelector('.ma-auth-heading');
        const subtext = document.querySelector('.ma-auth-sub');
        
        switch(type) {
          case 'student':
            heading.textContent = 'Student Login';
            subtext.textContent = 'Sign in to continue your learning journey';
            break;
          case 'teacher':
            heading.textContent = 'Teacher Login';
            subtext.textContent = 'Access your teaching dashboard and manage classes';
            break;
          case 'admin':
            heading.textContent = 'Admin Login';
            subtext.textContent = 'Manage courses, users, and platform settings';
            break;
        }
      }
    </script>
  </body>
</html>
