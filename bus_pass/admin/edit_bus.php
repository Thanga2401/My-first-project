<?php
/**
 * Admin - Edit Bus
 * Bus Pass Management System
 */
include_once __DIR__ . '/../config/db.php';
include_once __DIR__ . '/../includes/auth.php';

requireAdmin('login.php');

$bus_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($bus_id <= 0) {
    $_SESSION['flash_error'] = "Invalid bus record specified.";
    header("Location: buses.php");
    exit;
}

// Fetch existing bus details
$fetch_sql = "SELECT * FROM buses WHERE id = ? LIMIT 1";
$stmt = mysqli_prepare($conn, $fetch_sql);
mysqli_stmt_bind_param($stmt, "i", $bus_id);
mysqli_stmt_execute($stmt);
$bus = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if (!$bus) {
    $_SESSION['flash_error'] = "Bus record not found.";
    header("Location: buses.php");
    exit;
}

$page_title = "Edit Bus - " . $bus['bus_number'];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bus_number = strtoupper(trim($_POST['bus_number'] ?? ''));
    $bus_name   = trim($_POST['bus_name'] ?? '');
    $capacity   = (int)($_POST['capacity'] ?? 0);
    $status     = trim($_POST['status'] ?? 'Active');

    if (empty($bus_number)) {
        $errors[] = "Bus Registration Number is required.";
    }

    if (empty($bus_name)) {
        $errors[] = "Bus Name is required.";
    }

    if ($capacity <= 0 || $capacity > 200) {
        $errors[] = "Please enter a valid seating capacity (1-200).";
    }

    if (!in_array($status, ['Active', 'Inactive'])) {
        $status = 'Active';
    }

    // Check duplicate bus number (excluding current ID)
    if (empty($errors)) {
        $check_sql = "SELECT id FROM buses WHERE bus_number = ? AND id != ? LIMIT 1";
        $stmt_check = mysqli_prepare($conn, $check_sql);
        mysqli_stmt_bind_param($stmt_check, "si", $bus_number, $bus_id);
        mysqli_stmt_execute($stmt_check);
        mysqli_stmt_store_result($stmt_check);
        if (mysqli_stmt_num_rows($stmt_check) > 0) {
            $errors[] = "Another bus with registration number '$bus_number' already exists.";
        }
        mysqli_stmt_close($stmt_check);
    }

    // Update Bus
    if (empty($errors)) {
        $update_sql = "UPDATE buses SET bus_number = ?, bus_name = ?, capacity = ?, status = ? WHERE id = ?";
        $stmt_update = mysqli_prepare($conn, $update_sql);
        mysqli_stmt_bind_param($stmt_update, "ssisi", $bus_number, $bus_name, $capacity, $status, $bus_id);

        if (mysqli_stmt_execute($stmt_update)) {
            $_SESSION['flash_success'] = "Bus '$bus_number' updated successfully.";
            header("Location: buses.php");
            exit;
        } else {
            $errors[] = "Failed to update bus record.";
        }
        mysqli_stmt_close($stmt_update);
    }

    $bus['bus_number'] = $bus_number;
    $bus['bus_name']   = $bus_name;
    $bus['capacity']   = $capacity;
    $bus['status']     = $status;
}

include_once __DIR__ . '/includes/header.php';
?>

<div class="row justify-content-center py-3">
    <div class="col-lg-7 col-md-9">
        <div class="card card-custom border-0 shadow-lg">
            <div class="card-header bg-primary text-white py-3 rounded-top-3">
                <h5 class="fw-bold mb-0"><i class="bi bi-pencil-square me-2"></i>Edit Bus Record</h5>
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

                <form action="edit_bus.php?id=<?php echo $bus_id; ?>" method="POST">
                    <div class="mb-3">
                        <label for="bus_number" class="form-label fw-semibold">Bus Number / Code <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="bus_number" name="bus_number" value="<?php echo htmlspecialchars($bus['bus_number']); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="bus_name" class="form-label fw-semibold">Bus Name / Model <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="bus_name" name="bus_name" value="<?php echo htmlspecialchars($bus['bus_name']); ?>" required>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="capacity" class="form-label fw-semibold">Seating Capacity <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="capacity" name="capacity" value="<?php echo htmlspecialchars($bus['capacity']); ?>" required min="1" max="200">
                        </div>
                        <div class="col-md-6">
                            <label for="status" class="form-label fw-semibold">Operational Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="Active" <?php echo ($bus['status'] === 'Active') ? 'selected' : ''; ?>>Active</option>
                                <option value="Inactive" <?php echo ($bus['status'] === 'Inactive') ? 'selected' : ''; ?>>Inactive / Maintenance</option>
                            </select>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                        <a href="buses.php" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary px-4 fw-bold shadow-sm">
                            <i class="bi bi-save me-1"></i> Update Bus Record
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/includes/footer.php'; ?>
