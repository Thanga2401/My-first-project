<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$current_page = basename($_SERVER['PHP_SELF']);
$is_admin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
$is_student = isset($_SESSION['role']) && $_SESSION['role'] === 'student';
$is_logged_in = isset($_SESSION['user_id']);
$base_path = isset($is_admin_area) && $is_admin_area ? "../" : "";
?>

<nav class="navbar navbar-expand-lg navbar-dark navbar-custom sticky-top no-print">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2" href="<?php echo $base_path; ?>index.php">
            <i class="bi bi-bus-front-fill fs-3 text-warning"></i>
            <span>BUS PASS MS</span>
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'index.php') ? 'active' : ''; ?>" href="<?php echo $base_path; ?>index.php">
                        <i class="bi bi-house-door me-1"></i> Home
                    </a>
                </li>

                <?php if ($is_student): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>" href="<?php echo $base_path; ?>dashboard.php">
                            <i class="bi bi-speedometer2 me-1"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($current_page == 'apply_pass.php') ? 'active' : ''; ?>" href="<?php echo $base_path; ?>apply_pass.php">
                            <i class="bi bi-card-checklist me-1"></i> Apply Pass
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($current_page == 'my_applications.php') ? 'active' : ''; ?>" href="<?php echo $base_path; ?>my_applications.php">
                            <i class="bi bi-clock-history me-1"></i> My Applications
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($current_page == 'my_pass.php') ? 'active' : ''; ?>" href="<?php echo $base_path; ?>my_pass.php">
                            <i class="bi bi-qr-code me-1"></i> My Pass
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($current_page == 'profile.php') ? 'active' : ''; ?>" href="<?php echo $base_path; ?>profile.php">
                            <i class="bi bi-person-circle me-1"></i> Profile
                        </a>
                    </li>
                <?php endif; ?>

                <?php if ($is_admin): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>" href="<?php echo $base_path; ?>admin/dashboard.php">
                            <i class="bi bi-speedometer2 me-1"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo in_array($current_page, ['students.php', 'view_student.php']) ? 'active' : ''; ?>" href="<?php echo $base_path; ?>admin/students.php">
                            <i class="bi bi-people me-1"></i> Students
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo in_array($current_page, ['applications.php', 'view_application.php']) ? 'active' : ''; ?>" href="<?php echo $base_path; ?>admin/applications.php">
                            <i class="bi bi-file-earmark-text me-1"></i> Applications
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo in_array($current_page, ['buses.php', 'add_bus.php', 'edit_bus.php']) ? 'active' : ''; ?>" href="<?php echo $base_path; ?>admin/buses.php">
                            <i class="bi bi-bus-front me-1"></i> Buses
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo in_array($current_page, ['routes.php', 'add_route.php', 'edit_route.php']) ? 'active' : ''; ?>" href="<?php echo $base_path; ?>admin/routes.php">
                            <i class="bi bi-map me-1"></i> Routes
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($current_page == 'database_view.php') ? 'active' : ''; ?>" href="<?php echo $base_path; ?>admin/database_view.php">
                            <i class="bi bi-database me-1"></i> Database View
                        </a>
                    </li>
                <?php endif; ?>
            </ul>

            <ul class="navbar-nav ms-auto align-items-lg-center gap-2">
                <?php if ($is_logged_in): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="badge bg-warning text-dark px-2 py-1 text-uppercase"><?php echo htmlspecialchars($_SESSION['role']); ?></span>
                            <span><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0" aria-labelledby="userDropdown">
                            <?php if ($is_student): ?>
                                <li><a class="dropdown-item" href="<?php echo $base_path; ?>profile.php"><i class="bi bi-person me-2"></i> My Profile</a></li>
                                <li><a class="dropdown-item" href="<?php echo $base_path; ?>my_applications.php"><i class="bi bi-list-check me-2"></i> My Applications</a></li>
                            <?php else: ?>
                                <li><a class="dropdown-item" href="<?php echo $base_path; ?>admin/dashboard.php"><i class="bi bi-speedometer2 me-2"></i> Admin Dashboard</a></li>
                                <li><a class="dropdown-item" href="<?php echo $base_path; ?>admin/database_view.php"><i class="bi bi-database me-2"></i> Database Viewer</a></li>
                            <?php endif; ?>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item text-danger" href="<?php echo ($is_admin) ? $base_path . 'admin/logout.php' : $base_path . 'logout.php'; ?>">
                                    <i class="bi bi-box-arrow-right me-2"></i> Logout
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="btn btn-outline-light btn-sm px-3" href="<?php echo $base_path; ?>login.php">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Student Login
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-warning btn-sm px-3 text-dark fw-semibold" href="<?php echo $base_path; ?>register.php">
                            <i class="bi bi-person-plus me-1"></i> Register
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-outline-info btn-sm px-3" href="<?php echo $base_path; ?>admin/login.php">
                            <i class="bi bi-shield-lock me-1"></i> Admin Login
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
