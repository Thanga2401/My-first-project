<?php
/**
 * Student Registration Page
 * Bus Pass Management System
 */
include_once __DIR__ . '/config/db.php';
include_once __DIR__ . '/includes/auth.php';

// If already logged in as student, redirect to dashboard
if (isLoggedIn()) {
    if ($_SESSION['role'] === 'student') {
        header("Location: dashboard.php");
        exit;
    } elseif ($_SESSION['role'] === 'admin') {
        header("Location: admin/dashboard.php");
        exit;
    }
}

$page_title = "Student Registration";
$errors = [];
$form_data = [
    'name' => '',
    'email' => '',
    'student_id' => '',
    'phone' => '',
    'college' => '',
    'department' => '',
    'year' => '',
    'address' => ''
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and collect inputs
    $name             = trim($_POST['name'] ?? '');
    $email            = trim($_POST['email'] ?? '');
    $password         = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $student_id       = trim($_POST['student_id'] ?? '');
    $phone            = trim($_POST['phone'] ?? '');
    $college          = trim($_POST['college'] ?? '');
    $department       = trim($_POST['department'] ?? '');
    $year             = trim($_POST['year'] ?? '');
    $address          = trim($_POST['address'] ?? '');

    // Retain form data on error
    $form_data = [
        'name' => $name,
        'email' => $email,
        'student_id' => $student_id,
        'phone' => $phone,
        'college' => $college,
        'department' => $department,
        'year' => $year,
        'address' => $address
    ];

    // Validations
    if (empty($name)) {
        $errors[] = "Full Name is required.";
    }

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "A valid Email address is required.";
    }

    if (empty($student_id)) {
        $errors[] = "Student ID Number is required.";
    }

    if (empty($phone) || !preg_match('/^[0-9]{10,15}$/', $phone)) {
        $errors[] = "A valid Phone number (10-15 digits) is required.";
    }

    if (empty($college)) {
        $errors[] = "College/Institution name is required.";
    }

    if (empty($department)) {
        $errors[] = "Department/Course is required.";
    }

    if (empty($year)) {
        $errors[] = "Current Year of study is required.";
    }

    if (empty($address)) {
        $errors[] = "Residential Address is required.";
    }

    if (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters long.";
    }

    if ($password !== $confirm_password) {
        $errors[] = "Password and Confirm Password do not match.";
    }

    // Check duplicate email
    if (empty($errors)) {
        $check_email_sql = "SELECT id FROM users WHERE email = ? LIMIT 1";
        $stmt = mysqli_prepare($conn, $check_email_sql);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $errors[] = "This email is already registered. Please login or use a different email.";
        }
        mysqli_stmt_close($stmt);
    }

    // Check duplicate student ID
    if (empty($errors)) {
        $check_sid_sql = "SELECT id FROM students WHERE student_id = ? LIMIT 1";
        $stmt = mysqli_prepare($conn, $check_sid_sql);
        mysqli_stmt_bind_param($stmt, "s", $student_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $errors[] = "This Student ID is already registered in our system.";
        }
        mysqli_stmt_close($stmt);
    }

    // Insert user and student records inside transaction
    if (empty($errors)) {
        mysqli_begin_transaction($conn);

        try {
            // Hash password securely
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $role = 'student';

            // 1. Insert into users table
            $insert_user_sql = "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)";
            $user_stmt = mysqli_prepare($conn, $insert_user_sql);
            mysqli_stmt_bind_param($user_stmt, "ssss", $name, $email, $hashed_password, $role);
            
            if (!mysqli_stmt_execute($user_stmt)) {
                throw new Exception("Error creating user profile.");
            }

            $user_id = mysqli_insert_id($conn);
            mysqli_stmt_close($user_stmt);

            // 2. Insert into students table
            $insert_student_sql = "INSERT INTO students (user_id, student_id, phone, college, department, year, address) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $student_stmt = mysqli_prepare($conn, $insert_student_sql);
            mysqli_stmt_bind_param($student_stmt, "issssss", $user_id, $student_id, $phone, $college, $department, $year, $address);
            
            if (!mysqli_stmt_execute($student_stmt)) {
                throw new Exception("Error saving student information.");
            }
            mysqli_stmt_close($student_stmt);

            // Commit transaction
            mysqli_commit($conn);

            $_SESSION['flash_success'] = "Registration successful! Please login with your credentials.";
            header("Location: login.php");
            exit;

        } catch (Exception $e) {
            mysqli_rollback($conn);
            $errors[] = "Registration failed: " . $e->getMessage();
        }
    }
}

