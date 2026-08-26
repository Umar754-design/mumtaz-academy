<?php
require_once 'config.php';

// Get database connection
$pdo = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['material_id'])) {
    $materialId = (int)$_POST['material_id'];
    
    try {
        $stmt = $pdo->prepare("UPDATE study_materials SET download_count = download_count + 1 WHERE id = ?");
        $stmt->execute([$materialId]);
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
}
