
<?php
session_start();

?>

  <!DOCTYPE html>
<html>
<head>
    
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <link rel="stylesheet" href="style.css">
    
    <?php include "header.php"; ?>
    
    <title>Login Page</title>
</head>
<body>
</head>
<body>

<div class="auth-page">
    <div class="auth-card">

        <div class="auth-title text-center mb-4">
            <h1>Login Here</h1>
            <p>Welcome back, you've been missed!</p>
        </div>

        <form action="login_process.php" method="POST">
            <div class="form-group">
                <input type="email" name="email" class="form-control" placeholder="Email" required>
            </div>

            <div class="form-group">
                <input type="password" name="password" class="form-control" placeholder="Password" required>
            </div>

            <div class="text-right mb-3">
                <a href="#" class="auth-link">Forgot your password?</a>
            </div>

            <button type="submit" class="btn auth-btn btn-block">
                Sign In
            </button>
        </form>

        <div class="text-center mt-3">
            <a href="register.php" class="auth-link">
                Create new account
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
