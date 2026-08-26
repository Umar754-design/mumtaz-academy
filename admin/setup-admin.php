<?php
/**
 * Setup First Admin Account
 * Run this file once to create the initial admin account
 * After running, delete this file for security
 */

require_once 'config.php';

// Get database connection
$pdo = getDB();

// Check if admins table exists
try {
    $stmt = $pdo->query("SELECT COUNT(*) FROM admins");
    $adminCount = $stmt->fetchColumn();
    
    if ($adminCount > 0) {
        echo "Admins already exist in the database. This setup script should only be run once.";
        echo "<br><br><a href='login.php'>Go to Admin Login</a>";
        exit;
    }
} catch (PDOException $e) {
    echo "Error checking admins table: " . $e->getMessage();
    echo "<br><br>Please run the database.sql file first to create the admins table.";
    exit;
}

// Create default admin
$username = 'umar';
$password = 'password';
$fullName = 'Super Admin';
$email = 'umar@mumtazacademy.com';

// Hash password
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

try {
    $stmt = $pdo->prepare("
        INSERT INTO admins (username, password, full_name, email, is_active, created_at)
        VALUES (?, ?, ?, ?, 1, NOW())
    ");
    $stmt->execute([$username, $hashedPassword, $fullName, $email]);
    
    echo "✅ Default admin account created successfully!<br><br>";
    echo "<strong>Login Credentials:</strong><br>";
    echo "Username: " . htmlspecialchars($username) . "<br>";
    echo "Password: " . htmlspecialchars($password) . "<br><br>";
    echo "<strong>⚠️ IMPORTANT:</strong><br>";
    echo "1. Login immediately and change the password<br>";
    echo "2. Delete this file (setup-admin.php) for security<br><br>";
    echo "<a href='login.php'>Go to Admin Login →</a>";
    
} catch (PDOException $e) {
    echo "Error creating admin: " . $e->getMessage();
}
?>
