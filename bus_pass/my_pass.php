<?php
/**
 * Digital Bus Pass Viewer & Print
 * Bus Pass Management System
 */
include_once __DIR__ . '/config/db.php';
include_once __DIR__ . '/includes/auth.php';

requireStudent();

$page_title = "My Digital Bus Pass";
$user_id = $_SESSION['user_id'];
$student = getLoggedInStudent($conn, $user_id);
$student_table_id = $student['student_table_id'] ?? 0;

$pass_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$pass = null;

if ($student_table_id > 0) {
    if ($pass_id > 0) {
        // Fetch specific approved pass
        $sql = "SELECT a.*, r.route_name, r.start_point, r.end_point, r.stops, r.fare, b.bus_number, b.bus_name,
                       u.name AS student_name, s.student_id AS reg_no, s.college, s.department, s.year, s.phone
                FROM bus_pass_applications a
                JOIN students s ON a.student_id = s.id
                JOIN users u ON s.user_id = u.id
                JOIN routes r ON a.route_id = r.id
                JOIN buses b ON a.bus_id = b.id
                WHERE a.id = ? AND a.student_id = ? AND a.status = 'Approved' LIMIT 1";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $pass_id, $student_table_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $pass = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
    } else {
        // Fetch latest approved pass
        $sql = "SELECT a.*, r.route_name, r.start_point, r.end_point, r.stops, r.fare, b.bus_number, b.bus_name,
                       u.name AS student_name, s.student_id AS reg_no, s.college, s.department, s.year, s.phone
                FROM bus_pass_applications a
                JOIN students s ON a.student_id = s.id
                JOIN users u ON s.user_id = u.id
                JOIN routes r ON a.route_id = r.id
                JOIN buses b ON a.bus_id = b.id
                WHERE a.student_id = ? AND a.status = 'Approved' 
                ORDER BY a.id DESC LIMIT 1";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $student_table_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $pass = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
    }
}

// Fetch list of all approved passes for quick selector if multiple exist
$all_approved = [];
if ($student_table_id > 0) {
    $all_sql = "SELECT a.id, a.pass_type, a.start_date, a.end_date, r.route_name 
                FROM bus_pass_applications a
                JOIN routes r ON a.route_id = r.id
                WHERE a.student_id = ? AND a.status = 'Approved' 
                ORDER BY a.id DESC";
    $stmt_all = mysqli_prepare($conn, $all_sql);
    mysqli_stmt_bind_param($stmt_all, "i", $student_table_id);
    mysqli_stmt_execute($stmt_all);
    $res_all = mysqli_stmt_get_result($stmt_all);
    while ($row = mysqli_fetch_assoc($res_all)) {
        $all_approved[] = $row;
    }
    mysqli_stmt_close($stmt_all);
}

include_once __DIR__ . '/includes/header.php';
?>

