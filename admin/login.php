<?php
/**
 * Admin Login Page
 * Bus Pass Management System
 */
include_once __DIR__ . '/../config/db.php';
include_once __DIR__ . '/../includes/auth.php';

// Redirect if already logged in
if (isLoggedIn()) {
    if ($_SESSION['role'] === 'admin') {
        header("Location: dashboard.php");
        exit;
    } else {
        header("Location: ../dashboard.php");
        exit;
    }
}

$page_title = "Admin Login";
$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = "Please provide both admin email and password.";
    } else {
        $sql = "SELECT id, name, email, password, role FROM users WHERE email = ? LIMIT 1";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($user = mysqli_fetch_assoc($result)) {
            if ($user['role'] !== 'admin') {
                $error = "Access denied: This user is not authorized as an administrator.";
            } elseif (password_verify($password, $user['password'])) {
                $_SESSION['user_id']   = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['role']      = $user['role'];

                $_SESSION['flash_success'] = "Welcome to the Administrative Dashboard, " . $user['name'] . "!";
                header("Location: dashboard.php");
                exit;
            } else {
                $error = "Invalid administrator password.";
            }
        } else {
            $error = "No administrator account found with that email.";
        }
        mysqli_stmt_close($stmt);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Bus Pass MS</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body class="bg-dark d-flex align-items-center justify-content-center min-vh-100 py-4">

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">
            <div class="card card-custom border-0 shadow-lg">
                <div class="card-header bg-dark text-white text-center py-4 rounded-top-3 border-bottom border-secondary">
                    <div class="p-3 bg-secondary bg-opacity-25 rounded-circle d-inline-block mb-2">
                        <i class="bi bi-shield-lock-fill fs-1 text-warning"></i>
                    </div>
                    <h4 class="fw-bold mb-0">Administrator Portal</h4>
                    <p class="text-light small mb-0 mt-1">Bus Pass Management System</p>
                </div>

                <div class="card-body p-4 p-md-4">
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger alert-dismissible fade show small" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i>
                            <?php echo htmlspecialchars($error); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <form action="login.php" method="POST" autocomplete="off">
                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold small">Admin Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required placeholder="Enter administrator email">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="password" class="form-label fw-semibold small">Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-key"></i></span>
                                <input type="password" class="form-control" id="password" name="password" required placeholder="Enter admin password">
                                <button class="btn btn-outline-secondary toggle-password-btn" type="button" data-target="password">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="d-grid gap-2 mb-3">
                            <button type="submit" class="btn btn-dark btn-lg fw-bold shadow-sm">
                                <i class="bi bi-shield-check me-1 text-warning"></i> Login to Console
                            </button>
                        </div>
                    </form>

                    <div class="text-center mt-3 pt-3 border-top">
                        <a href="../login.php" class="text-muted small text-decoration-none me-3">
                            <i class="bi bi-person me-1"></i> Student Login
                        </a>
                        <a href="../index.php" class="text-muted small text-decoration-none">
                            <i class="bi bi-house-door me-1"></i> Home
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="../js/script.js"></script>
</body>
</html>
