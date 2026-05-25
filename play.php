<?php
// Start session only if not already started (prevents duplicate session_start() errors)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pixel Dash</title>
  <link href="node_modules/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <!-- Use your external CSS -->
  <link rel="stylesheet" href="assets/style.css">
  <link rel="stylesheet" href="assets/header.css">
  <style>
    /* Import the variables from your external CSS */
    

    /* Body styling for full background */
    body {
      background: linear-gradient(135deg, #0f0c29 0%, #302b63 50%, #24243e 100%);
      color: white;
      margin: 0;
      min-height: 100vh;
      font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
    }

    /* Main container centering */
    .main-container {
      padding-top: 80px; /* Account for fixed navbar */
      min-height: calc(100vh - 150px); /* Account for footer */
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
    }

    .game-canvas-container {
      display: flex;
      justify-content: center;
      margin: 20px 0;
    }

    #gameCanvas {
      border: 3px solid var(--primary-color);
      border-radius: 12px;
      display: none;
      background: #0a0a1a;
      max-width: 100%;
      height: auto;
    }

    .login-card {
      background: var(--card-bg);
      border: 1px solid var(--border-color);
      padding: 30px;
      border-radius: 15px;
      margin-bottom: 25px;
      backdrop-filter: blur(10px);
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
      max-width: 800px;
      width: 100%;
    }

    .btn-start {
      background: linear-gradient(to right, var(--primary), var(--secondary));
      border: none;
      padding: 12px 35px;
      border-radius: 25px;
      color: white;
      font-weight: 600;
      font-size: 1.1rem;
      transition: all 0.3s ease;
      box-shadow: 0 4px 15px rgba(106, 17, 203, 0.4);
    }

    .btn-start:hover {
      transform: translateY(-3px);
      box-shadow: 0 6px 20px rgba(106, 17, 203, 0.6);
    }

    .btn-restart {
      background: linear-gradient(to right, #ff416c, #ff4b2b);
      border: none;
      padding: 12px 35px;
      border-radius: 25px;
      color: white;
      font-weight: 600;
      font-size: 1.1rem;
      transition: all 0.3s ease;
      box-shadow: 0 4px 15px rgba(255, 65, 108, 0.4);
    }

    .btn-restart:hover {
      transform: translateY(-3px);
      box-shadow: 0 6px 20px rgba(255, 65, 108, 0.6);
    }

    .stats-card {
      background: var(--card-bg);
      border: 1px solid var(--border-color);
      border-radius: 15px;
      padding: 20px;
      margin: 20px auto;
      max-width: 800px;
      display: none;
      backdrop-filter: blur(10px);
      width: 100%;
    }

    .stat-value {
      font-size: 2rem;
      font-weight: bold;
      color: var(--primary-color);
      text-shadow: 0 0 10px rgba(0, 255, 157, 0.5);
    }

    .stat-label {
      font-size: 0.9rem;
      color: rgba(255, 255, 255, 0.7);
      margin-bottom: 5px;
    }

    .game-over-card {
      background: var(--card-bg);
      border: 1px solid var(--border-color);
      border-radius: 15px;
      padding: 40px;
      margin: 20px auto;
      max-width: 600px;
      text-align: center;
      display: none;
      backdrop-filter: blur(10px);
      animation: fadeIn 0.5s ease;
      width: 100%;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .final-score {
      font-size: 3.5rem;
      color: var(--primary-color);
      font-weight: bold;
      margin: 20px 0;
      text-shadow: 0 0 20px rgba(0, 255, 157, 0.7);
    }

    .alert-warning {
      background: rgba(255, 193, 7, 0.15);
      border: 1px solid rgba(255, 193, 7, 0.3);
      color: #ffc107;
      padding: 25px;
      border-radius: 12px;
      margin: 30px auto;
      max-width: 600px;
      text-align: center;
      backdrop-filter: blur(10px);
      width: 100%;
    }

    .welcome-text {
      font-size: 1.2rem;
      color: rgba(255, 255, 255, 0.9);
      margin-bottom: 20px;
    }

    .controls-list {
      background: rgba(0, 0, 0, 0.2);
      padding: 15px;
      border-radius: 10px;
      margin: 15px 0;
    }

    .controls-list p {
      margin: 8px 0;
      color: rgba(255, 255, 255, 0.8);
    }

    .game-id {
      font-size: 0.9rem;
      color: rgba(255, 255, 255, 0.6);
      font-style: italic;
    }

    /* Footer styling */
    footer {
      background: rgba(0, 0, 0, 0.7);
      padding: 20px 0;
      text-align: center;
      margin-top: auto;
    }

    .social-icons {
      display: flex;
      justify-content: center;
      gap: 15px;
      margin-bottom: 15px;
    }

    .social-icons a {
      color: white;
      font-size: 1.5rem;
      transition: color 0.3s;
    }

    .social-icons a:hover {
      color: var(--primary-color);
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
      .login-card, .stats-card, .game-over-card, .alert-warning {
        margin: 15px;
        padding: 20px;
      }
      
      #gameCanvas {
        width: 95vw;
        height: auto;
        aspect-ratio: 2 / 1; /* Maintain 800x400 ratio */
      }
      
      .final-score {
        font-size: 2.5rem;
      }
    }
  </style>
</head>

<body>
  <?php
  // Include Header - Fix the path if needed
  $headerPath = 'includes/header.php';
  if (file_exists($headerPath)) {
    include $headerPath;
  } else {
    // Fallback header
    echo '<nav class="navbar navbar-expand-lg navbar-dark fixed-top">
      <div class="container">
        <a class="navbar-brand" href="index.php">Pixel Dash</a>
        <div class="ms-auto">
          <a href="index.php" class="btn btn-outline-light me-2">Home</a>
          <a href="leaderboard.php" class="btn btn-outline-light">Leaderboard</a>
        </div>
      </div>
    </nav>';
  }
  ?>

  <?php
  // Now check login status AFTER session is started
 require_once 'Hybrid_CRUD.php';

// Check if extended class exists and include it
if (file_exists('Hybrid_CRUD_Extended.php')) {
    require_once 'Hybrid_CRUD_Extended.php';
    $crud = new Hybrid_CRUD_Extended();
} else {
    // Fallback to original class
    $crud = new Hybrid_CRUD();
}

  $isLoggedIn = isset($_SESSION['USERNAME']);
  $username = $isLoggedIn ? $_SESSION['USERNAME'] : '';
  $firstname = $isLoggedIn ? $_SESSION['FNAME'] : '';
  $lastname = $isLoggedIn ? $_SESSION['LNAME'] : '';
  
  // Get user ID from database
  $user_id = 0;
  if ($isLoggedIn) {
    $user = $crud->get_user_profile($username);
    if ($user) {
      $user_id = $user['user_id'];
    }
  }
  ?>
  
  <div class="main-container">
    <div class="container text-center">

      <?php if (!$isLoggedIn): ?>
        <!-- SHOW LOGIN REQUIRED MESSAGE -->
        <div class="alert-warning">
          <h3><i class="fas fa-exclamation-triangle me-2"></i>Login Required</h3>
          <p class="mb-3">You need to be logged in to play Pixel Dash and save your scores!</p>
          <a href="login.php" class="btn-start">Login Now</a>
          <a href="index.php" class="btn btn-outline-light ms-2">Back to Home</a>
        </div>
      <?php else: ?>

        <!-- GAME START SCREEN -->
        <div id="gameStartScreen">
          <div class="row justify-content-center">
            <div class="col-12 col-md-10 col-lg-8">
              <h1 class="mb-4"><i class="fas fa-gamepad me-2"></i>Play Pixel Dash</h1>
              
              <div class="login-card mx-auto">
                <h4>Welcome, <?php echo htmlspecialchars($firstname . ' ' . $lastname); ?>! <i class="fas fa-user-circle ms-2"></i></h4>
                <p class="welcome-text">Ready to play? Your score will be automatically saved to the leaderboard.</p>
                <div class="text-center mt-4">
                  <button onclick="startGame()" class="btn-start">
                    <i class="fas fa-play me-2"></i>Start Game
                  </button>
                </div>
              </div>

              <div class="login-card mx-auto">
                <h5><i class="fas fa-keyboard me-2"></i>Game Controls</h5>
                <div class="controls-list">
                  <p><i class="fas fa-space-shuttle me-2"></i>SPACE / CLICK / UP ARROW = Jump</p>
                  <p><i class="fas fa-pause me-2"></i>ESC = Pause/Resume</p>
                </div>
                <p class="game-id"><i class="fas fa-hashtag me-1"></i>Game ID: 1 (Pixel Dash)</p>
              </div>

              <!-- User Stats -->
              <div class="login-card mx-auto">
                <h5><i class="fas fa-chart-line me-2"></i>Your Stats</h5>
                <div id="userStats">
                  <div class="text-center py-3">
                    <div class="spinner-border text-primary" role="status">
                      <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Loading your statistics...</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- GAME CANVAS CONTAINER -->
        <div class="game-canvas-container">
          <canvas id="gameCanvas" width="800" height="400"></canvas>
        </div>

        <!-- STATS -->
        <div id="gameStats" class="stats-card mx-auto" style="display:none;">
          <div class="row text-center">
            <div class="col">
              <div class="stat-label">Score</div>
              <div class="stat-value" id="scoreValue">0</div>
            </div>
            <div class="col">
              <div class="stat-label">Distance</div>
              <div class="stat-value" id="distanceValue">0m</div>
            </div>
            <div class="col">
              <div class="stat-label">Speed</div>
              <div class="stat-value" id="speedValue">1x</div>
            </div>
          </div>
        </div>

        <!-- GAME OVER SCREEN -->
        <div id="gameOverScreen" class="game-over-card mx-auto">
          <h2><i class="fas fa-gamepad me-2"></i>Game Over!</h2>
          <div class="final-score" id="finalScore">0</div>
          <p id="finalDistance">Distance: 0m</p>
          <p id="scoreSavedMessage" style="display:none; color: var(--primary-color);">
            <i class="fas fa-check-circle"></i> Score saved to leaderboard!
          </p>
          <div class="mt-4">
            <button onclick="restartGame()" class="btn-restart">
              <i class="fas fa-redo me-2"></i>Play Again
            </button>
            <a href="leaderboard.php" class="btn btn-outline-light ms-2">
              <i class="fas fa-trophy me-2"></i>View Leaderboard
            </a>
          </div>
        </div>

      <?php endif; ?>
    </div>
  </div>

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

<script>
// ===========================================================
// ASSET PATHS 
// ===========================================================
const ASSETS = {
  forest: {
    sky: "assets/bg_layer/forest/sky.png",
    far: "assets/bg_layer/forest/far.png",
    middle: "assets/bg_layer/forest/middle.png",
    ground: "assets/bg_layer/forest/ground.png",
    dark: "assets/bg_layer/forest/dark.png"
  },
  pinkForest: {
    sky: "assets/bg_layer/pinkForest/sky.png",
    far: "assets/bg_layer/pinkForest/far.png",
    middle: "assets/bg_layer/pinkForest/middle.png",
    ground: "assets/bg_layer/pinkForest/ground.png",
    dark: "assets/bg_layer/pinkForest/dark.png"
  },
  player: {
    run: [
      "assets/characters/character/run/run1.png",
      "assets/characters/character/run/run2.png"
    ],
    jump: ["assets/characters/character/jump/jump.png"],
    fall: ["assets/characters/character/fall/fall1.png"],
    death: [
      "assets/characters/character/death/death1.png",
      "assets/characters/character/death/death2.png",
      "assets/characters/character/death/death3.png",
      "assets/characters/character/death/death4.png",
      "assets/characters/character/death/death5.png"
    ]
  },
  obstacles: {
    snail: [
      "assets/Obstacles/obstacle1/obstacle1x4.png",
      "assets/Obstacles/obstacle1/obstacle1x5.png",
      "assets/Obstacles/obstacle1/obstacle1x6.png"
    ],
    hedgehog: [
      "assets/Obstacles/obstacle2/obstacle2x4.png",
      "assets/Obstacles/obstacle2/obstacle2x5.png",
      "assets/Obstacles/obstacle2/obstacle2x6.png",
      "assets/Obstacles/obstacle2/obstacle2x7.png"
    ],
    slime: [
      "assets/Obstacles/obstacle3/slimex4.png",
      "assets/Obstacles/obstacle3/slimex5.png",
      "assets/Obstacles/obstacle3/slimex6.png"
    ]
  }
};

// ===========================================================
// GAME CONFIGURATION
// ===========================================================
const CONFIG = {
  canvas: {
    width: 800,
    height: 400,
    scaleFactor: 400 / 1080
  },
  player: {
    width: 60,
    height: 70,
    x: 100,
    groundY: null,
    gravity: 0.5,
    jumpForce: -12,
    animationSpeed: {
      run: 6,
      death: 10
    }
  },
  obstacle: {
    width: 50,
    height: 50,
    y: null,
    baseSpeed: 3,
    spawnInterval: {
      min: 80,
      max: 120
    }
  },
  background: {
    speeds: [0.1, 0.3, 0.8, 2, 5],
    currentSet: 'forest',
    transitionScoreInterval: 1000,
    transitionDuration: 500,
    bgSequence: ['forest', 'pinkForest'],
    currentSequenceIndex: 0,
    transitionCooldown: 200,
    groundYOffset: 258,
    positions: {
      forest: [0, 0, 0, 0, 0],
      pinkForest: [0, 0, 0, 0, 0]
    },
    groundLevels: {
      forest: 258,
      pinkForest: 258
    }
  }
};

// ===========================================================
// GAME STATE
// ===========================================================
const GameState = {
  isRunning: false,
  isPaused: false,
  score: 0,
  distance: 0,
  gameSpeed: 1,
  frames: 0,
  lastObstacleFrame: 0,
  
  isTransitioning: false,
  transitionProgress: 0,
  targetBgSet: 'forest',
  currentBgSet: 'forest',
  lastTransitionScore: 0,
  transitionCooldownFrames: 0,
  canTransition: true,
  
  cameraShake: {
    intensity: 0,
    duration: 0,
    time: 0
  },
  
  obstacles: [],
  animationId: null,
  
  assets: {
    backgrounds: {},
    player: {
      run: [],
      jump: null,
      fall: null,
      death: []
    },
    obstacles: {
      snail: [],
      hedgehog: [],
      slime: []
    }
  },
  assetsLoaded: false,
  
  bgPositions: [0, 0, 0, 0, 0],
  foregroundPositions: [0, 0, 0, 0, 0]
};

// ===========================================================
// DOM ELEMENTS
// ===========================================================
const canvas = document.getElementById('gameCanvas');
const ctx = canvas.getContext('2d');

// ===========================================================
// ASSET LOADER
// ===========================================================
class AssetLoader {
  static async load() {
    const promises = [];
    
    // Load forest background
    for (const [key, path] of Object.entries(ASSETS.forest)) {
      promises.push(this.loadImage(path).then(img => {
        GameState.assets.backgrounds[`forest_${key}`] = img;
        console.log(`Loaded forest_${key}:`, img.src);
      }));
    }
    
    // Load pink forest background
    for (const [key, path] of Object.entries(ASSETS.pinkForest)) {
      promises.push(this.loadImage(path).then(img => {
        GameState.assets.backgrounds[`pinkForest_${key}`] = img;
        console.log(`Loaded pinkForest_${key}:`, img.src);
      }));
    }
    
    // Load player animations
    ASSETS.player.run.forEach((path, index) => {
      promises.push(this.loadImage(path).then(img => {
        GameState.assets.player.run[index] = img;
      }));
    });
    
    promises.push(this.loadImage(ASSETS.player.jump[0]).then(img => {
      GameState.assets.player.jump = img;
    }));
    
    promises.push(this.loadImage(ASSETS.player.fall[0]).then(img => {
      GameState.assets.player.fall = img;
    }));
    
    ASSETS.player.death.forEach((path, index) => {
      promises.push(this.loadImage(path).then(img => {
        GameState.assets.player.death[index] = img;
      }));
    });
    
    // Load obstacles
    ASSETS.obstacles.snail.forEach((path, index) => {
      promises.push(this.loadImage(path).then(img => {
        GameState.assets.obstacles.snail[index] = img;
      }));
    });
    
    ASSETS.obstacles.hedgehog.forEach((path, index) => {
      promises.push(this.loadImage(path).then(img => {
        GameState.assets.obstacles.hedgehog[index] = img;
      }));
    });
    
    ASSETS.obstacles.slime.forEach((path, index) => {
      promises.push(this.loadImage(path).then(img => {
        GameState.assets.obstacles.slime[index] = img;
      }));
    });
    
    await Promise.all(promises);
    console.log('All assets loaded successfully!');
    console.log('Available backgrounds:', Object.keys(GameState.assets.backgrounds));
    
    this.setGroundPosition();
  }
  
  static loadImage(src) {
    return new Promise((resolve, reject) => {
      const img = new Image();
      img.onload = () => resolve(img);
      img.onerror = () => {
        console.error(`Failed to load image: ${src}`);
        const placeholder = document.createElement('canvas');
        placeholder.width = 100;
        placeholder.height = 100;
        const ctx = placeholder.getContext('2d');
        ctx.fillStyle = '#ff4757';
        ctx.fillRect(0, 0, 100, 100);
        ctx.fillStyle = '#fff';
        ctx.font = '12px Arial';
        ctx.fillText('IMG', 35, 55);
        resolve(placeholder);
      };
      img.src = src;
    });
  }
  
  static setGroundPosition() {
    CONFIG.player.groundY = CONFIG.background.groundYOffset;
    CONFIG.obstacle.y = CONFIG.player.groundY - CONFIG.obstacle.height;
    
    console.log('Ground positions set:');
    console.log('- Player groundY:', CONFIG.player.groundY);
    console.log('- Obstacle y:', CONFIG.obstacle.y);
    
    GameState.assetsLoaded = true;
  }
}

// ===========================================================
// PLAYER CLASS
// ===========================================================
class Player {
  constructor() {
    this.width = CONFIG.player.width;
    this.height = CONFIG.player.height;
    this.x = CONFIG.player.x;
    this.groundY = CONFIG.player.groundY;
    this.y = this.groundY - this.height;
    this.velocityY = 0;
    this.gravity = CONFIG.player.gravity;
    this.jumpForce = CONFIG.player.jumpForce;
    this.isJumping = false;
    this.isAlive = true;
    
    this.animationState = 'run';
    this.animationFrame = 0;
    this.animationTick = 0;
    this.deathAnimationComplete = false;
  }
  
  jump() {
    if (!this.isJumping && !GameState.isPaused && this.isAlive) {
      this.velocityY = this.jumpForce;
      this.isJumping = true;
      this.animationState = 'jump';
    }
  }
  
  update() {
    if (!this.isAlive || GameState.isPaused) {
      this.updateAnimation();
      return;
    }
    
    this.velocityY += this.gravity;
    this.y += this.velocityY;
    
    if (this.y >= this.groundY - this.height) {
      this.y = this.groundY - this.height;
      this.velocityY = 0;
      this.isJumping = false;
      
      if (this.isAlive) {
        this.animationState = 'run';
      }
    }
    
    if (this.isAlive && this.isJumping) {
      if (this.velocityY < 0) {
        this.animationState = 'jump';
      } else if (this.velocityY > 1) {
        this.animationState = 'fall';
      }
    }
    
    this.updateAnimation();
  }
  
  updateAnimation() {
    this.animationTick++;
    
    if (this.animationState === 'run') {
      if (this.animationTick >= CONFIG.player.animationSpeed.run) {
        this.animationFrame = (this.animationFrame + 1) % GameState.assets.player.run.length;
        this.animationTick = 0;
      }
    } else if (this.animationState === 'death') {
      if (this.animationTick >= CONFIG.player.animationSpeed.death) {
        if (this.animationFrame < GameState.assets.player.death.length - 1) {
          this.animationFrame++;
          this.animationTick = 0;
        } else {
          this.deathAnimationComplete = true;
        }
      }
    }
  }
  
  draw() {
    let image;
    
    switch(this.animationState) {
      case 'run':
        image = GameState.assets.player.run[this.animationFrame];
        break;
      case 'jump':
        image = GameState.assets.player.jump;
        break;
      case 'fall':
        image = GameState.assets.player.fall;
        break;
      case 'death':
        image = GameState.assets.player.death[this.animationFrame] || 
                GameState.assets.player.death[GameState.assets.player.death.length - 1];
        break;
      default:
        image = GameState.assets.player.run[0];
    }
    
    if (image) {
      ctx.drawImage(image, this.x, this.y, this.width, this.height);
    } else {
      ctx.fillStyle = '#00ff9d';
      ctx.fillRect(this.x, this.y, this.width, this.height);
    }
  }
  
  kill() {
    if (this.isAlive) {
      this.isAlive = false;
      this.animationState = 'death';
      this.animationFrame = 0;
      this.animationTick = 0;
      this.deathAnimationComplete = false;
    }
  }
  
  reset() {
    this.x = CONFIG.player.x;
    this.groundY = CONFIG.player.groundY;
    this.y = this.groundY - this.height;
    this.velocityY = 0;
    this.isJumping = false;
    this.isAlive = true;
    this.animationState = 'run';
    this.animationFrame = 0;
    this.animationTick = 0;
    this.deathAnimationComplete = false;
  }
}

// ===========================================================
// BACKGROUND RENDERER
// ===========================================================
class BackgroundRenderer {
  static draw() {
    // Clear canvas with appropriate background color
    if (GameState.currentBgSet === 'pinkForest') {
      ctx.fillStyle = '#ffccf9'; // Light pink background for pink forest
    } else {
      ctx.fillStyle = '#0a0a1a'; // Dark background for forest
    }
    ctx.fillRect(0, 0, CONFIG.canvas.width, CONFIG.canvas.height);
    
    const backgroundLayers = ['sky', 'far', 'middle', 'ground'];
    
    for (let i = 0; i < backgroundLayers.length; i++) {
      GameState.bgPositions[i] -= CONFIG.background.speeds[i] * GameState.gameSpeed;
      
      if (GameState.isTransitioning) {
        // Draw both current and target backgrounds during transition
        const currentKey = `${GameState.currentBgSet}_${backgroundLayers[i]}`;
        const targetKey = `${GameState.targetBgSet}_${backgroundLayers[i]}`;
        const currentImage = GameState.assets.backgrounds[currentKey];
        const targetImage = GameState.assets.backgrounds[targetKey];
        
        // Draw current background (fading out)
        if (currentImage) {
          ctx.globalAlpha = 1 - GameState.transitionProgress;
          this.drawLayer(currentImage, i, GameState.bgPositions[i]);
        }
        
        // Draw target background (fading in)
        if (targetImage) {
          ctx.globalAlpha = GameState.transitionProgress;
          this.drawLayer(targetImage, i, GameState.bgPositions[i]);
        }
        
        ctx.globalAlpha = 1.0;
      } else {
        // Draw only current background
        const currentKey = `${GameState.currentBgSet}_${backgroundLayers[i]}`;
        const currentImage = GameState.assets.backgrounds[currentKey];
        
        if (currentImage) {
          this.drawLayer(currentImage, i, GameState.bgPositions[i]);
        } else {
          console.warn(`Missing image: ${currentKey}`);
        }
      }
      
      // Reset position when image scrolls off screen
      const currentKey = `${GameState.currentBgSet}_${backgroundLayers[i]}`;
      const currentImage = GameState.assets.backgrounds[currentKey];
      if (currentImage) {
        const scaledHeight = CONFIG.canvas.height;
        const scaledWidth = currentImage.width * (scaledHeight / currentImage.height);
        if (Math.abs(GameState.bgPositions[i]) >= scaledWidth) {
          GameState.bgPositions[i] = 0;
        }
      }
    }
  }
  
  static drawLayer(image, layerIndex, position) {
    const scaledHeight = CONFIG.canvas.height;
    const scaledWidth = image.width * (scaledHeight / image.height);
    
    const x1 = position % scaledWidth;
    const x2 = x1 + scaledWidth;
    
    ctx.drawImage(image, x1, 0, scaledWidth, scaledHeight);
    ctx.drawImage(image, x2, 0, scaledWidth, scaledHeight);
  }
  
  static drawForeground() {
    const foregroundLayer = 'dark';
    const layerIndex = 4;
    
    GameState.foregroundPositions[layerIndex] -= CONFIG.background.speeds[layerIndex] * GameState.gameSpeed;
    
    if (GameState.isTransitioning) {
      const currentKey = `${GameState.currentBgSet}_${foregroundLayer}`;
      const targetKey = `${GameState.targetBgSet}_${foregroundLayer}`;
      const currentImage = GameState.assets.backgrounds[currentKey];
      const targetImage = GameState.assets.backgrounds[targetKey];
      
      if (currentImage) {
        ctx.globalAlpha = 1 - GameState.transitionProgress;
        this.drawForegroundLayer(currentImage, layerIndex, GameState.foregroundPositions[layerIndex]);
      }
      
      if (targetImage) {
        ctx.globalAlpha = GameState.transitionProgress;
        this.drawForegroundLayer(targetImage, layerIndex, GameState.foregroundPositions[layerIndex]);
      }
      
      ctx.globalAlpha = 1.0;
    } else {
      const currentKey = `${GameState.currentBgSet}_${foregroundLayer}`;
      const currentImage = GameState.assets.backgrounds[currentKey];
      
      if (currentImage) {
        this.drawForegroundLayer(currentImage, layerIndex, GameState.foregroundPositions[layerIndex]);
      }
    }
    
    const currentKey = `${GameState.currentBgSet}_${foregroundLayer}`;
    const currentImage = GameState.assets.backgrounds[currentKey];
    if (currentImage) {
      const scaledHeight = CONFIG.canvas.height;
      const scaledWidth = currentImage.width * (scaledHeight / currentImage.height);
      if (Math.abs(GameState.foregroundPositions[layerIndex]) >= scaledWidth) {
        GameState.foregroundPositions[layerIndex] = 0;
      }
    }
  }
  
  static drawForegroundLayer(image, layerIndex, position) {
    const scaledHeight = CONFIG.canvas.height;
    const scaledWidth = image.width * (scaledHeight / image.height);
    
    const x1 = position % scaledWidth;
    const x2 = x1 + scaledWidth;
    
    ctx.drawImage(image, x1, 0, scaledWidth, scaledHeight);
    ctx.drawImage(image, x2, 0, scaledWidth, scaledHeight);
  }
}

// ===========================================================
// OBSTACLE CLASS
// ===========================================================
class Obstacle {
  constructor() {
    let obstacleType;
    if (GameState.score < 500) {
      obstacleType = 'snail';
    } else if (GameState.score < 1500) {
      obstacleType = Math.random() < 0.7 ? 'snail' : 'hedgehog';
    } else {
      const rand = Math.random();
      if (rand < 0.4) obstacleType = 'snail';
      else if (rand < 0.8) obstacleType = 'hedgehog';
      else obstacleType = 'slime';
    }
    
    // Animation properties
    this.type = obstacleType;
    this.animationFrames = GameState.assets.obstacles[obstacleType];
    this.currentFrame = 0;
    this.animationTick = 0;
    
    // Set animation speed based on obstacle type
    switch(obstacleType) {
      case 'snail':
        this.animationSpeed = 15;
        break;
      case 'hedgehog':
        this.animationSpeed = 10;
        break;
      case 'slime':
        this.animationSpeed = 8;
        break;
      default:
        this.animationSpeed = 10;
    }
    
    // Size variation for more visual interest
    const sizeVariation = 0.9 + Math.random() * 0.2;
    this.width = CONFIG.obstacle.width * sizeVariation;
    this.height = CONFIG.obstacle.height * sizeVariation;
    
    // Position
    this.x = CONFIG.canvas.width;
    this.y = CONFIG.player.groundY - this.height;
    this.speed = CONFIG.obstacle.baseSpeed * GameState.gameSpeed;
    this.passed = false;
  }
  
  update() {
    // Movement
    this.x -= this.speed;
    this.speed = CONFIG.obstacle.baseSpeed * GameState.gameSpeed;
    
    // Animation update
    this.animationTick++;
    if (this.animationTick >= this.animationSpeed) {
      this.currentFrame = (this.currentFrame + 1) % this.animationFrames.length;
      this.animationTick = 0;
    }
    
    // Special behaviors based on type
    switch(this.type) {
      case 'slime':
        // Add slight bouncing for slime
        this.y = CONFIG.player.groundY - this.height + Math.sin(GameState.frames * 0.08) * 2;
        break;
    }
  }
  
  draw() {
    const frameImage = this.animationFrames[this.currentFrame];
    if (frameImage) {
      ctx.drawImage(frameImage, this.x, this.y, this.width, this.height);
    } else {
      // Fallback drawing if animation frames fail to load
      switch(this.type) {
        case 'snail': 
          ctx.fillStyle = '#8B4513';
          ctx.beginPath();
          ctx.ellipse(this.x + this.width/2, this.y + this.height/2, 
                     this.width/2, this.height/2, 0, 0, Math.PI * 2);
          ctx.fill();
          ctx.fillStyle = '#A0522D';
          ctx.beginPath();
          ctx.arc(this.x + this.width/2, this.y + this.height/2, this.width/3, 0, Math.PI * 2);
          ctx.fill();
          break;
          
        case 'hedgehog': 
          ctx.fillStyle = '#A0522D';
          ctx.fillRect(this.x, this.y, this.width, this.height);
          ctx.fillStyle = '#8B4513';
          for (let i = 0; i < 5; i++) {
            const spikeX = this.x + (this.width / 6) + (this.width / 5) * i;
            ctx.beginPath();
            ctx.moveTo(spikeX, this.y);
            ctx.lineTo(spikeX + this.width/12, this.y + this.height/2);
            ctx.lineTo(spikeX, this.y + this.height);
            ctx.fill();
          }
          break;
          
        case 'slime': 
          const bounce = Math.sin(GameState.frames * 0.08) * 2;
          ctx.fillStyle = '#32CD32';
          ctx.beginPath();
          ctx.ellipse(this.x + this.width/2, this.y + this.height/2 + bounce/2, 
                     this.width/2, this.height/2.5, 0, 0, Math.PI * 2);
          ctx.fill();
          ctx.fillStyle = 'white';
          ctx.beginPath();
          ctx.arc(this.x + this.width/3, this.y + this.height/3, this.width/10, 0, Math.PI * 2);
          ctx.arc(this.x + 2*this.width/3, this.y + this.height/3, this.width/10, 0, Math.PI * 2);
          ctx.fill();
          ctx.fillStyle = 'black';
          ctx.beginPath();
          ctx.arc(this.x + this.width/3, this.y + this.height/3, this.width/20, 0, Math.PI * 2);
          ctx.arc(this.x + 2*this.width/3, this.y + this.height/3, this.width/20, 0, Math.PI * 2);
          ctx.fill();
          break;
          
        default: 
          ctx.fillStyle = '#ff4757';
          ctx.fillRect(this.x, this.y, this.width, this.height);
      }
    }
  }
  
  isOffScreen() {
    return this.x + this.width < 0;
  }
  
  checkCollision(player) {
    // Make obstacle hitbox smaller
    const obstacleHitboxWidth = this.width * 0.7;
    const obstacleHitboxHeight = this.height * 0.8;
    const obstacleHitboxX = this.x + (this.width - obstacleHitboxWidth) / 2;
    const obstacleHitboxY = this.y + (this.height - obstacleHitboxHeight) / 2;
    
    // Player hitbox adjustment
    const playerHitboxWidth = player.width * 0.7;
    const playerHitboxHeight = player.height * 0.8;
    const playerHitboxX = player.x + (player.width - playerHitboxWidth) / 2;
    const playerHitboxY = player.y + (player.height - playerHitboxHeight) / 2;
    
    return (
      playerHitboxX < obstacleHitboxX + obstacleHitboxWidth &&
      playerHitboxX + playerHitboxWidth > obstacleHitboxX &&
      playerHitboxY < obstacleHitboxY + obstacleHitboxHeight &&
      playerHitboxY + playerHitboxHeight > obstacleHitboxY
    );
  }
}

// ===========================================================
// CAMERA SHAKE MANAGER
// ===========================================================
class CameraShake {
  static update() {
    if (GameState.cameraShake.duration > 0) {
      GameState.cameraShake.time++;
      GameState.cameraShake.duration--;
      
      // Exponential decay for smoother shake
      GameState.cameraShake.intensity *= 0.9;
      
      if (GameState.cameraShake.intensity < 0.5) {
        this.resetShake();
      }
    }
  }
  
  static apply() {
    if (GameState.cameraShake.intensity > 0) {
      // Generate random offset for shake effect
      const offsetX = (Math.random() - 0.5) * 2 * GameState.cameraShake.intensity;
      const offsetY = (Math.random() - 0.5) * 2 * GameState.cameraShake.intensity;
      
      // Apply translation
      ctx.translate(offsetX, offsetY);
    }
  }
  
  static triggerShake(intensity = 15, duration = 20) {
    GameState.cameraShake.intensity = intensity;
    GameState.cameraShake.duration = duration;
    GameState.cameraShake.time = 0;
  }
  
  static resetShake() {
    GameState.cameraShake.intensity = 0;
    GameState.cameraShake.duration = 0;
    GameState.cameraShake.time = 0;
  }
}

// ===========================================================
// BACKGROUND TRANSITION MANAGER
// ===========================================================
class BackgroundTransition {
  static update() {
    // Update transition cooldown
    if (GameState.transitionCooldownFrames > 0) {
      GameState.transitionCooldownFrames--;
    } else {
      GameState.canTransition = true;
    }
    
    // Check if we should start a new transition based on score intervals
    if (!GameState.isTransitioning && GameState.canTransition) {
      const scoreSinceLastTransition = GameState.score - GameState.lastTransitionScore;
      
      if (scoreSinceLastTransition >= CONFIG.background.transitionScoreInterval) {
        // Get next background in sequence
        const nextIndex = (CONFIG.background.currentSequenceIndex + 1) % CONFIG.background.bgSequence.length;
        const nextBgSet = CONFIG.background.bgSequence[nextIndex];
        
        // Only transition if it's different from current
        if (nextBgSet !== GameState.currentBgSet) {
          this.startTransition(nextBgSet);
          CONFIG.background.currentSequenceIndex = nextIndex;
          GameState.lastTransitionScore = GameState.score;
        }
      }
    }
    
    // Update transition progress
    if (GameState.isTransitioning) {
      GameState.transitionProgress += 1 / CONFIG.background.transitionDuration;
      
      // Interpolate ground level during transition
      const currentGround = CONFIG.background.groundLevels[GameState.currentBgSet];
      const targetGround = CONFIG.background.groundLevels[GameState.targetBgSet];
      const interpolatedGround = currentGround + (targetGround - currentGround) * GameState.transitionProgress;
      
      // Update config with interpolated ground level
      CONFIG.player.groundY = interpolatedGround;
      CONFIG.background.groundLevels[CONFIG.background.currentSet] = interpolatedGround;
      
      // Complete transition
      if (GameState.transitionProgress >= 1) {
        GameState.transitionProgress = 1;
        GameState.isTransitioning = false;
        GameState.currentBgSet = GameState.targetBgSet;
        CONFIG.background.currentSet = GameState.targetBgSet;
        
        // Update to final ground level
        const finalGround = CONFIG.background.groundLevels[GameState.targetBgSet];
        CONFIG.player.groundY = finalGround;
        CONFIG.background.groundLevels[CONFIG.background.currentSet] = finalGround;
        
        // Update player and obstacles to new ground level
        if (player) {
          player.groundY = finalGround;
          if (player.y + player.height > player.groundY) {
            player.y = player.groundY - player.height;
          }
        }
        
        // Update all obstacles to new ground level
        GameState.obstacles.forEach(obstacle => {
          obstacle.y = finalGround - CONFIG.obstacle.height;
        });
        
        // Start cooldown before next transition
        GameState.canTransition = false;
        GameState.transitionCooldownFrames = CONFIG.background.transitionCooldown;
        
        // Show transition complete message
        setTimeout(() => {
          this.showTransitionMessage(`${this.getBgName(GameState.targetBgSet)} Realm`, false);
        }, 100);
      }
    }
  }
  
  static startTransition(targetSet) {
    if (GameState.isTransitioning || GameState.currentBgSet === targetSet || !GameState.canTransition) return;
    
    GameState.isTransitioning = true;
    GameState.transitionProgress = 0;
    GameState.targetBgSet = targetSet;
    
    // Clear all existing obstacles when transition starts
    this.clearAllObstacles();
    
    // Show transition start message
    this.showTransitionMessage(`Entering ${this.getBgName(targetSet)}...`, true);
  }
  
  static clearAllObstacles() {
    // Remove all obstacles from the game
    GameState.obstacles = [];
  }
  
  static getBgName(bgSet) {
    switch(bgSet) {
      case 'forest': return 'Forest';
      case 'pinkForest': return 'Pink Forest';
      default: return bgSet;
    }
  }
  
  static showTransitionMessage(message, isStart) {
    let messageEl = document.getElementById('transitionMessage');
    if (!messageEl) {
      messageEl = document.createElement('div');
      messageEl.id = 'transitionMessage';
      messageEl.style.position = 'absolute';
      messageEl.style.top = '50%';
      messageEl.style.left = '50%';
      messageEl.style.transform = 'translate(-50%, -50%)';
      messageEl.style.color = '#ffffff';
      messageEl.style.fontSize = '24px';
      messageEl.style.fontWeight = 'bold';
      messageEl.style.textShadow = '2px 2px 4px rgba(0,0,0,0.8)';
      messageEl.style.zIndex = '1000';
      messageEl.style.pointerEvents = 'none';
      messageEl.style.textAlign = 'center';
      messageEl.style.backgroundColor = 'rgba(0,0,0,0.5)';
      messageEl.style.padding = '10px 20px';
      messageEl.style.borderRadius = '10px';
      messageEl.style.opacity = '0';
      messageEl.style.transition = 'opacity 0.5s ease-in-out';
      document.body.appendChild(messageEl);
    }
    
    // Set color based on background
    if (GameState.targetBgSet === 'pinkForest') {
      messageEl.style.color = '#ff69b4';
      messageEl.style.border = '2px solid #ff69b4';
    } else {
      messageEl.style.color = '#00ff9d';
      messageEl.style.border = '2px solid #00ff9d';
    }
    
    messageEl.textContent = message;
    messageEl.style.display = 'block';
    
    setTimeout(() => {
      messageEl.style.opacity = '1';
    }, 10);
    
    const displayTime = isStart ? 1500 : 2000;
    setTimeout(() => {
      messageEl.style.opacity = '0';
      setTimeout(() => {
        messageEl.style.display = 'none';
      }, 500);
    }, displayTime);
  }
}

// ===========================================================
// GAME ENGINE
// ===========================================================
let player = new Player();

function updateGame() {
  if (!GameState.isRunning || GameState.isPaused) return;
  
  GameState.frames++;
  GameState.score += 1;
  GameState.distance = Math.floor(GameState.frames / 10);
  
  const speedIncrement = Math.floor(GameState.score / 500) * 0.2;
  GameState.gameSpeed = Math.min(1 + speedIncrement, 3);
  
  BackgroundTransition.update();
  CameraShake.update();
  
  // DON'T SPAWN OBSTACLES DURING TRANSITIONS
  if (!GameState.isTransitioning) {
    const spawnInterval = Math.max(
      CONFIG.obstacle.spawnInterval.min,
      CONFIG.obstacle.spawnInterval.max - Math.floor(GameState.score / 100)
    );
    
    if (GameState.frames - GameState.lastObstacleFrame > spawnInterval) {
      GameState.obstacles.push(new Obstacle());
      GameState.lastObstacleFrame = GameState.frames;
    }
  }
  
  player.update();
  
  for (let i = GameState.obstacles.length - 1; i >= 0; i--) {
    const obstacle = GameState.obstacles[i];
    obstacle.update();
    
    if (obstacle.checkCollision(player) && player.isAlive) {
      player.kill();
      
      // Trigger camera shake
      CameraShake.triggerShake(15, 20);
      
      setTimeout(gameOver, 1000);
      return;
    }
    
    if (!obstacle.passed && obstacle.x + obstacle.width < player.x) {
      obstacle.passed = true;
      GameState.score += 10;
    }
    
    if (obstacle.isOffScreen()) {
      GameState.obstacles.splice(i, 1);
    }
  }
  
  updateStats();
}

function drawGame() {
  // Save the current canvas state
  ctx.save();
  
  // Apply camera shake
  CameraShake.apply();
  
  BackgroundRenderer.draw();
  GameState.obstacles.forEach(obstacle => obstacle.draw());
  player.draw();
  BackgroundRenderer.drawForeground();
  drawUI();
  
  // Restore canvas state
  ctx.restore();
}

function drawUI() {
  ctx.fillStyle = 'white';
  ctx.font = '16px Arial';
  ctx.textAlign = 'left';
  
  ctx.fillText(`Score: ${GameState.score}`, 20, 30);
  ctx.fillText(`Distance: ${GameState.distance}m`, 20, 55);
  ctx.fillText(`Speed: ${GameState.gameSpeed.toFixed(1)}x`, 20, 80);
  
  if (GameState.isTransitioning) {
    ctx.fillStyle = GameState.targetBgSet === 'pinkForest' ? 'rgba(255, 105, 180, 0.6)' : 'rgba(0, 255, 157, 0.6)';
    ctx.font = '14px Arial';
    ctx.textAlign = 'right';
    const progressPercent = Math.floor(GameState.transitionProgress * 100);
    ctx.fillText(`Transitioning... ${progressPercent}%`, CONFIG.canvas.width - 20, 30);
    ctx.textAlign = 'left';
  }
  
  if (GameState.isPaused) {
    ctx.fillStyle = 'rgba(0, 0, 0, 0.7)';
    ctx.fillRect(0, 0, CONFIG.canvas.width, CONFIG.canvas.height);
    
    ctx.fillStyle = 'white';
    ctx.font = '40px Arial';
    ctx.textAlign = 'center';
    ctx.fillText('PAUSED', CONFIG.canvas.width / 2, CONFIG.canvas.height / 2 - 20);
    ctx.font = '20px Arial';
    ctx.fillText('Press ESC to resume', CONFIG.canvas.width / 2, CONFIG.canvas.height / 2 + 20);
    ctx.textAlign = 'left';
  }
}

function gameLoop() {
  updateGame();
  drawGame();
  
  if (GameState.isRunning) {
    GameState.animationId = requestAnimationFrame(gameLoop);
  }
}

// ===========================================================
// GAME CONTROL FUNCTIONS
// ===========================================================
async function startGame() {
  if (!GameState.assetsLoaded) {
    try {
      await AssetLoader.load();
    } catch (error) {
      console.error('Failed to load assets:', error);
      alert('Failed to load game assets. Please refresh the page.');
      return;
    }
  }
  
  GameState.isRunning = true;
  GameState.isPaused = false;
  GameState.score = 0;
  GameState.distance = 0;
  GameState.gameSpeed = 1;
  GameState.frames = 0;
  GameState.lastObstacleFrame = 0;
  GameState.obstacles = [];
  GameState.bgPositions = [0, 0, 0, 0, 0];
  GameState.foregroundPositions = [0, 0, 0, 0, 0];
  GameState.isTransitioning = false;
  GameState.transitionProgress = 0;
  GameState.lastTransitionScore = 0;
  GameState.transitionCooldownFrames = 0;
  GameState.canTransition = true;
  GameState.currentBgSet = 'forest';
  GameState.targetBgSet = 'forest';
  CONFIG.background.currentSet = 'forest';
  CONFIG.background.currentSequenceIndex = 0;
  
  CONFIG.player.groundY = CONFIG.background.groundYOffset;
  CONFIG.obstacle.y = CONFIG.player.groundY - CONFIG.obstacle.height;
  
  player.reset();
  CameraShake.resetShake();
  
  if (GameState.animationId) {
    cancelAnimationFrame(GameState.animationId);
  }
  
  document.getElementById('gameStartScreen').style.display = 'none';
  document.getElementById('gameCanvas').style.display = 'block';
  document.getElementById('gameStats').style.display = 'block';
  
  gameLoop();
}

function gameOver() {
  GameState.isRunning = false;
  
  // Save score to database
  saveScoreToDatabase(GameState.score, GameState.distance, GameState.currentBgSet);
  
  document.getElementById('finalScore').textContent = GameState.score;
  document.getElementById('finalDistance').innerHTML = 
    `Distance: ${GameState.distance}m<br>Final Realm: ${GameState.currentBgSet === 'pinkForest' ? 'Pink Forest' : 'Forest'}`;
  
  setTimeout(() => {
    document.getElementById('gameCanvas').style.display = "none";
    document.getElementById("gameStats").style.display = "none";
    document.getElementById("gameOverScreen").style.display = "block";
  }, 500);
}

function saveScoreToDatabase(score, distance, realm) {
  const userId = <?php echo $user_id; ?>;
  
  fetch('save_score.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({
      user_id: userId,
      game_id: 1,
      score: score,
      distance: distance,
      realm: realm
    })
  })
  .then(response => response.text().then(text => {
    try {
      return JSON.parse(text);
    } catch {
      return { success: false, message: 'Invalid JSON: ' + text };
    }
  }))
  .then(data => {
    if (data.success) {
      document.getElementById('scoreSavedMessage').style.display = 'block';
    }
  })
  .catch(error => {
    console.error('❌ Fetch error:', error);
  });
}

