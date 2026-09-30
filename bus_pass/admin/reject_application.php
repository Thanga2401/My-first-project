<?php
/**
 * Admin - Reject Application
 * Bus Pass Management System
 */
include_once __DIR__ . '/../config/db.php';
include_once __DIR__ . '/../includes/auth.php';

requireAdmin('login.php');

$app_id = isset($_REQUEST['id']) ? (int)$_REQUEST['id'] : 0;

if ($app_id <= 0) {
    $_SESSION['flash_error'] = "Invalid application ID specified.";
    header("Location: applications.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $remarks = trim($_POST['remarks'] ?? '');
    if (empty($remarks)) {
        $remarks = "Rejected by Administrator due to documentation/eligibility issues.";
    }

    $sql = "UPDATE bus_pass_applications 
            SET status = 'Rejected', remarks = ?, approved_at = NULL 
            WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "si", $remarks, $app_id);

    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['flash_success'] = "Application #APP-" . str_pad($app_id, 4, '0', STR_PAD_LEFT) . " has been rejected.";
    } else {
        $_SESSION['flash_error'] = "Failed to update application status.";
    }
    mysqli_stmt_close($stmt);

    header("Location: applications.php");
    exit;
}

// If accessed via GET, fetch app details and render rejection form
$sql = "SELECT a.*, u.name AS student_name, s.student_id AS reg_no, r.route_name 
        FROM bus_pass_applications a
        JOIN students s ON a.student_id = s.id
        JOIN users u ON s.user_id = u.id
        JOIN routes r ON a.route_id = r.id
        WHERE a.id = ? LIMIT 1";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $app_id);
mysqli_stmt_execute($stmt);
$app = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if (!$app) {
    $_SESSION['flash_error'] = "Application not found.";
    header("Location: applications.php");
    exit;
}

$page_title = "Reject Application #APP-" . str_pad($app_id, 4, '0', STR_PAD_LEFT);
include_once __DIR__ . '/includes/header.php';
?>

<div class="row justify-content-center py-4">
    <div class="col-md-7 col-lg-6">
        <div class="card card-custom border-0 shadow-lg">
            <div class="card-header bg-danger text-white py-3 rounded-top-3">
                <h5 class="fw-bold mb-0"><i class="bi bi-x-circle me-2"></i>Reject Application #APP-<?php echo str_pad($app['id'], 4, '0', STR_PAD_LEFT); ?></h5>
            </div>
            <div class="card-body p-4">
                <p class="text-muted">
                    You are rejecting the bus pass request for student <strong><?php echo htmlspecialchars($app['student_name']); ?></strong> (<?php echo htmlspecialchars($app['reg_no']); ?>) on route <strong><?php echo htmlspecialchars($app['route_name']); ?></strong>.
                </p>

                <form action="reject_application.php" method="POST">
                    <input type="hidden" name="id" value="<?php echo $app['id']; ?>">

                    <div class="mb-3">
                        <label for="remarks" class="form-label fw-semibold">Reason for Rejection <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="remarks" name="remarks" rows="4" required placeholder="State the reason for rejection (e.g. Incomplete details, route unavailable, invalid student ID)..."></textarea>
                        <small class="text-muted">This message will be visible to the student.</small>
                    </div>

                    <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                        <a href="applications.php" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-danger fw-bold">
                            <i class="bi bi-x-circle-fill me-1"></i> Confirm Rejection
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/includes/footer.php'; ?>
