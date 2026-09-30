<?php
/**
 * Admin - Route Management
 * Bus Pass Management System
 */
include_once __DIR__ . '/../config/db.php';
include_once __DIR__ . '/../includes/auth.php';

requireAdmin('login.php');

$page_title = "Manage Routes";

// Fetch all routes with count of pass applications on this route
$sql = "SELECT r.*, 
               (SELECT COUNT(*) FROM bus_pass_applications a WHERE a.route_id = r.id) AS total_apps,
               (SELECT COUNT(*) FROM bus_pass_applications a WHERE a.route_id = r.id AND a.status = 'Approved') AS approved_passes
        FROM routes r 
        ORDER BY r.id ASC";
$result = mysqli_query($conn, $sql);
$routes = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $routes[] = $row;
    }
}

include_once __DIR__ . '/includes/header.php';
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-2 pb-3 mb-4 border-bottom">
    <div>
        <h1 class="h3 fw-bold text-dark mb-0">Transit Routes</h1>
        <small class="text-muted">Configure and manage student bus transit pathways and fares</small>
    </div>
    <div>
        <a href="add_route.php" class="btn btn-primary fw-bold shadow-sm">
            <i class="bi bi-plus-circle me-1"></i> Add New Route
        </a>
    </div>
</div>

<div class="card card-custom border-0 shadow-sm">
    <div class="card-body p-0">
        <?php if (!empty($routes)): ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">ID</th>
                            <th>Route Name</th>
                            <th>Origin & Destination</th>
                            <th>Stops Along Route</th>
                            <th>Fare Rate</th>
                            <th>Pass Stats</th>
                            <th>Created On</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($routes as $route): ?>
                            <tr>
                                <td class="ps-4 fw-bold text-muted"><?php echo $route['id']; ?></td>
                                <td>
                                    <div class="fw-bold text-primary"><?php echo htmlspecialchars($route['route_name']); ?></div>
                                </td>
                                <td>
                                    <div><strong>Start:</strong> <?php echo htmlspecialchars($route['start_point']); ?></div>
                                    <small class="text-muted"><strong>End:</strong> <?php echo htmlspecialchars($route['end_point']); ?></small>
                                </td>
                                <td>
                                    <div class="small text-secondary" style="max-width: 280px;">
                                        <?php echo htmlspecialchars($route['stops']); ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="fw-bold text-success fs-6">₹<?php echo number_format($route['fare'], 2); ?></span>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border me-1">Total: <?php echo $route['total_apps']; ?></span>
                                    <span class="badge bg-success-subtle text-success border">Active: <?php echo $route['approved_passes']; ?></span>
                                </td>
                                <td><small class="text-muted"><?php echo date('d M Y', strtotime($route['created_at'])); ?></small></td>
                                <td class="text-end pe-4">
                                    <a href="edit_route.php?id=<?php echo $route['id']; ?>" class="btn btn-sm btn-outline-primary me-1" title="Edit Route">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                    <a href="delete_route.php?id=<?php echo $route['id']; ?>" class="btn btn-sm btn-outline-danger confirm-action" data-confirm="Are you sure you want to delete this route?" title="Delete Route">
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
                <i class="bi bi-map fs-1 d-block mb-2"></i>
                No transit routes defined yet. Click "Add New Route" to create one.
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include_once __DIR__ . '/includes/footer.php'; ?>
