<?php
/**
 * Admin - View Student Details
 * Bus Pass Management System
 */
include_once __DIR__ . '/../config/db.php';
include_once __DIR__ . '/../includes/auth.php';

requireAdmin('login.php');

$student_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($student_id <= 0) {
    $_SESSION['flash_error'] = "Invalid student ID specified.";
    header("Location: students.php");
    exit;
}

// Fetch student & user details
$sql = "SELECT s.*, u.name, u.email, u.created_at AS user_registered_on
        FROM students s
        JOIN users u ON s.user_id = u.id
        WHERE s.id = ? LIMIT 1";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $student_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$student = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$student) {
    $_SESSION['flash_error'] = "Student record not found.";
    header("Location: students.php");
    exit;
}

$page_title = "Student Details - " . $student['name'];

// Fetch application history for this student
$apps = [];
$app_sql = "SELECT a.*, r.route_name, b.bus_number 
            FROM bus_pass_applications a
            JOIN routes r ON a.route_id = r.id
            JOIN buses b ON a.bus_id = b.id
            WHERE a.student_id = ?
            ORDER BY a.id DESC";
$app_stmt = mysqli_prepare($conn, $app_sql);
mysqli_stmt_bind_param($app_stmt, "i", $student_id);
mysqli_stmt_execute($app_stmt);
$app_result = mysqli_stmt_get_result($app_stmt);
while ($row = mysqli_fetch_assoc($app_result)) {
    $apps[] = $row;
}
mysqli_stmt_close($app_stmt);

include_once __DIR__ . '/includes/header.php';
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-2 pb-3 mb-4 border-bottom">
    <div>
        <h1 class="h3 fw-bold text-dark mb-0">Student Profile & History</h1>
        <small class="text-muted">Viewing student file for <?php echo htmlspecialchars($student['name']); ?></small>
    </div>
    <div>
        <a href="students.php" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Back to Student List
        </a>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Student Details Card -->
    <div class="col-lg-5">
        <div class="card card-custom border-0 shadow-sm h-100">
            <div class="card-header bg-primary text-white py-3">
                <div class="d-flex align-items-center justify-content-between">
                    <h5 class="fw-bold mb-0"><i class="bi bi-person-badge me-2"></i>Personal & Academic Info</h5>
                    <span class="badge bg-warning text-dark"><?php echo htmlspecialchars($student['student_id']); ?></span>
                </div>
            </div>
            <div class="card-body">
                <div class="text-center py-3 mb-3 border-bottom">
                    <div class="bg-primary-subtle text-primary p-3 rounded-circle d-inline-block mb-2">
                        <i class="bi bi-person-fill fs-1"></i>
                    </div>
                    <h4 class="fw-bold mb-0"><?php echo htmlspecialchars($student['name']); ?></h4>
                    <p class="text-muted small mb-0"><?php echo htmlspecialchars($student['email']); ?></p>
                </div>

                <table class="table table-borderless small mb-0">
                    <tbody>
                        <tr>
                            <th class="text-muted" style="width: 40%;">Student ID:</th>
                            <td class="fw-bold"><?php echo htmlspecialchars($student['student_id']); ?></td>
                        </tr>
                        <tr>
                            <th class="text-muted">Phone:</th>
                            <td class="fw-bold"><?php echo htmlspecialchars($student['phone']); ?></td>
                        </tr>
                        <tr>
                            <th class="text-muted">College:</th>
                            <td class="fw-bold"><?php echo htmlspecialchars($student['college']); ?></td>
                        </tr>
                        <tr>
                            <th class="text-muted">Department:</th>
                            <td class="fw-bold"><?php echo htmlspecialchars($student['department']); ?></td>
                        </tr>
                        <tr>
                            <th class="text-muted">Year of Study:</th>
                            <td class="fw-bold"><?php echo htmlspecialchars($student['year']); ?></td>
                        </tr>
                        <tr>
                            <th class="text-muted">Residential Address:</th>
                            <td class="fw-bold"><?php echo nl2br(htmlspecialchars($student['address'])); ?></td>
                        </tr>
                        <tr>
                            <th class="text-muted">Registered On:</th>
                            <td><?php echo date('d M Y, h:i A', strtotime($student['user_registered_on'])); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Application History -->
    <div class="col-lg-7">
        <div class="card card-custom border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-clock-history me-2 text-primary"></i>Bus Pass Applications History
                </h5>
                <span class="badge bg-primary"><?php echo count($apps); ?> Record(s)</span>
            </div>
            <div class="card-body p-0">
                <?php if (!empty($apps)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 small">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">App ID</th>
                                    <th>Route</th>
                                    <th>Bus</th>
                                    <th>Pass Type</th>
                                    <th>Validity</th>
                                    <th>Status</th>
                                    <th class="text-end pe-3">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($apps as $a): ?>
                                    <tr>
                                        <td class="ps-3 fw-bold">#APP-<?php echo str_pad($a['id'], 4, '0', STR_PAD_LEFT); ?></td>
                                        <td><?php echo htmlspecialchars($a['route_name']); ?></td>
                                        <td><span class="badge bg-light text-dark border"><?php echo htmlspecialchars($a['bus_number']); ?></span></td>
                                        <td><?php echo htmlspecialchars($a['pass_type']); ?></td>
                                        <td>
                                            <?php echo date('d M y', strtotime($a['start_date'])); ?> - <?php echo date('d M y', strtotime($a['end_date'])); ?>
                                        </td>
                                        <td>
                                            <?php if ($a['status'] === 'Approved'): ?>
                                                <span class="badge badge-approved">Approved</span>
                                            <?php elseif ($a['status'] === 'Rejected'): ?>
                                                <span class="badge badge-rejected">Rejected</span>
                                            <?php else: ?>
                                                <span class="badge badge-pending">Pending</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end pe-3">
                                            <a href="view_application.php?id=<?php echo $a['id']; ?>" class="btn btn-sm btn-outline-primary py-0">
                                                View
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                        This student has not submitted any pass applications yet.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/includes/footer.php'; ?>
