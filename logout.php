<?php
// logout.php - Complete logout
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';

// Clear all session data
$_SESSION = array();

// Destroy session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destroy session
session_destroy();

// Redirect to home page with logout message
header('Location: index.php?logout=success');
exit;
?>