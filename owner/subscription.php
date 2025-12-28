<?php
session_start();
include "../db_connect.php";
include "../header.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'owner') {
    header("Location: ../login.php");
    exit();
}

$owner_id = $_SESSION['user_id'];

// Check if owner already has an active subscription
$check_sql = "SELECT * FROM owner_subscriptions 
              WHERE owner_id='$owner_id' 
              AND status IN ('pending','approved')";
$check_result = mysqli_query($conn, $check_sql);
$has_active = mysqli_num_rows($check_result) > 0;

// Get all packages
$packages = mysqli_query($conn,"SELECT * FROM subscriptions");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Owner Subscription</title>

    <!-- Bootstrap 4 -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
     <?php include "../header.php";?>
    <style>
        body{
            background:#f4f6f9;
        }
        .page-title{
            font-weight:700;
            color:#2c3e50;
        }
        .sub-card{
            border:none;
            border-radius:15px;
            box-shadow:0 10px 25px rgba(0,0,0,0.1);
            transition:0.3s;
        }
        .sub-card:hover{
            transform:translateY(-8px);
        }
        .price{
            font-size:32px;
            font-weight:700;
            color:#27ae60;
        }
        .duration{
            color:#7f8c8d;
            font-size:14px;
        }
        .subscribe-btn{
            border-radius:30px;
            padding:10px 25px;
            font-weight:600;
        }
        .badge-popular{
            position:absolute;
            top:15px;
            right:15px;
            background:#ff7675;
        }
    </style>
</head>

<body>

<div class="container mt-5">
    <div class="text-center mb-4">
        <h2 class="page-title">
            <i class="fas fa-crown text-warning"></i> Subscription Plans
        </h2>
        <p class="text-muted">
            Subscribe to unlock flat upload & booking management
        </p>
    </div>

    <?php if($has_active): ?>
        <div class="alert alert-warning text-center">
            You already have an active subscription. Wait for admin approval or expiry.
        </div>
    <?php endif; ?>

    <div class="row justify-content-center">
        <?php while($p=mysqli_fetch_assoc($packages)): ?>
        <div class="col-md-4 mb-4">
            <div class="card sub-card position-relative">
                
                <?php if($p['duration_days']==365): ?>
                    <span class="badge badge-popular">Best Value</span>
                <?php endif; ?>

                <div class="card-body text-center">
                    <h5 class="mb-3 font-weight-bold"><?= $p['name'] ?></h5>

                    <div class="price">
                        ৳ <?= $p['price'] ?>
                    </div>

                    <div class="duration mb-3">
                        Valid for <?= $p['duration_days'] ?> days
                    </div>

                    <ul class="list-unstyled text-muted mb-4">
                        <li>✔ Flat Upload Access</li>
                        <li>✔ Booking Management</li>
                        <li>✔ Notification Support</li>
                    </ul>

                    <?php if($has_active): ?>
                        <button class="btn btn-secondary subscribe-btn" disabled>
                            <i class="fas fa-ban"></i> Already Subscribed
                        </button>
                    <?php else: ?>
                        <button 
                            class="btn btn-primary subscribe-btn"
                            data-toggle="modal"
                            data-target="#subscribeModal"
                            data-id="<?= $p['id'] ?>">
                            <i class="fas fa-check-circle"></i> Subscribe Now
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
</div>

<!-- Subscription Modal -->
<div class="modal fade" id="subscribeModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">

      <div class="modal-header bg-warning text-dark">
        <h5 class="modal-title">
            <i class="fas fa-hourglass-half"></i> Subscription Pending
        </h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <div class="modal-body text-center">
        <p class="mb-2">
            ✅ Your subscription request has been sent.
        </p>
        <p class="text-muted">
            ⏳ Please wait for <strong>Admin approval</strong>.
        </p>
      </div>

      <div class="modal-footer justify-content-center">
        <a href="#" id="confirmSubscribe" class="btn btn-success">
            OK
        </a>
      </div>

    </div>
  </div>
</div>

<!-- Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
let selectedPackage = 0;

$('#subscribeModal').on('show.bs.modal', function (event) {
    let button = $(event.relatedTarget);
    selectedPackage = button.data('id');
});

document.getElementById("confirmSubscribe").onclick = function () {
    window.location.href = "subscribe_action.php?id=" + selectedPackage;
};
</script>

</body>
</html>
