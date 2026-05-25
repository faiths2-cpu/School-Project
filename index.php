<?php
session_start();
include "Hybrid_CRUD.php";
$hybrid = new Hybrid_CRUD();

if(isset($_POST['logout'])){
    $hybrid->logout();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pixel Dash - Home</title>
  <link href="node_modules/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link rel="stylesheet" href="assets/style.css">
  <link rel="stylesheet" href="assets/header.css">

</head>
<body>
  <!-- Include the header -->
  <?php include 'includes/header.php'; ?>

  <section class="hero-section">
    <div class="container">
      <h1 class="hero-title">PIXEL DASH</h1>
      <p class="hero-subtitle">Experience the ultimate retro-style platformer with modern twists. Jump, dash, and conquer challenging levels in this pixel-perfect adventure!</p>
    </div>
  </section>

  <!-- 2 cards -->
  <section class="card-container">
    <div class="container">
      <div class="row justify-content-center g-4">
        <!-- card - play now -->
        <div class="col-lg-4 col-md-6">
          <div class="game-card text-center">
            <span class="feature-badge">NEW</span>
            <div class="pixel-art">
              <div class="pixel-character"></div>
            </div>
            <i class="fas fa-play-circle card-icon"></i>
            <h3 class="card-title">Play Now</h3>
            <p class="card-text">Jump right into the action! Start your pixel adventure and test your skills in our fast-paced platformer.</p>
            <a href="play.php" class="btn card-btn">Start Game <i class="fas fa-arrow-right ms-2"></i></a>
          </div>
        </div>
        
        <!-- card - leaderboards -->
        <div class="col-lg-4 col-md-6">
          <div class="game-card text-center">
            <div class="pixel-art">
              <div class="pixel-character"></div>
            </div>
            <i class="fas fa-trophy card-icon"></i>
            <h3 class="card-title">Leaderboard</h3>
            <p class="card-text">Compete with players worldwide. Climb the ranks and prove you're the ultimate Pixel Dash champion!</p>
            <a href="leaderboard.php" class="btn card-btn">View Ranking <i class="fas fa-arrow-right ms-2"></i></a>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- footer -->
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
</body>
</html>