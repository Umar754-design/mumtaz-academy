<?php
require_once 'config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $csrfToken = $_POST['csrf_token'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = 'Please enter both username and password.';
    } elseif (!verifyCSRFToken($csrfToken)) {
        $error = 'Invalid request. Please try again.';
    } elseif (adminLogin($username, $password)) {
        header('Location: index.php');
        exit;
    } else {
        $error = 'Invalid username or password.';
    }
}

$csrfToken = generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Mumtaz Academy</title>
    <link rel="stylesheet" href="../styles.css?v=20260723">
    <style>
        .ma-admin-login {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #0b2b2b 0%, #0d3333 50%, #0b2b2b 100%);
            padding: 20px;
        }
        .ma-admin-login-card {
            background: #fff;
            border-radius: 12px;
            padding: 40px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.2);
        }
        .ma-admin-login-header {
            text-align: center;
            margin-bottom: 32px;
        }
        .ma-admin-login-header h1 {
            font-size: 24px;
            font-weight: 800;
            color: #0b2b2b;
            margin-bottom: 8px;
        }
        .ma-admin-login-header p {
            font-size: 14px;
            color: #666;
        }
        .ma-admin-form {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }
        .ma-admin-form label {
            font-size: 13px;
            font-weight: 600;
            color: #444;
            margin-bottom: 4px;
        }
        .ma-admin-form input {
            padding: 12px 14px;
            border: 1.5px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.2s;
        }
        .ma-admin-form input:focus {
            outline: none;
            border-color: #0b2b2b;
        }
        .ma-admin-btn {
            background: #0b2b2b;
            color: #fff;
            border: none;
            padding: 14px;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: background 0.2s;
        }
        .ma-admin-btn:hover {
            background: #143a3a;
        }
        .ma-admin-error {
            background: #fee;
            border: 1px solid #fcc;
            color: #c33;
            padding: 12px 14px;
            border-radius: 8px;
            font-size: 13px;
            margin-bottom: 16px;
        }
        .ma-admin-back {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }
        .ma-admin-back a {
            color: #c9a227;
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
        }
        .ma-admin-back a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="ma-admin-login">
        <div class="ma-admin-login-card">
            <div class="ma-admin-login-header">
                <h1>Admin Login</h1>
                <p>Mumtaz Academy Administration</p>
            </div>
            
            <?php if ($error): ?>
                <div class="ma-admin-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <form class="ma-admin-form" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
                
                <div>
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" placeholder="admin" required>
                </div>
                
                <div>
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter password" required>
                </div>
                
                <button type="submit" class="ma-admin-btn">Login to Admin Panel</button>
            </form>
            
            <div class="ma-admin-back">
                <a href="../index.php">← Back to Website</a>
            </div>
        </div>
    </div>
</body>
</html>
