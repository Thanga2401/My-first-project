<?php
/**
 * Apply for Bus Pass
 * Bus Pass Management System
 */
include_once __DIR__ . '/config/db.php';
include_once __DIR__ . '/includes/auth.php';

requireStudent();

$page_title = "Apply for Bus Pass";
$user_id = $_SESSION['user_id'];
$student = getLoggedInStudent($conn, $user_id);
$student_table_id = $student['student_table_id'] ?? 0;

if ($student_table_id === 0) {
    $_SESSION['flash_error'] = "Please complete your student profile before applying for a pass.";
    header("Location: profile.php");
    exit;
}

$errors = [];
$form_data = [
    'route_id' => '',
    'bus_id' => '',
    'pass_type' => 'Monthly',
    'start_date' => date('Y-m-d'),
    'end_date' => date('Y-m-d', strtotime('+1 month'))
];

// Fetch active routes
$routes = [];
$routes_res = mysqli_query($conn, "SELECT id, route_name, start_point, end_point, fare FROM routes ORDER BY route_name ASC");
if ($routes_res) {
    while ($row = mysqli_fetch_assoc($routes_res)) {
        $routes[] = $row;
    }
}

// Fetch active buses
$buses = [];
$buses_res = mysqli_query($conn, "SELECT id, bus_number, bus_name, capacity FROM buses WHERE status = 'Active' ORDER BY bus_number ASC");
if ($buses_res) {
    while ($row = mysqli_fetch_assoc($buses_res)) {
        $buses[] = $row;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $route_id   = (int)($_POST['route_id'] ?? 0);
    $bus_id     = (int)($_POST['bus_id'] ?? 0);
    $pass_type  = trim($_POST['pass_type'] ?? '');
    $start_date = trim($_POST['start_date'] ?? '');
    $end_date   = trim($_POST['end_date'] ?? '');

    $form_data = [
        'route_id' => $route_id,
        'bus_id' => $bus_id,
        'pass_type' => $pass_type,
        'start_date' => $start_date,
        'end_date' => $end_date
    ];

    $allowed_pass_types = ['Daily', 'Monthly', 'Quarterly', 'Semester'];

    if ($route_id <= 0) {
        $errors[] = "Please select a valid bus route.";
    }

    if ($bus_id <= 0) {
        $errors[] = "Please select an assigned bus.";
    }

    if (!in_array($pass_type, $allowed_pass_types)) {
        $errors[] = "Please select a valid pass duration/type.";
    }

    if (empty($start_date) || empty($end_date)) {
        $errors[] = "Both Start Date and End Date are required.";
    } else {
        $start_timestamp = strtotime($start_date);
        $end_timestamp   = strtotime($end_date);

        if (!$start_timestamp || !$end_timestamp) {
            $errors[] = "Invalid date format provided.";
        } elseif ($end_timestamp < $start_timestamp) {
            $errors[] = "End Date cannot be earlier than Start Date.";
        }
    }

    // Check if there is already a Pending application for this student
    if (empty($errors)) {
        $check_pending = "SELECT id FROM bus_pass_applications WHERE student_id = ? AND status = 'Pending' LIMIT 1";
        $stmt_check = mysqli_prepare($conn, $check_pending);
        mysqli_stmt_bind_param($stmt_check, "i", $student_table_id);
        mysqli_stmt_execute($stmt_check);
        mysqli_stmt_store_result($stmt_check);
        if (mysqli_stmt_num_rows($stmt_check) > 0) {
            $errors[] = "You already have a Pending bus pass application. Please wait for it to be reviewed.";
        }
        mysqli_stmt_close($stmt_check);
    }

    // Insert Application
    if (empty($errors)) {
        $insert_sql = "INSERT INTO bus_pass_applications (student_id, route_id, bus_id, pass_type, start_date, end_date, status) 
                       VALUES (?, ?, ?, ?, ?, ?, 'Pending')";
        $stmt = mysqli_prepare($conn, $insert_sql);
        mysqli_stmt_bind_param($stmt, "iiisss", $student_table_id, $route_id, $bus_id, $pass_type, $start_date, $end_date);
        
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['flash_success'] = "Bus pass application submitted successfully.";
            header("Location: my_applications.php");
            exit;
        } else {
            $errors[] = "Failed to submit application. Please try again.";
        }
        mysqli_stmt_close($stmt);
    }
}

