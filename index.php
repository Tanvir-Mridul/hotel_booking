<?php
include "db_connect.php";
include "header.php";

/* Show some approved hotels */
$sql = "SELECT * FROM hotels WHERE status='approved' ORDER BY id DESC LIMIT 4";
$result = mysqli_query($conn, $sql);
?>


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
        <form class="search-form" action="hotel/hotel_list.php" method="GET">

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

<!-- ================= POPULAR DESTINATION ================= -->
<div class="container mt-5">
  <h3 class="mb-4">Popular Destinations</h3>

  <div class="row text-center">

    <?php
    $destinations = [
      ["Dhaka","dhaka.jpg"],
      ["Cox's Bazar","coxbazar.jpg"],
      ["Sylhet","sylhet.jpg"],
      ["Chittagong","chittagong.jpg"]
    ];
    foreach ($destinations as $d):
    ?>

    <div class="col-md-3 mb-3">
      <a href="hotel/hotel_list.php?location=<?php echo $d[0]; ?>" class="text-decoration-none text-dark">
        <div class="card destination-card">
          <img src="assets/img/<?php echo $d[1]; ?>" class="card-img">
          <div class="destination-title"><?php echo $d[0]; ?></div>
        </div>
      </a>
    </div>

    <?php endforeach; ?>

  </div>
</div>

<!-- ================= HOTEL LIST ================= -->
<div class="container mt-5">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Featured Hotels</h3>
    <a href="hotel/hotel_list.php" class="btn btn-outline-primary">More</a>
  </div>

  <div class="row">

    <?php while($row = mysqli_fetch_assoc($result)): ?>

    <div class="col-md-3 mb-4">
      <div class="card hotel-card h-100 shadow-sm">
        <img src="assets/img/<?php echo $row['image']; ?>" class="card-img-top" style="height:200px;object-fit:cover;">

        <div class="card-body">
          <h6><?php echo $row['hotel_name']; ?></h6>
          <p class="text-muted mb-1"><?php echo $row['location']; ?></p>
          <strong class="text-success">৳ <?php echo $row['price']; ?></strong>
        </div>

        <div class="card-footer bg-white border-0">
          <a href="hotel/hotel_details.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-primary w-100">
            View Details
          </a>
        </div>
      </div>
    </div>

    <?php endwhile; ?>

  </div>
</div>
<!-- ================= Choose Us ================= -->
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

<!-- =================Deals / Offers ================= -->
 <section class="deals-section">
  <h2 class="section-title">Deals & Special Offers</h2>

  <div class="deals-grid">

    <div class="deal-card">
      <span class="deal-badge">30% OFF</span>
      <h4>Cox's Bazar Special</h4>
      <p>Enjoy sea view hotels with exciting discounts.</p>
      <a href="hotel/hotel_list.php" class="deal-btn">View Hotels</a>
    </div>

    <div class="deal-card">
      <span class="deal-badge">20% OFF</span>
      <h4>Weekend Deal</h4>
      <p>Perfect weekend getaway at affordable prices.</p>
      <a href="hotel/hotel_list.php" class="deal-btn">Book Now</a>
    </div>

    <div class="deal-card">
      <span class="deal-badge">FREE</span>
      <h4>Breakfast Included</h4>
      <p>Selected hotels offer free breakfast.</p>
      <a href="hotel/hotel_list.php" class="deal-btn">Explore</a>
    </div>

  </div>
</section>
<!-- =================Testimonials================= -->
<section class="testimonials-section">
  <h2 class="section-title">What Our Guests Say</h2>

  <div class="testimonial-grid">

    <div class="testimonial-card">
      <div class="stars">★★★★★</div>
      <p>
        Booking was super easy and the hotel was amazing.
        Highly recommended!
      </p>
      <h4>Rahim Ahmed</h4>
      <span>Dhaka</span>
    </div>

    <div class="testimonial-card">
      <div class="stars">★★★★★</div>
      <p>
        Best hotel booking platform in Bangladesh.
        Great support service.
      </p>
      <h4>Nusrat Jahan</h4>
      <span>Cox's Bazar</span>
    </div>

    <div class="testimonial-card">
      <div class="stars">★★★★★</div>
      <p>
        Clean interface, verified hotels and smooth booking.
        Loved it!
      </p>
      <h4>Tanvir Hasan</h4>
      <span>Sylhet</span>
    </div>

  </div>
</section>

<!-- ================Owner CTA ================= -->

<section class="owner-cta-section">
  <div class="owner-cta-content">
    <h2>Are You a Hotel Owner?</h2>
    <p>
      List your hotel on our platform and reach thousands of travelers
      across Bangladesh.
    </p>
    <a href="owner/add_hotel.php" class="owner-cta-btn">
      List Your Hotel
    </a>
  </div>
</section>
<!-- =================User Login Modal================= -->

<div class="modal fade" id="userLoginModal">
  <div class="modal-dialog">
    <form class="modal-content" method="POST" action="/hotel_booking/login_process.php">
      <div class="modal-header">
        <h5>User Login</h5>
        <button class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <input type="email" name="email" class="form-control mb-3" placeholder="Email" required>
        <input type="password" name="password" class="form-control" placeholder="Password" required>
      </div>

      <div class="modal-footer">
        <button class="btn btn-primary w-100">Login</button>
      </div>
    </form>
  </div>
</div>
<!-- =================registerModal================= -->
<div class="modal fade" id="registerModal">
  <div class="modal-dialog">
    <form class="modal-content" method="POST" action="register_save.php">
      <div class="modal-header">
        <h5>User Register</h5>
        <button class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <input type="text" name="name" class="form-control mb-2" placeholder="Name" required>
        <input type="email" name="email" class="form-control mb-2" placeholder="Email" required>
        <input type="password" name="password" class="form-control" placeholder="Password" required>
      </div>

      <div class="modal-footer">
        <button class="btn btn-success w-100">Register</button>
      </div>
    </form>
  </div>
</div>
<!-- =================Logout ================= -->





<?php include "footer.php"; ?>

