<?php
/**
 * Home Page
 * Bus Pass Management System
 */
include_once __DIR__ . '/config/db.php';
$page_title = "Welcome";

// Fetch quick live stats for public display
$bus_count_query = mysqli_query($conn, "SELECT COUNT(*) AS total FROM buses WHERE status='Active'");
$bus_count = ($bus_count_query) ? mysqli_fetch_assoc($bus_count_query)['total'] : 0 ;

$route_count_query = mysqli_query($conn, "SELECT COUNT(*) AS total FROM routes");
$route_count = ($route_count_query) ? mysqli_fetch_assoc($route_count_query)['total'] : 0;

include_once __DIR__ . '/includes/header.php';
?>

<!-- Hero Section -->
<section class="hero-section text-center text-lg-start">
    <div class="container">
        <div class="row align-items-center gy-4">
            <div class="col-lg-7">
                <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold mb-3">
                    <i class="bi bi-patch-check-fill me-1"></i> Official Student Transit Portal
                </span>
                <h1 class="display-4 fw-bold text-white mb-3">
                    Smart, Easy & Fast <br><span class="text-warning">Bus Pass Management</span>
                </h1>
                <p class="lead text-light mb-4">
                    Apply for student bus passes online, track real-time approval status, and download or print verified digital passes without long queues.
                </p>
                <div class="d-flex flex-wrap gap-3 justify-content-center justify-content-lg-start">
                    <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'student'): ?>
                        <a href="dashboard.php" class="btn btn-warning btn-lg px-4 fw-bold shadow">
                            <i class="bi bi-speedometer2 me-2"></i> Go to Dashboard
                        </a>
                        <a href="apply_pass.php" class="btn btn-outline-light btn-lg px-4">
                            <i class="bi bi-plus-circle me-2"></i> Apply Bus Pass
                        </a>
                    <?php elseif (isset($_SESSION['user_id']) && $_SESSION['role'] === 'admin'): ?>
                        <a href="admin/dashboard.php" class="btn btn-warning btn-lg px-4 fw-bold shadow">
                            <i class="bi bi-speedometer2 me-2"></i> Admin Dashboard
                        </a>
                    <?php else: ?>
                        <a href="register.php" class="btn btn-warning btn-lg px-4 fw-bold shadow">
                            <i class="bi bi-person-plus-fill me-2"></i> New Student Registration
                        </a>
                        <a href="login.php" class="btn btn-outline-light btn-lg px-4">
                            <i class="bi bi-box-arrow-in-right me-2"></i> Student Login
                        </a>
                        <a href="admin/login.php" class="btn btn-outline-info btn-lg px-3">
                            <i class="bi bi-shield-lock me-1"></i> Admin Portal
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-lg-5 text-center">
                <div class="p-4 bg-white rounded-4 shadow-lg text-dark">
                    <div class="d-flex justify-content-center mb-3">
                        <div class="bg-primary-subtle text-primary p-3 rounded-circle">
                            <i class="bi bi-bus-front fs-1"></i>
                        </div>
                    </div>
                    <h4 class="fw-bold mb-2">Transit Network Overview</h4>
                    <p class="text-muted small mb-4">Access our expanding network of student transit routes and reliable fleets.</p>
                    <div class="row g-2">
                        <div class="col-6">
                            <div class="p-3 bg-light rounded-3 border">
                                <h3 class="fw-bold text-primary mb-0"><?php echo htmlspecialchars($route_count); ?></h3>
                                <small class="text-muted fw-semibold">Active Routes</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 bg-light rounded-3 border">
                                <h3 class="fw-bold text-success mb-0"><?php echo htmlspecialchars($bus_count); ?></h3>
                                <small class="text-muted fw-semibold">Buses in Fleet</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="py-5 bg-white">
    <div class="container py-4">
        <div class="text-center mb-5">
            <span class="text-primary fw-bold text-uppercase small">Key Capabilities</span>
            <h2 class="fw-bold mt-1">Why Use Digital Bus Pass MS?</h2>
            <p class="text-muted">A modern workflow designed to simplify institutional transit administration.</p>
        </div>

        <div class="row g-4">
            <!-- Feature 1 -->
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border-0 shadow-sm p-4 rounded-4 text-center">
                    <div class="mx-auto mb-3 p-3 bg-primary-subtle text-primary rounded-circle" style="width: fit-content;">
                        <i class="bi bi-laptop fs-2"></i>
                    </div>
                    <h5 class="fw-bold">Easy Online Application</h5>
                    <p class="text-muted small">Select your route, preferred bus, and duration (Monthly, Semester, etc.) in just a few clicks.</p>
                </div>
            </div>

            <!-- Feature 2 -->
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border-0 shadow-sm p-4 rounded-4 text-center">
                    <div class="mx-auto mb-3 p-3 bg-success-subtle text-success rounded-circle" style="width: fit-content;">
                        <i class="bi bi-clock-history fs-2"></i>
                    </div>
                    <h5 class="fw-bold">Real-time Application Tracking</h5>
                    <p class="text-muted small">Know immediately when your pass application is reviewed, approved, or if notes were added.</p>
                </div>
            </div>

            <!-- Feature 3 -->
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border-0 shadow-sm p-4 rounded-4 text-center">
                    <div class="mx-auto mb-3 p-3 bg-warning-subtle text-warning rounded-circle" style="width: fit-content;">
                        <i class="bi bi-qr-code fs-2"></i>
                    </div>
                    <h5 class="fw-bold">Digital Bus Pass & Print</h5>
                    <p class="text-muted small">Instant generation of digital verified bus passes formatted ready for printing.</p>
                </div>
            </div>

            <!-- Feature 4 -->
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border-0 shadow-sm p-4 rounded-4 text-center">
                    <div class="mx-auto mb-3 p-3 bg-info-subtle text-info rounded-circle" style="width: fit-content;">
                        <i class="bi bi-map fs-2"></i>
                    </div>
                    <h5 class="fw-bold">Route Management</h5>
                    <p class="text-muted small">Transparent listings of pickup points, stops, destinations, and calculated transit fares.</p>
                </div>
            </div>

            <!-- Feature 5 -->
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border-0 shadow-sm p-4 rounded-4 text-center">
                    <div class="mx-auto mb-3 p-3 bg-danger-subtle text-danger rounded-circle" style="width: fit-content;">
                        <i class="bi bi-shield-check fs-2"></i>
                    </div>
                    <h5 class="fw-bold">Administrative Approval</h5>
                    <p class="text-muted small">Authorized administrators review student profiles and approve valid passes safely.</p>
                </div>
            </div>

            <!-- Feature 6 -->
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border-0 shadow-sm p-4 rounded-4 text-center">
                    <div class="mx-auto mb-3 p-3 bg-secondary-subtle text-secondary rounded-circle" style="width: fit-content;">
                        <i class="bi bi-lock fs-2"></i>
                    </div>
                    <h5 class="fw-bold">Secure Authentication</h5>
                    <p class="text-muted small">Encrypted passwords, secure sessions, and role-based permissions protect student data.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Active Routes Preview -->
