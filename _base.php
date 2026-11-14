<?php
// ============================================================================
// PHP Setups
// ============================================================================

session_start();

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

// Helper functions
function redirect($url) {
    header("Location: $url");
    exit();
}

function encode($value) {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}