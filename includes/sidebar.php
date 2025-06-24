        <!-- Sidebar -->
    <ul class="navbar-nav sidebar sidebar-dark accordion" id="accordionSidebar">

        <!-- Sidebar - Brand -->
        <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php">
            <div class="sidebar-brand-icon">
                <img src="../assets/images/tuplogo.png" alt="TUP Logo" style="width: 40px; height: 40px;">
            </div>
            <div class="sidebar-brand-text mx-3">TUP SAMS</div>
        </a>

        <!-- Divider -->
        <hr class="sidebar-divider my-0">

        <!-- Nav Item - Dashboard -->
        <li class="nav-item <?php echo (!isset($_GET['page']) || $_GET['page'] == 'dashboard') ? 'active' : ''; ?>">
            <a class="nav-link" href="index.php">
                <i class="fas fa-fw fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
        </li>

        <?php if (isset($_SESSION["user_type"]) && $_SESSION["user_type"] === "admin"): ?>
        <!-- Divider -->
        <hr class="sidebar-divider">

        <!-- Heading -->
        <div class="sidebar-heading">
            Admin
        </div>



        <!-- Nav Item - Colleges & Programs -->
        <li class="nav-item <?php echo (isset($_GET['page']) && ($_GET['page'] == 'colleges' || $_GET['page'] == 'programs')) ? 'active' : ''; ?>">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseColleges" aria-expanded="true" aria-controls="collapseColleges">
                <i class="fas fa-fw fa-university"></i>
                <span>Colleges & Programs</span>
            </a>
            <div id="collapseColleges" class="collapse <?php echo (isset($_GET['page']) && ($_GET['page'] == 'colleges' || $_GET['page'] == 'programs')) ? 'show' : ''; ?>" aria-labelledby="headingColleges" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <a class="collapse-item <?php echo (isset($_GET['page']) && $_GET['page'] == 'colleges') ? 'active' : ''; ?>" href="index.php?page=colleges">
                        <i class="fas fa-fw fa-building"></i>
                        <span>Colleges</span>
                    </a>
                    <a class="collapse-item <?php echo (isset($_GET['page']) && $_GET['page'] == 'programs') ? 'active' : ''; ?>" href="index.php?page=programs">
                        <i class="fas fa-fw fa-graduation-cap"></i>
                        <span>Programs</span>
                    </a>
                </div>
            </div>
        </li>

        <!-- Nav Item - Exam Management -->
        <li class="nav-item <?php echo (isset($_GET['page']) && in_array($_GET['page'], ['program_cutoffs', 'exam_results', 'exam_schedules', 'encode_scores'])) ? 'active' : ''; ?>">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseExamManagement" aria-expanded="true" aria-controls="collapseExamManagement">
                <i class="fas fa-fw fa-tasks"></i>
                <span>Exam Management</span>
            </a>
            <div id="collapseExamManagement" class="collapse <?php echo (isset($_GET['page']) && in_array($_GET['page'], ['program_cutoffs', 'exam_results', 'exam_schedules', 'encode_scores'])) ? 'show' : ''; ?>" aria-labelledby="headingExamManagement" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <a class="collapse-item <?php echo (isset($_GET['page']) && $_GET['page'] == 'exam_schedules') ? 'active' : ''; ?>" href="index.php?page=exam_schedules">
                        <i class="fas fa-fw fa-calendar-alt"></i>
                        <span>Exam Schedules</span>
                    </a>
                    <a class="collapse-item <?php echo (isset($_GET['page']) && $_GET['page'] == 'encode_scores') ? 'active' : ''; ?>" href="index.php?page=encode_scores">
                        <i class="fas fa-fw fa-edit"></i>
                        <span>Encode Scores</span>
                    </a>
                    <a class="collapse-item <?php echo (isset($_GET['page']) && $_GET['page'] == 'exam_results') ? 'active' : ''; ?>" href="index.php?page=exam_results">
                        <i class="fas fa-fw fa-chart-bar"></i>
                        <span>Exam Results</span>
                    </a>
                    <a class="collapse-item <?php echo (isset($_GET['page']) && $_GET['page'] == 'program_cutoffs') ? 'active' : ''; ?>" href="index.php?page=program_cutoffs">
                        <i class="fas fa-fw fa-sort-numeric-down"></i>
                        <span>Program Cutoffs</span>
                    </a>
                </div>
            </div>
        </li>

        <!-- Nav Item - Rankings -->
        <li class="nav-item <?php echo (isset($_GET['page']) && $_GET['page'] == 'student_rankings') ? 'active' : ''; ?>">
            <a class="nav-link" href="index.php?page=student_rankings">
                <i class="fas fa-fw fa-list-ol"></i>
                <span>Rankings</span>
            </a>
        </li>

        <!-- Nav Item - Interview Management -->
        <li class="nav-item <?php echo (isset($_GET['page']) && in_array($_GET['page'], ['interview_schedules', 'interview_results'])) ? 'active' : ''; ?>">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseInterviewManagement" aria-expanded="true" aria-controls="collapseInterviewManagement">
                <i class="fas fa-fw fa-clipboard-list"></i>
                <span>Interview Management</span>
            </a>
            <div id="collapseInterviewManagement" class="collapse <?php echo (isset($_GET['page']) && in_array($_GET['page'], ['interview_schedules', 'interview_results'])) ? 'show' : ''; ?>" aria-labelledby="headingInterviewManagement" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <a class="collapse-item <?php echo (isset($_GET['page']) && $_GET['page'] == 'interview_schedules') ? 'active' : ''; ?>" href="index.php?page=interview_schedules">
                        <i class="fas fa-fw fa-calendar-check"></i>
                        <span>Interview Schedules</span>
                    </a>
                    <a class="collapse-item <?php echo (isset($_GET['page']) && $_GET['page'] == 'interview_results') ? 'active' : ''; ?>" href="index.php?page=interview_results">
                        <i class="fas fa-fw fa-clipboard-check"></i>
                        <span>Interview Results</span>
                    </a>
                </div>
            </div>
        </li>

        <!-- Nav Item - Student Records -->
        <li class="nav-item <?php echo (isset($_GET['page']) && $_GET['page'] == 'student_records') ? 'active' : ''; ?>">
            <a class="nav-link" href="index.php?page=student_records">
                <i class="fas fa-fw fa-user-graduate"></i>
                <span>Student Records</span>
            </a>
        </li>

        <!-- Nav Item - Reports -->
        <li class="nav-item <?php echo (isset($_GET['page']) && $_GET['page'] == 'reports') ? 'active' : ''; ?>">
            <a class="nav-link" href="index.php?page=reports">
                <i class="fas fa-fw fa-file-alt"></i>
                <span>Reports</span>
            </a>
        </li>
        <?php endif; ?>

        <?php if (isset($_SESSION["user_type"]) && $_SESSION["user_type"] === "interviewer"): ?>
        <!-- Divider -->
        <hr class="sidebar-divider">

        <!-- Heading -->
        <div class="sidebar-heading">
            Interviewer
        </div>

        <!-- Nav Item - Interview Schedules -->
        <li class="nav-item <?php echo (isset($_GET['page']) && $_GET['page'] == 'interview_schedules') ? 'active' : ''; ?>">
            <a class="nav-link" href="index.php?page=interview_schedules">
                <i class="fas fa-fw fa-calendar-check"></i>
                <span>Interview Schedules</span>
            </a>
        </li>

        <!-- Nav Item - Interview Evaluation -->
        <li class="nav-item <?php echo (isset($_GET['page']) && $_GET['page'] == 'interview_evaluation') ? 'active' : ''; ?>">
            <a class="nav-link" href="index.php?page=interview_evaluation">
                <i class="fas fa-fw fa-clipboard-list"></i>
                <span>Interview Evaluation</span>
            </a>
        </li>

        <!-- Nav Item - Interview Results -->
        <li class="nav-item <?php echo (isset($_GET['page']) && $_GET['page'] == 'interview_results') ? 'active' : ''; ?>">
            <a class="nav-link" href="index.php?page=interview_results">
                <i class="fas fa-fw fa-clipboard-check"></i>
                <span>Interview Results</span>
            </a>
        </li>

        <!-- Nav Item - Profile -->
        <li class="nav-item <?php echo (isset($_GET['page']) && $_GET['page'] == 'profile') ? 'active' : ''; ?>">
            <a class="nav-link" href="index.php?page=profile">
                <i class="fas fa-fw fa-user"></i>
                <span>Profile</span>
            </a>
        </li>
        <?php endif; ?>

        <?php if (isset($_SESSION["user_type"]) && $_SESSION["user_type"] === "applicant"): ?>
        <!-- Divider -->
        <hr class="sidebar-divider">

        <!-- Heading -->
        <div class="sidebar-heading">
            Applicant
        </div>

        <!-- Nav Item - Select Exam Schedule -->
        <li class="nav-item <?php echo (isset($_GET['page']) && $_GET['page'] == 'exam_registration') ? 'active' : ''; ?>">
            <a class="nav-link" href="index.php?page=exam_registration">
                <i class="fas fa-fw fa-calendar-alt"></i>
                <span>Select Exam Schedule</span>
            </a>
        </li>

        <!-- Nav Item - Select Interview -->
        <li class="nav-item <?php echo (isset($_GET['page']) && $_GET['page'] == 'select_interview') ? 'active' : ''; ?>">
            <a class="nav-link" href="index.php?page=select_interview">
                <i class="fas fa-fw fa-calendar-plus"></i>
                <span>Select Interview</span>
            </a>
        </li>

        <!-- Nav Item - Profile -->
        <li class="nav-item <?php echo (isset($_GET['page']) && $_GET['page'] == 'profile') ? 'active' : ''; ?>">
            <a class="nav-link" href="index.php?page=profile">
                <i class="fas fa-fw fa-user"></i>
                <span>Profile</span>
            </a>
        </li>
        <?php endif; ?>

        <!-- Divider -->
        <hr class="sidebar-divider">

        <!-- Sidebar Toggler (Sidebar) -->
        <div class="text-center d-none d-md-inline">
            <button class="rounded-circle border-0" id="sidebarToggle" style="background-color: rgba(0, 0, 0, 0.2); width: 2.5rem; height: 2.5rem; display: flex; align-items: center; justify-content: center; margin: 1rem auto;">
                <i class="fas fa-angle-left text-white"></i>
            </button>
        </div>
    </ul>
    <!-- End of Sidebar --> 