<?php
require_once 'config.php';
require_once 'auth.php';

// Get database connection
$pdo = getDB();

// Check if user is logged in
$user = getCurrentUser();
if (!$user) {
    header('Location: login.php?redirect=' . urlencode($_SERVER['HTTP_REFERER']));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
        $_SESSION['error'] = 'Invalid request. Please try again.';
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }
    
    $courseId = isset($_POST['course_id']) ? (int)$_POST['course_id'] : 0;
    
    if ($courseId <= 0) {
        $_SESSION['error'] = 'Invalid course.';
        header('Location: courses.php');
        exit;
    }
    
    // Check if course exists and is published
    $stmt = $pdo->prepare("SELECT * FROM courses WHERE id = ? AND is_published = 1");
    $stmt->execute([$courseId]);
    $course = $stmt->fetch();
    
    if (!$course) {
        $_SESSION['error'] = 'Course not found.';
        header('Location: courses.php');
        exit;
    }
    
    // Check if already enrolled
    $stmt = $pdo->prepare("SELECT * FROM enrollments WHERE user_id = ? AND course_id = ?");
    $stmt->execute([$user['id'], $courseId]);
    $existingEnrollment = $stmt->fetch();
    
    if ($existingEnrollment) {
        $_SESSION['success'] = 'You are already enrolled in this course.';
        header('Location: course-detail.php?id=' . $courseId);
        exit;
    }
    
    // Enroll user in course
    $stmt = $pdo->prepare("INSERT INTO enrollments (user_id, course_id, enrolled_at, progress) VALUES (?, ?, NOW(), 0)");
    
    try {
        $stmt->execute([$user['id'], $courseId]);
        $_SESSION['success'] = 'Successfully enrolled in the course!';
        header('Location: course-detail.php?id=' . $courseId);
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Failed to enroll. Please try again.';
        header('Location: course-detail.php?id=' . $courseId);
    }
    exit;
} else {
    // Redirect if not POST request
    header('Location: courses.php');
    exit;
}
