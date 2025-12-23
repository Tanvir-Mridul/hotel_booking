<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header("Location: ../login.php");
    exit();
}

include "../db_connect.php";

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['name'];

// Function to get home page content without header/footer
function getHomeContent() {
    ob_start();
    
    // Start output buffering
    $db_conn = $GLOBALS['conn'];
    
    // Get approved hotels for featured section
    $sql = "SELECT * FROM hotels WHERE status='approved' ORDER BY id DESC LIMIT 8";
    $result = mysqli_query($db_conn, $sql);
    
    // Get popular destinations
    $destinations = [
        ["Dhaka","dhaka.jpg"],
        ["Cox's Bazar","coxbazar.jpg"],
        ["Sylhet","sylhet.jpg"],
        ["Chittagong","chittagong.jpg"]
    ];
    ?>
    
    <!-- HOME PAGE CONTENT STARTS HERE -->
    
    <!-- Hero Section -->
    <div class="hero-section">
        <div class="hero-overlay"></div>

        <!-- SEARCH BOX -->
        <div class="search-wrapper">
            <!-- Tabs -->
            <div class="search-tabs">
                <button class="active">Hotel</button>
                <button disabled>Flight</button>
                <button disabled>Tour</button>
                <button disabled>Visa</button>
            </div>

            <!-- Search Form -->
            <form class="search-form" action="../hotel/hotel_list.php" method="GET">
                <div class="search-item">
                    <label>City / Hotel / Area</label>
                    <input type="text" name="location" placeholder="Cox's Bazar">
                </div>

                <div class="search-item">
                    <label>Check In</label>
                    <input type="date" name="checkin">
                </div>

                <div class="search-item">
                    <label>Check Out</label>
                    <input type="date" name="checkout">
                </div>

                <div class="search-item">
                    <label>Rooms & Guests</label>
                    <select name="guest">
                        <option>1 Room, 1 Guest</option>
                        <option>1 Room, 2 Guests</option>
                        <option>2 Rooms, 4 Guests</option>
                    </select>
                </div>

                <div class="search-btn">
                    <button type="submit">Search</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Popular Destinations -->
    <div class="container mt-5">
      <h3 class="mb-4">Popular Destinations</h3>

      <div class="row text-center">
        <?php foreach ($destinations as $d): ?>
        <div class="col-md-3 mb-3">
          <a href="../hotel/hotel_list.php?location=<?php echo $d[0]; ?>" class="text-decoration-none text-dark">
            <div class="card destination-card">
              <img src="../assets/img/<?php echo $d[1]; ?>" class="card-img">
              <div class="destination-title"><?php echo $d[0]; ?></div>
            </div>
          </a>
        </div>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- Featured Hotels -->
    <div class="container mt-5">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Featured Hotels</h3>
        <a href="../hotel/hotel_list.php" class="btn btn-outline-primary">View All</a>
      </div>

      <div class="row">
        <?php if(mysqli_num_rows($result) > 0): ?>
          <?php while($row = mysqli_fetch_assoc($result)): ?>
          <div class="col-md-3 mb-4">
            <div class="card hotel-card h-100 shadow-sm">
              <img src="../uploads/<?php echo $row['image']; ?>" 
                   class="card-img-top" 
                   style="height:200px;object-fit:cover;"
                   onerror="this.src='../assets/img/default.jpg'">
              
              <div class="card-body">
                <h6><?php echo $row['hotel_name']; ?></h6>
                <p class="text-muted mb-1"><?php echo $row['location']; ?></p>
                <strong class="text-success">৳ <?php echo $row['price']; ?> / night</strong>
              </div>

              <div class="card-footer bg-white border-0">
                <a href="../hotel/hotel_details.php?id=<?php echo $row['id']; ?>" 
                   class="btn btn-sm btn-primary w-100">
                  View Details
                </a>
              </div>
            </div>
          </div>
          <?php endwhile; ?>
        <?php else: ?>
          <div class="col-12">
            <div class="alert alert-info">
              No hotels available at the moment.
            </div>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- Why Choose Us -->
    <section class="why-section">
      <h2 class="section-title">Why Choose Us</h2>
      <div class="why-grid">
        <div class="why-box">
          <div class="why-icon">💰</div>
          <h4>Best Price Guarantee</h4>
          <p>We offer the best hotel prices in the market.</p>
        </div>
        <div class="why-box">
          <div class="why-icon">✔️</div>
          <h4>Verified Hotels</h4>
          <p>All hotels are verified by our admin team.</p>
        </div>
        <div class="why-box">
          <div class="why-icon">🔒</div>
          <h4>Secure Booking</h4>
          <p>Your payment and personal data are safe.</p>
        </div>
        <div class="why-box">
          <div class="why-icon">📞</div>
          <h4>24/7 Support</h4>
          <p>Our support team is always ready to help.</p>
        </div>
      </div>
    </section>

    <!-- Deals & Offers -->
    <section class="deals-section">
      <h2 class="section-title">Deals & Special Offers</h2>
      <div class="deals-grid">
        <div class="deal-card">
          <span class="deal-badge">30% OFF</span>
          <h4>Cox's Bazar Special</h4>
          <p>Enjoy sea view hotels with exciting discounts.</p>
          <a href="../hotel/hotel_list.php" class="deal-btn">View Hotels</a>
        </div>
        <div class="deal-card">
          <span class="deal-badge">20% OFF</span>
          <h4>Weekend Deal</h4>
          <p>Perfect weekend getaway at affordable prices.</p>
          <a href="../hotel/hotel_list.php" class="deal-btn">Book Now</a>
        </div>
        <div class="deal-card">
          <span class="deal-badge">FREE</span>
          <h4>Breakfast Included</h4>
          <p>Selected hotels offer free breakfast.</p>
          <a href="../hotel/hotel_list.php" class="deal-btn">Explore</a>
        </div>
      </div>
    </section>
    
    <?php
    return ob_get_clean();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    
    <!-- Bootstrap & Font Awesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Main style.css -->
    <link rel="stylesheet" href="../style.css">
    
    <style>
        body {
            margin: 0;
            padding: 0;
            background: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        /* User Top Bar */
        .user-top-bar {
            background: white;
            padding: 15px 30px;
            border-bottom: 1px solid #e0e0e0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        
        .welcome-text {
            font-size: 18px;
            color: #2c3e50;
            font-weight: 500;
            display: flex;
            align-items: center;
        }
        
        .user-quick-links {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        
        .notification-icon {
            position: relative;
            margin-right: 10px;
        }
        
        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #e74c3c;
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            font-size: 11px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        /* User Navigation (Mini Menu) */
        .user-nav {
            display: flex;
            gap: 5px;
            margin-left: 20px;
        }
        
        .user-nav a {
            color: #666;
            text-decoration: none;
            padding: 8px 15px;
            border-radius: 5px;
            font-size: 14px;
            transition: all 0.3s;
        }
        
        .user-nav a:hover {
            background: #f0f0f0;
            color: #3498db;
        }
        
        .user-nav a.active {
            background: #3498db;
            color: white;
        }
        
        /* Home Content Container */
        .home-content {
            padding: 0;
        }
        
        /* Fix for Home Page Sections */
        .hero-section {
            margin-top: 0 !important;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .user-top-bar {
                flex-direction: column;
                gap: 10px;
                padding: 15px;
                text-align: center;
            }
            
            .user-quick-links {
                flex-wrap: wrap;
                justify-content: center;
            }
            
            .user-nav {
                margin-left: 0;
                margin-top: 10px;
                justify-content: center;
            }
        }
    </style>
</head>
<body>

<!-- Main Container (No Sidebar) -->
<div class="main-container">
    <!-- User Top Bar -->
    <div class="user-top-bar">
        <div style="display: flex; align-items: center;">
            <div class="welcome-text">
                <i class="fas fa-user-circle me-2"></i>
                Welcome, <strong><?php echo $user_name; ?></strong>
            </div>
            
            <!-- User Navigation -->
            <div class="user-nav">
                <a href="dashboard.php" class="active">🏠 Home</a>
                <a href="my_booking.php">📅 My Bookings</a>
                <a href="profile.php">👤 Profile</a>
            </div>
        </div>
        
        <div class="user-quick-links">
            <!-- Notification -->
            <div class="notification-icon">
                <a href="my_booking.php" class="text-dark">
                    <i class="fas fa-bell fa-lg"></i>
                    <span class="notification-badge">0</span>
                </a>
            </div>
            
            <!-- Quick Action Buttons -->
            <a href="my_booking.php" class="btn btn-outline-primary btn-sm">
                <i class="fas fa-calendar-alt me-1"></i> Bookings
            </a>
            <a href="../hotel/hotel_list.php" class="btn btn-success btn-sm">
                <i class="fas fa-hotel me-1"></i> Browse Hotels
            </a>
            <a href="../logout.php" class="btn btn-outline-danger btn-sm">
                <i class="fas fa-sign-out-alt me-1"></i> Logout
            </a>
        </div>
    </div>
    
    <!-- Display Home Page Content -->
    <div class="home-content">
        <?php echo getHomeContent(); ?>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Fix any styling issues
document.addEventListener('DOMContentLoaded', function() {
    // Remove any duplicate navbars (from home page content)
    const navbars = document.querySelectorAll('nav.navbar');
    if(navbars.length > 0) {
        navbars.forEach(nav => nav.remove());
    }
    
    // Remove any duplicate footers
    const footers = document.querySelectorAll('footer');
    if(footers.length > 0) {
        footers.forEach(footer => footer.remove());
    }
    
    // Remove any duplicate body/html tags
    const duplicateBody = document.querySelectorAll('body');
    if(duplicateBody.length > 1) {
        for(let i = 1; i < duplicateBody.length; i++) {
            duplicateBody[i].remove();
        }
    }
    
    // Fix margin issues for hero section
    const heroSection = document.querySelector('.hero-section');
    if(heroSection) {
        heroSection.style.marginTop = '0';
    }
    
    // Fix image paths if needed
    const images = document.querySelectorAll('img');
    images.forEach(img => {
        const src = img.src;
        if(src.includes('assets/img/') && !src.includes('../assets/img/')) {
            img.src = '../' + src.split('/').slice(-2).join('/');
        }
        if(src.includes('uploads/') && !src.includes('../uploads/')) {
            img.src = '../' + src.split('/').slice(-2).join('/');
        }
    });
    
    // Fix link paths
    const links = document.querySelectorAll('a');
    links.forEach(link => {
        const href = link.getAttribute('href');
        if(href && href.includes('hotel/') && !href.includes('../hotel/')) {
            link.href = '../' + href;
        }
    });
});
</script>

</body>
</html>