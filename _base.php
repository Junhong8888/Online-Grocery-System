<?php
// ============================================================================
// PHP Setups
// ============================================================================

session_start();

// ============================================================================
// PDO Initialization
// ============================================================================

$host     = 'localhost';
$user     = 'root';
$password = '';
$dbname   = 'online_grocery_db';

$dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";

$pdo = new PDO($dsn, $user, $password, [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
]);


// ============================================================================
// Helper Function
// ============================================================================
function redirect($url) {
    header("Location: $url");
    exit();
}

function encode($value) {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

// ============================================================================
// Admin Registration Secret Key
// ============================================================================
define('ADMIN_SECRET_CODE', 'WILLEM_ADMIN_2026');

// ============================================================================
// CSRF Protection Helpers
// ============================================================================
function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field() {
    return '<input type="hidden" name="csrf_token" value="' . encode(csrf_token()) . '">';
}

function verify_csrf() {
    $token = $_POST['csrf_token'] ?? '';
    if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
        http_response_code(403);
        exit('Invalid or expired security token (CSRF). Please go back and refresh the page.');
    }
}

// ============================================================================
// Authentication & Restricted Admin Security Checks
// ============================================================================
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function is_admin() {
    return is_logged_in() && strcasecmp($_SESSION['role'] ?? '', 'Admin') === 0;
}

function is_member() {
    return is_logged_in() && strcasecmp($_SESSION['role'] ?? '', 'Member') === 0;
}

function check_session_timeout($max_idle_seconds = 1800) {
    if (is_logged_in()) {
        if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $max_idle_seconds) {
            session_unset();
            session_destroy();
            redirect('login.php?timeout=1');
        }
        $_SESSION['last_activity'] = time();
    }
}

function require_login() {
    check_session_timeout();
    if (!is_logged_in()) {
        redirect('login.php');
    }
}

function require_admin() {
    global $pdo;
    require_login();

    // Re-verify role directly against the database on every restricted admin page
    $stmt = $pdo->prepare("SELECT role FROM users WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $db_user = $stmt->fetch();

    if (!$db_user || strcasecmp($db_user['role'], 'Admin') !== 0) {
        $_SESSION['role'] = $db_user['role'] ?? 'Member';
        http_response_code(403);
        redirect('index.php?error=unauthorized');
    }

    $_SESSION['role'] = $db_user['role'];
}