<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SAMS - Student Admissions Management System</title>
    
    <!-- Custom fonts for this template-->
    <link href="../assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    
    <!-- Custom styles for this template-->
    <link href="../assets/css/sb-admin-2.min.css" rel="stylesheet">
    <link href="../assets/css/custom.css" rel="stylesheet">
    
    <!-- DataTables CSS -->
    <link href="../assets/vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.bootstrap4.min.css" rel="stylesheet">
    
    <!-- jQuery -->
    <script src="../assets/vendor/jquery/jquery.min.js"></script>
    
    <!-- Bootstrap core JavaScript-->
    <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    
    <!-- Core plugin JavaScript-->
    <script src="../assets/vendor/jquery-easing/jquery.easing.min.js"></script>
    
    <!-- DataTables JS -->
    <script src="../assets/vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="../assets/vendor/datatables/dataTables.bootstrap4.min.js"></script>
    
    <!-- DataTables Buttons -->
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
    
    <!-- JSZip -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    
    <!-- PDFMake -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    
    <!-- SweetAlert2 -->
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