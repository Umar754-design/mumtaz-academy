<?php
require_once 'config.php';
require_once 'auth.php';

// Check if user is logged in
$user = getCurrentUser();
$csrfToken = generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1" />
    <title>Mumtaz Academy</title>
    <meta name="description" content="Mumtaz Academy — Online Islamic Learning Platform with Free Courses" />
    <meta name="robots" content="index, follow" />
    <meta property="og:title" content="Mumtaz Academy" />
    <meta property="og:description" content="Mumtaz Academy — Online Islamic Learning Platform with Free Courses" />
    <meta property="og:type" content="website" />
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="Mumtaz Academy" />
    <meta name="twitter:description" content="Mumtaz Academy — Online Islamic Learning Platform with Free Courses" />
    <link rel="icon" type="image/svg+xml" href="public/favicon.svg" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Amiri:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css?v=20260723">
  </head>
  <body>
    <nav class="ma-nav">
      <div class="ma-nav-inner">
        <a href="index.php" class="ma-logo" aria-label="Mumtaz Academy Home">
          <div class="ma-logo-icon">
            <svg aria-hidden="true" width="36" height="36" viewBox="0 0 36 36" fill="none">
              <path d="M18 2 L34 12 L34 30 L18 34 L2 30 L2 12 Z" fill="#c9a227" opacity="0.2"/>
              <path d="M18 4 L32 13 L32 29 L18 32 L4 29 L4 13 Z" stroke="#c9a227" stroke-width="1.5" fill="none"/>
              <rect x="14" y="16" width="8" height="12" rx="4" fill="#c9a227"/>
              <path d="M10 16 Q18 8 26 16" fill="none" stroke="#c9a227" stroke-width="1.5"/>
              <path d="M6 18 Q18 6 30 18" fill="none" stroke="#c9a227" stroke-width="1" opacity="0.6"/>
              <circle cx="18" cy="4" r="1.5" fill="#c9a227"/>
            </svg>
          </div>
          <div class="ma-logo-text">
            <span class="ma-logo-title">MUMTAZ</span>
            <span class="ma-logo-sub">ACADEMY</span>
          </div>
        </a>

        <ul class="ma-nav-links">
          <li><a href="index.php" class="active">Home</a></li>
          <li><a href="courses.php">Courses</a></li>
          <li><a href="live-classes.php">Live Classes</a></li>
          <li><a href="about.php">About Us</a></li>
          <li><a href="teachers.php">Teachers</a></li>
          <li><a href="blog.php">Blog</a></li>
          <?php if ($user): ?>
            <li><a href="dashboard.php">Dashboard</a></li>
          <?php endif; ?>
          <li><a href="contact.php">Contact</a></li>
        </ul>

        <div class="ma-nav-actions">
          <?php if ($user): ?>
            <span style="margin-right: 12px; font-size: 14px; color: #666;">Welcome, <?php echo htmlspecialchars($user['name']); ?></span>
            <a href="logout.php" class="ma-btn-outline">Logout</a>
          <?php else: ?>
            <a href="login.php" class="ma-btn-outline">Login</a>
            <a href="register.php" class="ma-btn-gold">Register</a>
          <?php endif; ?>
          <button class="ma-hamburger" aria-label="Open menu" aria-expanded="false" id="hamburgerBtn">
            <span></span><span></span><span></span>
          </button>
        </div>
      </div>

      <div class="ma-mobile-menu" id="mobileMenu" role="navigation" aria-label="Mobile navigation" style="display: none;">
        <ul>
          <li><a href="index.php" class="active">Home</a></li>
          <li><a href="courses.php">Courses</a></li>
          <li><a href="live-classes.php">Live Classes</a></li>
          <li><a href="about.php">About Us</a></li>
          <li><a href="teachers.php">Teachers</a></li>
          <li><a href="blog.php">Blog</a></li>
          <?php if ($user): ?>
            <li><a href="dashboard.php">Dashboard</a></li>
          <?php endif; ?>
          <li><a href="contact.php">Contact</a></li>
        </ul>
        <div class="ma-mobile-actions">
          <?php if ($user): ?>
            <span style="margin-bottom: 12px; font-size: 14px; color: #666;">Welcome, <?php echo htmlspecialchars($user['name']); ?></span>
            <a href="logout.php" class="ma-btn-outline">Logout</a>
          <?php else: ?>
            <a href="login.php" class="ma-btn-outline">Login</a>
            <a href="register.php" class="ma-btn-gold">Register</a>
          <?php endif; ?>
        </div>
      </div>
    </nav>

    <main>
