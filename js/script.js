/**
 * Bus Pass Management System - Main JavaScript
 */

document.addEventListener('DOMContentLoaded', function () {
    // 1. Auto-dismiss Bootstrap alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert-dismissible');
    alerts.forEach(function (alert) {
        setTimeout(function () {
            const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
            if (bsAlert) {
                bsAlert.close();
            }
        }, 5000);
    });

    // 2. Password Visibility Toggle
    const togglePasswordButtons = document.querySelectorAll('.toggle-password-btn');
    togglePasswordButtons.forEach(function (btn) {
        btn.addEventListener('click', function () {
            const targetInputId = this.getAttribute('data-target');
            const targetInput = document.getElementById(targetInputId);
            if (targetInput) {
                if (targetInput.type === 'password') {
                    targetInput.type = 'text';
                    this.innerHTML = '<i class="bi bi-eye-slash"></i>';
                } else {
                    targetInput.type = 'password';
                    this.innerHTML = '<i class="bi bi-eye"></i>';
                }
            }
        });
    });

    // 3. Date validation on Apply Pass form
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');
    const passTypeSelect = document.getElementById('pass_type');

    if (startDateInput && endDateInput) {
        // Set minimum start date to today
        const today = new Date().toISOString().split('T')[0];
        startDateInput.min = today;

        startDateInput.addEventListener('change', function () {
            endDateInput.min = this.value;
            autoCalculateEndDate();
        });

        if (passTypeSelect) {
            passTypeSelect.addEventListener('change', function () {
                autoCalculateEndDate();
            });
        }
    }

    function autoCalculateEndDate() {
        if (!startDateInput || !endDateInput || !startDateInput.value || !passTypeSelect) return;
        const start = new Date(startDateInput.value);
        let end = new Date(startDateInput.value);

        const type = passTypeSelect.value;
        if (type === 'Daily') {
            end.setDate(start.getDate());
        } else if (type === 'Monthly') {
            end.setMonth(start.getMonth() + 1);
        } else if (type === 'Quarterly') {
            end.setMonth(start.getMonth() + 3);
        } else if (type === 'Semester') {
            end.setMonth(start.getMonth() + 6);
        }

        endDateInput.value = end.toISOString().split('T')[0];
    }

    // 4. Confirmation dialog for critical actions
    const confirmButtons = document.querySelectorAll('.confirm-action');
    confirmButtons.forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            const message = this.getAttribute('data-confirm') || 'Are you sure you want to proceed?';
            if (!confirm(message)) {
                e.preventDefault();
            }
        });
    });
});
