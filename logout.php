<?php
require_once 'config.php';
require_once 'auth.php';

// Logout the user
$result = auth()->logout();

if ($result['success']) {
    setFlashMessage('success', 'You have been logged out successfully.');
} else {
    setFlashMessage('error', 'Logout failed. Please try again.');
}

redirect('index.php');
