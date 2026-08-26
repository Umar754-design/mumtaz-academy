<?php
/**
 * Mumtaz Academy - Configuration File
 * Database and application settings
 */

// Prevent direct access
if (!defined('APP_ACCESS')) {
    define('APP_ACCESS', true);
}


// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'your_database_name');
define('DB_USER', 'your_database_user');
define('DB_PASS', 'your_database_password');
define('DB_CHARSET', 'utf8mb4');

// Application Configuration
define('APP_NAME', 'Mumtaz Academy');
define('APP_URL', 'http://localhost/mumtaz-academy');
define('APP_ENV', 'development'); // development or production

// Session Configuration
define('SESSION_NAME', 'mumtaz_session');
define('SESSION_LIFETIME', 86400); // 24 hours in seconds

// Security Configuration
define('CSRF_TOKEN_NAME', 'csrf_token');
define('CSRF_TOKEN_LIFETIME', 3600); // 1 hour in seconds

// Password Configuration
define('PASSWORD_MIN_LENGTH', 8);
define('PASSWORD_HASH_ALGO', PASSWORD_DEFAULT);

// File Upload Configuration
define('UPLOAD_MAX_SIZE', 5242880); // 5MB in bytes
define('UPLOAD_ALLOWED_TYPES', ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png']);

// Email Configuration (for contact forms)
define('ADMIN_EMAIL', 'khanumar865446@gmail.com');
define('EMAIL_FROM_NAME', 'Mumtaz Academy');

// Pagination
define('ITEMS_PER_PAGE', 12);

// Error Reporting (set to 0 in production)
if (APP_ENV === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_name(SESSION_NAME);
    
    // Set session cookie parameters for security
    session_set_cookie_params([
        'lifetime' => SESSION_LIFETIME,
        'path' => '/',
        'domain' => '', // Current domain
        'secure' => APP_ENV === 'production', // HTTPS only in production
        'httponly' => true, // Prevent JavaScript access
        'samesite' => 'Strict' // Prevent CSRF
    ]);
    
    session_start();
    
    // Regenerate session ID on login to prevent session fixation
    if (isset($_SESSION['user_id']) && !isset($_SESSION['session_regenerated'])) {
        session_regenerate_id(true);
        $_SESSION['session_regenerated'] = true;
    }
}

// Database Connection Class
class Database {
    private static $instance = null;
    private $connection;

    private function __construct() {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_PERSISTENT => false
            ];
            $this->connection = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            if (APP_ENV === 'development') {
                die("Database Connection Failed: " . $e->getMessage());
            } else {
                die("Database connection failed. Please try again later.");
            }
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->connection;
    }

    // Prevent cloning
    private function __clone() {}

    // Prevent unserialization
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
}

// Helper function to get database connection
function getDB() {
    return Database::getInstance()->getConnection();
}

// Helper function for safe redirect
function redirect($url) {
    header("Location: " . $url);
    exit();
}

// Helper function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['user_email']);
}

// Helper function to get current user
function getCurrentUser() {
    if (isLoggedIn()) {
        return [
            'id' => $_SESSION['user_id'],
            'name' => $_SESSION['user_name'] ?? '',
            'email' => $_SESSION['user_email']
        ];
    }
    return null;
}

// Helper function to set flash message
function setFlashMessage($type, $message) {
    $_SESSION['flash_message'] = [
        'type' => $type,
        'message' => $message
    ];
}

// Helper function to get and clear flash message
function getFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $message;
    }
    return null;
}

// Helper function to generate CSRF token
function generateCSRFToken() {
    if (!isset($_SESSION[CSRF_TOKEN_NAME]) || 
        !isset($_SESSION[CSRF_TOKEN_NAME . '_time']) || 
        time() - $_SESSION[CSRF_TOKEN_NAME . '_time'] > CSRF_TOKEN_LIFETIME) {
        $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
        $_SESSION[CSRF_TOKEN_NAME . '_time'] = time();
    }
    return $_SESSION[CSRF_TOKEN_NAME];
}

// Helper function to verify CSRF token
function verifyCSRFToken($token) {
    if (!isset($_SESSION[CSRF_TOKEN_NAME]) || 
        !isset($_SESSION[CSRF_TOKEN_NAME . '_time'])) {
        return false;
    }
    
    if (time() - $_SESSION[CSRF_TOKEN_NAME . '_time'] > CSRF_TOKEN_LIFETIME) {
        unset($_SESSION[CSRF_TOKEN_NAME]);
        unset($_SESSION[CSRF_TOKEN_NAME . '_time']);
        return false;
    }
    
    return hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);
}

// Helper function to sanitize input
function sanitizeInput($data) {
    if (is_array($data)) {
        return array_map('sanitizeInput', $data);
    }
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

// Helper function to validate email
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

// Helper function to validate password strength
function isValidPassword($password) {
    if (strlen($password) < PASSWORD_MIN_LENGTH) {
        return false;
    }
    return true;
}

// Helper function to hash password
function hashPassword($password) {
    return password_hash($password, PASSWORD_HASH_ALGO);
}

// Helper function to verify password
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

// Helper function to generate random string
function generateRandomString($length = 32) {
    return bin2hex(random_bytes($length / 2));
}

// Helper function to log error (in production, use proper logging)
function logError($message, $context = []) {
    if (APP_ENV === 'development') {
        error_log("[" . date('Y-m-d H:i:s') . "] " . $message . " | Context: " . json_encode($context));
    }
}
