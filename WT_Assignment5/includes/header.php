<!DOCTYPE html>
<html>
<head>
    <title><?php echo $pageTitle ?? 'Mentee Management System'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg fixed-top custom-navbar">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="index.php">
                <div class="logo-wrapper">
                    <img src="https://raw.githubusercontent.com/icons8/flat-color-icons/master/svg/mentoring.svg" 
                         alt="Mentor Management"
                         height="45"
                         class="brand-logo"
                         onerror="this.onerror=null; this.src='https://via.placeholder.com/45x45/2b4c8a/ffffff?text=M';">
                </div>
                <div class="brand-text">
                    <span class="fs-4 fw-bold text-white">Mentor</span>
                    <span class="fs-4 fw-light text-white">Connect</span>
                </div>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link text-light" href="index.php">
                            <i class="bi bi-house-door"></i> Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-light" href="mentors.php">
                            <i class="bi bi-people"></i> Mentors
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-light" href="schedule_meeting.php">
                            <i class="bi bi-calendar-check"></i> Schedule Meeting
                        </a>
                    </li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link text-light position-relative" href="#" data-bs-toggle="dropdown">
                                <i class="bi bi-bell"></i>
                                <?php 
                                require_once 'config/notifications.php';
                                $notifications = new Notifications($conn);
                                $unread = $notifications->getUnreadCount($_SESSION['user_id']);
                                if ($unread > 0):
                                ?>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    <?php echo $unread; ?>
                                </span>
                                <?php endif; ?>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end notification-dropdown shadow">
                                <!-- Notifications will be loaded here via AJAX -->
                            </div>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle text-light" href="#" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle"></i> Account
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow">
                                <?php if ($_SESSION['role'] === 'admin'): ?>
                                    <li><a class="dropdown-item" href="manage_users.php">
                                        <i class="bi bi-gear me-2"></i> Manage Users
                                    </a></li>
                                    <li><hr class="dropdown-divider"></li>
                                <?php endif; ?>
                                <li><a class="dropdown-item" href="profile.php">
                                    <i class="bi bi-person me-2"></i> My Profile
                                </a></li>
                                <li><a class="dropdown-item" href="meetings.php">
                                    <i class="bi bi-calendar me-2"></i> My Meetings
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="logout.php">
                                    <i class="bi bi-box-arrow-right me-2"></i> Logout
                                </a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link text-light" href="login.php">
                                <i class="bi bi-box-arrow-in-right"></i> Login
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-light btn btn-primary ms-2" href="register.php">
                                <i class="bi bi-person-plus"></i> Register
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    <!-- Add spacing for fixed navbar -->
    <div class="navbar-spacing"></div>
</body>
</html>
