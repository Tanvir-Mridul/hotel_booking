<?php
session_start();
include "header.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Hotel Booking System</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .about-section {
            padding: 80px 0;
            background: linear-gradient(rgba(255,255,255,0.9), rgba(255,255,255,0.9)), 
                        url('assets/img/hotel-bg.jpg') no-repeat center/cover;
        }
        
        .about-content {
            max-width: 1000px;
            margin: 0 auto;
        }
        
        .about-card {
            background: white;
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        
        .mission-vision {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-top: 40px;
        }
        
        .mission-box, .vision-box {
            background: #f8f9fa;
            padding: 30px;
            border-radius: 10px;
            border-left: 5px solid #3498db;
        }
        
        .vision-box {
            border-left-color: #2ecc71;
        }
        
        .team-section {
            background: #f8f9fa;
            padding: 60px 0;
        }
        
        .team-card {
            background: white;
            border-radius: 10px;
            padding: 25px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            transition: transform 0.3s;
        }
        
        .team-card:hover {
            transform: translateY(-10px);
        }
        
        .team-img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            margin: 0 auto 20px;
            border: 5px solid #f1f1f1;
        }
        
        .stats-section {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin: 50px 0;
        }
        
        .stat-box {
            text-align: center;
            padding: 25px;
            background: #3498db;
            color: white;
            border-radius: 10px;
        }
        
        .stat-number {
            font-size: 40px;
            font-weight: bold;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

<!-- About Hero Section -->
<section class="about-section">
    <div class="container">
        <div class="about-content">
            <div class="about-card">
                <h1 class="text-center mb-4">About STAYNOVA</h1>
                <p class="lead text-center mb-4">
                    Your trusted partner for hotel bookings across Bangladesh
                </p>
                
                <p>
                    Welcome to <strong>STAYNOVA</strong>, Bangladesh's premier online hotel booking platform. 
                    We connect travelers with verified hotels, ensuring a seamless and secure booking experience.
                </p>
                
                <p>
                    Founded in 2023, our mission is to simplify hotel bookings while providing the best prices 
                    and quality accommodations. We work with hotel owners to showcase their properties to 
                    thousands of potential guests.
                </p>
                
                <div class="stats-section">
                    <div class="stat-box">
                        <div class="stat-number">500+</div>
                        <div>Hotels Listed</div>
                    </div>
                    <div class="stat-box" style="background: #2ecc71;">
                        <div class="stat-number">10,000+</div>
                        <div>Happy Customers</div>
                    </div>
                    <div class="stat-box" style="background: #e74c3c;">
                        <div class="stat-number">50+</div>
                        <div>Cities Covered</div>
                    </div>
                    <div class="stat-box" style="background: #f39c12;">
                        <div class="stat-number">24/7</div>
                        <div>Customer Support</div>
                    </div>
                </div>
                
                <div class="mission-vision">
                    <div class="mission-box">
                        <h3><i class="fas fa-bullseye text-primary me-2"></i> Our Mission</h3>
                        <p>
                            To provide travelers with easy access to quality accommodations at affordable prices, 
                            while helping hotel owners reach more customers through our verified platform.
                        </p>
                    </div>
                    
                    <div class="vision-box">
                        <h3><i class="fas fa-eye text-success me-2"></i> Our Vision</h3>
                        <p>
                            To become Bangladesh's most trusted hotel booking platform, known for transparency, 
                            reliability, and exceptional customer service.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Team Section -->
<section class="team-section">
    <div class="container">
        <h2 class="text-center mb-5">Meet Our Team</h2>
        
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="team-card">
                    <img src="assets/img/team1.jpg" class="team-img" alt="CEO" onerror="this.src='assets/img/ceo.png'">
                    <h4>Md. Tanvir Ahammed</h4>
                    <p class="text-muted">CEO & Founder</p>
                    <p>10+ years in hospitality industry</p>
                </div>
            </div>
            
            <div class="col-md-4 mb-4">
                <div class="team-card">
                    <img src="assets/img/team2.jpg" class="team-img" alt="CTO" onerror="this.src='assets/img/cto.png'">
                    <h4>Robiul Ahmed</h4>
                    <p class="text-muted">CTO</p>
                    <p>Technology & Platform Development</p>
                </div>
            </div>
            
            <div class="col-md-4 mb-4">
                <div class="team-card">
                    <img src="assets/img/team3.jpg" class="team-img" alt="Head of Operations" onerror="this.src='assets/img/cto.png'">
                    <h4>Kamal Hossain</h4>
                    <p class="text-muted">Head of Operations</p>
                    <p>Hotel Partnerships & Quality Control</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Why Choose Us -->
<section style="padding: 60px 0;">
    <div class="container">
        <h2 class="text-center mb-5">Why Choose STAYNOVA?</h2>
        
        <div class="row">
            <div class="col-md-3 mb-4">
                <div class="text-center p-3">
                    <div style="font-size: 40px; color: #3498db; margin-bottom: 15px;">✅</div>
                    <h5>Verified Hotels</h5>
                    <p>All hotels are verified by our team for quality and safety</p>
                </div>
            </div>
            
            <div class="col-md-3 mb-4">
                <div class="text-center p-3">
                    <div style="font-size: 40px; color: #3498db; margin-bottom: 15px;">💰</div>
                    <h5>Best Price Guarantee</h5>
                    <p>Get the best prices or we'll match the difference</p>
                </div>
            </div>
            
            <div class="col-md-3 mb-4">
                <div class="text-center p-3">
                    <div style="font-size: 40px; color: #3498db; margin-bottom: 15px;">🔒</div>
                    <h5>Secure Booking</h5>
                    <p>Your personal and payment data is 100% secure</p>
                </div>
            </div>
            
            <div class="col-md-3 mb-4">
                <div class="text-center p-3">
                    <div style="font-size: 40px; color: #3498db; margin-bottom: 15px;">📞</div>
                    <h5>24/7 Support</h5>
                    <p>Round-the-clock customer support</p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include "footer.php"; ?>

</body>
</html>