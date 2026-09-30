<?php
/**
 * Admin - Applications Management
 * Bus Pass Management System
 */
include_once __DIR__ . '/../config/db.php';
include_once __DIR__ . '/../includes/auth.php';

requireAdmin('login.php');

$page_title = "Manage Applications";
$status_filter = trim($_GET['status'] ?? '');
$search = trim($_GET['search'] ?? '');

$sql = "SELECT a.*, r.route_name, r.start_point, r.end_point, b.bus_number, b.bus_name,
               u.name AS student_name, s.student_id AS reg_no, s.phone, s.college
        FROM bus_pass_applications a
        JOIN students s ON a.student_id = s.id
        JOIN users u ON s.user_id = u.id
        JOIN routes r ON a.route_id = r.id
        JOIN buses b ON a.bus_id = b.id
        WHERE 1=1 ";

$params = [];
$types = "";

if (!empty($status_filter) && in_array($status_filter, ['Pending', 'Approved', 'Rejected'])) {
    $sql .= " AND a.status = ? ";
    $params[] = $status_filter;
    $types .= "s";
}

if (!empty($search)) {
    $sql .= " AND (u.name LIKE ? OR s.student_id LIKE ? OR r.route_name LIKE ? OR b.bus_number LIKE ?) ";
    $search_param = "%" . $search . "%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= "ssss";
}

$sql .= " ORDER BY (a.status = 'Pending') DESC, a.id DESC";

$stmt = mysqli_prepare($conn, $sql);
if (!empty($params)) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$applications = [];
while ($row = mysqli_fetch_assoc($result)) {
    $applications[] = $row;
}
mysqli_stmt_close($stmt);

include_once __DIR__ . '/includes/header.php';
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-2 pb-3 mb-4 border-bottom">
    <div>
        <h1 class="h3 fw-bold text-dark mb-0">Bus Pass Applications</h1>
        <small class="text-muted">Review, approve, or reject student pass applications</small>
    </div>
</div>

<!-- Filter and Search Bar -->
<div class="card card-custom border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <form method="GET" action="applications.php" class="row g-2 align-items-center">
            <div class="col-md-3">
                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="" <?php echo empty($status_filter) ? 'selected' : ''; ?>>All Statuses</option>
                    <option value="Pending" <?php echo ($status_filter === 'Pending') ? 'selected' : ''; ?>>Pending Only</option>
                    <option value="Approved" <?php echo ($status_filter === 'Approved') ? 'selected' : ''; ?>>Approved Only</option>
                    <option value="Rejected" <?php echo ($status_filter === 'Rejected') ? 'selected' : ''; ?>>Rejected Only</option>
                </select>
            </div>
            <div class="col-md-6">
                <div class="input-group input-group-sm">
                    <input type="text" name="search" class="form-control" placeholder="Search by student name, ID, route, bus..." value="<?php echo htmlspecialchars($search); ?>">
                    <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i> Search</button>
                    <?php if (!empty($search) || !empty($status_filter)): ?>
                        <a href="applications.php" class="btn btn-outline-secondary" title="Reset filters"><i class="bi bi-x-circle"></i> Clear</a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-md-3 text-md-end text-muted small">
                Showing <strong><?php echo count($applications); ?></strong> application(s)
            </div>
        </form>
    </div>
</div>

<!-- Applications Table -->
<div class="card card-custom border-0 shadow-sm">
    <div class="card-body p-0">
        <?php if (!empty($applications)): ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">App ID</th>
                            <th>Student Details</th>
                            <th>Route</th>
                            <th>Bus</th>
                            <th>Pass Type</th>
                            <th>Validity Period</th>
                            <th>Applied Date</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($applications as $app): ?>
                            <tr>
                                <td class="ps-4 fw-bold">#APP-<?php echo str_pad($app['id'], 4, '0', STR_PAD_LEFT); ?></td>
                                <td>
                                    <div class="fw-bold text-dark"><?php echo htmlspecialchars($app['student_name']); ?></div>
                                    <div class="small text-muted">
                                        ID: <span class="fw-semibold text-primary"><?php echo htmlspecialchars($app['reg_no']); ?></span> &bull; 
                                        Ph: <?php echo htmlspecialchars($app['phone']); ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-semibold"><?php echo htmlspecialchars($app['route_name']); ?></div>
                                    <small class="text-muted"><?php echo htmlspecialchars($app['start_point']); ?> &rarr; <?php echo htmlspecialchars($app['end_point']); ?></small>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border"><?php echo htmlspecialchars($app['bus_number']); ?></span>
                                </td>
                                <td>
                                    <span class="badge bg-secondary-subtle text-secondary border"><?php echo htmlspecialchars($app['pass_type']); ?></span>
                                </td>
                                <td>
                                    <small class="d-block text-muted">
                                        <?php echo date('d M Y', strtotime($app['start_date'])); ?> to <br>
                                        <?php echo date('d M Y', strtotime($app['end_date'])); ?>
                                    </small>
                                </td>
                                <td><small class="text-muted"><?php echo date('d M Y', strtotime($app['application_date'])); ?></small></td>
                                <td>
                                    <?php if ($app['status'] === 'Approved'): ?>
                                        <span class="badge badge-approved"><i class="bi bi-check-circle-fill me-1"></i>Approved</span>
                                    <?php elseif ($app['status'] === 'Rejected'): ?>
                                        <span class="badge badge-rejected"><i class="bi bi-x-circle-fill me-1"></i>Rejected</span>
                                    <?php else: ?>
                                        <span class="badge badge-pending"><i class="bi bi-hourglass-split me-1"></i>Pending</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="btn-group btn-group-sm">
                                        <a href="view_application.php?id=<?php echo $app['id']; ?>" class="btn btn-outline-primary" title="View Full Details">
                                            <i class="bi bi-eye"></i> View
                                        </a>
                                        <?php if ($app['status'] === 'Pending'): ?>
                                            <a href="approve_application.php?id=<?php echo $app['id']; ?>" class="btn btn-success confirm-action" data-confirm="Are you sure you want to APPROVE application #APP-<?php echo $app['id']; ?>?" title="Quick Approve">
                                                <i class="bi bi-check-lg"></i>
                                            </a>
                                            <a href="reject_application.php?id=<?php echo $app['id']; ?>" class="btn btn-danger" title="Reject Application">
                                                <i class="bi bi-x-lg"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="text-center py-5 text-muted">
                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                No bus pass applications match your selected criteria.
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include_once __DIR__ . '/includes/footer.php'; ?>
