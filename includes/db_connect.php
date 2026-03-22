<?php
/**
 * Database Connection - SIMPLE CONNECTION ONLY
 */

declare(strict_types=1);

// ============================================================================
// DATABASE CONFIGURATION
// ============================================================================

$type     = 'mysql';
$server   = 'localhost';
$db       = 'kids-activity-book';
$port     = '3306';
$charset  = 'utf8mb4';

$username = 'root';
$password = '';

// Set PDO options
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

// ============================================================================
// CREATE DATABASE CONNECTION
// ============================================================================

$dsn = "{$type}:host={$server};dbname={$db};port={$port};charset={$charset}";

try {
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Initialize cart
if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// ============================================================================
// DO NOT DECLARE ANY FUNCTIONS HERE! All functions go in functions.php
// ============================================================================
?>