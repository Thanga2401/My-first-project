<?php
/**
 * Admin Dashboard
 * Bus Pass Management System
 */
include_once __DIR__ . '/../config/db.php';
include_once __DIR__ . '/../includes/auth.php';

requireAdmin('login.php');

$page_title = "Admin Dashboard";

// 1. Total Students
$q_students = mysqli_query($conn, "SELECT COUNT(*) AS total FROM students");
$total_students = $q_students ? mysqli_fetch_assoc($q_students)['total'] : 0;

// 2. Application statistics
$q_apps = mysqli_query($conn, "SELECT 
            COUNT(*) AS total,
            SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) AS pending,
            SUM(CASE WHEN status = 'Approved' THEN 1 ELSE 0 END) AS approved,
            SUM(CASE WHEN status = 'Rejected' THEN 1 ELSE 0 END) AS rejected
          FROM bus_pass_applications");
$app_stats = $q_apps ? mysqli_fetch_assoc($q_apps) : ['total' => 0, 'pending' => 0, 'approved' => 0, 'rejected' => 0];

// 3. Total Buses
$q_buses = mysqli_query($conn, "SELECT COUNT(*) AS total FROM buses");
$total_buses = $q_buses ? mysqli_fetch_assoc($q_buses)['total'] : 0;

// 4. Total Routes
$q_routes = mysqli_query($conn, "SELECT COUNT(*) AS total FROM routes");
$total_routes = $q_routes ? mysqli_fetch_assoc($q_routes)['total'] : 0;

