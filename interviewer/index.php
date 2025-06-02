<?php
session_start();
require_once '../includes/auth_check.php';
require_once "../includes/header.php";
// Check if user is interviewer
if (!isset($_SESSION["user_type"]) || $_SESSION["user_type"] !== "interviewer") {
    header("Location: ../auth/login.php");
    exit();
}

require_once '../includes/auth_check.php';

$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
$allowed_pages = ['dashboard', 'interview_schedules', 'interview_evaluation', 'interview_results', 'profile'];

if (!in_array($page, $allowed_pages)) {
    $page = 'dashboard';
}

$page_title = ucfirst(str_replace('_', ' ', $page)) . " - Student Admissions Management System";

// Include the page content first to handle any redirects
$page_file = $page . '.php';
if (file_exists($page_file)) {
    ob_start();
    include $page_file;
    $page_content = ob_get_clean();
} else {
    ob_start();
    include 'dashboard.php';
    $page_content = ob_get_clean();
}

// Now include the header and output the page
require_once "../includes/header.php";
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
                    <?php echo $page_content; ?>
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
    <?php if (in_array($page, ['interview_schedules', 'interview_results', 'interview_evaluation'])): ?>
    <script src="../assets/vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="../assets/vendor/datatables/dataTables.bootstrap4.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#dataTable').DataTable();
        });
    </script>
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
?>