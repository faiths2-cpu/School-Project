<?php
// setup_admin.php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Admin Account</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .setup-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            max-width: 500px;
            width: 100%;
        }
        .password-strength {
            height: 5px;
            margin-top: 5px;
            border-radius: 3px;
        }
        .strength-0 { background: #dc3545; width: 25%; }
        .strength-1 { background: #ffc107; width: 50%; }
        .strength-2 { background: #28a745; width: 75%; }
        .strength-3 { background: #20c997; width: 100%; }
    </style>
</head>
<body>
    <div class="setup-card p-4">
        <h2 class="text-center mb-4">🔧 Create Admin Account</h2>
        
        <?php
        include "Hybrid_CRUD.php";
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'];
            $password = $_POST['password'];
            $confirm_password = $_POST['confirm_password'];
            $firstname = $_POST['firstname'];
            $lastname = $_POST['lastname'];
            
            // Validation
            $errors = [];
            
            if (strlen($username) < 3) {
                $errors[] = "Username must be at least 3 characters";
            }
            
            if (strlen($password) < 6) {
                $errors[] = "Password must be at least 6 characters";
            }
            
            if ($password !== $confirm_password) {
                $errors[] = "Passwords do not match";
            }
            
            if (empty($firstname) || empty($lastname)) {
                $errors[] = "First and last name are required";
            }
            
            if (empty($errors)) {
                $hybrid = new Hybrid_CRUD();
                
                // Check if username exists
                if ($hybrid->username_exists($username)) {
                    echo '<div class="alert alert-warning">Username already exists!</div>';
                } else {
                    // Create admin using modified create_user method
                    $hybrid->create_user($username, $password, $firstname, $lastname);
                    
                    // Manually update role to Admin
                    $pdo = $hybrid->getPDO();
                    $update_stmt = $pdo->prepare("UPDATE user SET Role = 'Admin' WHERE username = ?");
                    $update_stmt->execute([$username]);
                    
                    echo '<div class="alert alert-success">
                        <h4>✅ Admin Account Created Successfully!</h4>
                        <p><strong>Username:</strong> ' . htmlspecialchars($username) . '</p>
                        <p><strong>Password:</strong> ********</p>
                        <p><strong>Role:</strong> Administrator</p>
                        <hr>
                        <a href="login.php" class="btn btn-success">Go to Login</a>
                        <a href="admin/index.php" class="btn btn-primary">Go to Admin Dashboard</a>
                    </div>';
                }
            } else {
                echo '<div class="alert alert-danger"><ul>';
                foreach ($errors as $error) {
                    echo '<li>' . htmlspecialchars($error) . '</li>';
                }
                echo '</ul></div>';
            }
        }
        ?>
        
        <form method="POST" id="adminForm">
            <div class="mb-3">
                <label for="username" class="form-label">Username *</label>
                <input type="text" class="form-control" id="username" name="username" 
                       placeholder="Enter admin username" required minlength="3">
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="firstname" class="form-label">First Name *</label>
                    <input type="text" class="form-control" id="firstname" name="firstname" required>
                </div>
                <div class="col-md-6">
                    <label for="lastname" class="form-label">Last Name *</label>
                    <input type="text" class="form-control" id="lastname" name="lastname" required>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="password" class="form-label">Password *</label>
                <input type="password" class="form-control" id="password" name="password" 
                       placeholder="Minimum 6 characters" required minlength="6"
                       onkeyup="checkPasswordStrength(this.value)">
                <div class="password-strength" id="passwordStrength"></div>
                <small class="text-muted">Password strength: <span id="strengthText">Weak</span></small>
            </div>
            
            <div class="mb-3">
                <label for="confirm_password" class="form-label">Confirm Password *</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                <div class="form-text" id="passwordMatch"></div>
            </div>
            
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary btn-lg">Create Admin Account</button>
                <a href="login.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
        
        <div class="mt-4">
            <div class="alert alert-info">
                <h5>📋 Pre-configured Admin Accounts:</h5>
                <p><strong>Option 1:</strong> Username: <code>admin</code>, Password: <code>admin123</code></p>
                <p><strong>Option 2:</strong> Username: <code>superadmin</code>, Password: <code>Admin@2024</code></p>
                <p class="mb-0"><small>These passwords are already hashed in the SQL script above.</small></p>
            </div>
        </div>
    </div>

    <script>
        function checkPasswordStrength(password) {
            let strength = 0;
            const strengthBar = document.getElementById('passwordStrength');
            const strengthText = document.getElementById('strengthText');
            
            if (password.length >= 6) strength++;
            if (password.length >= 8) strength++;
            if (/[A-Z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            if (/[^A-Za-z0-9]/.test(password)) strength++;
            
            // Cap at 4
            strength = Math.min(strength, 4);
            
            // Update strength bar
            strengthBar.className = 'password-strength strength-' + strength;
            
            // Update text
            const texts = ['Very Weak', 'Weak', 'Fair', 'Good', 'Strong'];
            strengthText.textContent = texts[strength];
            strengthText.className = strength < 2 ? 'text-danger' : 
                                    strength < 3 ? 'text-warning' : 'text-success';
        }
        
        document.getElementById('confirm_password').addEventListener('keyup', function() {
            const password = document.getElementById('password').value;
            const confirm = this.value;
            const matchText = document.getElementById('passwordMatch');
            
            if (confirm === '') {
                matchText.textContent = '';
                matchText.className = 'form-text';
            } else if (password === confirm) {
                matchText.textContent = '✓ Passwords match';
                matchText.className = 'form-text text-success';
            } else {
                matchText.textContent = '✗ Passwords do not match';
                matchText.className = 'form-text text-danger';
            }
        });
        
        // Auto-fill for testing
        document.addEventListener('DOMContentLoaded', function() {
            // Uncomment for testing
            // document.getElementById('username').value = 'admin';
            // document.getElementById('firstname').value = 'System';
            // document.getElementById('lastname').value = 'Administrator';
            // document.getElementById('password').value = 'admin123';
            // document.getElementById('confirm_password').value = 'admin123';
            // checkPasswordStrength('admin123');
        });
    </script>
</body>
</html>