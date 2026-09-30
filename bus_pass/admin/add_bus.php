<?php
/**
 * Admin - Add Bus
 * Bus Pass Management System
 */
include_once __DIR__ . '/../config/db.php';
include_once __DIR__ . '/../includes/auth.php';

requireAdmin('login.php');

$page_title = "Add New Bus";
$errors = [];
$form_data = [
    'bus_number' => '',
    'bus_name'   => '',
    'capacity'   => 40,
    'status'     => 'Active'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bus_number = strtoupper(trim($_POST['bus_number'] ?? ''));
    $bus_name   = trim($_POST['bus_name'] ?? '');
    $capacity   = (int)($_POST['capacity'] ?? 0);
    $status     = trim($_POST['status'] ?? 'Active');

    $form_data = [
        'bus_number' => $bus_number,
        'bus_name'   => $bus_name,
        'capacity'   => $capacity,
        'status'     => $status
    ];

    if (empty($bus_number)) {
        $errors[] = "Bus Registration Number (e.g., BUS-101) is required.";
    }

    if (empty($bus_name)) {
        $errors[] = "Bus Name / Route Description is required.";
    }

    if ($capacity <= 0 || $capacity > 200) {
        $errors[] = "Please enter a valid seating capacity (between 1 and 200).";
    }

    if (!in_array($status, ['Active', 'Inactive'])) {
        $status = 'Active';
    }

    // Check duplicate bus number
    if (empty($errors)) {
        $check_sql = "SELECT id FROM buses WHERE bus_number = ? LIMIT 1";
        $stmt_check = mysqli_prepare($conn, $check_sql);
        mysqli_stmt_bind_param($stmt_check, "s", $bus_number);
        mysqli_stmt_execute($stmt_check);
        mysqli_stmt_store_result($stmt_check);
        if (mysqli_stmt_num_rows($stmt_check) > 0) {
            $errors[] = "A bus with registration number '$bus_number' already exists.";
        }
        mysqli_stmt_close($stmt_check);
    }

    // Insert Bus
    if (empty($errors)) {
        $insert_sql = "INSERT INTO buses (bus_number, bus_name, capacity, status) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $insert_sql);
        mysqli_stmt_bind_param($stmt, "ssis", $bus_number, $bus_name, $capacity, $status);

        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['flash_success'] = "New bus '$bus_number' added successfully.";
            header("Location: buses.php");
            exit;
        } else {
            $errors[] = "Failed to add bus to the fleet.";
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
                <h5 class="fw-bold mb-0"><i class="bi bi-bus-front-fill me-2"></i>Add New Fleet Bus</h5>
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

                <form action="add_bus.php" method="POST">
                    <div class="mb-3">
                        <label for="bus_number" class="form-label fw-semibold">Bus Number / Code <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="bus_number" name="bus_number" value="<?php echo htmlspecialchars($form_data['bus_number']); ?>" required placeholder="e.g. BUS-106 or TN-57-AB-1234">
                    </div>

                    <div class="mb-3">
                        <label for="bus_name" class="form-label fw-semibold">Bus Name / Model <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="bus_name" name="bus_name" value="<?php echo htmlspecialchars($form_data['bus_name']); ?>" required placeholder="e.g. Campus Super Express">
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="capacity" class="form-label fw-semibold">Seating Capacity <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="capacity" name="capacity" value="<?php echo htmlspecialchars($form_data['capacity']); ?>" required min="1" max="200">
                        </div>
                        <div class="col-md-6">
                            <label for="status" class="form-label fw-semibold">Operational Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="Active" <?php echo ($form_data['status'] === 'Active') ? 'selected' : ''; ?>>Active</option>
                                <option value="Inactive" <?php echo ($form_data['status'] === 'Inactive') ? 'selected' : ''; ?>>Inactive / Maintenance</option>
                            </select>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                        <a href="buses.php" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i> Back to Buses
                        </a>
                        <button type="submit" class="btn btn-primary px-4 fw-bold shadow-sm">
                            <i class="bi bi-save me-1"></i> Save Bus Record
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/includes/footer.php'; ?>
