<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once "db_connect.php";

/* ===== PREMIUM CHECK START ===== */
$is_premium = false;
$remaining_days = 0;

if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'owner') {
    $owner_id = $_SESSION['user_id'];

    $sub_q = mysqli_query($conn, "
        SELECT end_date, DATEDIFF(end_date, CURDATE()) AS remaining_days
        FROM owner_subscriptions
        WHERE owner_id='$owner_id'
        AND status='approved'
        ORDER BY id DESC
        LIMIT 1
    ");

    if ($sub_q && mysqli_num_rows($sub_q) > 0) {
        $sub = mysqli_fetch_assoc($sub_q);
        $remaining_days = (int) $sub['remaining_days'];

        if ($remaining_days > 0) {
            $is_premium = true;
        }
    }
}
/* ===== PREMIUM CHECK END ===== */

/* ===== Notification ===== */
$notifications = [];
$unread_count = 0;

if (isset($_SESSION['user_id'], $_SESSION['role'])) {
    $uid = $_SESSION['user_id'];
    $role = $_SESSION['role'];

    // Fetch notifications
    $noti_q = mysqli_query($conn, "
        SELECT * FROM notifications 
        WHERE receiver_id='$uid' AND receiver_role='$role'
        ORDER BY id DESC
        LIMIT 15
    ");

    if ($noti_q) {
        while ($row = mysqli_fetch_assoc($noti_q)) {
            $notifications[] = $row;
        }
    }
    
    // Calculate unread count
    $count_sql = "SELECT COUNT(*) as total FROM notifications 
                  WHERE receiver_id = '$uid' 
                  AND receiver_role = '$role' 
                  AND status = 'unread'";
    
    $count_result = mysqli_query($conn, $count_sql);
    if ($count_result && mysqli_num_rows($count_result) > 0) {
        $count_data = mysqli_fetch_assoc($count_result);
        $unread_count = (int)$count_data['total'];
    }
}
?>








<!-- Bootstrap 4 CSS -->
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">


<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm fixed-top">
    <div class="container">
        <!-- Logo/Brand -->
        <a class="navbar-brand font-weight-bold text-primary" href="/hotel_booking/index.php">
            <i class="fa-solid fa-plane"></i> STAYNOVA
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
                    <a class="nav-link" href="/hotel_booking/about.php">
                        <i class="fas fa-info-circle mr-1"></i> About
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/hotel_booking/contact.php">
                        <i class="fas fa-phone mr-1"></i> Contact
                    </a>
                </li>
            </ul>

            <!-- Right Menu Items -->
            <ul class="navbar-nav ml-auto">
                <?php if (isset($_SESSION['user_id'])): ?>
                    
                    <!-- PREMIUM BADGE (ONLY OWNER) - LEFT SIDE -->
                    <?php if ($_SESSION['role'] === 'owner' && $is_premium): ?>
                        <li class="nav-item d-flex align-items-center mr-3">
                            <span class="badge badge-warning" style="font-size: 12px; padding: 5px 10px;">
                                ⭐ Premium | <?= $remaining_days ?> days left
                            </span>
                        </li>
                    <?php endif; ?>

                    <!-- UPGRADE NOW (ONLY OWNER & NOT PREMIUM) - LEFT SIDE -->
                    <?php if ($_SESSION['role'] === 'owner' && !$is_premium): ?>
                        <li class="nav-item mr-3">
                            <a href="/hotel_booking/owner/subscription.php" class="btn btn-warning btn-sm" style="font-size: 12px;">
                                ⭐ Upgrade Now
                            </a>
                        </li>
                    <?php endif; ?>

                    <!-- Notification Bell - MIDDLE -->
                    <li class="nav-item dropdown mr-3">
                        <a class="nav-link dropdown-toggle position-relative" href="#" data-toggle="dropdown">
                            <i class="fas fa-bell"></i>
                            <?php if ($unread_count > 0): ?>
                                <span class="badge badge-danger" 
                                      style="position: absolute; top: 0; right: 0; transform: translate(50%, -50%); 
                                             font-size: 10px; padding: 2px 5px; min-width: 18px; height: 18px;">
                                    <?php echo $unread_count; ?>
                                </span>
                            <?php endif; ?>
                        </a>
                        
                        <div class="dropdown-menu dropdown-menu-right" style="width: 350px; padding: 0;">
                            <div class="p-2 border-bottom bg-light">
                                <h6 class="mb-0"><i class="fas fa-bell mr-2"></i> Notifications</h6>
                            </div>
                            
                            <!-- SCROLLABLE NOTIFICATIONS -->
                            <div style="max-height: 350px; overflow-y: auto;">
                                <?php if (count($notifications) > 0): ?>
                                    <?php foreach ($notifications as $n): ?>
                                        <a href="<?php echo $n['link']; ?>" 
                                           class="dropdown-item d-block py-2 px-3 border-bottom text-decoration-none"
                                           onclick="markAsRead(<?php echo $n['id']; ?>, this)"
                                           style="white-space: normal; word-wrap: break-word;">
                                            <div class="d-flex">
                                                <div class="me-2" style="font-size: 18px; color: #666;">
                                                    <?php 
                                                    $msg = $n['message'];
                                                    if (strpos($msg, '📅') !== false) echo '📅';
                                                    elseif (strpos($msg, '✅') !== false) echo '✅';
                                                    elseif (strpos($msg, '❌') !== false) echo '❌';
                                                    elseif (strpos($msg, '💳') !== false) echo '💳';
                                                    elseif (strpos($msg, '🏨') !== false) echo '🏨';
                                                    elseif (strpos($msg, '⚠️') !== false) echo '⚠️';
                                                    elseif (strpos($msg, '💬') !== false) echo '💬';
                                                    else echo '🔔';
                                                    ?>
                                                </div>
                                                <div style="flex: 1;">
                                                    <div style="font-size: 13px; color: #333; line-height: 1.4;">
                                                        <?php echo $n['message']; ?>
                                                    </div>
                                                    <div style="font-size: 11px; color: #888; margin-top: 2px;">
                                                        <?php echo date("h:i A", strtotime($n['created_at'])); ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="py-3 px-3 text-muted text-center">
                                        <i class="far fa-bell fa-lg mb-2"></i><br>
                                        No notifications
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="p-2 border-top bg-light text-center">
                                <small class="text-muted">Latest notifications</small>
                            </div>
                        </div>
                    </li>

                    <!-- User Dropdown - RIGHT SIDE -->
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
                            <?php if ($_SESSION['role'] == 'user'): ?>
                                <a class="dropdown-item" href="/hotel_booking/user/dashboard.php">
                                    <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
                                </a>
                                <a class="dropdown-item" href="/hotel_booking/user/my_booking.php">
                                    <i class="fas fa-calendar-alt mr-2"></i> My Bookings
                                </a>
                                <a class="dropdown-item" href="/hotel_booking/user/profile.php">
                                    <i class="fas fa-user mr-2"></i> Profile
                                </a>
                                <!-- Messages Link -->
                                <a class="dropdown-item" href="/hotel_booking/user/messages.php">
                                    <i class="fas fa-comments mr-2"></i> Messages
                                    <?php
                                    // Unread message count
                                    $uid = $_SESSION['user_id'];
                                    $unread_q = mysqli_query($conn, "
                                        SELECT COUNT(*) as unread 
                                        FROM chat_messages 
                                        WHERE receiver_id='$uid' 
                                        AND receiver_role='user' 
                                        AND is_read=0
                                    ");
                                    $msg_unread = mysqli_fetch_assoc($unread_q)['unread'];
                                    if ($msg_unread > 0): ?>
                                        <span class="badge badge-danger float-right"><?php echo $msg_unread; ?></span>
                                    <?php endif; ?>
                                </a>
                            <?php elseif ($_SESSION['role'] == 'owner'): ?>
                                <a class="dropdown-item" href="/hotel_booking/owner/dashboard.php">
                                    <i class="fas fa-tachometer-alt mr-2"></i> Owner Panel
                                </a>
                                <a class="dropdown-item" href="/hotel_booking/owner/upload_flat.php">
                                    <i class="fas fa-plus-circle mr-2"></i> Upload Flat
                                </a>
                                <a class="dropdown-item" href="/hotel_booking/owner/manage_bookings.php">
                                    <i class="fas fa-calendar-check mr-2"></i> Manage Booking
                                </a>
                                <a class="dropdown-item" href="/hotel_booking/owner/subscription.php">
                                    <i class="fas fa-crown mr-2"></i> Subscription
                                </a>
                                <a class="dropdown-item" href="/hotel_booking/owner/finance.php">
                                     💰 Finance
                                </a>

                                
                                <a class="dropdown-item" href="/hotel_booking/owner/messages.php">
                                    <i class="fas fa-comments mr-2"></i> Messages
                                    <?php
                                    $uid = $_SESSION['user_id'];
                                    $unread_q = mysqli_query($conn, "
                                        SELECT COUNT(*) as unread 
                                        FROM chat_messages 
                                        WHERE receiver_id='$uid' 
                                        AND receiver_role='owner' 
                                        AND is_read=0
                                    ");
                                    $msg_unread = mysqli_fetch_assoc($unread_q)['unread'];
                                    if ($msg_unread > 0): ?>
                                        <span class="badge badge-danger float-right"><?php echo $msg_unread; ?></span>
                                    <?php endif; ?>
                                </a>
                            <?php elseif ($_SESSION['role'] == 'admin'): ?>
                                <a class="dropdown-item" href="admin/dashboard.php">
                                    <i class="fas fa-cog mr-2"></i> Admin Panel
                                </a>
                                <a class="dropdown-item" href="admin/hotels.php">
                                    <i class="fas fa-hotel mr-2"></i> Manage Hotels
                                </a>
                                <a class="dropdown-item" href="admin/manage_subscriptions.php">
                                    <i class="fas fa-crown mr-2"></i> Manage Subscriptions
                                </a>
                            <?php endif; ?>

                            <div class="dropdown-divider"></div>

                            <!-- Logout -->
                            <a class="dropdown-item text-danger" href="/hotel_booking/logout.php">
                                <i class="fas fa-sign-out-alt mr-2"></i> Logout
                            </a>
                        </div>
                    </li>

                <?php else: ?>
                    <!-- User is NOT Logged In -->
                    <li class="nav-item">
                        <a class="nav-link" href="/hotel_booking/login.php">
                            <i class="fas fa-sign-in-alt mr-1"></i> Login
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-primary ml-2" href="/hotel_booking/register.php">
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
    $(document).ready(function () {
        var currentPage = window.location.pathname.split('/').pop();
        $('.nav-link').each(function () {
            var linkPage = $(this).attr('href');
            if (linkPage === currentPage) {
                $(this).addClass('active');
            } else {
                $(this).removeClass('active');
            }
        });
    });
    
    // Mark notification as read
    function markAsRead(notiId, element) {
        // AJAX call to mark as read
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "/hotel_booking/mark_notification_read.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.send("notification_id=" + notiId);
        
        // Update count badge
        xhr.onload = function() {
            if (xhr.status === 200) {
                var badge = document.querySelector('.fa-bell + .badge');
                if (badge) {
                    var currentCount = parseInt(badge.textContent);
                    if (currentCount > 1) {
                        badge.textContent = currentCount - 1;
                    } else {
                        badge.remove();
                    }
                }
                
                // Remove unread styling
                if (element) {
                    element.style.backgroundColor = '#fff';
                }
            }
        };
    }
</script>

<style>
    /* Custom scrollbar for notifications */
    .dropdown-menu div[style*="overflow-y"]::-webkit-scrollbar {
        width: 6px;
    }
    .dropdown-menu div[style*="overflow-y"]::-webkit-scrollbar-track {
        background: #f8f9fa;
        border-radius: 3px;
    }
    .dropdown-menu div[style*="overflow-y"]::-webkit-scrollbar-thumb {
        background: #ccc;
        border-radius: 3px;
    }
    .dropdown-menu div[style*="overflow-y"]::-webkit-scrollbar-thumb:hover {
        background: #aaa;
    }
</style>