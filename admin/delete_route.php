<?php
/**
 * Admin - Delete Route
 * Bus Pass Management System
 */
include_once __DIR__ . '/../config/db.php';
include_once __DIR__ . '/../includes/auth.php';

requireAdmin('login.php');

$route_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($route_id <= 0) {
    $_SESSION['flash_error'] = "Invalid route ID specified.";
    header("Location: routes.php");
    exit;
}

// Check if any student pass application references this route
$check_sql = "SELECT COUNT(*) AS total FROM bus_pass_applications WHERE route_id = ?";
$stmt = mysqli_prepare($conn, $check_sql);
mysqli_stmt_bind_param($stmt, "i", $route_id);
mysqli_stmt_execute($stmt);
$row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
$referenced_count = (int)($row['total'] ?? 0);
mysqli_stmt_close($stmt);

if ($referenced_count > 0) {
    $_SESSION['flash_error'] = "Cannot delete this route because $referenced_count student pass application(s) are linked to it. Please preserve historical application records.";
} else {
    $delete_sql = "DELETE FROM routes WHERE id = ?";
    $stmt_del = mysqli_prepare($conn, $delete_sql);
    mysqli_stmt_bind_param($stmt_del, "i", $route_id);
    if (mysqli_stmt_execute($stmt_del)) {
        $_SESSION['flash_success'] = "Route deleted successfully.";
    } else {
        $_SESSION['flash_error'] = "Failed to delete route.";
    }
    mysqli_stmt_close($stmt_del);
}

header("Location: routes.php");
exit;
?>
