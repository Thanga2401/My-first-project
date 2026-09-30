<?php
/**
 * Admin - View Application Details
 * Bus Pass Management System
 */
include_once __DIR__ . '/../config/db.php';
include_once __DIR__ . '/../includes/auth.php';

requireAdmin('login.php');

$app_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($app_id <= 0) {
    $_SESSION['flash_error'] = "Invalid application ID specified.";
    header("Location: applications.php");
    exit;
}

$sql = "SELECT a.*, r.route_name, r.start_point, r.end_point, r.stops, r.fare,
               b.bus_number, b.bus_name, b.capacity AS bus_capacity,
               u.name AS student_name, u.email AS student_email,
               s.id AS student_table_id, s.student_id AS reg_no, s.phone, s.college, s.department, s.year, s.address
        FROM bus_pass_applications a
        JOIN students s ON a.student_id = s.id
        JOIN users u ON s.user_id = u.id
        JOIN routes r ON a.route_id = r.id
        JOIN buses b ON a.bus_id = b.id
        WHERE a.id = ? LIMIT 1";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $app_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$app = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$app) {
    $_SESSION['flash_error'] = "Application not found.";
    header("Location: applications.php");
    exit;
}

$page_title = "Application Details - #APP-" . str_pad($app['id'], 4, '0', STR_PAD_LEFT);

include_once __DIR__ . '/includes/header.php';
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-2 pb-3 mb-4 border-bottom">
    <div>
        <h1 class="h3 fw-bold text-dark mb-0">Application Details #APP-<?php echo str_pad($app['id'], 4, '0', STR_PAD_LEFT); ?></h1>
        <small class="text-muted">Review student information, route selection, and pass validity</small>
    </div>
    <div>
        <a href="applications.php" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Back to Applications
        </a>
    </div>
</div>

<div class="row g-4">
    <!-- Student & Route Details -->
    <div class="col-lg-8">
        <!-- Student Information -->
        <div class="card card-custom border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="fw-bold mb-0 text-primary">
                    <i class="bi bi-person-badge me-2"></i>Applicant Details
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="text-muted small">Student Full Name</label>
                        <div class="fw-bold fs-6"><?php echo htmlspecialchars($app['student_name']); ?></div>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small">Student ID / Roll No</label>
                        <div class="fw-bold text-primary"><?php echo htmlspecialchars($app['reg_no']); ?></div>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small">Email Address</label>
                        <div><?php echo htmlspecialchars($app['student_email']); ?></div>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small">Phone Number</label>
                        <div class="fw-bold"><?php echo htmlspecialchars($app['phone']); ?></div>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small">College / Institution</label>
                        <div><?php echo htmlspecialchars($app['college']); ?></div>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small">Department & Year</label>
                        <div><?php echo htmlspecialchars($app['department']); ?> (<?php echo htmlspecialchars($app['year']); ?>)</div>
                    </div>
                    <div class="col-12">
                        <label class="text-muted small">Permanent Address</label>
                        <div><?php echo nl2br(htmlspecialchars($app['address'])); ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Route & Bus Assignment -->
        <div class="card card-custom border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="fw-bold mb-0 text-primary">
                    <i class="bi bi-bus-front me-2"></i>Requested Route & Bus
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="text-muted small">Route Name</label>
                        <div class="fw-bold text-dark fs-6"><?php echo htmlspecialchars($app['route_name']); ?></div>
                        <small class="text-muted"><?php echo htmlspecialchars($app['start_point']); ?> &rarr; <?php echo htmlspecialchars($app['end_point']); ?></small>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small">Route Stops</label>
                        <div class="small text-secondary"><?php echo htmlspecialchars($app['stops']); ?></div>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small">Assigned Bus</label>
                        <div class="fw-bold">
                            <span class="badge bg-primary px-2 py-1"><?php echo htmlspecialchars($app['bus_number']); ?></span>
                            <span class="ms-2"><?php echo htmlspecialchars($app['bus_name']); ?></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small">Route Fare Rate</label>
                        <div class="fw-bold text-success">₹<?php echo number_format($app['fare'], 2); ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Status & Decision Panel -->
    <div class="col-lg-4">
        <div class="card card-custom border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-info-circle me-2"></i>Pass Status
                </h5>
            </div>
            <div class="card-body">
                <div class="text-center py-3 mb-3 border-bottom">
                    <label class="text-muted small d-block mb-1">Current Status</label>
                    <?php if ($app['status'] === 'Approved'): ?>
                        <span class="badge badge-approved fs-6 px-3 py-2">
                            <i class="bi bi-check-circle-fill me-1"></i> APPROVED
                        </span>
                        <div class="small text-muted mt-2">
                            Approved on <?php echo date('d M Y, h:i A', strtotime($app['approved_at'])); ?>
                        </div>
                    <?php elseif ($app['status'] === 'Rejected'): ?>
                        <span class="badge badge-rejected fs-6 px-3 py-2">
                            <i class="bi bi-x-circle-fill me-1"></i> REJECTED
                        </span>
                    <?php else: ?>
                        <span class="badge badge-pending fs-6 px-3 py-2">
                            <i class="bi bi-hourglass-split me-1"></i> PENDING REVIEW
                        </span>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label class="text-muted small">Pass Duration Type</label>
                    <div class="fw-bold"><?php echo htmlspecialchars($app['pass_type']); ?> Pass</div>
                </div>

                <div class="mb-3">
                    <label class="text-muted small">Validity Period</label>
                    <div><strong>From:</strong> <?php echo date('d M Y', strtotime($app['start_date'])); ?></div>
                    <div><strong>To:</strong> <?php echo date('d M Y', strtotime($app['end_date'])); ?></div>
                </div>

                <div class="mb-3">
                    <label class="text-muted small">Application Submitted On</label>
                    <div><?php echo date('d M Y, h:i A', strtotime($app['application_date'])); ?></div>
                </div>

                <?php if (!empty($app['remarks'])): ?>
                    <div class="p-3 bg-light rounded border mb-3">
                        <label class="text-muted small fw-bold">Admin Remarks</label>
                        <p class="mb-0 text-dark small"><?php echo nl2br(htmlspecialchars($app['remarks'])); ?></p>
                    </div>
                <?php endif; ?>

                <!-- Action Forms -->
                <?php if ($app['status'] === 'Pending'): ?>
                    <hr>
                    <h6 class="fw-bold text-dark mb-3">Take Action:</h6>

                    <!-- Approve Form -->
                    <form action="approve_application.php" method="POST" class="mb-3">
                        <input type="hidden" name="id" value="<?php echo $app['id']; ?>">
                        <div class="mb-2">
                            <input type="text" name="remarks" class="form-control form-control-sm" placeholder="Optional approval remarks...">
                        </div>
                        <button type="submit" class="btn btn-success w-100 fw-bold confirm-action" data-confirm="Approve this application?">
                            <i class="bi bi-check-circle me-1"></i> Approve Bus Pass
                        </button>
                    </form>

                    <!-- Reject Form -->
                    <form action="reject_application.php" method="POST">
                        <input type="hidden" name="id" value="<?php echo $app['id']; ?>">
                        <div class="mb-2">
                            <input type="text" name="remarks" class="form-control form-control-sm" required placeholder="Reason for rejection (required)...">
                        </div>
                        <button type="submit" class="btn btn-outline-danger w-100 fw-semibold confirm-action" data-confirm="Are you sure you want to REJECT this application?">
                            <i class="bi bi-x-circle me-1"></i> Reject Application
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/includes/footer.php'; ?>