<div class="container py-4">
    <div class="no-print d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1 text-primary">
                <i class="bi bi-qr-code me-2"></i>Digital Bus Pass
            </h3>
            <p class="text-muted mb-0">Official institutional digital transit identifier</p>
        </div>

        <?php if ($pass): ?>
            <div class="d-flex gap-2">
                <?php if (count($all_approved) > 1): ?>
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Select Pass
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow">
                            <?php foreach ($all_approved as $ap): ?>
                                <li>
                                    <a class="dropdown-item <?php echo ($pass['id'] == $ap['id']) ? 'active' : ''; ?>" href="my_pass.php?id=<?php echo $ap['id']; ?>">
                                        #PASS-<?php echo str_pad($ap['id'], 4, '0', STR_PAD_LEFT); ?> (<?php echo htmlspecialchars($ap['route_name']); ?>)
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <button onclick="window.print()" class="btn btn-primary fw-bold shadow-sm">
                    <i class="bi bi-printer-fill me-1"></i> Print Pass
                </button>
                <a href="dashboard.php" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Dashboard
                </a>
            </div>
        <?php endif; ?>
    </div>

    <?php if ($pass): ?>
        <div class="digital-pass-container">
            <div class="digital-pass">
                <!-- Pass Header -->
                <div class="pass-header">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-bus-front-fill fs-2 text-warning"></i>
                        <div>
                            <h5 class="fw-bold mb-0 text-white">BUS PASS MANAGEMENT SYSTEM</h5>
                            <small class="text-light opacity-75">Institutional Student Transit Pass</small>
                        </div>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-success text-white px-3 py-2 fw-bold text-uppercase border">
                            <i class="bi bi-shield-check me-1"></i> APPROVED
                        </span>
                    </div>
                </div>

                <!-- Pass Body -->
                <div class="pass-body">
                    <div class="pass-watermark">VERIFIED</div>

                    <div class="row align-items-center mb-4 pb-3 border-bottom">
                        <div class="col-8">
                            <h4 class="fw-bold text-dark mb-1"><?php echo htmlspecialchars($pass['student_name']); ?></h4>
                            <div class="text-primary fw-semibold">
                                <i class="bi bi-mortarboard-fill me-1"></i> Student ID: <?php echo htmlspecialchars($pass['reg_no']); ?>
                            </div>
                            <div class="text-muted small">
                                <?php echo htmlspecialchars($pass['college']); ?> &bull; <?php echo htmlspecialchars($pass['department']); ?> (<?php echo htmlspecialchars($pass['year']); ?>)
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="p-2 border rounded bg-light d-inline-block text-center">
                                <i class="bi bi-qr-code fs-1 text-dark"></i>
                                <div class="font-monospace small" style="font-size: 0.75rem;">PASS-<?php echo str_pad($pass['id'], 5, '0', STR_PAD_LEFT); ?></div>
                            </div>
                        </div>
                    </div>

                    <!-- Pass Details Table -->
                    <table class="table table-borderless pass-table mb-0">
                        <tbody>
                            <tr>
                                <th>Assigned Route:</th>
                                <td>
                                    <span class="text-primary"><?php echo htmlspecialchars($pass['route_name']); ?></span>
                                    <div class="small text-muted fw-normal">
                                        <?php echo htmlspecialchars($pass['start_point']); ?> &rarr; <?php echo htmlspecialchars($pass['end_point']); ?>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th>Bus Allocated:</th>
                                <td>
                                    <span class="badge bg-primary px-2 py-1"><?php echo htmlspecialchars($pass['bus_number']); ?></span>
                                    <span class="ms-2 text-muted fw-normal"><?php echo htmlspecialchars($pass['bus_name']); ?></span>
                                </td>
                            </tr>
                            <tr>
                                <th>Pass Category:</th>
                                <td><span class="fw-bold"><?php echo htmlspecialchars($pass['pass_type']); ?> Transit Pass</span></td>
                            </tr>
                            <tr>
                                <th>Valid From:</th>
                                <td><i class="bi bi-calendar-check text-success me-1"></i> <?php echo date('d M Y', strtotime($pass['start_date'])); ?></td>
                            </tr>
                            <tr>
                                <th>Valid Until:</th>
                                <td><i class="bi bi-calendar-x text-danger me-1"></i> <?php echo date('d M Y', strtotime($pass['end_date'])); ?></td>
                            </tr>
                            <tr>
                                <th>Emergency Phone:</th>
                                <td><?php echo htmlspecialchars($pass['phone']); ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pass Footer -->
                <div class="pass-footer d-flex justify-content-between align-items-center">
                    <div>
                        <strong>Approved on:</strong> <?php echo ($pass['approved_at']) ? date('d M Y, h:i A', strtotime($pass['approved_at'])) : 'Verified by Administrator'; ?>
                    </div>
                    <div class="font-monospace">
                        ID: #BPMS-<?php echo strtoupper(substr(md5($pass['id'] . $pass['start_date']), 0, 8)); ?>
                    </div>
                </div>
            </div>

            <div class="no-print text-center mt-4">
                <small class="text-muted">
                    <i class="bi bi-info-circle me-1"></i> Present this digital pass or printed copy upon boarding institutional transit buses.
                </small>
            </div>
        </div>
    <?php else: ?>
        <div class="card card-custom border-0 shadow-sm text-center py-5">
            <div class="card-body">
                <i class="bi bi-shield-exclamation fs-1 text-warning d-block mb-3"></i>
                <h4 class="fw-bold">No Approved Bus Pass Found</h4>
                <p class="text-muted mb-4">You do not have an approved bus pass yet. Once your application is reviewed and approved by the administrator, your official digital pass will be displayed here.</p>
                <div class="d-flex justify-content-center gap-3">
                    <a href="apply_pass.php" class="btn btn-primary fw-semibold">
                        <i class="bi bi-plus-circle me-1"></i> Apply for a Pass
                    </a>
                    <a href="my_applications.php" class="btn btn-outline-secondary">
                        <i class="bi bi-clock-history me-1"></i> Check Application Status
                    </a>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include_once __DIR__ . '/includes/footer.php'; ?>
