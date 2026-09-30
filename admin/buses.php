<?php
/**
 * Admin - Bus Management
 * Bus Pass Management System
 */
include_once __DIR__ . '/../config/db.php';
include_once __DIR__ . '/../includes/auth.php';

requireAdmin('login.php');

$page_title = "Manage Buses";

// Fetch all buses with count of assigned applications
$sql = "SELECT b.*, 
               (SELECT COUNT(*) FROM bus_pass_applications a WHERE a.bus_id = b.id) AS total_assigned_apps,
               (SELECT COUNT(*) FROM bus_pass_applications a WHERE a.bus_id = b.id AND a.status = 'Approved') AS active_pass_count
        FROM buses b 
        ORDER BY b.id ASC";
$result = mysqli_query($conn, $sql);
$buses = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $buses[] = $row;
    }
}

include_once __DIR__ . '/includes/header.php';
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-2 pb-3 mb-4 border-bottom">
    <div>
        <h1 class="h3 fw-bold text-dark mb-0">Bus Fleet Management</h1>
        <small class="text-muted">Manage transit buses, seating capacity, and operational status</small>
    </div>
    <div>
        <a href="add_bus.php" class="btn btn-primary fw-bold shadow-sm">
            <i class="bi bi-plus-circle me-1"></i> Add New Bus
        </a>
    </div>
</div>

<div class="card card-custom border-0 shadow-sm">
    <div class="card-body p-0">
        <?php if (!empty($buses)): ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">ID</th>
                            <th>Bus Number</th>
                            <th>Bus Name / Model</th>
                            <th>Seating Capacity</th>
                            <th>Allocated Passes</th>
                            <th>Status</th>
                            <th>Created On</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($buses as $bus): ?>
                            <tr>
                                <td class="ps-4 fw-bold text-muted"><?php echo $bus['id']; ?></td>
                                <td>
                                    <span class="badge bg-primary fs-6 px-2 py-1"><?php echo htmlspecialchars($bus['bus_number']); ?></span>
                                </td>
                                <td class="fw-semibold text-dark"><?php echo htmlspecialchars($bus['bus_name']); ?></td>
                                <td>
                                    <i class="bi bi-people-fill text-muted me-1"></i>
                                    <strong><?php echo (int)$bus['capacity']; ?></strong> Seats
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border me-1">Total: <?php echo $bus['total_assigned_apps']; ?></span>
                                    <span class="badge bg-success-subtle text-success border">Active: <?php echo $bus['active_pass_count']; ?></span>
                                </td>
                                <td>
                                    <?php if ($bus['status'] === 'Active'): ?>
                                        <span class="badge bg-success-subtle text-success border px-2 py-1">
                                            <i class="bi bi-check-circle-fill me-1"></i> Active
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-danger-subtle text-danger border px-2 py-1">
                                            <i class="bi bi-slash-circle-fill me-1"></i> Inactive
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td><small class="text-muted"><?php echo date('d M Y', strtotime($bus['created_at'])); ?></small></td>
                                <td class="text-end pe-4">
                                    <a href="edit_bus.php?id=<?php echo $bus['id']; ?>" class="btn btn-sm btn-outline-primary me-1" title="Edit Bus">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                    <a href="delete_bus.php?id=<?php echo $bus['id']; ?>" class="btn btn-sm btn-outline-danger confirm-action" data-confirm="Are you sure you want to delete/deactivate this bus?" title="Delete Bus">
                                        <i class="bi bi-trash"></i> Delete
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="text-center py-5 text-muted">
                <i class="bi bi-bus-front fs-1 d-block mb-2"></i>
                No buses in the fleet yet. Click "Add New Bus" to add one.
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include_once __DIR__ . '/includes/footer.php'; ?>