// 5. Recent 5 Pending/Latest Applications
$recent_apps = [];
$q_recent = mysqli_query($conn, "SELECT a.*, r.route_name, b.bus_number, u.name AS student_name, s.student_id AS reg_no
                                 FROM bus_pass_applications a
                                 JOIN students s ON a.student_id = s.id
                                 JOIN users u ON s.user_id = u.id
                                 JOIN routes r ON a.route_id = r.id
                                 JOIN buses b ON a.bus_id = b.id
                                 ORDER BY (a.status = 'Pending') DESC, a.id DESC LIMIT 5");
if ($q_recent) {
    while ($r = mysqli_fetch_assoc($q_recent)) {
        $recent_apps[] = $r;
    }
}

include_once __DIR__ . '/includes/header.php';
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-2 pb-3 mb-4 border-bottom">
    <div>
        <h1 class="h3 fw-bold text-dark mb-0">Administrator Overview</h1>
        <small class="text-muted">Real-time system health and bus pass applications monitoring</small>
    </div>
    <div class="btn-toolbar mb-2 mb-md-0 gap-2">
        <a href="applications.php" class="btn btn-sm btn-primary">
            <i class="bi bi-list-check me-1"></i> View All Applications
        </a>
        <a href="add_bus.php" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-plus-lg me-1"></i> Add Bus
        </a>
        <a href="add_route.php" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-plus-lg me-1"></i> Add Route
        </a>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row g-3 mb-4">
    <!-- Total Students -->
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card stat-card-blue shadow-sm">
            <div class="stat-icon"><i class="bi bi-people-fill"></i></div>
            <h6 class="text-uppercase fw-semibold small opacity-75">Registered Students</h6>
            <h2 class="fw-bold mb-0"><?php echo $total_students; ?></h2>
            <small class="opacity-75"><a href="students.php" class="text-white text-decoration-underline">Manage students &rarr;</a></small>
        </div>
    </div>

    <!-- Pending Applications -->
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card stat-card-amber shadow-sm">
            <div class="stat-icon"><i class="bi bi-hourglass-split"></i></div>
            <h6 class="text-uppercase fw-semibold small opacity-75">Pending Reviews</h6>
            <h2 class="fw-bold mb-0"><?php echo (int)($app_stats['pending'] ?? 0); ?></h2>
            <small class="opacity-75"><a href="applications.php?status=Pending" class="text-white text-decoration-underline">Review pending &rarr;</a></small>
        </div>
    </div>

    <!-- Approved Applications -->
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card stat-card-green shadow-sm">
            <div class="stat-icon"><i class="bi bi-check2-circle"></i></div>
            <h6 class="text-uppercase fw-semibold small opacity-75">Approved Passes</h6>
            <h2 class="fw-bold mb-0"><?php echo (int)($app_stats['approved'] ?? 0); ?></h2>
            <small class="opacity-75"><a href="applications.php?status=Approved" class="text-white text-decoration-underline">View approved &rarr;</a></small>
        </div>
    </div>

    <!-- Rejected Applications -->
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card stat-card-red shadow-sm">
            <div class="stat-icon"><i class="bi bi-x-circle"></i></div>
            <h6 class="text-uppercase fw-semibold small opacity-75">Rejected Passes</h6>
            <h2 class="fw-bold mb-0"><?php echo (int)($app_stats['rejected'] ?? 0); ?></h2>
            <small class="opacity-75"><a href="applications.php?status=Rejected" class="text-white text-decoration-underline">View rejected &rarr;</a></small>
        </div>
    </div>

    <!-- Total Buses -->
    <div class="col-sm-6 col-xl-6">
        <div class="stat-card stat-card-purple shadow-sm">
            <div class="stat-icon"><i class="bi bi-bus-front"></i></div>
            <h6 class="text-uppercase fw-semibold small opacity-75">Fleet Buses</h6>
            <h2 class="fw-bold mb-0"><?php echo $total_buses; ?></h2>
            <small class="opacity-75"><a href="buses.php" class="text-white text-decoration-underline">Manage fleet buses &rarr;</a></small>
        </div>
    </div>

    <!-- Total Routes -->
    <div class="col-sm-6 col-xl-6">
        <div class="stat-card stat-card-teal shadow-sm">
            <div class="stat-icon"><i class="bi bi-signpost-2"></i></div>
            <h6 class="text-uppercase fw-semibold small opacity-75">Active Transit Routes</h6>
            <h2 class="fw-bold mb-0"><?php echo $total_routes; ?></h2>
            <small class="opacity-75"><a href="routes.php" class="text-white text-decoration-underline">Manage transit routes &rarr;</a></small>
        </div>
    </div>
</div>

<!-- Recent Applications Table Section -->
<div class="card card-custom border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
        <h5 class="card-title fw-bold mb-0 text-dark">
            <i class="bi bi-file-earmark-text me-2 text-primary"></i>Recent Bus Pass Applications
        </h5>
        <a href="applications.php" class="btn btn-sm btn-outline-primary">View All</a>
    </div>
    <div class="card-body p-0">
        <?php if (!empty($recent_apps)): ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">App ID</th>
                            <th>Student</th>
                            <th>Route</th>
                            <th>Bus</th>
                            <th>Pass Type</th>
                            <th>Applied Date</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_apps as $app): ?>
                            <tr>
                                <td class="ps-4 fw-bold">#APP-<?php echo str_pad($app['id'], 4, '0', STR_PAD_LEFT); ?></td>
                                <td>
                                    <div class="fw-bold"><?php echo htmlspecialchars($app['student_name']); ?></div>
                                    <small class="text-muted"><?php echo htmlspecialchars($app['reg_no']); ?></small>
                                </td>
                                <td><?php echo htmlspecialchars($app['route_name']); ?></td>
                                <td><span class="badge bg-light text-dark border"><?php echo htmlspecialchars($app['bus_number']); ?></span></td>
                                <td><?php echo htmlspecialchars($app['pass_type']); ?></td>
                                <td><small class="text-muted"><?php echo date('d M Y', strtotime($app['application_date'])); ?></small></td>
                                <td>
                                    <?php if ($app['status'] === 'Approved'): ?>
                                        <span class="badge badge-approved">Approved</span>
                                    <?php elseif ($app['status'] === 'Rejected'): ?>
                                        <span class="badge badge-rejected">Rejected</span>
                                    <?php else: ?>
                                        <span class="badge badge-pending">Pending</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end pe-4">
                                    <a href="view_application.php?id=<?php echo $app['id']; ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i> View
                                    </a>
                                    <?php if ($app['status'] === 'Pending'): ?>
                                        <a href="approve_application.php?id=<?php echo $app['id']; ?>" class="btn btn-sm btn-success confirm-action" data-confirm="Approve this application?">
                                            <i class="bi bi-check"></i>
                                        </a>
                                        <a href="reject_application.php?id=<?php echo $app['id']; ?>" class="btn btn-sm btn-danger">
                                            <i class="bi bi-x"></i>
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
                <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                No bus pass applications received yet.
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include_once __DIR__ . '/includes/footer.php'; ?>
