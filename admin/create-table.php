<?php
/**
 * Create Admins Table
 * Run this file to create the admins table in the database
 */

require_once 'config.php';

try {
    $pdo = getDB();
    
    $sql = "CREATE TABLE IF NOT EXISTS admins (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        full_name VARCHAR(100) NOT NULL,
        email VARCHAR(150) UNIQUE NOT NULL,
        is_active TINYINT(1) DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        last_login TIMESTAMP NULL,
        INDEX idx_username (username),
        INDEX idx_email (email)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $pdo->exec($sql);
    
    echo "✅ Admins table created successfully!<br><br>";
    echo "<a href='setup-admin.php'>Create Default Admin Account →</a>";
    
} catch (PDOException $e) {
    echo "❌ Error creating admins table: " . $e->getMessage();
}
?>