<section class="py-5 bg-light">
    <div class="container py-3">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold mb-1">Available Bus Routes</h3>
                <p class="text-muted mb-0">Browse current routes serviced by the transit system.</p>
            </div>
            <a href="register.php" class="btn btn-outline-primary btn-sm fw-semibold">Apply For Route</a>
        </div>

        <div class="row g-3">
            <?php
            $routes_query = mysqli_query($conn, "SELECT * FROM routes ORDER BY id ASC LIMIT 4");
            if ($routes_query && mysqli_num_rows($routes_query) > 0):
                while ($route = mysqli_fetch_assoc($routes_query)):
            ?>
                <div class="col-md-6 col-lg-3">
                    <div class="card border-0 shadow-sm h-100 rounded-3 p-3">
                        <div class="d-flex align-items-center gap-2 text-primary fw-bold mb-2">
                            <i class="bi bi-geo-alt-fill"></i>
                            <span><?php echo htmlspecialchars($route['route_name']); ?></span>
                        </div>
                        <div class="small text-muted mb-2">
                            <div><strong>From:</strong> <?php echo htmlspecialchars($route['start_point']); ?></div>
                            <div><strong>To:</strong> <?php echo htmlspecialchars($route['end_point']); ?></div>
                        </div>
                        <div class="mt-auto pt-2 border-top d-flex justify-content-between align-items-center">
                            <span class="badge bg-light text-dark border">Fare</span>
                            <span class="fw-bold text-success">₹<?php echo number_format($route['fare'], 2); ?></span>
                        </div>
                    </div>
                </div>
            <?php 
                endwhile;
            else: 
            ?>
                <div class="col-12 text-center text-muted py-4">No routes listed yet.</div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php include_once __DIR__ . '/includes/footer.php'; ?>
