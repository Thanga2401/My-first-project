<?php
$current_admin_page = basename($_SERVER['PHP_SELF']);
?>
<div class="position-sticky pt-3">
    <div class="px-3 pb-3 mb-2 border-bottom border-secondary text-secondary small text-uppercase fw-bold">
        Navigation
    </div>
    <ul class="nav flex-column mb-auto">
        <li class="nav-item">
            <a class="nav-link <?php echo ($current_admin_page == 'dashboard.php') ? 'active' : ''; ?>" href="dashboard.php">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo in_array($current_admin_page, ['students.php', 'view_student.php']) ? 'active' : ''; ?>" href="students.php">
                <i class="bi bi-people"></i> Manage Students
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo in_array($current_admin_page, ['applications.php', 'view_application.php', 'reject_application.php']) ? 'active' : ''; ?>" href="applications.php">
                <i class="bi bi-file-earmark-text"></i> Pass Applications
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo in_array($current_admin_page, ['buses.php', 'add_bus.php', 'edit_bus.php']) ? 'active' : ''; ?>" href="buses.php">
                <i class="bi bi-bus-front"></i> Manage Buses
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo in_array($current_admin_page, ['routes.php', 'add_route.php', 'edit_route.php']) ? 'active' : ''; ?>" href="routes.php">
                <i class="bi bi-map"></i> Manage Routes
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo ($current_admin_page == 'database_view.php') ? 'active' : ''; ?>" href="database_view.php">
                <i class="bi bi-database"></i> Database Tables View
            </a>
        </li>
    </ul>

    <hr class="border-secondary mx-3 my-4">

    <div class="px-3 pb-3">
        <a href="logout.php" class="btn btn-outline-danger btn-sm w-100 d-flex align-items-center justify-content-center gap-2">
            <i class="bi bi-box-arrow-right"></i> Sign Out
        </a>
    </div>
</div>
