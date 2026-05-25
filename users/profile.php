<?php
session_start();
include "../Hybrid_CRUD.php";
$hybrid = new Hybrid_CRUD();

// Check if user is logged in
if(!isset($_SESSION['USERNAME'])){
    header("location:login.php");
    exit();
}

$username = $_SESSION['USERNAME'];
$message = '';
$error = '';

// Get current user data
$user = $hybrid->get_user_profile($username);

if(!$user){
    $error = "User not found!";
} else {
    $firstname = $user['firstname'];
    $lastname = $user['lastname'];
    $role = $user['Role'];
    $created_at = date('F j, Y', strtotime($user['created_at']));
    $updated_at = date('F j, Y', strtotime($user['updated_at']));
    $fullname = $firstname . ' ' . $lastname;
}

// Handle form submission for updating profile
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    if(isset($_POST['update_profile'])) {
        $new_firstname = trim($_POST['firstname']);
        $new_lastname = trim($_POST['lastname']);
        $current_password = trim($_POST['current_password']);
        $new_password = trim($_POST['new_password']);
        $confirm_password = trim($_POST['confirm_password']);
        
        // Validate required fields
        if(empty($new_firstname) || empty($new_lastname)) {
            $error = "First name and last name are required!";
        } else {
            // If trying to change password
            if(!empty($new_password)) {
                // Verify current password
                if(!$hybrid->verify_current_password($username, $current_password)) {
                    $error = "Current password is incorrect!";
                } elseif($new_password !== $confirm_password) {
                    $error = "New passwords do not match!";
                } elseif(strlen($new_password) < 6) {
                    $error = "New password must be at least 6 characters!";
                } else {
                    // Update WITH new password (using the correct method)
                    if($hybrid->update_user_profile_with_password($username, $new_firstname, $new_lastname, $new_password)) {
                        $_SESSION['FNAME'] = $new_firstname;
                        $_SESSION['LNAME'] = $new_lastname;
                        $message = "Profile and password updated successfully!";
                        
                        // Refresh user data
                        $user = $hybrid->get_user_profile($username);
                        $firstname = $user['firstname'];
                        $lastname = $user['lastname'];
                        $updated_at = date('F j, Y', strtotime($user['updated_at']));
                        $fullname = $firstname . ' ' . $lastname;
                    } else {
                        $error = "Failed to update profile. Please try again.";
                    }
                }
            } else {
                // Update WITHOUT changing password (using the new method)
                if($hybrid->update_user_profile($username, $new_firstname, $new_lastname)) {
                    $_SESSION['FNAME'] = $new_firstname;
                    $_SESSION['LNAME'] = $new_lastname;
                    $message = "Profile updated successfully!";
                    
                    // Refresh user data
                    $user = $hybrid->get_user_profile($username);
                    $firstname = $user['firstname'];
                    $lastname = $user['lastname'];
                    $updated_at = date('F j, Y', strtotime($user['updated_at']));
                    $fullname = $firstname . ' ' . $lastname;
                } else {
                    $error = "Failed to update profile. Please try again.";
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Pixel Dash</title>
    <link href="../node_modules/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/style.css">
   
    <style>
        .profile-container {
            padding: 40px 0;
        }
        
        .profile-header {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            padding: 30px;
            margin-bottom: 30px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }
        
        .profile-avatar {
            width: 120px;
            height: 120px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 3rem;
            color: white;
            border: 4px solid var(--accent);
        }
        
        .profile-info-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            padding: 25px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            margin-bottom: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }
        
        .info-label {
            color: var(--accent);
            font-weight: 600;
            margin-bottom: 5px;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .info-value {
            color: var(--light);
            font-size: 1.1rem;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .role-badge {
            background: rgba(0, 255, 157, 0.2);
            color: var(--accent);
            padding: 5px 15px;
            border-radius: 50px;
            font-size: 0.9rem;
            font-weight: 600;
            border: 1px solid var(--accent);
            display: inline-block;
        }
        
        .stats-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            padding: 20px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            text-align: center;
            margin-bottom: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
        }
        
        .stats-card:hover {
            transform: translateY(-5px);
            border-color: var(--accent);
        }
        
        .stats-value {
            font-size: 2rem;
            font-weight: 800;
            color: var(--accent);
            margin-bottom: 5px;
        }
        
        .stats-label {
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.9rem;
        }
        
        .form-control {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: var(--light);
            border-radius: 10px;
            padding: 12px 15px;
        }
        
        .form-control:focus {
            background: rgba(255, 255, 255, 0.15);
            border-color: var(--accent);
            color: var(--light);
            box-shadow: 0 0 0 3px rgba(0, 255, 157, 0.1);
        }
        
        .form-label {
            color: var(--light);
            font-weight: 500;
            margin-bottom: 8px;
        }
        
        .alert {
            border-radius: 12px;
            border: none;
            backdrop-filter: blur(10px);
        }
        
        .alert-success {
            background: rgba(0, 255, 157, 0.1);
            color: var(--accent);
            border: 1px solid rgba(0, 255, 157, 0.3);
        }
        
        .alert-danger {
            background: rgba(255, 107, 107, 0.1);
            color: #ff6b6b;
            border: 1px solid rgba(255, 107, 107, 0.3);
        }
        
        .section-title {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 25px;
            color: var(--light);
            border-bottom: 2px solid rgba(0, 255, 157, 0.3);
            padding-bottom: 10px;
        }
        
        .tab-content {
            padding: 25px 0;
        }
        
        .nav-tabs {
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .nav-tabs .nav-link {
            background: transparent;
            border: none;
            color: rgba(255, 255, 255, 0.7);
            font-weight: 500;
            padding: 12px 25px;
            border-radius: 10px 10px 0 0;
            margin-right: 5px;
        }
        
        .nav-tabs .nav-link.active {
            background: rgba(0, 255, 157, 0.1);
            color: var(--accent);
            border-bottom: 3px solid var(--accent);
        }
        
        .nav-tabs .nav-link:hover {
            color: var(--accent);
            background: rgba(0, 255, 157, 0.05);
        }
        
        .password-hint {
            font-size: 0.85rem;
            color: rgba(255, 255, 255, 0.6);
            margin-top: 5px;
        }
        
        @media (max-width: 768px) {
            .profile-container {
                padding: 20px 0;
            }
            
            .profile-header {
                padding: 20px;
            }
            
            .profile-avatar {
                width: 100px;
                height: 100px;
                font-size: 2.5rem;
            }
        }
    </style>
</head>
<body>
    <!-- Include the header -->
    <?php include '../includes/header.php'; ?>

    <div class="profile-container">
        <div class="container">
            <?php if($error): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <?php if($message): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i><?php echo $message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <!-- Profile Header -->
            <div class="profile-header text-center">
                <div class="profile-avatar">
                    <i class="fas fa-user"></i>
                </div>
                <h1 class="hero-title mb-2"><?php echo htmlspecialchars($fullname); ?></h1>
                <p class="mb-3">@<?php echo htmlspecialchars($username); ?></p>
                <div class="role-badge mb-3">
                    <i class="fas fa-user-tag me-1"></i><?php echo $role; ?>
                </div>
                <p class="mb-0 text-muted">
                    <small>
                        <i class="far fa-calendar-plus me-1"></i>Joined <?php echo $created_at; ?> | 
                        <i class="far fa-calendar-check me-1"></i>Last updated <?php echo $updated_at; ?>
                    </small>
                </p>
            </div>
            
            <!-- Tabs Navigation -->
            <ul class="nav nav-tabs" id="profileTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="edit-tab" data-bs-toggle="tab" data-bs-target="#edit" type="button" role="tab">
                        <i class="fas fa-user-edit me-2"></i>Edit Profile
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="view-tab" data-bs-toggle="tab" data-bs-target="#view" type="button" role="tab">
                        <i class="fas fa-eye me-2"></i>View Profile
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="stats-tab" data-bs-toggle="tab" data-bs-target="#stats" type="button" role="tab">
                        <i class="fas fa-chart-bar me-2"></i>Statistics
                    </button>
                </li>
            </ul>
            
            <!-- Tab Content -->
            <div class="tab-content" id="profileTabsContent">
                
                <!-- Edit Profile Tab -->
                <div class="tab-pane fade show active" id="edit" role="tabpanel">
                    <h3 class="section-title">Edit Profile Information</h3>
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="profile-info-card">
                                <form method="POST" action="">
                                    <div class="row mb-4">
                                        <div class="col-md-6">
                                            <label for="username" class="form-label">Username</label>
                                            <input type="text" class="form-control" id="username" value="<?php echo htmlspecialchars($username); ?>" readonly>
                                            <div class="password-hint">Username cannot be changed</div>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="role" class="form-label">Account Role</label>
                                            <input type="text" class="form-control" id="role" value="<?php echo $role; ?>" readonly>
                                        </div>
                                    </div>
                                    
                                    <div class="row mb-4">
                                        <div class="col-md-6">
                                            <label for="firstname" class="form-label">First Name *</label>
                                            <input type="text" class="form-control" id="firstname" name="firstname" value="<?php echo htmlspecialchars($firstname); ?>" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="lastname" class="form-label">Last Name *</label>
                                            <input type="text" class="form-control" id="lastname" name="lastname" value="<?php echo htmlspecialchars($lastname); ?>" required>
                                        </div>
                                    </div>
                                    
                                    <hr class="my-4" style="border-color: rgba(255, 255, 255, 0.1);">
                                    
                                    <h5 class="mb-3" style="color: var(--accent);">
                                        <i class="fas fa-key me-2"></i>Change Password (Optional)
                                    </h5>
                                    <div class="row mb-4">
                                        <div class="col-md-6">
                                            <label for="current_password" class="form-label">Current Password</label>
                                            <input type="password" class="form-control" id="current_password" name="current_password" placeholder="Enter current password to change">
                                        </div>
                                    </div>
                                    
                                    <div class="row mb-4">
                                        <div class="col-md-6">
                                            <label for="new_password" class="form-label">New Password</label>
                                            <input type="password" class="form-control" id="new_password" name="new_password" placeholder="Enter new password">
                                            <div class="password-hint">Minimum 6 characters</div>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="confirm_password" class="form-label">Confirm New Password</label>
                                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm new password">
                                        </div>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between align-items-center mt-4">
                                        <a href="../index.php" class="btn btn-outline-light">
                                            <i class="fas fa-arrow-left me-2"></i>Back to Home
                                        </a>
                                        <button type="submit" name="update_profile" class="btn btn-primary" style="background: linear-gradient(to right, var(--primary), var(--secondary)); border: none;">
                                            <i class="fas fa-save me-2"></i>Save Changes
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        
                        <div class="col-lg-4">
                            <div class="profile-info-card">
                                <h5 class="mb-3" style="color: var(--accent);">
                                    <i class="fas fa-info-circle me-2"></i>Profile Tips
                                </h5>
                                <ul class="list-unstyled" style="color: rgba(255, 255, 255, 0.8);">
                                    <li class="mb-3">
                                        <i class="fas fa-check-circle me-2" style="color: var(--accent);"></i>
                                        Keep your information up to date
                                    </li>
                                    <li class="mb-3">
                                        <i class="fas fa-check-circle me-2" style="color: var(--accent);"></i>
                                        Use a strong, unique password
                                    </li>
                                    <li class="mb-3">
                                        <i class="fas fa-check-circle me-2" style="color: var(--accent);"></i>
                                        Your username cannot be changed
                                    </li>
                                    <li class="mb-3">
                                        <i class="fas fa-check-circle me-2" style="color: var(--accent);"></i>
                                        Contact support for account issues
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- View Profile Tab -->
                <div class="tab-pane fade" id="view" role="tabpanel">
                    <h3 class="section-title">Your Profile Information</h3>
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="profile-info-card">
                                <div class="row">
                                    <div class="col-md-6 mb-4">
                                        <div class="info-label">Username</div>
                                        <div class="info-value"><?php echo htmlspecialchars($username); ?></div>
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <div class="info-label">Account Role</div>
                                        <div class="info-value"><?php echo $role; ?></div>
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <div class="info-label">First Name</div>
                                        <div class="info-value"><?php echo htmlspecialchars($firstname); ?></div>
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <div class="info-label">Last Name</div>
                                        <div class="info-value"><?php echo htmlspecialchars($lastname); ?></div>
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <div class="info-label">Full Name</div>
                                        <div class="info-value"><?php echo htmlspecialchars($fullname); ?></div>
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <div class="info-label">Account Created</div>
                                        <div class="info-value"><?php echo $created_at; ?></div>
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <div class="info-label">Last Updated</div>
                                        <div class="info-value"><?php echo $updated_at; ?></div>
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <div class="info-label">Account Status</div>
                                        <div class="info-value">
                                            <span class="badge bg-success" style="background: rgba(0, 255, 157, 0.2)!important; color: var(--accent);">
                                                <i class="fas fa-check-circle me-1"></i>Active
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-4">
                            <div class="profile-info-card">
                                <h5 class="mb-3" style="color: var(--accent);">
                                    <i class="fas fa-shield-alt me-2"></i>Account Security
                                </h5>
                                <div class="d-grid gap-2">
                                    <a href="#" class="btn btn-outline-light mb-2">
                                        <i class="fas fa-history me-2"></i>Login History
                                    </a>
                                    <a href="#" class="btn btn-outline-light mb-2">
                                        <i class="fas fa-device me-2"></i>Connected Devices
                                    </a>
                                    <a href="#" class="btn btn-outline-danger">
                                        <i class="fas fa-user-slash me-2"></i>Deactivate Account
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Statistics Tab -->
                <div class="tab-pane fade" id="stats" role="tabpanel">
                    <h3 class="section-title">Your Game Statistics</h3>
                    <div class="row">
                        <div class="col-md-3 col-6">
                            <div class="stats-card">
                                <div class="stats-value">0</div>
                                <div class="stats-label">Games Played</div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="stats-card">
                                <div class="stats-value">0</div>
                                <div class="stats-label">Total Score</div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="stats-card">
                                <div class="stats-value">0</div>
                                <div class="stats-label">Highest Score</div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="stats-card">
                                <div class="stats-value">0</div>
                                <div class="stats-label">Average Score</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="profile-info-card mt-4">
                        <h5 class="mb-3" style="color: var(--accent);">
                            <i class="fas fa-trophy me-2"></i>Achievements
                        </h5>
                        <div class="text-center py-4">
                            <i class="fas fa-gamepad fa-3x mb-3" style="color: rgba(255, 255, 255, 0.2);"></i>
                            <p class="mb-0" style="color: rgba(255, 255, 255, 0.6);">
                                Play more games to unlock achievements!
                            </p>
                            <a href="../../pixeldash/play.php" class="btn btn-primary mt-3" style="background: linear-gradient(to right, var(--primary), var(--secondary)); border: none;">
                                <i class="fas fa-play me-2"></i>Start Playing
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="social-icons mb-3">
                <a href="#"><i class="fab fa-twitter"></i></a>
                <a href="#"><i class="fab fa-discord"></i></a>
                <a href="#"><i class="fab fa-youtube"></i></a>
                <a href="#"><i class="fab fa-twitch"></i></a>
            </div>
            <p>&copy; 2025 Pixel Dash. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Activate the first tab
        document.addEventListener('DOMContentLoaded', function() {
            var firstTab = new bootstrap.Tab(document.getElementById('edit-tab'));
            firstTab.show();
        });
    </script>
</body>
</html>