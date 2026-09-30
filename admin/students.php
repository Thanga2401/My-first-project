<?php
/**
 * Admin - Students Management
 * Bus Pass Management System
 */
include_once __DIR__ . '/../config/db.php';
include_once __DIR__ . '/../includes/auth.php';

requireAdmin('login.php');

$page_title = "Manage Students";
$search = trim($_GET['search'] ?? '');

$sql = "SELECT s.*, u.name, u.email, u.created_at AS user_created_at,
               (SELECT COUNT(*) FROM bus_pass_applications a WHERE a.student_id = s.id) AS total_apps,
               (SELECT COUNT(*) FROM bus_pass_applications a WHERE a.student_id = s.id AND a.status = 'Approved') AS approved_apps
        FROM students s
        JOIN users u ON s.user_id = u.id ";

if (!empty($search)) {
    $sql .= " WHERE u.name LIKE ? OR u.email LIKE ? OR s.student_id LIKE ? OR s.college LIKE ? OR s.phone LIKE ? ";
}

$sql .= " ORDER BY s.id DESC";

$stmt = mysqli_prepare($conn, $sql);
if (!empty($search)) {
    $search_param = "%" . $search . "%";
    mysqli_stmt_bind_param($stmt, "sssss", $search_param, $search_param, $search_param, $search_param, $search_param);
}
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$students = [];
while ($row = mysqli_fetch_assoc($result)) {
    $students[] = $row;
}
mysqli_stmt_close($stmt);

include_once __DIR__ . '/includes/header.php';
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-2 pb-3 mb-4 border-bottom">
    <div>
        <h1 class="h3 fw-bold text-dark mb-0">Registered Students</h1>
        <small class="text-muted">Manage registered student transit accounts</small>
    </div>
    <div class="d-flex gap-2">
        <form class="d-flex" method="GET" action="students.php">
            <div class="input-group">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Search by name, ID, email..." value="<?php echo htmlspecialchars($search); ?>">
                <button class="btn btn-outline-primary btn-sm" type="submit"><i class="bi bi-search"></i></button>
                <?php if (!empty($search)): ?>
                    <a href="students.php" class="btn btn-outline-secondary btn-sm" title="Clear search"><i class="bi bi-x-lg"></i></a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<div class="card card-custom border-0 shadow-sm">
    <div class="card-body p-0">
        <?php if (!empty($students)): ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Student ID</th>
                            <th>Student Name</th>
                            <th>Email & Phone</th>
                            <th>College & Dept</th>
                            <th>Year</th>
                            <th>Pass Stats</th>
                            <th>Registered</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students as $stu): ?>
                            <tr>
                                <td class="ps-4">
                                    <span class="badge bg-light text-dark border fw-bold">
                                        <?php echo htmlspecialchars($stu['student_id']); ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark"><?php echo htmlspecialchars($stu['name']); ?></div>
                                </td>
                                <td>
                                    <div><i class="bi bi-envelope small text-muted me-1"></i><?php echo htmlspecialchars($stu['email']); ?></div>
                                    <small class="text-muted"><i class="bi bi-telephone small text-muted me-1"></i><?php echo htmlspecialchars($stu['phone']); ?></small>
                                </td>
                                <td>
                                    <div class="fw-semibold small"><?php echo htmlspecialchars($stu['college']); ?></div>
                                    <small class="text-muted"><?php echo htmlspecialchars($stu['department']); ?></small>
                                </td>
                                <td><span class="badge bg-secondary-subtle text-secondary border"><?php echo htmlspecialchars($stu['year']); ?></span></td>
                                <td>
                                    <span class="badge bg-primary-subtle text-primary border me-1" title="Total Applications">Apps: <?php echo $stu['total_apps']; ?></span>
                                    <span class="badge bg-success-subtle text-success border" title="Approved Passes">Active: <?php echo $stu['approved_apps']; ?></span>
                                </td>
                                <td><small class="text-muted"><?php echo date('d M Y', strtotime($stu['created_at'])); ?></small></td>
                                <td class="text-end pe-4">
                                    <a href="view_student.php?id=<?php echo $stu['id']; ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye me-1"></i> View Profile
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="text-center py-5 text-muted">
                <i class="bi bi-people fs-1 d-block mb-2"></i>
                No students found matching the query.
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include_once __DIR__ . '/includes/footer.php'; ?>
