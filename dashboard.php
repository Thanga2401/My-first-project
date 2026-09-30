<?php
/**
 * Student Dashboard
 * Bus Pass Management System
 */
include_once __DIR__ . '/config/db.php';
include_once __DIR__ . '/includes/auth.php';

requireStudent();

$page_title = "Student Dashboard";
$user_id = $_SESSION['user_id'];

// Get full student details
$student = getLoggedInStudent($conn, $user_id);
$student_table_id = $student['student_table_id'] ?? 0;

// Fetch Application Counts for this student
$counts = [
    'total' => 0,
    'pending' => 0,
    'approved' => 0,
    'rejected' => 0
];

if ($student_table_id > 0) {
    $count_sql = "SELECT 
                    COUNT(*) AS total,
                    SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) AS pending,
                    SUM(CASE WHEN status = 'Approved' THEN 1 ELSE 0 END) AS approved,
                    SUM(CASE WHEN status = 'Rejected' THEN 1 ELSE 0 END) AS rejected
                  FROM bus_pass_applications 
                  WHERE student_id = ?";
    $stmt = mysqli_prepare($conn, $count_sql);
    mysqli_stmt_bind_param($stmt, "i", $student_table_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($row = mysqli_fetch_assoc($result)) {
        $counts['total'] = (int)($row['total'] ?? 0);
        $counts['pending'] = (int)($row['pending'] ?? 0);
        $counts['approved'] = (int)($row['approved'] ?? 0);
        $counts['rejected'] = (int)($row['rejected'] ?? 0);
    }
    mysqli_stmt_close($stmt);
}

// Fetch recent 3 applications
$recent_apps = [];
if ($student_table_id > 0) {
    $recent_sql = "SELECT a.*, r.route_name, b.bus_number 
                   FROM bus_pass_applications a
                   JOIN routes r ON a.route_id = r.id
                   JOIN buses b ON a.bus_id = b.id
                   WHERE a.student_id = ?
                   ORDER BY a.id DESC LIMIT 3";
    $stmt = mysqli_prepare($conn, $recent_sql);
    mysqli_stmt_bind_param($stmt, "i", $student_table_id);
    mysqli_stmt_execute($stmt);
    $recent_result = mysqli_stmt_get_result($stmt);
    while ($r = mysqli_fetch_assoc($recent_result)) {
        $recent_apps[] = $r;
    }
    mysqli_stmt_close($stmt);
}

include_once __DIR__ . '/includes/header.php';
?>

