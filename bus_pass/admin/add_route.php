<?php
/**
 * Admin - Add Route
 * Bus Pass Management System
 */
include_once __DIR__ . '/../config/db.php';
include_once __DIR__ . '/../includes/auth.php';

requireAdmin('login.php');

$page_title = "Add New Route";
$errors = [];
$form_data = [
    'route_name'  => '',
    'start_point' => '',
    'end_point'   => '',
    'stops'       => '',
    'fare'        => '40.00'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $route_name  = trim($_POST['route_name'] ?? '');
    $start_point = trim($_POST['start_point'] ?? '');
    $end_point   = trim($_POST['end_point'] ?? '');
    $stops       = trim($_POST['stops'] ?? '');
    $fare        = (float)($_POST['fare'] ?? 0);

    $form_data = [
        'route_name'  => $route_name,
        'start_point' => $start_point,
        'end_point'   => $end_point,
        'stops'       => $stops,
        'fare'        => $fare
    ];

    if (empty($route_name)) {
        $errors[] = "Route Name (e.g., Dindigul → Batlagundu) is required.";
    }

    if (empty($start_point)) {
        $errors[] = "Origin / Starting Point is required.";
    }

    if (empty($end_point)) {
        $errors[] = "Destination / End Point is required.";
    }

    if (empty($stops)) {
        $errors[] = "Please list the key intermediate stops along this route.";
    }

    if ($fare < 0) {
        $errors[] = "Transit fare cannot be negative.";
    }

    if (empty($errors)) {
        $insert_sql = "INSERT INTO routes (route_name, start_point, end_point, stops, fare) VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $insert_sql);
        mysqli_stmt_bind_param($stmt, "ssssd", $route_name, $start_point, $end_point, $stops, $fare);

        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['flash_success'] = "Route '$route_name' created successfully.";
            header("Location: routes.php");
            exit;
        } else {
            $errors[] = "Failed to create route record in database.";
        }
        mysqli_stmt_close($stmt);
    }
}

include_once __DIR__ . '/includes/header.php';
?>

<div class="row justify-content-center py-3">
    <div class="col-lg-7 col-md-9">
        <div class="card card-custom border-0 shadow-lg">
            <div class="card-header bg-primary text-white py-3 rounded-top-3">
                <h5 class="fw-bold mb-0"><i class="bi bi-signpost-2-fill me-2"></i>Add New Transit Route</h5>
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

                <form action="add_route.php" method="POST">
                    <div class="mb-3">
                        <label for="route_name" class="form-label fw-semibold">Route Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="route_name" name="route_name" value="<?php echo htmlspecialchars($form_data['route_name']); ?>" required placeholder="e.g. Dindigul → Batlagundu">
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="start_point" class="form-label fw-semibold">Starting Point (Origin) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="start_point" name="start_point" value="<?php echo htmlspecialchars($form_data['start_point']); ?>" required placeholder="e.g. Dindigul Central Bus Stand">
                        </div>
                        <div class="col-md-6">
                            <label for="end_point" class="form-label fw-semibold">Ending Point (Destination) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="end_point" name="end_point" value="<?php echo htmlspecialchars($form_data['end_point']); ?>" required placeholder="e.g. Batlagundu Main Stop">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="stops" class="form-label fw-semibold">Intermediate Stops <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="stops" name="stops" rows="2" required placeholder="Comma separated stops, e.g. Dindigul BS, Vadamadurai Cross, Sempatty, Batlagundu"><?php echo htmlspecialchars($form_data['stops']); ?></textarea>
                    </div>

                    <div class="mb-4">
                        <label for="fare" class="form-label fw-semibold">Base Fare Rate (₹) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="0" class="form-control" id="fare" name="fare" value="<?php echo htmlspecialchars($form_data['fare']); ?>" required placeholder="45.00">
                    </div>

                    <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                        <a href="routes.php" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i> Back to Routes
                        </a>
                        <button type="submit" class="btn btn-primary px-4 fw-bold shadow-sm">
                            <i class="bi bi-save me-1"></i> Save Route
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/includes/footer.php'; ?>
