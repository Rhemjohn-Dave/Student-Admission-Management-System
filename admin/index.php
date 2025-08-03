<?php
// Start session
session_start();

// Include database connection and other required files
require_once "../config/database.php";

// Include the application handler to process POST requests before any output
require_once "handlers/application_handler.php";

// Check if the user is logged in as admin
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["user_type"] !== 'admin') {
    header("location: ../login.php");
    exit;
}

// Determine the current page
$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

// Set the page title based on the current page
$page_title = ucfirst($page) . " - Admin Dashboard";

// Include the header template
// NOTE: The actual HTML output starts AFTER this PHP block.
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title><?php echo $page_title; ?></title>

    <!-- Custom fonts for this template-->
    <link href="../assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="../assets/css/sb-admin-2.min.css" rel="stylesheet">
    
    <!-- TUP Custom Styles -->
    <link href="../assets/css/custom.css" rel="stylesheet">
    
    <!-- DataTables CSS -->
    <link href="../assets/vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">

    <!-- SweetAlert2 CSS and JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- jQuery first, then Bootstrap JS -->
    <script src="../assets/vendor/jquery/jquery.min.js"></script>
</head>

<body id="page-top">
    <!-- Page Wrapper -->
    <div id="wrapper">
        <?php include '../includes/sidebar.php'; ?>

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">
            <!-- Main Content -->
            <div id="content">
                <?php include '../includes/navbar.php'; ?>

                <!-- Begin Page Content -->
                <div class="container-fluid">
                    <?php 
                    // Include the requested page
                    switch($page) {
                        case 'dashboard':
                            include 'dashboard.php';
                            break;
                        case 'colleges':
                            include 'colleges.php';
                            break;
                        case 'programs':
                            include 'programs.php';
                            break;
                        case 'exam_schedules':
                            include 'exam_schedules.php';
                            break;
                        case 'interview_schedules':
                            include 'interview_schedules.php';
                            break;
                        case 'exam_results':
                            include 'exam_results.php';
                            break;
                        case 'encode_scores':
                            include 'encode_scores.php';
                            break;
                        case 'program_cutoffs':
                            include 'program_cutoffs.php';
                            break;
                        case 'student_rankings':
                            include 'student_rankings.php';
                            break;
                        case 'student_records':
                            include 'student_records.php';
                            break;
                        case 'manage_student_programs':
                            include 'manage_student_programs.php';
                            break;
                        case 'exam_rankings':
                            include 'exam_rankings.php';
                            break;
                        case 'reports':
                            include 'reports.php';
                            break;
                        case 'interview_results':
                            include 'interview_results.php';
                            break;
                        default:
                            include 'dashboard.php';
                            break;
                    }
                    ?>
                </div>
                <!-- /.container-fluid -->
            </div>
            <!-- End of Main Content -->

            <?php include '../includes/footer.php'; ?>
        </div>
        <!-- End of Content Wrapper -->
    </div>
    <!-- End of Page Wrapper -->

    <?php include '../includes/logout_modal.php'; ?>

    <!-- Bootstrap core JavaScript-->
    <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="../assets/vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="../assets/js/sb-admin-2.min.js"></script>

    <!-- Page level plugins - Only include if needed -->
    <?php if (in_array($page, ['interviewers', 'colleges', 'exam_schedules', 'interview_schedules', 'student_records', 'manage_student_programs', 'programs', 'exam_rankings', 'encode_scores', 'exam_results', 'program_cutoffs', 'student_rankings'])): ?>
    <!-- DataTables -->
    <script src="../assets/vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="../assets/vendor/datatables/dataTables.bootstrap4.min.js"></script>
    <?php endif; ?>

    <!-- Initialize sidebar toggle -->
    <script>
        $(document).ready(function() {
            // Toggle the side navigation
            $("#sidebarToggle").on('click', function(e) {
                e.preventDefault();
                
                // Toggle classes
                $("body").toggleClass("sidebar-toggled");
                $(".sidebar").toggleClass("toggled");
                $("#content-wrapper").toggleClass("toggled");
                $(".navbar").toggleClass("toggled");
                $("#sidebarToggle").toggleClass("toggled");
                
                // Toggle text visibility in sidebar
                if ($(".sidebar").hasClass("toggled")) {
                    $('.sidebar .collapse').collapse('hide');
                    $('.sidebar .nav-item .nav-link span').hide();
                    $('.sidebar .sidebar-heading').hide();
                } else {
                    $('.sidebar .nav-item .nav-link span').show();
                    $('.sidebar .sidebar-heading').show();
                }
            });

            // Close any open menu accordions when window is resized below 768px
            $(window).resize(function() {
                if ($(window).width() < 768) {
                    $('.sidebar .collapse').collapse('hide');
                }
            });

            // Prevent the content wrapper from scrolling when the fixed side navigation hovered over
            $('body.fixed-nav .sidebar').on('mousewheel DOMMouseScroll wheel', function(e) {
                if ($(window).width() > 768) {
                    var e0 = e.originalEvent,
                        delta = e0.wheelDelta || -e0.detail;
                    this.scrollTop += (delta < 0 ? 1 : -1) * 30;
                    e.preventDefault();
                }
            });
        });
    </script>
</body>

</html>

