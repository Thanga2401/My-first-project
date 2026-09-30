<?php
/**
 * Login Page (Student & System Users)
 * Bus Pass Management System
 */
include_once __DIR__ . '/config/db.php';
include_once __DIR__ . '/includes/auth.php';

// Redirect if already logged in
if (isLoggedIn()) {
    if ($_SESSION['role'] === 'student') {
        header("Location: dashboard.php");
        exit;
    } elseif ($_SESSION['role'] === 'admin') {
        header("Location: admin/dashboard.php");
        exit;
    }
}

$page_title = "Student Login";
$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($email) || empty($password)) {
        $error = "Please enter both Email and Password.";
    } else {
        $sql = "SELECT id, name, email, password, role FROM users WHERE LOWER(email) = LOWER(?) LIMIT 1";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($user = mysqli_fetch_assoc($result)) {
            // Verify BCrypt password hash
            if (password_verify($password, $user['password'])) {
                // Set session variables
                $_SESSION['user_id']   = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['role']      = $user['role'];

                // Role-based redirection
                if ($user['role'] === 'admin') {
                    $_SESSION['flash_success'] = "Welcome back, Administrator " . $user['name'] . "!";
                    header("Location: admin/dashboard.php");
                    exit;
                } else {
                    $_SESSION['flash_success'] = "Welcome back, " . $user['name'] . "!";
                    header("Location: dashboard.php");
                    exit;
                }
            } else {
                $error = "Incorrect password. Please verify and try again.";
            }
        } else {
            $error = "No account found with this email address. Please register if you don't have an account.";
        }
        mysqli_stmt_close($stmt);
    }
}

include_once __DIR__ . '/includes/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card card-custom border-0 shadow-lg">
                <div class="card-header bg-primary text-white text-center py-4 rounded-top-3">
                    <div class="mb-2">
                        <i class="bi bi-person-lock fs-1 text-warning"></i>
                    </div>
                    <h4 class="fw-bold mb-0">Student Portal Login</h4>
                    <p class="text-light small mb-0 mt-1">Sign in to manage and view your bus pass</p>
                </div>

                <div class="card-body p-4 p-md-5">
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <?php echo htmlspecialchars($error); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <!-- Demo Student Login Helper Box -->
                    <div class="alert alert-info py-2 px-3 small mb-3 border-0 bg-info-subtle text-info-emphasis">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <strong><i class="bi bi-person-check-fill me-1"></i> Demo Student Account:</strong>
                            <button type="button" class="btn btn-xs btn-outline-info text-dark py-0 px-1 font-monospace" onclick="fillStudentDemo()" style="font-size: 0.75rem;">
                                Auto-fill
                            </button>
                        </div>
                        <div>Email: <code>student@buspass.com</code></div>
                        <div>Password: <code>Student@123</code></div>
                    </div>

                    <form action="login.php" method="POST" autocomplete="off">
                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-envelope"></i></span>
                                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required placeholder="student@buspass.com">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="password" class="form-label fw-semibold">Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-key"></i></span>
                                <input type="password" class="form-control" id="password" name="password" required placeholder="Enter your password">
                                <button class="btn btn-outline-secondary toggle-password-btn" type="button" data-target="password">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="d-grid gap-2 mb-3">
                            <button type="submit" class="btn btn-primary btn-lg fw-bold shadow-sm">
                                <i class="bi bi-box-arrow-in-right me-1"></i> Sign In as Student
                            </button>
                        </div>
                    </form>

                    <div class="text-center mt-4 pt-3 border-top">
                        <p class="text-muted mb-2">New student? 
                            <a href="register.php" class="text-primary fw-bold text-decoration-none">Register here</a>
                        </p>
                        <p class="mb-0 small">
                            <a href="admin/login.php" class="text-secondary text-decoration-none">
                                <i class="bi bi-shield-lock me-1"></i> Admin Login
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function fillStudentDemo() {
    document.getElementById('email').value = 'student@buspass.com';
    document.getElementById('password').value = 'Student@123';
}
</script>

<?php include_once __DIR__ . '/includes/footer.php'; ?>
