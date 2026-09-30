<?php
/**
 * Admin - Approve Application
 * Bus Pass Management System
 */
include_once __DIR__ . '/../config/db.php';
include_once __DIR__ . '/../includes/auth.php';

requireAdmin('login.php');

$app_id = 0;
$remarks = 'Approved by Administrator.';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $app_id = (int)($_POST['id'] ?? 0);
    $user_remarks = trim($_POST['remarks'] ?? '');
    if (!empty($user_remarks)) {
        $remarks = $user_remarks;
    }
} elseif (isset($_GET['id'])) {
    $app_id = (int)$_GET['id'];
}

if ($app_id <= 0) {
    $_SESSION['flash_error'] = "Invalid application ID provided.";
    header("Location: applications.php");
    exit;
}

// Update application status to Approved with timestamp
$sql = "UPDATE bus_pass_applications 
        SET status = 'Approved', remarks = ?, approved_at = NOW() 
        WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "si", $remarks, $app_id);

if (mysqli_stmt_execute($stmt)) {
    $_SESSION['flash_success'] = "Application #APP-" . str_pad($app_id, 4, '0', STR_PAD_LEFT) . " has been approved successfully.";
} else {
    $_SESSION['flash_error'] = "Failed to approve application. Please try again.";
}
mysqli_stmt_close($stmt);

header("Location: applications.php");
exit;
?>
