<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SAMS - Student Admissions Management System</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css">
    <!-- Custom CSS -->
    <link href="/sams2/assets/css/custom.css" rel="stylesheet">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>
    <!-- Initialize SweetAlert2 -->
    <script>
        // Wait for SweetAlert2 to load
        function waitForSwal(callback) {
            if (typeof Swal !== 'undefined') {
                callback();
            } else {
                setTimeout(function() {
                    waitForSwal(callback);
                }, 100);
            }
        }

        // Initialize SweetAlert2 with default settings
        waitForSwal(function() {
            // Store original Swal.fire
            const originalSwalFire = Swal.fire;
            
            // Override Swal.fire to ensure alerts stay open
            Swal.fire = function(...args) {
                const config = args[0] || {};
                
                // Ensure alerts stay open unless explicitly configured otherwise
                if (config.timer === undefined) {
                    config.timer = null;
                }
                
                // Ensure backdrop is enabled
                if (config.backdrop === undefined) {
                    config.backdrop = true;
                }
                
                // Ensure outside click is disabled
                if (config.allowOutsideClick === undefined) {
                    config.allowOutsideClick = false;
                }
                
                // Ensure escape key is disabled
                if (config.allowEscapeKey === undefined) {
                    config.allowEscapeKey = false;
                }
                
                // Ensure confirm button is shown
                if (config.showConfirmButton === undefined) {
                    config.showConfirmButton = true;
                }
                
                // Ensure close button is shown
                if (config.showCloseButton === undefined) {
                    config.showCloseButton = true;
                }
                
                return originalSwalFire.call(this, config);
            };

            // Set window.Swal to ensure global access
            window.Swal = Swal;
        });
    </script>
</head>
<body id="page-top"> 