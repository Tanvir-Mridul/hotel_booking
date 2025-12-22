<?php include "header.php"; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>

    <!-- BOOTSTRAP 4.5 -->
    <link rel="stylesheet"
          href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <!-- FONT AWESOME -->
    <script src="https://kit.fontawesome.com/162e11130a.js" crossorigin="anonymous"></script>

    <!-- CUSTOM CSS -->
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="auth-page">
    <div class="auth-card">

        <div class="auth-title text-center mb-4">
            <h1>Create Account</h1>
            <p>Create an account to book hotels easily</p>
        </div>

        <form action="register_process.php" method="POST">
            <div class="form-group">
                <input type="email" name="email" class="form-control" placeholder="Email" required>
            </div>

            <div class="form-group">
                <input type="password" name="password" class="form-control" placeholder="Password" required>
            </div>

            <div class="form-group">
                <input type="password" name="confirm_password" class="form-control" placeholder="Confirm Password" required>
            </div>

             <div class="mb-3">
            <label>Register As</label>
            <select name="role" class="form-control">
                <option value="user">User</option>
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

        <div class="text-center mt-4 social-login">
            <p class="text-muted mb-2">Or continue with</p>
            <i class="fab fa-google"></i>
            <i class="fab fa-facebook-f"></i>
            <i class="fab fa-apple"></i>
        </div>

    </div>
</div>

</body>
</html>


<?php include "footer.php"; ?>
