<?php
    include "Hybrid_CRUD.php";
    $hybrid = new Hybrid_CRUD();

    if(isset($_POST['login'])){
        $username = $_POST['username'];
        $password = $_POST['password'];

        $hybrid->login($username,$password);
    }
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pixel Dash - Login</title>
	<link href="node_modules/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="container">
        <div class="login-container">
            <div class="login-header">
                <h1 class="login-title">PIXEL DASH</h1>
                <p class="login-subtitle">Enter the gaming universe</p>
            </div>
            
            <form method="post" class="login-body">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                        <input type="text" name="username" id="username" 
                               placeholder="Enter your username" 
                               class="form-control" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" name="password" id="password" 
                               placeholder="Enter your password" 
                               class="form-control" required>
                    </div>
                </div>

                <div class="form-options mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="remember">
                        <label class="form-check-label" for="remember">
                            Remember me
                        </label>
                    </div>
                    <a href="#" class="forgot-link">Forgot Password?</a>
                </div>

                <button name="login" class="btn btn-login">
                    <i class="fas fa-sign-in-alt me-2"></i>Login
                </button>
                
                <a href="register.php" class="btn btn-register">
                    <i class="fas fa-user-plus me-2"></i>Create Account
                </a>
            </form>

            <div class="login-footer">
                <p>Or continue with</p>
                <div class="social-login">
                    <a href="#" class="social-btn">
                        <i class="fab fa-google"></i>
                    </a>
                    <a href="#" class="social-btn">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="#" class="social-btn">
                        <i class="fab fa-discord"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>