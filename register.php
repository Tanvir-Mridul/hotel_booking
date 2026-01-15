<?php include "header.php"; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="auth-page">
    <div class="auth-card">
        <div class="auth-title text-center mb-4">
            <h1>Create Account</h1>
            <p>Create an account to manage your hotel</p>
        </div>

        <form action="register_process.php" method="POST">
            <div class="form-group">
                <input type="text" name="name" class="form-control" placeholder="Your Name" required>
            </div>
            
            <div class="form-group">
                <input type="email" name="email" class="form-control" placeholder="Email" required>
            </div>

            <div class="form-group">
                <input type="password" name="password" class="form-control" placeholder="Password" required>
            </div>

            <div class="form-group">
                <input type="password" name="confirm_password" class="form-control" placeholder="Confirm Password" required>
            </div>

            <!-- Hotel Information (Owner এর জন্য) -->
            <div id="ownerFields" style="display:none;">
                <div class="form-group">
                    <label>Hotel Name *</label>
                    <input type="text" name="hotel_name" class="form-control" placeholder="Enter your hotel name">
                </div>
                
                <div class="form-group">
                    <label>Hotel Location *</label>
                    <input type="text" name="hotel_location" class="form-control" placeholder="e.g., Dhaka, Cox's Bazar">
                </div>
            </div>

            <div class="mb-3">
                <label>Register As</label>
                <select name="role" class="form-control" id="roleSelect" onchange="toggleHotelFields()">
                    <option value="user">User (Book Hotels)</option>
                    <option value="owner">Hotel Owner</option>
                </select>
            </div>

            <button type="submit" class="btn auth-btn btn-block">
                Sign Up
            </button>
        </form>

        <div class="text-center mt-3">
            <a href="login.php" class="auth-link">
                Already have an account?
            </a>
        </div>
    </div>
</div>

<script>
function toggleHotelFields() {
    var role = document.getElementById('roleSelect').value;
    var ownerFields = document.getElementById('ownerFields');
    
    if (role === 'owner') {
        ownerFields.style.display = 'block';
        // Required fields
        var hotelInputs = ownerFields.querySelectorAll('input');
        hotelInputs.forEach(function(input) {
            input.required = true;
        });
    } else {
        ownerFields.style.display = 'none';
        // Remove required
        var hotelInputs = ownerFields.querySelectorAll('input');
        hotelInputs.forEach(function(input) {
            input.required = false;
        });
    }
}
</script>

</body>
</html>

<?php include "footer.php"; ?>