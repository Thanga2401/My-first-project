<?php
/**
 * Logout Page
 * Bus Pass Management System
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Unset all session variables
$_SESSION = array();

// Destroy session cookie if it exists
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destroy session
session_destroy();

// Start fresh session just for flash message
session_start();
$_SESSION['flash_success'] = "You have been logged out successfully.";

header("Location: login.php");
exit;
?>
