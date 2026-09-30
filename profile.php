<?php
/**
 * Student Profile View & Edit
 * Bus Pass Management System
 */
include_once __DIR__ . '/config/db.php';
include_once __DIR__ . '/includes/auth.php';

requireStudent();

$page_title = "My Profile";
$user_id = $_SESSION['user_id'];
$student = getLoggedInStudent($conn, $user_id);
$student_table_id = $student['student_table_id'] ?? 0;

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name       = trim($_POST['name'] ?? '');
    $phone      = trim($_POST['phone'] ?? '');
    $college    = trim($_POST['college'] ?? '');
    $department = trim($_POST['department'] ?? '');
    $year       = trim($_POST['year'] ?? '');
    $address    = trim($_POST['address'] ?? '');

    if (empty($name)) {
        $errors[] = "Full Name cannot be empty.";
    }
    if (empty($phone) || !preg_match('/^[0-9]{10,15}$/', $phone)) {
        $errors[] = "A valid Phone number is required.";
    }
    if (empty($college)) {
        $errors[] = "College name cannot be empty.";
    }
    if (empty($department)) {
        $errors[] = "Department cannot be empty.";
    }
    if (empty($year)) {
        $errors[] = "Year of study cannot be empty.";
    }
    if (empty($address)) {
        $errors[] = "Address cannot be empty.";
    }

    if (empty($errors)) {
        mysqli_begin_transaction($conn);
        try {
            // Update users table name
            $update_user = "UPDATE users SET name = ? WHERE id = ?";
            $u_stmt = mysqli_prepare($conn, $update_user);
            mysqli_stmt_bind_param($u_stmt, "si", $name, $user_id);
            mysqli_stmt_execute($u_stmt);
            mysqli_stmt_close($u_stmt);

            // Update students table
            $update_student = "UPDATE students SET phone = ?, college = ?, department = ?, year = ?, address = ? WHERE id = ?";
            $s_stmt = mysqli_prepare($conn, $update_student);
            mysqli_stmt_bind_param($s_stmt, "sssssi", $phone, $college, $department, $year, $address, $student_table_id);
            mysqli_stmt_execute($s_stmt);
            mysqli_stmt_close($s_stmt);

            mysqli_commit($conn);

            $_SESSION['user_name'] = $name;
            $_SESSION['flash_success'] = "Profile updated successfully.";
            header("Location: profile.php");
            exit;

        } catch (Exception $e) {
            mysqli_rollback($conn);
            $errors[] = "Failed to update profile: " . $e->getMessage();
        }
    }
}

// Refresh student record
$student = getLoggedInStudent($conn, $user_id);

include_once __DIR__ . '/includes/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
            <div class="card card-custom border-0 shadow-lg">
                <div class="card-header bg-primary text-white py-3 rounded-top-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0 fw-bold"><i class="bi bi-person-gear me-2"></i>My Student Profile</h4>
                        <small class="text-light">Manage your personal and academic information</small>
                    </div>
                    <span class="badge bg-warning text-dark px-3 py-2 fw-semibold">
                        ID: <?php echo htmlspecialchars($student['student_id'] ?? 'N/A'); ?>
                    </span>
                </div>

                <div class="card-body p-4 p-md-5">
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger" role="alert">
                            <h6 class="fw-bold mb-2"><i class="bi bi-exclamation-octagon-fill me-1"></i> Update Errors:</h6>
                            <ul class="mb-0 ps-3">
                                <?php foreach ($errors as $error): ?>
                                    <li><?php echo htmlspecialchars($error); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form action="profile.php" method="POST">
                        <h6 class="text-primary fw-bold border-bottom pb-2 mb-3">
                            <i class="bi bi-person-badge me-1"></i> Account Details
                        </h6>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label fw-semibold">Full Name</label>
                                <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($student['name'] ?? ''); ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label fw-semibold">Email Address (Read-only)</label>
                                <input type="email" class="form-control bg-light" id="email" value="<?php echo htmlspecialchars($student['email'] ?? ''); ?>" readonly disabled>
                            </div>
                        </div>

                        <h6 class="text-primary fw-bold border-bottom pb-2 mb-3 mt-4">
                            <i class="bi bi-mortarboard me-1"></i> Academic Information
                        </h6>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="student_id_disp" class="form-label fw-semibold">Student ID / Roll No</label>
                                <input type="text" class="form-control bg-light" id="student_id_disp" value="<?php echo htmlspecialchars($student['student_id'] ?? ''); ?>" readonly disabled>
                            </div>
                            <div class="col-md-6">
                                <label for="phone" class="form-label fw-semibold">Phone Number</label>
                                <input type="tel" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($student['phone'] ?? ''); ?>" required>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="college" class="form-label fw-semibold">College / Institution</label>
                                <input type="text" class="form-control" id="college" name="college" value="<?php echo htmlspecialchars($student['college'] ?? ''); ?>" required>
                            </div>
                            <div class="col-md-3">
                                <label for="department" class="form-label fw-semibold">Department</label>
                                <input type="text" class="form-control" id="department" name="department" value="<?php echo htmlspecialchars($student['department'] ?? ''); ?>" required>
                            </div>
                            <div class="col-md-3">
                                <label for="year" class="form-label fw-semibold">Year of Study</label>
                                <select class="form-select" id="year" name="year" required>
                                    <option value="1st Year" <?php echo ($student['year'] === '1st Year') ? 'selected' : ''; ?>>1st Year</option>
                                    <option value="2nd Year" <?php echo ($student['year'] === '2nd Year') ? 'selected' : ''; ?>>2nd Year</option>
                                    <option value="3rd Year" <?php echo ($student['year'] === '3rd Year') ? 'selected' : ''; ?>>3rd Year</option>
                                    <option value="4th Year" <?php echo ($student['year'] === '4th Year') ? 'selected' : ''; ?>>4th Year</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="address" class="form-label fw-semibold">Permanent / Residential Address</label>
                            <textarea class="form-control" id="address" name="address" rows="3" required><?php echo htmlspecialchars($student['address'] ?? ''); ?></textarea>
                        </div>

                        <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                            <a href="dashboard.php" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-1"></i> Back to Dashboard
                            </a>
                            <button type="submit" class="btn btn-primary px-4 fw-bold shadow-sm">
                                <i class="bi bi-save me-1"></i> Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/includes/footer.php'; ?>
