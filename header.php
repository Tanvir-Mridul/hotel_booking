<!-- Bootstrap 4 CSS -->
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm fixed-top">
    <div class="container">
        <!-- Logo/Brand -->
        <a class="navbar-brand font-weight-bold text-primary" href="/hotel_booking/index.php">
            <i class="fas fa-hotel mr-2"></i> HOTEL BOOKING
        </a>

        <!-- Mobile Toggle Button -->
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navbar Content -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <!-- Left Menu Items -->
            <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                    <a class="nav-link active" href="/hotel_booking/index.php">
                        <i class="fas fa-home mr-1"></i> Home
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/hotel_booking/hotel/hotel_list.php">
                        <i class="fas fa-hotel mr-1"></i> Hotels
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="fas fa-info-circle mr-1"></i> About
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="fas fa-phone mr-1"></i> Contact
                    </a>
                </li>
            </ul>

            <!-- Right Menu Items -->
            <ul class="navbar-nav ml-auto">
                <?php if(isset($_SESSION['user_id'])): ?>
                    <!-- User is Logged In -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" 
                           role="button" data-toggle="dropdown">
                            <div class="mr-2" style="width: 30px; height: 30px; background: #3498db; 
                                  border-radius: 50%; display: flex; align-items: center; justify-content: center; 
                                  color: white; font-weight: bold;">
                                <?php echo strtoupper(substr($_SESSION['name'], 0, 1)); ?>
                            </div>
                            <span><?php echo htmlspecialchars($_SESSION['name']); ?></span>
                        </a>
                        
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
                            <!-- User Info -->
                            <div class="dropdown-header">
                                <strong><?php echo htmlspecialchars($_SESSION['name']); ?></strong>
                                <div class="small text-muted"><?php echo $_SESSION['role']; ?></div>
                            </div>
                            <div class="dropdown-divider"></div>
                            
                            <!-- User Links -->
                            <?php if($_SESSION['role'] == 'user'): ?>
                                <a class="dropdown-item" href="/hotel_booking/user/dashboard.php">
                                    <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
                                </a>
                                <a class="dropdown-item" href="/hotel_booking/user/my_booking.php">
                                    <i class="fas fa-calendar-alt mr-2"></i> My Bookings
                                </a>
                                <a class="dropdown-item" href="/hotel_booking/user/profile.php">
                                    <i class="fas fa-user mr-2"></i> Profile
                                </a>
                            <?php elseif($_SESSION['role'] == 'owner'): ?>
                                <a class="dropdown-item" href="owner/dashboard.php">
                                    <i class="fas fa-tachometer-alt mr-2"></i> Owner Panel
                                </a>
                                <a class="dropdown-item" href="owner/upload_flat.php">
                                    <i class="fas fa-plus-circle mr-2"></i> Upload Flat
                                </a>
                            <?php elseif($_SESSION['role'] == 'admin'): ?>
                                <a class="dropdown-item" href="admin/dashboard.php">
                                    <i class="fas fa-cog mr-2"></i> Admin Panel
                                </a>
                                <a class="dropdown-item" href="admin/hotels.php">
                                    <i class="fas fa-hotel mr-2"></i> Manage Hotels
                                </a>
                            <?php endif; ?>
                            
                            <div class="dropdown-divider"></div>
                            
                            <!-- Logout -->
                            <a class="dropdown-item text-danger" href="logout.php">
                                <i class="fas fa-sign-out-alt mr-2"></i> Logout
                            </a>
                        </div>
                    </li>
                    
                    <!-- Notification Bell -->
                    <li class="nav-item">
                        <a class="nav-link position-relative" href="user/my_booking.php">
                            <i class="fas fa-bell"></i>
                            <?php
                            // Count unread notifications
                            $notification_count = 0;
                            if($notification_count > 0): ?>
                            <span class="badge badge-danger badge-pill position-absolute" 
                                  style="top: 0; right: 0; font-size: 0.6em;">
                                <?php echo $notification_count; ?>
                            </span>
                            <?php endif; ?>
                        </a>
                    </li>
                    
                <?php else: ?>
                    <!-- User is NOT Logged In -->
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">
                            <i class="fas fa-sign-in-alt mr-1"></i> Login
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-primary ml-2" href="register.php">
                            <i class="fas fa-user-plus mr-1"></i> Sign Up
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<!-- Space for fixed navbar -->
<div style="height: 70px;"></div>

<!-- Bootstrap 4 JS -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
// Navbar active state
$(document).ready(function() {
    var currentPage = window.location.pathname.split('/').pop();
    $('.nav-link').each(function() {
        var linkPage = $(this).attr('href');
        if(linkPage === currentPage) {
            $(this).addClass('active');
        } else {
            $(this).removeClass('active');
        }
    });
});
</script>