<div class="container py-4">
    <!-- Welcome Header Banner -->
    <div class="card card-custom border-0 bg-primary text-white p-4 mb-4 rounded-4 shadow">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <span class="badge bg-warning text-dark px-3 py-1 rounded-pill fw-bold mb-2">Student Portal</span>
                <h2 class="fw-bold mb-1">Welcome back, <?php echo htmlspecialchars($student['name'] ?? $_SESSION['user_name']); ?>!</h2>
                <p class="text-light mb-0">
                    <i class="bi bi-mortarboard me-1"></i> <?php echo htmlspecialchars($student['college'] ?? 'Student'); ?> &bull; 
                    <span class="badge bg-light text-primary fw-semibold"><?php echo htmlspecialchars($student['student_id'] ?? 'N/A'); ?></span>
                </p>
            </div>
            <div>
                <a href="apply_pass.php" class="btn btn-warning fw-bold text-dark px-4 py-2 shadow-sm">
                    <i class="bi bi-plus-circle me-1"></i> Apply for Bus Pass
                </a>
            </div>
        </div>
    </div>

    <!-- Application Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="stat-card stat-card-blue shadow-sm">
                <div class="stat-icon"><i class="bi bi-file-earmark-text"></i></div>
                <h6 class="text-uppercase fw-semibold small opacity-75">Total Applications</h6>
                <h2 class="fw-bold mb-0"><?php echo $counts['total']; ?></h2>
                <small class="opacity-75">All-time submissions</small>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="stat-card stat-card-amber shadow-sm">
                <div class="stat-icon"><i class="bi bi-hourglass-split"></i></div>
                <h6 class="text-uppercase fw-semibold small opacity-75">Pending Reviews</h6>
                <h2 class="fw-bold mb-0"><?php echo $counts['pending']; ?></h2>
                <small class="opacity-75">Awaiting verification</small>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="stat-card stat-card-green shadow-sm">
                <div class="stat-icon"><i class="bi bi-check-circle"></i></div>
                <h6 class="text-uppercase fw-semibold small opacity-75">Approved Passes</h6>
                <h2 class="fw-bold mb-0"><?php echo $counts['approved']; ?></h2>
                <small class="opacity-75">Ready to use / print</small>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="stat-card stat-card-red shadow-sm">
                <div class="stat-icon"><i class="bi bi-x-circle"></i></div>
                <h6 class="text-uppercase fw-semibold small opacity-75">Rejected Passes</h6>
                <h2 class="fw-bold mb-0"><?php echo $counts['rejected']; ?></h2>
                <small class="opacity-75">Requires re-application</small>
            </div>
        </div>
    </div>

    <!-- Main Grid: Info + Quick Actions + Recent Activity -->
    <div class="row g-4">
        <!-- Student Information Card -->
        <div class="col-lg-4">
            <div class="card card-custom border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="card-title fw-bold mb-0 text-primary">
                        <i class="bi bi-person-lines-fill me-2"></i>Profile Information
                    </h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item px-0 d-flex justify-content-between">
                            <span class="text-muted">Student ID:</span>
                            <span class="fw-bold"><?php echo htmlspecialchars($student['student_id'] ?? 'N/A'); ?></span>
                        </li>
                        <li class="list-group-item px-0 d-flex justify-content-between">
                            <span class="text-muted">Department:</span>
                            <span class="fw-bold"><?php echo htmlspecialchars($student['department'] ?? 'N/A'); ?></span>
                        </li>
                        <li class="list-group-item px-0 d-flex justify-content-between">
                            <span class="text-muted">Year:</span>
                            <span class="fw-bold"><?php echo htmlspecialchars($student['year'] ?? 'N/A'); ?></span>
                        </li>
                        <li class="list-group-item px-0 d-flex justify-content-between">
                            <span class="text-muted">Phone:</span>
                            <span class="fw-bold"><?php echo htmlspecialchars($student['phone'] ?? 'N/A'); ?></span>
                        </li>
                        <li class="list-group-item px-0 d-flex justify-content-between">
                            <span class="text-muted">Email:</span>
                            <span class="fw-bold"><?php echo htmlspecialchars($student['email'] ?? 'N/A'); ?></span>
                        </li>
                    </ul>
                    <div class="mt-3">
                        <a href="profile.php" class="btn btn-outline-primary btn-sm w-100">
                            <i class="bi bi-pencil me-1"></i> Edit Profile Details
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions & Recent Applications -->
        <div class="col-lg-8">
            <!-- Quick Actions Bar -->
            <div class="card card-custom border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="card-title fw-bold mb-0 text-dark">
                        <i class="bi bi-lightning-charge me-2 text-warning"></i>Quick Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-sm-6 col-md-3">
                            <a href="apply_pass.php" class="btn btn-primary w-100 py-3 d-flex flex-column align-items-center gap-1 shadow-sm">
                                <i class="bi bi-plus-circle fs-4"></i>
                                <span class="small fw-semibold">Apply Pass</span>
                            </a>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <a href="my_applications.php" class="btn btn-outline-primary w-100 py-3 d-flex flex-column align-items-center gap-1">
                                <i class="bi bi-list-ul fs-4"></i>
                                <span class="small fw-semibold">My Applications</span>
                            </a>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <a href="my_pass.php" class="btn btn-outline-success w-100 py-3 d-flex flex-column align-items-center gap-1">
                                <i class="bi bi-qr-code fs-4"></i>
                                <span class="small fw-semibold">View Digital Pass</span>
                            </a>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <a href="profile.php" class="btn btn-outline-secondary w-100 py-3 d-flex flex-column align-items-center gap-1">
                                <i class="bi bi-person-gear fs-4"></i>
                                <span class="small fw-semibold">My Profile</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Applications Table -->
            <div class="card card-custom border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                    <h5 class="card-title fw-bold mb-0 text-dark">
                        <i class="bi bi-clock-history me-2 text-primary"></i>Recent Applications
                    </h5>
                    <a href="my_applications.php" class="text-primary small fw-semibold text-decoration-none">View All &rarr;</a>
                </div>
                <div class="card-body p-0">
                    <?php if (!empty($recent_apps)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>App ID</th>
                                        <th>Route</th>
                                        <th>Bus</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recent_apps as $app): ?>
                                        <tr>
                                            <td class="fw-bold">#APP-<?php echo str_pad($app['id'], 4, '0', STR_PAD_LEFT); ?></td>
                                            <td><?php echo htmlspecialchars($app['route_name']); ?></td>
                                            <td><span class="badge bg-light text-dark border"><?php echo htmlspecialchars($app['bus_number']); ?></span></td>
                                            <td><?php echo htmlspecialchars($app['pass_type']); ?></td>
                                            <td>
                                                <?php if ($app['status'] === 'Approved'): ?>
                                                    <span class="badge badge-approved"><i class="bi bi-check-circle me-1"></i>Approved</span>
                                                <?php elseif ($app['status'] === 'Rejected'): ?>
                                                    <span class="badge badge-rejected"><i class="bi bi-x-circle me-1"></i>Rejected</span>
                                                <?php else: ?>
                                                    <span class="badge badge-pending"><i class="bi bi-hourglass-split me-1"></i>Pending</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($app['status'] === 'Approved'): ?>
                                                    <a href="my_pass.php?id=<?php echo $app['id']; ?>" class="btn btn-sm btn-success">
                                                        <i class="bi bi-eye me-1"></i> Pass
                                                    </a>
                                                <?php else: ?>
                                                    <a href="my_applications.php" class="btn btn-sm btn-outline-secondary">
                                                        <i class="bi bi-info-circle me-1"></i> Details
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-inbox fs-2 d-block mb-2 text-secondary"></i>
                            <p class="mb-2">You haven't submitted any bus pass applications yet.</p>
                            <a href="apply_pass.php" class="btn btn-primary btn-sm">Apply for your first pass</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>
</div>

<?php include_once __DIR__ . '/includes/footer.php'; ?>
