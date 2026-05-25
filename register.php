<?php
    include "Hybrid_CRUD.php";
    $hybrid = new Hybrid_CRUD();

    if(isset($_POST['register'])){
        $username = $_POST['username'];
        $firstname = $_POST['firstname'];
        $lastname = $_POST['lastname'];
        $password = $_POST['password'];

        $hybrid->create_user($username,$password,$firstname,$lastname);
    }
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pixel Dash - Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="container">
        <div class="login-container">
            <div class="login-header">
                <h1 class="login-title">JOIN THE DASH</h1>
                <p class="login-subtitle">Create your gaming profile</p>
            </div>
            
            <form method="post" class="login-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="firstname" class="form-label">First Name</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                            <input type="text" name="firstname" id="firstname" 
                                   placeholder="Enter your first name" 
                                   class="form-control" required>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="lastname" class="form-label">Last Name</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                            <input type="text" name="lastname" id="lastname" 
                                   placeholder="Enter your last name" 
                                   class="form-control" required>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-at"></i></span>
                        <input type="text" name="username" id="username" 
                               placeholder="Choose a username" 
                               class="form-control" required>
                    </div>
                    <div class="form-text">This will be your gaming identity</div>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" name="password" id="password" 
                               placeholder="Create a strong password" 
                               class="form-control" required>
                        <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <div class="password-strength mt-2">
                        <div class="progress" style="height: 5px;">
                            <div class="progress-bar" id="passwordStrength" role="progressbar" style="width: 0%"></div>
                        </div>
                        <small id="passwordHelp" class="form-text"></small>
                    </div>
                </div>

                <div class="mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="terms" required>
                        <label class="form-check-label" for="terms">
                            I agree to the <a href="#" class="forgot-link">Terms & Conditions</a> and <a href="#" class="forgot-link">Privacy Policy</a>
                        </label>
                    </div>
                    <div class="form-check mt-2">
                        <input class="form-check-input" type="checkbox" id="newsletter" checked>
                        <label class="form-check-label" for="newsletter">
                            Subscribe to gaming updates and newsletters
                        </label>
                    </div>
                </div>

                <button name="register" class="btn btn-login">
                    <i class="fas fa-user-plus me-2"></i>Create Account
                </button>
                
                <a href="index.php" class="btn btn-register">
                    <i class="fas fa-times me-2"></i>Cancel
                </a>
                
                <div class="text-center mt-4">
                    <p>Already have an account? <a href="index.php" class="forgot-link">Login here</a></p>
                </div>
            </form>

            <div class="login-footer">
                <p>Benefits of joining:</p>
                <div class="row text-center">
                    <div class="col-4">
                        <i class="fas fa-gamepad fa-lg mb-2" style="color: var(--accent);"></i>
                        <p class="small mb-0">Access Games</p>
                    </div>
                    <div class="col-4">
                        <i class="fas fa-trophy fa-lg mb-2" style="color: var(--accent);"></i>
                        <p class="small mb-0">Track Progress</p>
                    </div>
                    <div class="col-4">
                        <i class="fas fa-users fa-lg mb-2" style="color: var(--accent);"></i>
                        <p class="small mb-0">Join Community</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Password visibility toggle
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const icon = this.querySelector('i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    </script>
</body>
</html>