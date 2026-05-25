<?php
session_start();
include "Hybrid_CRUD.php";
$hybrid = new Hybrid_CRUD();

// Get selected game from URL or default to first game
$selected_game_id = isset($_GET['game_id']) ? (int)$_GET['game_id'] : 1;
$search_term = isset($_GET['search']) ? trim($_GET['search']) : '';

// Get all games for dropdown
$games = $hybrid->get_all_games();

// Get game details
$selected_game = $hybrid->get_game_by_id($selected_game_id);

// Get leaderboard data
if(!empty($search_term)) {
    $leaderboard_data = $hybrid->search_leaderboard($selected_game_id, $search_term);
} else {
    $leaderboard_data = $hybrid->get_game_leaderboard($selected_game_id, 100);
}

// Get overall leaderboard
$overall_leaderboard = $hybrid->get_overall_leaderboard(10);

// Get top players
$top_players = $hybrid->get_top_players($selected_game_id, 5);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leaderboard - Pixel Dash</title>
    <link href="node_modules/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/style.css">
    <link rel="stylesheet" href="assets/header.css">
    
    <style>
        .leaderboard-container {
            padding: 40px 0;
        }
        
        .leaderboard-header {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            padding: 30px;
            margin-bottom: 30px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }
        
        .leaderboard-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            padding: 25px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            margin-bottom: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }
        
        .rank-badge {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.1rem;
            margin-right: 15px;
        }
        
        .rank-1 {
            background: linear-gradient(135deg, #FFD700, #FFA500);
            color: #000;
            box-shadow: 0 0 20px rgba(255, 215, 0, 0.5);
        }
        
        .rank-2 {
            background: linear-gradient(135deg, #C0C0C0, #A0A0A0);
            color: #000;
            box-shadow: 0 0 20px rgba(192, 192, 192, 0.5);
        }
        
        .rank-3 {
            background: linear-gradient(135deg, #CD7F32, #A0522D);
            color: #fff;
            box-shadow: 0 0 20px rgba(205, 127, 50, 0.5);
        }
        
        .rank-other {
            background: rgba(255, 255, 255, 0.1);
            color: var(--light);
            border: 2px solid rgba(255, 255, 255, 0.2);
        }
        
        .player-row {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            padding: 15px 20px;
            margin-bottom: 10px;
            transition: all 0.3s ease;
            border: 1px solid transparent;
        }
        
        .player-row:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: translateX(5px);
            border-color: rgba(0, 255, 157, 0.3);
        }
        
        .current-user-row {
            background: rgba(0, 255, 157, 0.1);
            border: 2px solid var(--accent);
            box-shadow: 0 0 15px rgba(0, 255, 157, 0.2);
        }
        
        .player-avatar {
            width: 45px;
            height: 45px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            color: white;
            margin-right: 15px;
            border: 2px solid rgba(255, 255, 255, 0.3);
        }
        
        .score-badge {
            background: rgba(0, 255, 157, 0.2);
            color: var(--accent);
            padding: 6px 15px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.9rem;
            border: 1px solid rgba(0, 255, 157, 0.4);
        }
        
        .game-selector {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: var(--light);
            border-radius: 10px;
            padding: 10px 15px;
            width: 100%;
            transition: all 0.3s ease;
        }
        
        .game-selector:focus {
            background: rgba(255, 255, 255, 0.15);
            border-color: var(--accent);
            color: var(--light);
            box-shadow: 0 0 0 3px rgba(0, 255, 157, 0.1);
        }
        
        .search-box {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: var(--light);
            border-radius: 10px;
            padding: 10px 15px;
            transition: all 0.3s ease;
        }
        
        .search-box:focus {
            background: rgba(255, 255, 255, 0.15);
            border-color: var(--accent);
            color: var(--light);
            box-shadow: 0 0 0 3px rgba(0, 255, 157, 0.1);
        }
        
        .search-btn {
            background: linear-gradient(to right, var(--primary), var(--secondary));
            border: none;
            border-radius: 10px;
            padding: 10px 20px;
            color: white;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .search-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        
        .top-player-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }
        
        .top-player-card:hover {
            transform: translateY(-5px);
            border-color: var(--accent);
        }
        
        .top-player-avatar {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            color: white;
            margin: 0 auto 15px;
            border: 3px solid rgba(255, 255, 255, 0.3);
        }
        
        .section-title {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 25px;
            color: var(--light);
            border-bottom: 2px solid rgba(0, 255, 157, 0.3);
            padding-bottom: 10px;
        }
        
        .stat-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.2);
            margin-bottom: 20px;
        }
        
        .stat-value {
            font-size: 2rem;
            font-weight: 800;
            color: var(--accent);
            margin-bottom: 5px;
        }
        
        .stat-label {
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .empty-state {
            text-align: center;
            padding: 50px 20px;
            color: rgba(255, 255, 255, 0.6);
        }
        
        .empty-icon {
            font-size: 4rem;
            margin-bottom: 20px;
            color: rgba(255, 255, 255, 0.2);
        }
        
        @media (max-width: 768px) {
            .leaderboard-container {
                padding: 20px 0;
            }
            
            .leaderboard-header {
                padding: 20px;
            }
            
            .player-avatar {
                width: 40px;
                height: 40px;
                font-size: 1rem;
            }
            
            .rank-badge {
                width: 35px;
                height: 35px;
                font-size: 1rem;
                margin-right: 10px;
            }
        }
        
        .game-info {
            background: rgba(0, 0, 0, 0.2);
            border-radius: 10px;
            padding: 15px;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <!-- Include the header -->
    <?php include 'includes/header.php'; ?>

    <div class="leaderboard-container">
        <div class="container">
            <!-- Leaderboard Header -->
            <div class="leaderboard-header">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h1 class="hero-title mb-2">LEADERBOARD</h1>
                        <p class="hero-subtitle mb-0">Compete with players worldwide. Climb the ranks and prove you're the best!</p>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex justify-content-end">
                            <div class="stat-card" style="width: 200px;">
                                <div class="stat-value"><?php echo count($leaderboard_data); ?></div>
                                <div class="stat-label">Total Players</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Controls Section -->
            <div class="row mb-4">
                <div class="col-lg-8">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-light mb-2"><i class="fas fa-gamepad me-2"></i>Select Game</label>
                            <select class="game-selector" onchange="window.location.href='?game_id=' + this.value">
                                <?php foreach($games as $game): ?>
                                    <option value="<?php echo $game['game_id']; ?>" <?php echo $selected_game_id == $game['game_id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($game['game_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <form method="GET" action="">
                                <input type="hidden" name="game_id" value="<?php echo $selected_game_id; ?>">
                                <label class="form-label text-light mb-2"><i class="fas fa-search me-2"></i>Search Players</label>
                                <div class="input-group">
                                    <input type="text" class="search-box" name="search" value="<?php echo htmlspecialchars($search_term); ?>" placeholder="Search by username or name...">
                                    <button type="submit" class="search-btn">
                                        <i class="fas fa-search"></i>
                                    </button>
                                    <?php if(!empty($search_term)): ?>
                                        <a href="?game_id=<?php echo $selected_game_id; ?>" class="btn btn-outline-light ms-2">
                                            <i class="fas fa-times"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="game-info">
                        <h5 class="text-light mb-2">
                            <i class="fas fa-info-circle me-2"></i>
                            <?php echo htmlspecialchars($selected_game['game_name'] ?? 'Game Information'); ?>
                        </h5>
                        <p class="mb-0 text-light" style="opacity: 0.8; font-size: 0.9rem;">
                            <?php echo htmlspecialchars($selected_game['game_desc'] ?? 'No description available.'); ?>
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="row">
                <!-- Leaderboard Column -->
                <div class="col-lg-8">
                    <div class="leaderboard-card">
                        <h3 class="section-title">
                            <i class="fas fa-trophy me-2"></i>
                            Top Players - <?php echo htmlspecialchars($selected_game['game_name'] ?? 'Game'); ?>
                        </h3>
                        
                        <?php if(empty($leaderboard_data)): ?>
                            <div class="empty-state">
                                <div class="empty-icon">
                                    <i class="fas fa-trophy"></i>
                                </div>
                                <h4 class="mb-3">No Players Yet</h4>
                                <p class="mb-4">Be the first to play and claim the top spot!</p>
                                <?php if(isset($_SESSION['USERNAME'])): ?>
                                    <a href="play.php" class="btn btn-primary">
                                        <i class="fas fa-play me-2"></i>Start Playing
                                    </a>
                                <?php else: ?>
                                    <a href="login.php" class="btn btn-primary">
                                        <i class="fas fa-sign-in-alt me-2"></i>Login to Play
                                    </a>
                                <?php endif; ?>
                            </div>
                        <?php else: ?>
                            <!-- Top 3 Players -->
                            <?php if(count($leaderboard_data) >= 3): ?>
                                <div class="row mb-4">
                                    <div class="col-md-4">
                                        <div class="top-player-card">
                                            <div class="rank-badge rank-2 mx-auto mb-3">2</div>
                                            <div class="top-player-avatar">
                                                <?php echo substr($leaderboard_data[1]['username'] ?? '', 0, 1); ?>
                                            </div>
                                            <h5 class="mb-1"><?php echo htmlspecialchars($leaderboard_data[1]['username'] ?? ''); ?></h5>
                                            <p class="text-muted mb-2" style="font-size: 0.9rem;"><?php echo htmlspecialchars($leaderboard_data[1]['full_name'] ?? ''); ?></p>
                                            <div class="score-badge d-inline-block">
                                                <?php echo number_format($leaderboard_data[1]['score'] ?? 0); ?> pts
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="top-player-card" style="transform: translateY(-10px);">
                                            <div class="rank-badge rank-1 mx-auto mb-3">1</div>
                                            <div class="top-player-avatar" style="border-color: #FFD700;">
                                                <?php echo substr($leaderboard_data[0]['username'] ?? '', 0, 1); ?>
                                            </div>
                                            <h5 class="mb-1"><?php echo htmlspecialchars($leaderboard_data[0]['username'] ?? ''); ?></h5>
                                            <p class="text-muted mb-2" style="font-size: 0.9rem;"><?php echo htmlspecialchars($leaderboard_data[0]['full_name'] ?? ''); ?></p>
                                            <div class="score-badge d-inline-block" style="background: rgba(255, 215, 0, 0.2); border-color: #FFD700; color: #FFD700;">
                                                <?php echo number_format($leaderboard_data[0]['score'] ?? 0); ?> pts
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="top-player-card">
                                            <div class="rank-badge rank-3 mx-auto mb-3">3</div>
                                            <div class="top-player-avatar">
                                                <?php echo substr($leaderboard_data[2]['username'] ?? '', 0, 1); ?>
                                            </div>
                                            <h5 class="mb-1"><?php echo htmlspecialchars($leaderboard_data[2]['username'] ?? ''); ?></h5>
                                            <p class="text-muted mb-2" style="font-size: 0.9rem;"><?php echo htmlspecialchars($leaderboard_data[2]['full_name'] ?? ''); ?></p>
                                            <div class="score-badge d-inline-block">
                                                <?php echo number_format($leaderboard_data[2]['score'] ?? 0); ?> pts
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Full Leaderboard List -->
                            <div class="mt-4">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="text-light mb-0">Full Ranking</h5>
                                    <span class="text-muted" style="font-size: 0.9rem;">
                                        Showing <?php echo count($leaderboard_data); ?> players
                                    </span>
                                </div>
                                
                                <?php 
                                $current_username = $_SESSION['USERNAME'] ?? '';
                                $rank_counter = 0;
                                
                                foreach($leaderboard_data as $index => $player): 
                                    $rank_counter++;
                                    $is_current_user = ($player['username'] === $current_username);
                                ?>
                                    <div class="player-row d-flex align-items-center <?php echo $is_current_user ? 'current-user-row' : ''; ?>">
                                        <div class="rank-badge <?php echo $rank_counter <= 3 ? 'rank-' . $rank_counter : 'rank-other'; ?>">
                                            <?php echo $rank_counter; ?>
                                        </div>
                                        <div class="player-avatar">
                                            <?php echo strtoupper(substr($player['username'], 0, 1)); ?>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">
                                                <?php echo htmlspecialchars($player['username']); ?>
                                                <?php if($is_current_user): ?>
                                                    <span class="badge bg-primary ms-2" style="background: var(--accent)!important;">You</span>
                                                <?php endif; ?>
                                            </h6>
                                            <p class="mb-0 text-muted" style="font-size: 0.85rem;">
                                                <?php echo htmlspecialchars($player['full_name']); ?>
                                            </p>
                                        </div>
                                        <div class="score-badge">
                                            <?php echo number_format($player['score']); ?> pts
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Sidebar Column -->
                <div class="col-lg-4">
                    <!-- Overall Ranking -->
                    <div class="leaderboard-card mb-4">
                        <h5 class="section-title" style="font-size: 1.4rem;">
                            <i class="fas fa-globe me-2"></i>Overall Ranking
                        </h5>
                        
                        <?php if(empty($overall_leaderboard)): ?>
                            <p class="text-muted mb-0">No overall data available.</p>
                        <?php else: ?>
                            <?php 
                            $overall_counter = 0;
                            foreach($overall_leaderboard as $player): 
                                $overall_counter++;
                                if($overall_counter > 5) break;
                            ?>
                                <div class="player-row d-flex align-items-center mb-2" style="padding: 10px 15px;">
                                    <div class="rank-badge rank-other" style="width: 35px; height: 35px; font-size: 0.9rem;">
                                        <?php echo $overall_counter; ?>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-0" style="font-size: 0.95rem;"><?php echo htmlspecialchars($player['username']); ?></h6>
                                    </div>
                                    <div class="score-badge" style="font-size: 0.8rem; padding: 4px 10px;">
                                        <?php echo number_format($player['total_score'] ?? 0); ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Quick Stats -->
                    <div class="leaderboard-card mb-4">
                        <h5 class="section-title" style="font-size: 1.4rem;">
                            <i class="fas fa-chart-line me-2"></i>Game Stats
                        </h5>
                        <div class="row">
                            <div class="col-6 mb-3">
                                <div class="stat-card" style="padding: 15px;">
                                    <div class="stat-value" style="font-size: 1.5rem;"><?php echo count($leaderboard_data); ?></div>
                                    <div class="stat-label">Players</div>
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="stat-card" style="padding: 15px;">
                                    <div class="stat-value" style="font-size: 1.5rem;">
                                        <?php 
                                        $top_score = !empty($leaderboard_data) ? max(array_column($leaderboard_data, 'score')) : 0;
                                        echo number_format($top_score);
                                        ?>
                                    </div>
                                    <div class="stat-label">Top Score</div>
                                </div>
                            </div>
                        </div>
                        <div class="text-center">
                            <a href="play.php" class="btn btn-primary w-100" style="background: linear-gradient(to right, var(--primary), var(--secondary)); border: none;">
                                <i class="fas fa-play me-2"></i>Play Now
                            </a>
                        </div>
                    </div>
                    
                    <!-- How to Climb -->
                    <div class="leaderboard-card">
                        <h5 class="section-title" style="font-size: 1.4rem;">
                            <i class="fas fa-medal me-2"></i>How to Climb
                        </h5>
                        <ul class="list-unstyled" style="color: rgba(255, 255, 255, 0.8);">
                            <li class="mb-3">
                                <i class="fas fa-check-circle me-2" style="color: var(--accent);"></i>
                                Play daily to improve your skills
                            </li>
                            <li class="mb-3">
                                <i class="fas fa-check-circle me-2" style="color: var(--accent);"></i>
                                Focus on accuracy over speed
                            </li>
                            <li class="mb-3">
                                <i class="fas fa-check-circle me-2" style="color: var(--accent);"></i>
                                Learn from top players' strategies
                            </li>
                            <li class="mb-3">
                                <i class="fas fa-check-circle me-2" style="color: var(--accent);"></i>
                                Complete challenges for bonus points
                            </li>
                        </ul>
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
        // Auto-refresh leaderboard every 30 seconds
        setInterval(function() {
            if(!document.hidden) {
                window.location.reload();
            }
        }, 30000);
    </script>
</body>
</html>