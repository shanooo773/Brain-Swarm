<?php
// Database connection and helper functions
require_once 'config.php';

class Database {
    private static $instance = null;
    private $connection;
    
    private function __construct() {
        try {
            if (defined('USE_SQLITE') && USE_SQLITE) {
                // SQLite connection
                $dsn = "sqlite:" . DB_PATH;
                $this->connection = new PDO($dsn, null, null, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);
            } else {
                // MySQL connection
                $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
                $this->connection = new PDO($dsn, DB_USER, DB_PASS, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);
            }
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
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
    
    public function query($sql, $params = []) {
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
    
    public function fetch($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetch();
    }
    
    public function fetchAll($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll();
    }
    
    public function lastInsertId() {
        return $this->connection->lastInsertId();
    }
}

// Session management
class SessionManager {
    public static function start() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    public static function set($key, $value) {
        self::start();
        $_SESSION[$key] = $value;
    }
    
    public static function get($key, $default = null) {
        self::start();
        return $_SESSION[$key] ?? $default;
    }
    
    public static function remove($key) {
        self::start();
        unset($_SESSION[$key]);
    }
    
    public static function destroy() {
        self::start();
        session_destroy();
    }
    
    public static function isLoggedIn() {
        return self::get('user_id') !== null;
    }
    
    public static function getUser() {
        if (!self::isLoggedIn()) {
            return null;
        }
        
        $db = Database::getInstance();
        $user = $db->fetch(
            "SELECT u.*, p.full_name, p.profile_picture, p.is_admin 
             FROM users u 
             LEFT JOIN profiles p ON u.id = p.user_id 
             WHERE u.id = ?",
            [self::get('user_id')]
        );
        
        return $user;
    }
    
    public static function isAdmin() {
        $user = self::getUser();
        return $user && $user['is_admin'];
    }
}

// Utility functions
function sanitizeInput($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

function redirect($url) {
    header("Location: $url");
    exit();
}

function setFlashMessage($type, $message) {
    SessionManager::set('flash_' . $type, $message);
}

function getFlashMessage($type) {
    $message = SessionManager::get('flash_' . $type);
    SessionManager::remove('flash_' . $type);
    return $message;
}

function formatDate($date, $format = 'Y-m-d H:i') {
    return date($format, strtotime($date));
}

function uploadFile($file, $uploadDir, $allowedTypes = ['jpg', 'jpeg', 'png', 'gif']) {
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'Upload failed'];
    }
    
    $filename = basename($file['name']);
    $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    
    if (!in_array($extension, $allowedTypes)) {
        return ['success' => false, 'message' => 'Invalid file type'];
    }
    
    $newFilename = uniqid() . '.' . $extension;
    $targetPath = $uploadDir . $newFilename;
    
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        return ['success' => true, 'filename' => $newFilename, 'path' => $targetPath];
    }
    
    return ['success' => false, 'message' => 'Failed to move uploaded file'];
}

// Form validation
function validateRequired($value, $fieldName) {
    if (empty(trim($value))) {
        return "$fieldName is required";
    }
    return null;
}

function validateLength($value, $min, $max, $fieldName) {
    $length = strlen($value);
    if ($length < $min || $length > $max) {
        return "$fieldName must be between $min and $max characters";
    }
    return null;
}

function validateEmailFormat($email) {
    if (!validateEmail($email)) {
        return "Please enter a valid email address";
    }
    return null;
}

// Template rendering function
function renderTemplate($template, $variables = []) {
    extract($variables);
    
    ob_start();
    include "templates/$template";
    return ob_get_clean();
}

// URL helper functions
function url($path = '') {
    return SITE_URL . '/' . ltrim($path, '/');
}

function asset($path) {
    return url('static/' . ltrim($path, '/'));
}

// Enhanced URL helper functions that work with any port/environment
function getBaseUrl() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $script_name = dirname($_SERVER['SCRIPT_NAME']);
    $base_path = $script_name === '/' ? '' : $script_name;
    return $protocol . $host . $base_path;
}

function smartUrl($path = '') {
    return getBaseUrl() . '/' . ltrim($path, '/');
}

function smartAsset($path) {
    return smartUrl('static/' . ltrim($path, '/'));
}

// Asset verification function
function assetExists($path) {
    $full_path = __DIR__ . '/../static/' . ltrim($path, '/');
    return file_exists($full_path);
}

// Asset with CDN fallback
function assetWithFallback($path, $cdnUrl = null) {
    $localUrl = smartAsset($path);
    
    if (assetExists($path)) {
        return $localUrl;
    } elseif ($cdnUrl) {
        return $cdnUrl;
    } else {
        return $localUrl; // Return local URL even if missing for debugging
    }
}

// Authentication helpers
function requireAuth() {
    if (!SessionManager::isLoggedIn()) {
        setFlashMessage('error', 'Please log in to access this page.');
        // Determine relative path to sign-in.php
        $script_dir = dirname($_SERVER['SCRIPT_NAME']);
        $base_dir = str_replace('/admin', '', $script_dir);
        $base_dir = str_replace('/blog', '', $base_dir);
        $signin_path = $base_dir . '/sign-in.php';
        redirect($signin_path);
    }
}

function requireAdmin() {
    requireAuth();
    if (!SessionManager::isAdmin()) {
        setFlashMessage('error', 'You do not have permission to access this page.');
        // Determine relative path to index.php
        $script_dir = dirname($_SERVER['SCRIPT_NAME']);
        $base_dir = str_replace('/admin', '', $script_dir);
        $base_dir = str_replace('/blog', '', $base_dir);
        $index_path = $base_dir . '/index.php';
        redirect($index_path);
    }
}

// Initialize session
SessionManager::start();
?>