<?php
/**
 * Admin - Delete / Deactivate Bus
 * Bus Pass Management System
 */
include_once __DIR__ . '/../config/db.php';
include_once __DIR__ . '/../includes/auth.php';

requireAdmin('login.php');

$bus_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($bus_id <= 0) {
    $_SESSION['flash_error'] = "Invalid bus ID specified.";
    header("Location: buses.php");
    exit;
}

// Check if there are any applications referencing this bus
$check_sql = "SELECT COUNT(*) AS total FROM bus_pass_applications WHERE bus_id = ?";
$stmt = mysqli_prepare($conn, $check_sql);
mysqli_stmt_bind_param($stmt, "i", $bus_id);
mysqli_stmt_execute($stmt);
$row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
$referenced_count = (int)($row['total'] ?? 0);
mysqli_stmt_close($stmt);

if ($referenced_count > 0) {
    // Soft disable: update status to Inactive to preserve relational integrity
    $update_sql = "UPDATE buses SET status = 'Inactive' WHERE id = ?";
    $stmt_up = mysqli_prepare($conn, $update_sql);
    mysqli_stmt_bind_param($stmt_up, "i", $bus_id);
    mysqli_stmt_execute($stmt_up);
    mysqli_stmt_close($stmt_up);

    $_SESSION['flash_info'] = "Bus has $referenced_count student pass application(s) linked to it. It has been safely deactivated (Status = Inactive) to maintain records.";
} else {
    // Hard delete if no records link to it
    $delete_sql = "DELETE FROM buses WHERE id = ?";
    $stmt_del = mysqli_prepare($conn, $delete_sql);
    mysqli_stmt_bind_param($stmt_del, "i", $bus_id);
    if (mysqli_stmt_execute($stmt_del)) {
        $_SESSION['flash_success'] = "Bus removed from the fleet successfully.";
    } else {
        $_SESSION['flash_error'] = "Failed to delete bus record.";
    }
    mysqli_stmt_close($stmt_del);
}

header("Location: buses.php");
exit;
?>