include_once __DIR__ . '/includes/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
            <div class="card card-custom border-0 shadow-lg">
                <div class="card-header bg-primary text-white py-3 rounded-top-3">
                    <h4 class="mb-0 fw-bold">
                        <i class="bi bi-card-checklist me-2"></i>Apply for Digital Bus Pass
                    </h4>
                    <p class="text-light small mb-0 mt-1">Select your transit route and duration for institutional bus approval</p>
                </div>

                <div class="card-body p-4 p-md-5">
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger" role="alert">
                            <h6 class="fw-bold mb-2"><i class="bi bi-exclamation-octagon-fill me-1"></i> Please correct the following issues:</h6>
                            <ul class="mb-0 ps-3">
                                <?php foreach ($errors as $error): ?>
                                    <li><?php echo htmlspecialchars($error); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <!-- Applicant Summary Box -->
                    <div class="p-3 bg-light rounded-3 border mb-4">
                        <div class="row g-2 small">
                            <div class="col-sm-6">
                                <strong>Student:</strong> <?php echo htmlspecialchars($student['name']); ?> (<?php echo htmlspecialchars($student['student_id']); ?>)
                            </div>
                            <div class="col-sm-6">
                                <strong>Department:</strong> <?php echo htmlspecialchars($student['department']); ?> (<?php echo htmlspecialchars($student['year']); ?>)
                            </div>
                            <div class="col-sm-12">
                                <strong>College:</strong> <?php echo htmlspecialchars($student['college']); ?>
                            </div>
                        </div>
                    </div>

                    <form action="apply_pass.php" method="POST">
                        <div class="row g-3 mb-3">
                            <!-- Route Selection -->
                            <div class="col-md-6">
                                <label for="route_id" class="form-label fw-semibold">Select Route <span class="text-danger">*</span></label>
                                <select class="form-select" id="route_id" name="route_id" required>
                                    <option value="" disabled <?php echo empty($form_data['route_id']) ? 'selected' : ''; ?>>Choose a Route...</option>
                                    <?php foreach ($routes as $r): ?>
                                        <option value="<?php echo $r['id']; ?>" <?php echo ($form_data['route_id'] == $r['id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($r['route_name']); ?> (₹<?php echo number_format($r['fare'], 2); ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Bus Selection -->
                            <div class="col-md-6">
                                <label for="bus_id" class="form-label fw-semibold">Select Bus <span class="text-danger">*</span></label>
                                <select class="form-select" id="bus_id" name="bus_id" required>
                                    <option value="" disabled <?php echo empty($form_data['bus_id']) ? 'selected' : ''; ?>>Choose a Bus...</option>
                                    <?php foreach ($buses as $b): ?>
                                        <option value="<?php echo $b['id']; ?>" <?php echo ($form_data['bus_id'] == $b['id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($b['bus_number']); ?> - <?php echo htmlspecialchars($b['bus_name']); ?> (Cap: <?php echo $b['capacity']; ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <!-- Pass Type -->
                        <div class="mb-3">
                            <label for="pass_type" class="form-label fw-semibold">Pass Type / Duration <span class="text-danger">*</span></label>
                            <select class="form-select" id="pass_type" name="pass_type" required>
                                <option value="Daily" <?php echo ($form_data['pass_type'] === 'Daily') ? 'selected' : ''; ?>>Daily Pass</option>
                                <option value="Monthly" <?php echo ($form_data['pass_type'] === 'Monthly') ? 'selected' : ''; ?>>Monthly Pass (30 Days)</option>
                                <option value="Quarterly" <?php echo ($form_data['pass_type'] === 'Quarterly') ? 'selected' : ''; ?>>Quarterly Pass (3 Months)</option>
                                <option value="Semester" <?php echo ($form_data['pass_type'] === 'Semester') ? 'selected' : ''; ?>>Semester Pass (6 Months)</option>
                            </select>
                            <small class="text-muted">Selecting duration automatically calculates standard validity dates below.</small>
                        </div>

                        <div class="row g-3 mb-4">
                            <!-- Start Date -->
                            <div class="col-md-6">
                                <label for="start_date" class="form-label fw-semibold">Start Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="start_date" name="start_date" value="<?php echo htmlspecialchars($form_data['start_date']); ?>" required>
                            </div>

                            <!-- End Date -->
                            <div class="col-md-6">
                                <label for="end_date" class="form-label fw-semibold">End Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="end_date" name="end_date" value="<?php echo htmlspecialchars($form_data['end_date']); ?>" required>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                            <a href="dashboard.php" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-1"></i> Back to Dashboard
                            </a>
                            <button type="submit" class="btn btn-primary px-4 fw-bold shadow-sm">
                                <i class="bi bi-send-check me-1"></i> Submit Application
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/includes/footer.php'; ?>