function togglePause() {
  if (!GameState.isRunning) return;
  
  GameState.isPaused = !GameState.isPaused;
  
  if (!GameState.isPaused) {
    gameLoop();
  }
}

function updateStats() {
  document.getElementById("scoreValue").textContent = GameState.score;
  document.getElementById("distanceValue").textContent = GameState.distance + "m";
  document.getElementById("speedValue").textContent = GameState.gameSpeed.toFixed(1) + "x";
}

function restartGame() {
  document.getElementById("gameOverScreen").style.display = "none";
  document.getElementById("gameStartScreen").style.display = "block";
  
  if (GameState.animationId) {
    cancelAnimationFrame(GameState.animationId);
  }
  
  ctx.clearRect(0, 0, CONFIG.canvas.width, CONFIG.canvas.height);
}

// ===========================================================
// EVENT HANDLERS
// ===========================================================
document.addEventListener("keydown", (e) => {
  if (e.code === "Space" || e.code === "ArrowUp") {
    e.preventDefault();
    player.jump();
  }
  
  if (e.code === "Escape") {
    e.preventDefault();
    togglePause();
  }
});

canvas.addEventListener("click", () => player.jump());
canvas.addEventListener("touchstart", (e) => {
  e.preventDefault();
  player.jump();
});

// ===========================================================
// LOAD USER STATS
// ===========================================================
function loadUserStats() {
  const userId = <?php echo $user_id; ?>;
  
  if (userId > 0) {
    fetch('get_user_stats.php?user_id=' + userId)
      .then(response => response.text().then(text => {
        try {
          return JSON.parse(text);
        } catch {
          return { success: false, message: 'Invalid JSON: ' + text };
        }
      }))
      .then(data => {
        if (data.success) {
          const statsHtml = `
            <div class="row text-center">
              <div class="col-md-4">
                <div class="stat-label">High Score</div>
                <div class="stat-value">${data.high_score}</div>
              </div>
              <div class="col-md-4">
                <div class="stat-label">Games Played</div>
                <div class="stat-value">${data.games_played}</div>
              </div>
              <div class="col-md-4">
                <div class="stat-label">Rank</div>
                <div class="stat-value">#${data.rank}</div>
              </div>
            </div>
          `;
          document.getElementById('userStats').innerHTML = statsHtml;
        }
      })
      .catch(error => {
        console.error('Error loading stats:', error);
      });
  }
}

// ===========================================================
// INITIALIZATION
// ===========================================================
canvas.width = CONFIG.canvas.width;
canvas.height = CONFIG.canvas.height;

// Preload assets
AssetLoader.load().then(() => {
  console.log('Assets preloaded successfully!');
  loadUserStats();
}).catch(error => {
  console.error('Failed to preload assets:', error);
});
</script>

</body>
</html>