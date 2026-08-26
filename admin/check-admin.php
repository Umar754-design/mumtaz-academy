<?php
/**
 * Admin Setup Diagnostic
 * Check if admins table exists and if admin account is created
 */

require_once 'config.php';

echo "<h2>Admin Setup Diagnostic</h2>";
echo "<hr>";

try {
    $pdo = getDB();
    echo "✅ Database connection successful<br><br>";
    
    // Check if admins table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'admins'");
    $tableExists = $stmt->fetch();
    
    if ($tableExists) {
        echo "✅ Admins table exists<br><br>";
        
        // Check if any admins exist
        $stmt = $pdo->query("SELECT COUNT(*) FROM admins");
        $adminCount = $stmt->fetchColumn();
        
        echo "Number of admin accounts: " . $adminCount . "<br><br>";
        
        if ($adminCount > 0) {
            echo "Existing admin accounts:<br>";
            $stmt = $pdo->query("SELECT id, username, full_name, email, is_active, created_at FROM admins");
            $admins = $stmt->fetchAll();
            
            echo "<table border='1' cellpadding='10'>";
            echo "<tr><th>ID</th><th>Username</th><th>Full Name</th><th>Email</th><th>Active</th><th>Created</th></tr>";
            foreach ($admins as $admin) {
                echo "<tr>";
                echo "<td>" . $admin['id'] . "</td>";
                echo "<td>" . htmlspecialchars($admin['username']) . "</td>";
                echo "<td>" . htmlspecialchars($admin['full_name']) . "</td>";
                echo "<td>" . htmlspecialchars($admin['email']) . "</td>";
                echo "<td>" . ($admin['is_active'] ? 'Yes' : 'No') . "</td>";
                echo "<td>" . $admin['created_at'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
            
            echo "<br><br>";
            echo "<strong>Your login credentials:</strong><br>";
            echo "Username: umar<br>";
            echo "Password: password<br><br>";
            echo "<a href='login.php'>Go to Admin Login →</a>";
        } else {
            echo "❌ No admin accounts found<br><br>";
            echo "<a href='setup-admin.php'>Create Admin Account →</a>";
        }
    } else {
        echo "❌ Admins table does not exist<br><br>";
        echo "<a href='create-table.php'>Create Admins Table →</a>";
    }
    
} catch (PDOException $e) {
    echo "❌ Database error: " . $e->getMessage();
}
?>
