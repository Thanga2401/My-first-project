<?php
/**
 * Admin - Database Table Viewer & Management
 * Bus Pass Management System
 */
include_once __DIR__ . '/../config/db.php';
include_once __DIR__ . '/../includes/auth.php';

requireAdmin('login.php');

$page_title = "Database Tables View";

// Allowed tables for safe inspection
$allowed_tables = [
    'users'                 => 'User Accounts (Auth & Roles)',
    'students'              => 'Student Profiles & Academic Info',
    'buses'                 => 'Fleet Buses & Seating Capacity',
    'routes'                => 'Transit Routes & Fares',
    'bus_pass_applications' => 'Bus Pass Applications & Approvals'
];

$selected_table = $_GET['table'] ?? 'users';
if (!array_key_exists($selected_table, $allowed_tables)) {
    $selected_table = 'users';
}

// Fetch Table Structure
$columns_res = mysqli_query($conn, "DESCRIBE `$selected_table`");
$columns = [];
if ($columns_res) {
    while ($col = mysqli_fetch_assoc($columns_res)) {
        $columns[] = $col;
    }
}

// Fetch Table Records (Limit 100)
$records_res = mysqli_query($conn, "SELECT * FROM `$selected_table` ORDER BY id DESC LIMIT 100");
$records = [];
if ($records_res) {
    while ($row = mysqli_fetch_assoc($records_res)) {
        $records[] = $row;
    }
}

// Total count
$count_res = mysqli_query($conn, "SELECT COUNT(*) AS total FROM `$selected_table`");
$total_rows = $count_res ? mysqli_fetch_assoc($count_res)['total'] : 0;

include_once __DIR__ . '/includes/header.php';
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-2 pb-3 mb-4 border-bottom">
    <div>
        <h1 class="h3 fw-bold text-dark mb-0">
            <i class="bi bi-database me-2 text-primary"></i>Database Management & Table Viewer
        </h1>
        <small class="text-muted">Database: <code>bus_pass_db</code> &bull; Live relational data inspector</small>
    </div>
    <div class="d-flex gap-2">
        <a href="http://localhost/phpmyadmin/index.php?route=/database/structure&db=bus_pass_db" target="_blank" class="btn btn-sm btn-outline-success">
            <i class="bi bi-box-arrow-up-right me-1"></i> Open in phpMyAdmin
        </a>
    </div>
</div>

<!-- Table Selector Navigation Tabs -->
<ul class="nav nav-pills mb-4 gap-2 bg-white p-2 rounded-3 border shadow-sm">
    <?php foreach ($allowed_tables as $tbl => $tbl_title): ?>
        <?php
            // Quick count for badge
            $q_cnt = mysqli_query($conn, "SELECT COUNT(*) AS total FROM `$tbl`");
            $badge_cnt = $q_cnt ? mysqli_fetch_assoc($q_cnt)['total'] : 0;
        ?>
        <li class="nav-item">
            <a class="nav-link <?php echo ($selected_table === $tbl) ? 'active fw-bold' : 'text-dark'; ?>" href="database_view.php?table=<?php echo urlencode($tbl); ?>">
                <i class="bi bi-table me-1"></i> <?php echo htmlspecialchars($tbl); ?>
                <span class="badge bg-secondary ms-1"><?php echo $badge_cnt; ?></span>
            </a>
        </li>
    <?php endforeach; ?>
</ul>

<!-- Table Meta Info Header -->
<div class="card card-custom border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
        <div>
            <h5 class="fw-bold mb-0 text-primary">
                <i class="bi bi-table me-2"></i>Table: <code><?php echo htmlspecialchars($selected_table); ?></code>
            </h5>
            <small class="text-muted"><?php echo htmlspecialchars($allowed_tables[$selected_table]); ?></small>
        </div>
        <div>
            <span class="badge bg-primary fs-6 px-3 py-2">
                Total Rows: <?php echo $total_rows; ?>
            </span>
        </div>
    </div>

    <!-- Data Rows Table View -->
    <div class="card-body p-0">
        <?php if (!empty($records)): ?>
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle mb-0 small">
                    <thead class="table-dark">
                        <tr>
                            <?php foreach ($columns as $col): ?>
                                <th class="text-nowrap">
                                    <?php echo htmlspecialchars($col['Field']); ?>
                                    <?php if ($col['Key'] === 'PRI'): ?>
                                        <i class="bi bi-key-fill text-warning ms-1" title="Primary Key"></i>
                                    <?php endif; ?>
                                </th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($records as $row): ?>
                            <tr>
                                <?php foreach ($columns as $col): ?>
                                    <?php 
                                        $val = $row[$col['Field']] ?? null;
                                        $is_password = ($col['Field'] === 'password');
                                    ?>
                                    <td class="text-nowrap">
                                        <?php if ($val === null): ?>
                                            <span class="badge bg-light text-muted border">NULL</span>
                                        <?php elseif ($is_password): ?>
                                            <span class="text-muted font-monospace" title="<?php echo htmlspecialchars($val); ?>">
                                                <?php echo substr($val, 0, 15); ?>... [Encrypted]
                                            </span>
                                        <?php elseif ($col['Field'] === 'status'): ?>
                                            <?php if ($val === 'Approved' || $val === 'Active'): ?>
                                                <span class="badge bg-success"><?php echo htmlspecialchars($val); ?></span>
                                            <?php elseif ($val === 'Rejected' || $val === 'Inactive'): ?>
                                                <span class="badge bg-danger"><?php echo htmlspecialchars($val); ?></span>
                                            <?php else: ?>
                                                <span class="badge bg-warning text-dark"><?php echo htmlspecialchars($val); ?></span>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <?php echo htmlspecialchars($val); ?>
                                        <?php endif; ?>
                                    </td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="text-center py-5 text-muted">
                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                No records found in table <code><?php echo htmlspecialchars($selected_table); ?></code>.
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Table Schema Details -->
<div class="card card-custom border-0 shadow-sm">
    <div class="card-header bg-white border-bottom py-3">
        <h6 class="fw-bold mb-0 text-secondary">
            <i class="bi bi-diagram-3 me-2"></i>Schema Definition for `<?php echo htmlspecialchars($selected_table); ?>`
        </h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm table-striped mb-0 small">
                <thead>
                    <tr>
                        <th class="ps-3">Column</th>
                        <th>Type</th>
                        <th>Null</th>
                        <th>Key</th>
                        <th>Default</th>
                        <th>Extra</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($columns as $col): ?>
                        <tr>
                            <td class="ps-3 fw-bold"><?php echo htmlspecialchars($col['Field']); ?></td>
                            <td><code><?php echo htmlspecialchars($col['Type']); ?></code></td>
                            <td><?php echo htmlspecialchars($col['Null']); ?></td>
                            <td>
                                <?php if ($col['Key'] === 'PRI'): ?>
                                    <span class="badge bg-primary">PRIMARY</span>
                                <?php elseif ($col['Key'] === 'UNI'): ?>
                                    <span class="badge bg-info text-dark">UNIQUE</span>
                                <?php elseif ($col['Key'] === 'MUL'): ?>
                                    <span class="badge bg-secondary">INDEX/FK</span>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td><?php echo $col['Default'] !== null ? htmlspecialchars($col['Default']) : '<span class="text-muted">None</span>'; ?></td>
                            <td><?php echo htmlspecialchars($col['Extra']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/includes/footer.php'; ?>
