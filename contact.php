<?php
session_start();
include "header.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Contact Us - Hotel Booking System</title>
    <style>
        .contact-section {
            padding: 80px 0;
            background: #f8f9fa;
        }
        
        .contact-info {
            background: #3498db;
            color: white;
            border-radius: 15px;
            padding: 40px;
            height: 100%;
        }
        
        .contact-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 30px;
        }
        
        .contact-icon {
            width: 50px;
            height: 50px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            margin-right: 15px;
            flex-shrink: 0;
        }
        
        .map-container {
            border-radius: 10px;
            overflow: hidden;
            margin-top: 50px;
        }
        
        .social-links {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }
        
        .social-icon {
            width: 40px;
            height: 40px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-decoration: none;
            transition: 0.3s;
        }
        
        .social-icon:hover {
            background: white;
            color: #3498db;
            transform: translateY(-3px);
        }
        
        .office-hours {
            background: rgba(255,255,255,0.1);
            padding: 20px;
            border-radius: 10px;
            margin-top: 30px;
        }
        
        .call-option {
            background: #2ecc71;
            color: white;
            padding: 30px;
            border-radius: 10px;
            text-align: center;
            margin-bottom: 30px;
        }
        
        .call-btn {
            background: white;
            color: #2ecc71;
            border: none;
            padding: 12px 25px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            margin-top: 15px;
            transition: 0.3s;
        }
        
        .call-btn:hover {
            background: #f8f9fa;
            transform: translateY(-2px);
        }
        
        .phone-icon {
            font-size: 40px;
            margin-bottom: 15px;
        }
        
        /* FAQ Styles */
        .faq-section {
            padding: 60px 0;
        }
        
        .faq-item {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }
        
        .faq-question {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 10px;
            font-size: 18px;
        }
        
        .faq-answer {
            color: #555;
            line-height: 1.6;
        }
        
        .contact-title {
            text-align: center;
            margin-bottom: 40px;
            color: #2c3e50;
        }
          .about-section {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 15px;
            margin-top: 80px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }
        
        .about-section h3 {
            color: #3498db;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f0f0f0;
        }
       

    </style>
</head>
<body>

<!-- Contact Section -->
<section class="contact-section">
    <div class="container">
        <h2 class="contact-title">Contact Us</h2>
        
        <div class="call-option">
            <div class="phone-icon">
                <i class="fas fa-phone-volume"></i>
            </div>
            <h3>24/7 Customer Support</h3>
            <p class="mb-3">Call us anytime for immediate assistance</p>
            <h2 class="mb-4">+880 1234-567890</h2>
            <button class="call-btn" onclick="window.location.href='tel:+8801643244840'">
                <i class="fas fa-phone me-2"></i> Call Now
            </button>
        </div>
        
        <div class="row">
            <div class="col-lg-8 mb-4">
                <!-- Map Section -->
                <div class="map-container">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d766.9028716216064!2d90.39125895903938!3d23.88871778125155!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3755c5a083ca8f83%3A0x12ba7e3581a0fb69!2sIUBAT%20Agricultural%20Field!5e0!3m2!1sen!2sbd!4v1768232702797!5m2!1sen!2sbd" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>

                <br>
                <br>
                   <div class="about-section">
            <h3>For Hotel Owners</h3>
            <p>
                Are you a hotel owner? List your property on STAYNOVA and reach thousands of customers.
            </p>
            <p>
                Features for owners:
            </p>
            <ul>
                <li>Easy hotel upload</li>
                <li>Booking management</li>
                <li>Customer chat system</li>
                <li>Premium subscription options</li>
            </ul>
            <a href="/hotel_booking/register.php" class=" btn auth-btn btn-block">
      List Your Hotel
    </a>
        </div>
            </div>
            
            <div class="col-lg-4">
                <div class="contact-info">
                    <h3 class="mb-4">Contact Information</h3>
                    
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div>
                            <h5>Our Office</h5>
                            <p>
                                Uttara,Sector 10<br>
                                Dhaka 1230<br>
                                Bangladesh
                            </p>
                        </div>
                    </div>
                    
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fas fa-phone"></i>
                        </div>
                        <div>
                            <h5>Phone Numbers</h5>
                            <p>
                                <strong>Customer Support (24/7):</strong> +880 1234-567890<br>
                                <strong>Hotel Partnership:</strong> +880 1234-567891<br>
                                <strong>Emergency Support:</strong> +880 1234-567892
                            </p>
                        </div>
                    </div>
                    
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div>
                            <h5>Email Addresses</h5>
                            <p>
                                <strong>Support:</strong> support@staynovabd.com<br>
                                <strong>Bookings:</strong> bookings@staynovabd.com<br>
                                <strong>Partnership:</strong> partners@staynovabd.com
                            </p>
                        </div>
                    </div>
                    
                    <div class="office-hours">
                        <h5><i class="fas fa-clock me-2"></i> Office Hours</h5>
                        <p class="mb-1"><strong>Sunday - Thursday:</strong> 9:00 AM - 6:00 PM</p>
                        <p class="mb-1"><strong>Friday:</strong> 9:00 AM - 1:00 PM</p>
                        <p class="mb-0"><strong>Saturday:</strong> Closed</p>
                        <p class="mt-2"><em>Customer support available 24/7 via phone</em></p>
                    </div>
                    
                    <div class="social-links">
                        <a href="#" class="social-icon">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="social-icon">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="social-icon">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="social-icon">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                        <a href="#" class="social-icon">
                            <i class="fab fa-youtube"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="faq-section">
    <div class="container">
        <h2 class="text-center mb-5">Frequently Asked Questions</h2>
        
        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="faq-item">
                    <div class="faq-question">1. How do I book a hotel?</div>
                    <div class="faq-answer">
                        Search for hotels using our search bar, select your dates, choose a hotel, and click "Book Now". 
                        You'll need to create an account or login to complete the booking.
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 mb-4">
                <div class="faq-item">
                    <div class="faq-question">2. What is your cancellation policy?</div>
                    <div class="faq-answer">
                        Cancellation policies vary by hotel. You can cancel your booking from "My Bookings" section. 
                        Some hotels offer free cancellation up to 24 hours before check-in.
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 mb-4">
                <div class="faq-item">
                    <div class="faq-question">3. How can I become a hotel partner?</div>
                    <div class="faq-answer">
                        Hotel owners can register as "Owner" during signup. After registration, you can upload your hotel details 
                        through the Owner Panel. Our team will verify and approve your listing.
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 mb-4">
                <div class="faq-item">
                    <div class="faq-question">4. Is my payment information secure?</div>
                    <div class="faq-answer">
                        Yes, we use SSL encryption and partner with secure payment gateways to ensure your payment 
                        information is completely secure.
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 mb-4">
                <div class="faq-item">
                    <div class="faq-question">5. How do I modify my booking?</div>
                    <div class="faq-answer">
                        Login to your account, go to "My Bookings" section, select the booking you want to modify 
                        and follow the instructions. Or call our support team for assistance.
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 mb-4">
                <div class="faq-item">
                    <div class="faq-question">6. What if I have problems during my stay?</div>
                    <div class="faq-answer">
                        First contact the hotel reception. If the issue is not resolved, call our 24/7 emergency 
                        support line: +880 1234-567892. We'll help resolve the problem.
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include "footer.php"; ?>

</body>
</html>