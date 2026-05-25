<?php
// header.php - This file contains the navigation header
// Make sure session is already started in the main files
?>
<!-- Navigation Header -->
<link rel="stylesheet" href="../assets/header.css">
<nav class="navbar navbar-expand-lg navbar-dark fixed-top">
  <div class="container">
    <a class="navbar-brand fw-bold" href="index.php">
      <i class="fas fa-gamepad me-2"></i>Pixel Dash
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto">
        <li class="nav-item">
          <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>" href="index.php">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'about.php' ? 'active' : ''; ?>" href="about.php">About Us</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'leaderboard.php' ? 'active' : ''; ?>" href="leaderboard.php">Leaderboard</a>
        </li>
      </ul>
      
      <ul class="navbar-nav">
        <?php if(isset($_SESSION['USERNAME'])): ?>
          <!-- User Dropdown Menu -->
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              <div class="user-avatar me-2">
                <i class="fas fa-user-circle"></i>
              </div>
              <span class="user-name">
                <?php echo htmlspecialchars($_SESSION['USERNAME']); ?>
              </span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
              <li>
                <span class="dropdown-header">
                  <i class="fas fa-user me-2"></i>Welcome, <?php echo htmlspecialchars($_SESSION['USERNAME']); ?>!
                </span>
              </li>
              <li><hr class="dropdown-divider"></li>
              <li>
                <a class="dropdown-item" href="users/profile.php">
                  <i class="fas fa-user-edit me-2"></i>My Profile
                </a>
              </li>
              <li>
                <a class="dropdown-item" href="mygames.php">
                  <i class="fas fa-gamepad me-2"></i>My Games
                </a>
              </li>
              <li><hr class="dropdown-divider"></li>
              <li>
                <form method="POST" class="dropdown-item-form">
                  <button type="submit" name="logout" class="btn logout-dropdown-btn w-100 text-start">
                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                  </button>
                </form>
              </li>
            </ul>
          </li>
        <?php else: ?>
          <!-- Show login link when not logged in -->
          <li class="nav-item">
            <a class="nav-link" href="login.php">
              <i class="fas fa-sign-in-alt me-1"></i>Login
            </a>
          </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>