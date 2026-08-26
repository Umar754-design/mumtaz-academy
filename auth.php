<?php
/**
 * Mumtaz Academy - Authentication Functions
 * User registration, login, logout, and session management
 */

require_once 'config.php';

class Auth {
    private $db;

    public function __construct() {
        $this->db = getDB();
    }

    /**
     * Register a new user
     */
    public function register($fullName, $email, $password, $phone = null) {
        // Validate inputs
        if (empty($fullName) || empty($email) || empty($password)) {
            return ['success' => false, 'message' => 'All fields are required'];
        }

        if (!isValidEmail($email)) {
            return ['success' => false, 'message' => 'Invalid email address'];
        }

        if (!isValidPassword($password)) {
            return ['success' => false, 'message' => 'Password must be at least ' . PASSWORD_MIN_LENGTH . ' characters'];
        }

        // Check if email already exists
        try {
            $stmt = $this->db->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                return ['success' => false, 'message' => 'Email already registered'];
            }
        } catch (PDOException $e) {
            logError('Database error during registration check', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'Registration failed. Please try again.'];
        }

        // Insert new user
        try {
            $hashedPassword = hashPassword($password);
            $stmt = $this->db->prepare("INSERT INTO users (full_name, email, password, phone) VALUES (?, ?, ?, ?)");
            $result = $stmt->execute([$fullName, $email, $hashedPassword, $phone]);
            
            if ($result) {
                $userId = $this->db->lastInsertId();
                return ['success' => true, 'message' => 'Registration successful', 'user_id' => $userId];
            } else {
                return ['success' => false, 'message' => 'Registration failed. Please try again.'];
            }
        } catch (PDOException $e) {
            logError('Database error during registration', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'Registration failed. Please try again.'];
        }
    }

    /**
     * Login user
     */
    public function login($email, $password, $rememberMe = false) {
        // Validate inputs
        if (empty($email) || empty($password)) {
            return ['success' => false, 'message' => 'Email and password are required'];
        }

        if (!isValidEmail($email)) {
            return ['success' => false, 'message' => 'Invalid email address'];
        }

        // Get user from database
        try {
            $stmt = $this->db->prepare("SELECT id, full_name, email, password, is_active FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if (!$user) {
                return ['success' => false, 'message' => 'Invalid email or password'];
            }

            // Check if account is active
            if (!$user['is_active']) {
                return ['success' => false, 'message' => 'Your account has been deactivated'];
            }

            // Verify password
            if (!verifyPassword($password, $user['password'])) {
                return ['success' => false, 'message' => 'Invalid email or password'];
            }

            // Update last login
            $stmt = $this->db->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
            $stmt->execute([$user['id']]);

            // Regenerate session ID to prevent session fixation
            session_regenerate_id(true);

            // Create session
            $this->createSession($user['id'], $user['full_name'], $user['email'], $rememberMe);

            return ['success' => true, 'message' => 'Login successful', 'user' => [
                'id' => $user['id'],
                'name' => $user['full_name'],
                'email' => $user['email']
            ]];

        } catch (PDOException $e) {
            logError('Database error during login', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'Login failed. Please try again.'];
        }
    }

    /**
     * Create user session
     */
    private function createSession($userId, $fullName, $email, $rememberMe = false) {
        // Set session variables
        $_SESSION['user_id'] = $userId;
        $_SESSION['user_name'] = $fullName;
        $_SESSION['user_email'] = $email;
        $_SESSION['logged_in'] = true;
        $_SESSION['login_time'] = time();

        // Set session expiry
        if ($rememberMe) {
            $expiry = time() + (30 * 24 * 60 * 60); // 30 days
            $_SESSION['expires_at'] = $expiry;
        } else {
            $expiry = time() + SESSION_LIFETIME;
            $_SESSION['expires_at'] = $expiry;
        }

        // Store session in database
        try {
            $sessionToken = generateRandomString(64);
            $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
            $expiresAt = date('Y-m-d H:i:s', $expiry);

            $stmt = $this->db->prepare("INSERT INTO user_sessions (user_id, session_token, ip_address, user_agent, expires_at) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$userId, $sessionToken, $ipAddress, $userAgent, $expiresAt]);

            $_SESSION['session_token'] = $sessionToken;
        } catch (PDOException $e) {
            logError('Failed to store session in database', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Logout user
     */
    public function logout() {
        // Remove session from database
        if (isset($_SESSION['session_token'])) {
            try {
                $stmt = $this->db->prepare("DELETE FROM user_sessions WHERE session_token = ?");
                $stmt->execute([$_SESSION['session_token']]);
            } catch (PDOException $e) {
                logError('Failed to remove session from database', ['error' => $e->getMessage()]);
            }
        }

        // Destroy session
        $_SESSION = [];
        
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }

        session_destroy();

        return ['success' => true, 'message' => 'Logged out successfully'];
    }

    /**
     * Check if user is logged in
     */
    public function check() {
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['logged_in'])) {
            return false;
        }

        // Check session expiry
        if (isset($_SESSION['expires_at']) && time() > $_SESSION['expires_at']) {
            $this->logout();
            return false;
        }

        // Verify session in database
        if (isset($_SESSION['session_token'])) {
            try {
                $stmt = $this->db->prepare("SELECT id FROM user_sessions WHERE session_token = ? AND expires_at > NOW()");
                $stmt->execute([$_SESSION['session_token']]);
                if (!$stmt->fetch()) {
                    $this->logout();
                    return false;
                }
            } catch (PDOException $e) {
                logError('Failed to verify session in database', ['error' => $e->getMessage()]);
            }
        }

        return true;
    }

    /**
     * Get current user
     */
    public function user() {
        if (!$this->check()) {
            return null;
        }

        try {
            $stmt = $this->db->prepare("SELECT id, full_name, email, phone, created_at, last_login FROM users WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $user = $stmt->fetch();

            if ($user) {
                unset($user['password']); // Never return password
                return $user;
            }
        } catch (PDOException $e) {
            logError('Failed to get user data', ['error' => $e->getMessage()]);
        }

        return null;
    }

    /**
     * Update user profile
     */
    public function updateProfile($userId, $fullName, $phone = null) {
        if (empty($fullName)) {
            return ['success' => false, 'message' => 'Name is required'];
        }

        try {
            $stmt = $this->db->prepare("UPDATE users SET full_name = ?, phone = ? WHERE id = ?");
            $result = $stmt->execute([$fullName, $phone, $userId]);

            if ($result) {
                // Update session
                $_SESSION['user_name'] = $fullName;
                return ['success' => true, 'message' => 'Profile updated successfully'];
            } else {
                return ['success' => false, 'message' => 'Failed to update profile'];
            }
        } catch (PDOException $e) {
            logError('Database error during profile update', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'Failed to update profile'];
        }
    }

    /**
     * Change password
     */
    public function changePassword($userId, $currentPassword, $newPassword) {
        if (empty($currentPassword) || empty($newPassword)) {
            return ['success' => false, 'message' => 'Both current and new passwords are required'];
        }

        if (!isValidPassword($newPassword)) {
            return ['success' => false, 'message' => 'New password must be at least ' . PASSWORD_MIN_LENGTH . ' characters'];
        }

        try {
            // Get current password
            $stmt = $this->db->prepare("SELECT password FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch();

            if (!$user) {
                return ['success' => false, 'message' => 'User not found'];
            }

            // Verify current password
            if (!verifyPassword($currentPassword, $user['password'])) {
                return ['success' => false, 'message' => 'Current password is incorrect'];
            }

            // Update password
            $hashedPassword = hashPassword($newPassword);
            $stmt = $this->db->prepare("UPDATE users SET password = ? WHERE id = ?");
            $result = $stmt->execute([$hashedPassword, $userId]);

            if ($result) {
                return ['success' => true, 'message' => 'Password changed successfully'];
            } else {
                return ['success' => false, 'message' => 'Failed to change password'];
            }
        } catch (PDOException $e) {
            logError('Database error during password change', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'Failed to change password'];
        }
    }

    /**
     * Request password reset
     */
    public function requestPasswordReset($email) {
        if (!isValidEmail($email)) {
            return ['success' => false, 'message' => 'Invalid email address'];
        }

        try {
            // Check if user exists
            $stmt = $this->db->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if (!$user) {
                return ['success' => false, 'message' => 'No account found with this email'];
            }

            // Generate reset token
            $token = generateRandomString(64);
            $expiresAt = date('Y-m-d H:i:s', time() + 3600); // 1 hour

            // Store token
            $stmt = $this->db->prepare("INSERT INTO password_reset_tokens (user_id, token, expires_at) VALUES (?, ?, ?)");
            $stmt->execute([$user['id'], $token, $expiresAt]);

            // In production, send email with reset link
            // For now, return the token (for testing)
            return [
                'success' => true, 
                'message' => 'Password reset link sent to your email',
                'token' => $token // Remove this in production
            ];
        } catch (PDOException $e) {
            logError('Database error during password reset request', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'Failed to request password reset'];
        }
    }

    /**
     * Reset password with token
     */
    public function resetPassword($token, $newPassword) {
        if (empty($token) || empty($newPassword)) {
            return ['success' => false, 'message' => 'Token and new password are required'];
        }

        if (!isValidPassword($newPassword)) {
            return ['success' => false, 'message' => 'Password must be at least ' . PASSWORD_MIN_LENGTH . ' characters'];
        }

        try {
            // Get token from database
            $stmt = $this->db->prepare("SELECT user_id, expires_at, used_at FROM password_reset_tokens WHERE token = ?");
            $stmt->execute([$token]);
            $resetToken = $stmt->fetch();

            if (!$resetToken) {
                return ['success' => false, 'message' => 'Invalid reset token'];
            }

            // Check if token is expired
            if (strtotime($resetToken['expires_at']) < time()) {
                return ['success' => false, 'message' => 'Reset token has expired'];
            }

            // Check if token already used
            if ($resetToken['used_at'] !== null) {
                return ['success' => false, 'message' => 'Reset token has already been used'];
            }

            // Update password
            $hashedPassword = hashPassword($newPassword);
            $stmt = $this->db->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->execute([$hashedPassword, $resetToken['user_id']]);

            // Mark token as used
            $stmt = $this->db->prepare("UPDATE password_reset_tokens SET used_at = NOW() WHERE token = ?");
            $stmt->execute([$token]);

            return ['success' => true, 'message' => 'Password reset successful'];
        } catch (PDOException $e) {
            logError('Database error during password reset', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'Failed to reset password'];
        }
    }

    /**
     * Clean up expired sessions
     */
    public function cleanupExpiredSessions() {
        try {
            $stmt = $this->db->prepare("DELETE FROM user_sessions WHERE expires_at < NOW()");
            $stmt->execute();
            
            $stmt = $this->db->prepare("DELETE FROM password_reset_tokens WHERE expires_at < NOW() OR used_at IS NOT NULL");
            $stmt->execute();
            
            return true;
        } catch (PDOException $e) {
            logError('Failed to cleanup expired sessions', ['error' => $e->getMessage()]);
            return false;
        }
    }
}

// Helper function to get auth instance
function auth() {
    static $auth = null;
    if ($auth === null) {
        $auth = new Auth();
    }
    return $auth;
}

// Middleware to require authentication
function requireAuth() {
    if (!auth()->check()) {
        setFlashMessage('error', 'Please login to continue');
        redirect('login.php');
    }
}

// Middleware to require guest (not logged in)
function requireGuest() {
    if (auth()->check()) {
        redirect('index.php');
    }
}
