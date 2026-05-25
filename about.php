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
  <title>About Us - Pixel Dash</title>
  <link href="node_modules/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link rel="stylesheet" href="assets/style.css">
  <link rel="stylesheet" href="assets/header.css">
  
</head>
<body>
  <!-- Include the header -->
  <?php include 'includes/header.php'; ?>

  <!-- about pixel dash -->
  <section class="hero-section">
    <div class="container">
      <h1 class="hero-title">ABOUT PIXEL DASH</h1>
      <p class="hero-subtitle">Our Database 2 Final Project • University of Baguio • Third Year BSIT</p>
      
      <div class="row justify-content-center mt-5">
        <div class="col-lg-10">
          <div class="mission-section p-5">
            <i class="fas fa-university mission-icon"></i>
            <h2 class="mb-4">Our Academic Project</h2>
            <p class="member-description mb-4">Pixel Dash is our Database 2 final project developed by third-year BSIT students at the University of Baguio. We created this game to demonstrate our understanding of database systems, web development, and game programming.</p>
            <div class="university-badge">University of Baguio • Third Year BSIT • Database 2 Final Project</div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="team-section">
    <div class="container">
      <div class="row">
        <div class="col-12 text-center mb-5">
          <h2 class="section-title">Meet Our Team</h2>
          <p class="hero-subtitle">The creative minds behind Pixel Dash</p>
        </div>
      </div>

      <div class="row g-4">
        <!-- priam section -->
        <div class="col-lg-6">
          <div class="team-member-card">
            <div class="card-body text-center p-4">
              <div class="pixel-art rounded-circle mb-3 mx-auto" style="width: 150px; height: 150px;">
                <div class="pixel-character" style="width: 60px; height: 80px;"></div>
              </div>
              <h3 class="h4">Priam Lemos</h3>
              <div class="member-role">Frontend Developer & Database Architect</div>
              <p class="member-description">Priam worked on the frontend design. He made sure everything looked great and worked smoothly for players, while also designing the database structure.</p>
              <div class="social-links">
                <a href="#" title="GitHub"><i class="fab fa-github"></i></a>
                <a href="#" title="LinkedIn"><i class="fab fa-linkedin"></i></a>
                <a href="#" title="Twitter"><i class="fab fa-twitter"></i></a>
              </div>
            </div>
          </div>
        </div>

        <!-- ivan section -->
        <div class="col-lg-6">
          <div class="team-member-card">
            <div class="card-body text-center p-4">
              <div class="pixel-art rounded-circle mb-3 mx-auto" style="width: 150px; height: 150px;">
                <div class="pixel-character" style="width: 60px; height: 80px;"></div>
              </div>
              <h3 class="h4">Ivan Cortes</h3>
              <div class="member-role">Game Developer and Designer</div>
              <p class="member-description">Ivan built the main game and helped design how it works. He implemented the core gameplay mechanics and ensured the game is fun and engaging.</p>
              <div class="social-links">
                <a href="#" title="GitHub"><i class="fab fa-github"></i></a>
                <a href="#" title="LinkedIn"><i class="fab fa-linkedin"></i></a>
                <a href="#" title="Twitter"><i class="fab fa-twitter"></i></a>
              </div>
            </div>
          </div>
        </div>

        <!-- faith section -->
        <div class="col-lg-6">
          <div class="team-member-card">
            <div class="card-body text-center p-4">
              <div class="pixel-art rounded-circle mb-3 mx-auto" style="width: 150px; height: 150px;">
                <div class="pixel-character" style="width: 60px; height: 80px;"></div>
              </div>
              <h3 class="h4">Faith Domoguen</h3>
              <div class="member-role">Designer & Game Tester</div>
              <p class="member-description">Faith worked on the game's visual design and user experience. She helped make Pixel Dash look amazing and ensured it was intuitive for players.</p>
              <div class="social-links">
                <a href="#" title="GitHub"><i class="fab fa-github"></i></a>
                <a href="#" title="LinkedIn"><i class="fab fa-linkedin"></i></a>
                <a href="#" title="Twitter"><i class="fab fa-twitter"></i></a>
              </div>
            </div>
          </div>
        </div>

        <!-- sav section -->
        <div class="col-lg-6">
          <div class="team-member-card">
            <div class="card-body text-center p-4">
              <div class="pixel-art rounded-circle mb-3 mx-auto" style="width: 150px; height: 150px;">
                <div class="pixel-character" style="width: 60px; height: 80px;"></div>
              </div>
              <h3 class="h4">Azriel Savella</h3>
              <div class="member-role">Backend Developer & Quality Assurance</div>
              <p class="member-description">Azriel worked on the backend systems and server integration. He made sure the game saves scores properly and runs reliably across different platforms.</p>
              <div class="social-links">
                <a href="#" title="GitHub"><i class="fab fa-github"></i></a>
                <a href="#" title="LinkedIn"><i class="fab fa-linkedin"></i></a>
                <a href="#" title="Twitter"><i class="fab fa-twitter"></i></a>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- mission -->
      <div class="pixel-divider"></div>
      
      <div class="row justify-content-center mt-5">
        <div class="col-lg-10">
          <div class="mission-section p-5 text-center">
            <i class="fas fa-rocket mission-icon"></i>
            <h2 class="mb-4">Why We Chose This Project</h2>
            <p class="member-description mb-4">We wanted to create a game that's fun to play and brings people together while showcasing our technical skills in database management and web development.</p>
            <p class="member-description">As students, we believe games can create happy memories and help people connect. We hope Pixel Dash gives players a fun break and maybe even starts some friendly competition while demonstrating our academic capabilities.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- footer -->
  <footer>
    <div class="container">
      <div class="social-icons mb-3">
        <a href="https://x.com/"><i class="fab fa-twitter"></i></a>
        <a href="https://discord.com/"><i class="fab fa-discord"></i></a>
        <a href="https://www.youtube.com/"><i class="fab fa-youtube"></i></a>
        <a href="https://www.twitch.tv/"><i class="fab fa-twitch"></i></a>
      </div>
      <p>&copy; 2025 Pixel Dash. All rights reserved.</p>
    </div>
  </footer>
  
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>