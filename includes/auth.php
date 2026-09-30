<?php
/**
 * Authentication and Access Control Helper
 * Bus Pass Management System
 */

// Start session if not started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Check if a user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Require general user login
 */
function requireLogin($redirect = 'login.php') {
    if (!isLoggedIn()) {
        $_SESSION['flash_error'] = "Please log in to access this page.";
        header("Location: " . $redirect);
        exit;
    }
}

/**
 * Require student role specifically
 */
function requireStudent($redirect = 'login.php') {
    if (!isLoggedIn()) {
        $_SESSION['flash_error'] = "Please log in as a student to access this page.";
        header("Location: " . $redirect);
        exit;
    }
    if ($_SESSION['role'] !== 'student') {
        if ($_SESSION['role'] === 'admin') {
            header("Location: admin/dashboard.php");
            exit;
        }
        $_SESSION['flash_error'] = "Access denied: Student access required.";
        header("Location: " . $redirect);
        exit;
    }
}

/**
 * Require admin role specifically
 */
function requireAdmin($redirect = 'login.php') {
    if (!isLoggedIn()) {
        $_SESSION['flash_error'] = "Please log in with admin privileges.";
        header("Location: " . $redirect);
        exit;
    }
    if ($_SESSION['role'] !== 'admin') {
        $_SESSION['flash_error'] = "Access denied: Administrator privileges required.";
        header("Location: ../dashboard.php");
        exit;
    }
}

/**
 * Get current logged in student record along with user details
 */
function getLoggedInStudent($conn, $user_id) {
    $sql = "SELECT u.id AS user_id, u.name, u.email, u.role, 
                   s.id AS student_table_id, s.student_id, s.phone, s.college, s.department, s.year, s.address 
            FROM users u 
            LEFT JOIN students s ON u.id = s.user_id 
            WHERE u.id = ? AND u.role = 'student' LIMIT 1";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($result);
}
?>
