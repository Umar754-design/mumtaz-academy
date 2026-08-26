<?php
require_once 'config.php';

requireAdminLogin();

$admin = getCurrentAdmin();
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) . ' - ' : ''; ?>Admin Panel - Mumtaz Academy</title>
    <link rel="stylesheet" href="../styles.css?v=20260723">
    <style>
        .ma-admin-layout {
            display: flex;
            min-height: 100vh;
        }
        .ma-admin-sidebar {
            width: 260px;
            background: #0b2b2b;
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
        }
        .ma-admin-logo {
            padding: 24px;
            border-bottom: 1px solid rgba(201,162,39,0.2);
        }
        .ma-admin-logo h2 {
            font-size: 18px;
            font-weight: 800;
            color: #c9a227;
            margin: 0;
        }
        .ma-admin-logo span {
            font-size: 11px;
            color: rgba(255,255,255,0.5);
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .ma-admin-nav {
            flex: 1;
            padding: 20px 0;
            overflow-y: auto;
        }
        .ma-admin-nav ul {
            list-style: none;
            margin: 0;
            padding: 0;
        }
        .ma-admin-nav li {
            margin-bottom: 4px;
        }
        .ma-admin-nav a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 24px;
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            font-size: 14px;
            transition: all 0.2s;
        }
        .ma-admin-nav a:hover,
        .ma-admin-nav a.active {
            background: rgba(201,162,39,0.1);
            color: #c9a227;
        }
        .ma-admin-nav a.active {
            border-left: 3px solid #c9a227;
        }
        .ma-admin-nav-icon {
            font-size: 18px;
        }
        .ma-admin-footer {
            padding: 20px 24px;
            border-top: 1px solid rgba(201,162,39,0.2);
        }
        .ma-admin-footer a {
            display: flex;
            align-items: center;
            gap: 10px;
            color: rgba(255,255,255,0.6);
            text-decoration: none;
            font-size: 13px;
            transition: color 0.2s;
        }
        .ma-admin-footer a:hover {
            color: #c9a227;
        }
        .ma-admin-main {
            flex: 1;
            background: #f5f5f5;
            display: flex;
            flex-direction: column;
        }
        .ma-admin-topbar {
            background: #fff;
            padding: 16px 32px;
            border-bottom: 1px solid #eee;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .ma-admin-topbar h1 {
            font-size: 20px;
            font-weight: 700;
            color: #111;
            margin: 0;
        }
        .ma-admin-user {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .ma-admin-user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: #0b2b2b;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #c9a227;
            font-weight: 700;
            font-size: 14px;
        }
        .ma-admin-user-info {
            display: flex;
            flex-direction: column;
        }
        .ma-admin-user-name {
            font-size: 14px;
            font-weight: 600;
            color: #111;
        }
        .ma-admin-user-role {
            font-size: 11px;
            color: #888;
        }
        .ma-admin-content {
            flex: 1;
            padding: 32px;
            overflow-y: auto;
        }
        @media (max-width: 768px) {
            .ma-admin-layout {
                flex-direction: column;
            }
            .ma-admin-sidebar {
                width: 100%;
                height: auto;
            }
            .ma-admin-nav {
                display: none;
            }
            .ma-admin-content {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="ma-admin-layout">
        <aside class="ma-admin-sidebar">
            <div class="ma-admin-logo">
                <h2>Mumtaz Academy</h2>
                <span>Admin Panel</span>
            </div>
            <nav class="ma-admin-nav">
                <ul>
                    <li>
                        <a href="index.php" class="<?php echo $currentPage === 'index.php' ? 'active' : ''; ?>">
                            <span class="ma-admin-nav-icon">📊</span>
                            Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="courses.php" class="<?php echo $currentPage === 'courses.php' ? 'active' : ''; ?>">
                            <span class="ma-admin-nav-icon">📚</span>
                            Courses
                        </a>
                    </li>
                    <li>
                        <a href="blog.php" class="<?php echo $currentPage === 'blog.php' ? 'active' : ''; ?>">
                            <span class="ma-admin-nav-icon">📝</span>
                            Blog Posts
                        </a>
                    </li>
                    <li>
                        <a href="teachers.php" class="<?php echo $currentPage === 'teachers.php' ? 'active' : ''; ?>">
                            <span class="ma-admin-nav-icon">👨‍🏫</span>
                            Teachers
                        </a>
                    </li>
                    <li>
                        <a href="live-classes.php" class="<?php echo $currentPage === 'live-classes.php' ? 'active' : ''; ?>">
                            <span class="ma-admin-nav-icon">📹</span>
                            Live Classes
                        </a>
                    </li>
                    <li>
                        <a href="materials.php" class="<?php echo $currentPage === 'materials.php' ? 'active' : ''; ?>">
                            <span class="ma-admin-nav-icon">📁</span>
                            Study Materials
                        </a>
                    </li>
                    <li>
                        <a href="mcq-tests.php" class="<?php echo $currentPage === 'mcq-tests.php' ? 'active' : ''; ?>">
                            <span class="ma-admin-nav-icon">📋</span>
                            MCQ Tests
                        </a>
                    </li>
                    <li>
                        <a href="users.php" class="<?php echo $currentPage === 'users.php' ? 'active' : ''; ?>">
                            <span class="ma-admin-nav-icon">👥</span>
                            Users
                        </a>
                    </li>
                    <li>
                        <a href="admin-management.php" class="<?php echo $currentPage === 'admin-management.php' ? 'active' : ''; ?>">
                            <span class="ma-admin-nav-icon">🔐</span>
                            Admin Management
                        </a>
                    </li>
                    <li>
                        <a href="messages.php" class="<?php echo $currentPage === 'messages.php' ? 'active' : ''; ?>">
                            <span class="ma-admin-nav-icon">✉️</span>
                            Messages
                        </a>
                    </li>
                </ul>
            </nav>
            <div class="ma-admin-footer">
                <a href="logout.php">
                    <span>🚪</span>
                    Logout
                </a>
            </div>
        </aside>
        
        <main class="ma-admin-main">
            <div class="ma-admin-topbar">
                <h1><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) : 'Dashboard'; ?></h1>
                <div class="ma-admin-user">
                    <div class="ma-admin-user-info">
                        <span class="ma-admin-user-name"><?php echo htmlspecialchars($admin['name']); ?></span>
                        <span class="ma-admin-user-role">Administrator</span>
                    </div>
                    <div class="ma-admin-user-avatar">A</div>
                </div>
            </div>
            <div class="ma-admin-content">
