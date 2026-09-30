<?php
/**
 * My Applications Page
 * Bus Pass Management System
 */
include_once __DIR__ . '/config/db.php';
include_once __DIR__ . '/includes/auth.php';

requireStudent();

$page_title = "My Bus Pass Applications";
$user_id = $_SESSION['user_id'];
$student = getLoggedInStudent($conn, $user_id);
$student_table_id = $student['student_table_id'] ?? 0;

$applications = [];

if ($student_table_id > 0) {
    $sql = "SELECT a.*, r.route_name, r.start_point, r.end_point, r.fare, b.bus_number, b.bus_name
            FROM bus_pass_applications a
            JOIN routes r ON a.route_id = r.id
            JOIN buses b ON a.bus_id = b.id
            WHERE a.student_id = ?
            ORDER BY a.id DESC";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $student_table_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    while ($row = mysqli_fetch_assoc($result)) {
        $applications[] = $row;
    }
    mysqli_stmt_close($stmt);
}

include_once __DIR__ . '/includes/header.php';
?>

<div class="container py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1 text-primary">
                <i class="bi bi-clock-history me-2"></i>My Applications
            </h3>
            <p class="text-muted mb-0">Track all your submitted bus pass requests and approval status</p>
        </div>
        <div>
            <a href="apply_pass.php" class="btn btn-primary fw-semibold shadow-sm">
                <i class="bi bi-plus-circle me-1"></i> New Application
            </a>
        </div>
    </div>

    <div class="card card-custom border-0 shadow-sm">
        <div class="card-body p-0">
            <?php if (!empty($applications)): ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">App ID</th>
                                <th>Route & Endpoints</th>
                                <th>Bus</th>
                                <th>Pass Type</th>
                                <th>Validity Period</th>
                                <th>Applied On</th>
                                <th>Status</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($applications as $app): ?>
                                <tr>
                                    <td class="ps-4 fw-bold text-primary">
                                        #APP-<?php echo str_pad($app['id'], 4, '0', STR_PAD_LEFT); ?>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark"><?php echo htmlspecialchars($app['route_name']); ?></div>
                                        <small class="text-muted">
                                            <?php echo htmlspecialchars($app['start_point']); ?> &rarr; <?php echo htmlspecialchars($app['end_point']); ?>
                                        </small>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border">
                                            <?php echo htmlspecialchars($app['bus_number']); ?>
                                        </span>
                                        <div class="small text-muted"><?php echo htmlspecialchars($app['bus_name']); ?></div>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary-subtle text-secondary border">
                                            <?php echo htmlspecialchars($app['pass_type']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="small">
                                            <strong>From:</strong> <?php echo date('d M Y', strtotime($app['start_date'])); ?><br>
                                            <strong>To:</strong> <?php echo date('d M Y', strtotime($app['end_date'])); ?>
                                        </div>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            <?php echo date('d M Y, h:i A', strtotime($app['application_date'])); ?>
                                        </small>
                                    </td>
                                    <td>
                                        <?php if ($app['status'] === 'Approved'): ?>
                                            <span class="badge badge-approved"><i class="bi bi-check-circle-fill me-1"></i>Approved</span>
                                        <?php elseif ($app['status'] === 'Rejected'): ?>
                                            <span class="badge badge-rejected"><i class="bi bi-x-circle-fill me-1"></i>Rejected</span>
                                        <?php else: ?>
                                            <span class="badge badge-pending"><i class="bi bi-hourglass-split me-1"></i>Pending</span>
                                        <?php endif; ?>

                                        <?php if (!empty($app['remarks'])): ?>
                                            <div class="small text-muted mt-1" style="max-width: 180px;">
                                                <em>"<?php echo htmlspecialchars($app['remarks']); ?>"</em>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end pe-4">
                                        <?php if ($app['status'] === 'Approved'): ?>
                                            <a href="my_pass.php?id=<?php echo $app['id']; ?>" class="btn btn-sm btn-success fw-semibold">
                                                <i class="bi bi-qr-code me-1"></i> View Pass
                                            </a>
                                        <?php elseif ($app['status'] === 'Rejected'): ?>
                                            <a href="apply_pass.php" class="btn btn-sm btn-outline-danger">
                                                <i class="bi bi-arrow-repeat me-1"></i> Re-apply
                                            </a>
                                        <?php else: ?>
                                            <button class="btn btn-sm btn-light border text-muted" disabled>
                                                <i class="bi bi-clock me-1"></i> In Review
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <div class="mb-3 text-secondary">
                        <i class="bi bi-file-earmark-text fs-1"></i>
                    </div>
                    <h5 class="fw-bold">No Bus Pass Applications Found</h5>
                    <p class="text-muted">You haven't submitted any bus pass applications yet.</p>
                    <a href="apply_pass.php" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i> Apply for Your First Bus Pass
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/includes/footer.php'; ?>
