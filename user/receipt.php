<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

include "../db_connect.php";
include "../header.php";

$receipt_id = $_GET['receipt_id'] ?? '';
$user_id = $_SESSION['user_id'];

// Get payment details
$sql = "SELECT up.*, u.name as user_name, o.name as owner_name,
               u.email as user_email
        FROM user_payments up
        JOIN users u ON up.user_id = u.id
        JOIN users o ON up.owner_id = o.id
        WHERE up.receipt_id = ? AND up.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $receipt_id, $user_id);
$stmt->execute();
$payment = $stmt->get_result()->fetch_assoc();

if (!$payment) {
    die("Receipt not found!");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Payment Receipt</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body { 
            background: #f8f9fa;
            padding-top: 20px;
            font-family: Arial, sans-serif;
        }
        
        .receipt-container { 
            max-width: 500px; 
            margin: 20px auto; 
            background: white; 
            padding: 25px; 
            border-radius: 10px; 
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            border: 1px solid #ddd;
        }
        
        .receipt-header { 
            text-align: center; 
            padding-bottom: 15px; 
            margin-bottom: 20px; 
            border-bottom: 2px solid #3498db;
        }
        
        .receipt-title { 
            color: #2c3e50; 
            font-size: 24px; 
            font-weight: bold; 
            margin-bottom: 5px;
        }
        
        .receipt-id { 
            color: #3498db; 
            font-size: 16px; 
            font-weight: bold;
        }
        
        .amount-box { 
            background: #27ae60; 
            color: white;
            padding: 20px; 
            border-radius: 8px; 
            text-align: center; 
            margin: 20px 0; 
        }
        
        .amount-number { 
            font-size: 36px; 
            font-weight: bold; 
            margin-bottom: 5px;
        }
        
        .details-box {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
            border: 1px solid #eee;
        }
        
        .details-row { 
            display: flex; 
            justify-content: space-between; 
            padding: 8px 0; 
            border-bottom: 1px dashed #ccc; 
        }
        
        .details-row:last-child {
            border-bottom: none;
        }
        
        .label { 
            color: #666; 
            font-weight: 500;
        }
        
        .value { 
            font-weight: 600; 
            color: #2c3e50;
            text-align: right;
        }
        
        .action-buttons { 
            margin-top: 30px; 
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }
        
        .btn-action {
            padding: 10px 25px;
            margin: 5px;
            border-radius: 5px;
            text-decoration: none;
            display: inline-block;
            border: none;
            font-weight: 500;
        }
        
        .btn-print {
            background: #3498db;
            color: white;
        }
        
        .btn-home {
            background: #27ae60;
            color: white;
        }
        
        .btn-bookings {
            background: #9b59b6;
            color: white;
        }
        
        /* ===== PRINT STYLES ===== */
        @media print {
            @page {
                margin: 10mm;
                size: auto;
            }
            
            body {
                background: white !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            
            .navbar, .action-buttons, .btn-action, 
            .no-print, .alert-success {
                display: none !important;
            }
            
            .receipt-container {
                max-width: 100% !important;
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                box-shadow: none !important;
                border: none !important;
                border-radius: 0 !important;
            }
            
            .receipt-header {
                border-bottom: 3px solid #000 !important;
                padding-bottom: 10px !important;
                margin-bottom: 15px !important;
            }
            
            .receipt-title {
                color: black !important;
                font-size: 22px !important;
            }
            
            .receipt-id {
                color: #333 !important;
            }
            
            .amount-box {
                background: white !important;
                color: black !important;
                border: 2px solid #000 !important;
                padding: 15px !important;
            }
            
            .amount-number {
                color: #000 !important;
                font-size: 32px !important;
            }
            
            .details-box {
                background: white !important;
                border: 1px solid #000 !important;
                padding: 10px !important;
            }
            
            .details-row {
                border-bottom: 1px solid #ccc !important;
            }
            
            .label {
                color: #000 !important;
            }
            
            .value {
                color: #000 !important;
            }
            
            /* Hide URL and timestamps */
            a[href]:after {
                content: none !important;
            }
            
            /* Page break control */
            .page-break {
                page-break-before: always;
            }
        }
        
        /* Small screen adjustments */
        @media (max-width: 576px) {
            .receipt-container {
                margin: 10px;
                padding: 15px;
            }
            
            .btn-action {
                display: block;
                width: 100%;
                margin: 10px 0;
            }
        }
    </style>
</head>
<body>

<?php include "../header.php"; ?>

<div class="receipt-container">
    <!-- Header -->
    <div class="receipt-header">
        <div class="receipt-title">STAYNOVA</div>
        <div class="receipt-id">Receipt #<?= $payment['receipt_id'] ?></div>
        <div class="text-muted" style="font-size: 14px;">
            Hotel Booking Platform
        </div>
    </div>
    
    <!-- Amount -->
    <div class="amount-box">
        <div class="amount-number">৳ <?= number_format($payment['amount'], 2) ?></div>
        <div style="font-size: 18px; margin-bottom: 5px;">Payment Successful</div>
        <div style="opacity: 0.9;">
            <i class="fas fa-calendar-check"></i> 
            <?= date('d F Y, h:i A', strtotime($payment['created_at'])) ?>
        </div>
    </div>
    
    <!-- Details -->
    <div class="details-box">
        <h5 style="color: #3498db; margin-bottom: 15px; font-size: 18px;">
            <i class="fas fa-info-circle"></i> Booking Details
        </h5>
        
        <div class="details-row">
            <span class="label">Booking ID:</span>
            <span class="value">#<?= $payment['booking_id'] ?></span>
        </div>
        <div class="details-row">
            <span class="label">Hotel Name:</span>
            <span class="value"><?= $payment['hotel_name'] ?></span>
        </div>
        <div class="details-row">
            <span class="label">Customer Name:</span>
            <span class="value"><?= $payment['user_name'] ?></span>
        </div>
        <div class="details-row">
            <span class="label">Customer Email:</span>
            <span class="value"><?= $payment['user_email'] ?></span>
        </div>
        <div class="details-row">
            <span class="label">Hotel Owner:</span>
            <span class="value"><?= $payment['owner_name'] ?></span>
        </div>
        <div class="details-row">
            <span class="label">Transaction ID:</span>
            <span class="value" style="font-family: monospace; font-size: 14px;"><?= $payment['tran_id'] ?></span>
        </div>
    </div>
    
    <!-- Success Message -->
    <div class="alert alert-success text-center no-print">
        <i class="fas fa-check-circle"></i> 
        <strong>Payment Completed Successfully!</strong>
    </div>
    
    <!-- Action Buttons -->
    <div class="action-buttons no-print">
        <button onclick="printReceipt()" class="btn-action btn-print">
            <i class="fas fa-print"></i> Print Receipt
        </button>
        
        <a href="../index.php" class="btn-action btn-home">
            <i class="fas fa-home"></i> Back to Home
        </a>
        
        <a href="my_booking.php" class="btn-action btn-bookings">
            <i class="fas fa-calendar-alt"></i> My Bookings
        </a>
    </div>
    
    <!-- Footer -->
    <div class="text-center mt-4 pt-3 border-top">
        <small class="text-muted">
            Thank you for booking with STAYNOVA<br>
            Contact: support@staynova.com | Phone: +880 1234-567890<br>
            <small>Receipt generated on <?= date('d M Y, h:i A') ?></small>
        </small>
    </div>
</div>

<!-- JavaScript for Better Print -->
<script>
function printReceipt() {
    // Store original styles
    const originalStyles = {
        bodyBg: document.body.style.background,
        bodyPadding: document.body.style.padding
    };
    
    // Apply print styles temporarily
    document.body.style.background = 'white';
    document.body.style.padding = '0';
    
    // Print
    window.print();
    
    // Restore styles
    document.body.style.background = originalStyles.bodyBg;
    document.body.style.padding = originalStyles.bodyPadding;
}

// Add print-specific class to non-printable elements
document.addEventListener('DOMContentLoaded', function() {
    const nonPrintable = document.querySelectorAll('.navbar, .action-buttons, .btn-action, .alert-success');
    nonPrintable.forEach(el => {
        el.classList.add('no-print');
    });
});

// Keyboard shortcut for print (Ctrl+P)
document.addEventListener('keydown', function(e) {
    if ((e.ctrlKey || e.metaKey) && e.key === 'p') {
        e.preventDefault();
        printReceipt();
    }
});
</script>

</body>
</html>