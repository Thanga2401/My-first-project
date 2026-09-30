<?php
/**
 * Admin - Edit Route
 * Bus Pass Management System
 */
include_once __DIR__ . '/../config/db.php';
include_once __DIR__ . '/../includes/auth.php';

requireAdmin('login.php');

$route_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($route_id <= 0) {
    $_SESSION['flash_error'] = "Invalid route ID specified.";
    header("Location: routes.php");
    exit;
}

// Fetch existing route details
$fetch_sql = "SELECT * FROM routes WHERE id = ? LIMIT 1";
$stmt = mysqli_prepare($conn, $fetch_sql);
mysqli_stmt_bind_param($stmt, "i", $route_id);
mysqli_stmt_execute($stmt);
$route = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if (!$route) {
    $_SESSION['flash_error'] = "Route record not found.";
    header("Location: routes.php");
    exit;
}

$page_title = "Edit Route - " . $route['route_name'];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $route_name  = trim($_POST['route_name'] ?? '');
    $start_point = trim($_POST['start_point'] ?? '');
    $end_point   = trim($_POST['end_point'] ?? '');
    $stops       = trim($_POST['stops'] ?? '');
    $fare        = (float)($_POST['fare'] ?? 0);

    if (empty($route_name)) {
        $errors[] = "Route Name is required.";
    }

    if (empty($start_point)) {
        $errors[] = "Starting Point is required.";
    }

    if (empty($end_point)) {
        $errors[] = "Ending Point is required.";
    }

    if (empty($stops)) {
        $errors[] = "Stops cannot be empty.";
    }

    if ($fare < 0) {
        $errors[] = "Fare cannot be negative.";
    }

    if (empty($errors)) {
        $update_sql = "UPDATE routes SET route_name = ?, start_point = ?, end_point = ?, stops = ?, fare = ? WHERE id = ?";
        $stmt_update = mysqli_prepare($conn, $update_sql);
        mysqli_stmt_bind_param($stmt_update, "ssssdi", $route_name, $start_point, $end_point, $stops, $fare, $route_id);

        if (mysqli_stmt_execute($stmt_update)) {
            $_SESSION['flash_success'] = "Route '$route_name' updated successfully.";
            header("Location: routes.php");
            exit;
        } else {
            $errors[] = "Failed to update route record.";
        }
        mysqli_stmt_close($stmt_update);
    }

    $route['route_name']  = $route_name;
    $route['start_point'] = $start_point;
    $route['end_point']   = $end_point;
    $route['stops']       = $stops;
    $route['fare']        = $fare;
}

include_once __DIR__ . '/includes/header.php';
?>

<div class="row justify-content-center py-3">
    <div class="col-lg-7 col-md-9">
        <div class="card card-custom border-0 shadow-lg">
            <div class="card-header bg-primary text-white py-3 rounded-top-3">
                <h5 class="fw-bold mb-0"><i class="bi bi-pencil-square me-2"></i>Edit Transit Route</h5>
            </div>
            <div class="card-body p-4 p-md-5">
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger" role="alert">
                        <h6 class="fw-bold mb-2"><i class="bi bi-exclamation-octagon-fill me-1"></i> Please correct the errors:</h6>
                        <ul class="mb-0 ps-3">
                            <?php foreach ($errors as $err): ?>
                                <li><?php echo htmlspecialchars($err); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form action="edit_route.php?id=<?php echo $route_id; ?>" method="POST">
                    <div class="mb-3">
                        <label for="route_name" class="form-label fw-semibold">Route Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="route_name" name="route_name" value="<?php echo htmlspecialchars($route['route_name']); ?>" required>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="start_point" class="form-label fw-semibold">Starting Point <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="start_point" name="start_point" value="<?php echo htmlspecialchars($route['start_point']); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="end_point" class="form-label fw-semibold">Ending Point <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="end_point" name="end_point" value="<?php echo htmlspecialchars($route['end_point']); ?>" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="stops" class="form-label fw-semibold">Intermediate Stops <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="stops" name="stops" rows="3" required><?php echo htmlspecialchars($route['stops']); ?></textarea>
                    </div>

                    <div class="mb-4">
                        <label for="fare" class="form-label fw-semibold">Fare Rate (₹) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="0" class="form-control" id="fare" name="fare" value="<?php echo htmlspecialchars($route['fare']); ?>" required>
                    </div>

                    <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                        <a href="routes.php" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary px-4 fw-bold shadow-sm">
                            <i class="bi bi-save me-1"></i> Update Route
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/includes/footer.php'; ?>
