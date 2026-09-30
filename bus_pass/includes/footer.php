<?php
$base_path = isset($is_admin_area) && $is_admin_area ? "../" : "";
?>
</main>

<footer class="footer-custom mt-auto no-print">
    <div class="container">
        <div class="row align-items-center py-3">
            <div class="col-md-6 text-center text-md-start mb-2 mb-md-0">
                <div class="d-flex align-items-center justify-content-center justify-content-md-start gap-2">
                    <i class="bi bi-bus-front-fill text-warning fs-5"></i>
                    <span class="fw-bold text-white">BUS PASS MANAGEMENT SYSTEM</span>
                </div>
                <small class="text-secondary d-block mt-1">A digital portal for seamless student bus pass issuance & tracking.</small>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <p class="mb-0 text-secondary small">
                    &copy; <?php echo date('Y'); ?> Bus Pass Management System. All rights reserved.
                </p>
            </div>
        </div>
    </div>
</footer>

<!-- Bootstrap 5 Bundle JS with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- Custom Application Script -->
<script src="<?php echo $base_path; ?>js/script.js"></script>
</body>
</html>
