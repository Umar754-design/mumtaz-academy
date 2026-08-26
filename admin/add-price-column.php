<?php
/**
 * Add Price Column to Courses Table
 * Run this file to add the price column to existing courses table
 */

require_once 'config.php';

try {
    $pdo = getDB();
    
    // Check if price column already exists
    $stmt = $pdo->query("SHOW COLUMNS FROM courses LIKE 'price'");
    $columnExists = $stmt->fetch();
    
    if ($columnExists) {
        echo "✅ Price column already exists in courses table.<br><br>";
        echo "<a href='courses.php'>Go to Courses Management →</a>";
    } else {
        // Add price column
        $sql = "ALTER TABLE courses ADD COLUMN price DECIMAL(10,2) DEFAULT 0.00 AFTER image_url";
        $pdo->exec($sql);
        
        echo "✅ Price column added successfully to courses table!<br><br>";
        echo "<a href='courses.php'>Go to Courses Management →</a>";
    }
    
} catch (PDOException $e) {
    echo "❌ Error adding price column: " . $e->getMessage();
}
?>