include_once __DIR__ . '/includes/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
            <div class="card card-custom border-0 shadow-lg">
                <div class="card-header bg-primary text-white py-3 rounded-top-3">
                    <h4 class="mb-0 fw-bold text-center">
                        <i class="bi bi-person-plus-fill me-2"></i>Student Registration
                    </h4>
                    <p class="text-center text-light small mb-0 mt-1">Create your student account to apply for digital bus passes</p>
                </div>
                <div class="card-body p-4 p-md-5">

                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger" role="alert">
                            <h6 class="fw-bold mb-2"><i class="bi bi-exclamation-octagon-fill me-1"></i> Please fix the following errors:</h6>
                            <ul class="mb-0 ps-3">
                                <?php foreach ($errors as $error): ?>
                                    <li><?php echo htmlspecialchars($error); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form action="register.php" method="POST" autocomplete="off">
                        <!-- Account Details Section -->
                        <h6 class="text-primary fw-bold border-bottom pb-2 mb-3">
                            <i class="bi bi-person-badge me-1"></i> Account Details
                        </h6>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($form_data['name']); ?>" required placeholder="e.g. John Doe">
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label fw-semibold">Email Address <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($form_data['email']); ?>" required placeholder="e.g. john@student.edu">
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="password" class="form-label fw-semibold">Password <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="password" name="password" required minlength="6" placeholder="Min. 6 characters">
                                    <button class="btn btn-outline-secondary toggle-password-btn" type="button" data-target="password">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="confirm_password" class="form-label fw-semibold">Confirm Password <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required minlength="6" placeholder="Re-enter password">
                                    <button class="btn btn-outline-secondary toggle-password-btn" type="button" data-target="confirm_password">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Academic & Contact Details Section -->
                        <h6 class="text-primary fw-bold border-bottom pb-2 mb-3">
                            <i class="bi bi-mortarboard me-1"></i> Academic & Contact Information
                        </h6>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="student_id" class="form-label fw-semibold">Student ID / Roll No <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="student_id" name="student_id" value="<?php echo htmlspecialchars($form_data['student_id']); ?>" required placeholder="e.g. STU-2026-001">
                            </div>
                            <div class="col-md-6">
                                <label for="phone" class="form-label fw-semibold">Phone Number <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($form_data['phone']); ?>" required placeholder="e.g. 9876543210">
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="college" class="form-label fw-semibold">College / Institution <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="college" name="college" value="<?php echo htmlspecialchars($form_data['college']); ?>" required placeholder="e.g. City Engineering College">
                            </div>
                            <div class="col-md-3">
                                <label for="department" class="form-label fw-semibold">Department <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="department" name="department" value="<?php echo htmlspecialchars($form_data['department']); ?>" required placeholder="e.g. Computer Science">
                            </div>
                            <div class="col-md-3">
                                <label for="year" class="form-label fw-semibold">Year of Study <span class="text-danger">*</span></label>
                                <select class="form-select" id="year" name="year" required>
                                    <option value="" disabled <?php echo empty($form_data['year']) ? 'selected' : ''; ?>>Select Year</option>
                                    <option value="1st Year" <?php echo ($form_data['year'] === '1st Year') ? 'selected' : ''; ?>>1st Year</option>
                                    <option value="2nd Year" <?php echo ($form_data['year'] === '2nd Year') ? 'selected' : ''; ?>>2nd Year</option>
                                    <option value="3rd Year" <?php echo ($form_data['year'] === '3rd Year') ? 'selected' : ''; ?>>3rd Year</option>
                                    <option value="4th Year" <?php echo ($form_data['year'] === '4th Year') ? 'selected' : ''; ?>>4th Year</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="address" class="form-label fw-semibold">Permanent / Residential Address <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="address" name="address" rows="3" required placeholder="Enter full address for bus pass route verification"><?php echo htmlspecialchars($form_data['address']); ?></textarea>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg fw-bold shadow-sm">
                                <i class="bi bi-check-circle me-1"></i> Register Account
                            </button>
                        </div>
                    </form>

                    <div class="text-center mt-4">
                        <p class="text-muted mb-0">Already registered? 
                            <a href="login.php" class="text-primary fw-bold text-decoration-none">Login here</a>
                        </p>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/includes/footer.php'; ?>
