<?php
session_start();
include "admin_PDO.php";
include "../Hybrid_CRUD.php";
$hybrid = new admin_PDO();

if(!isset($_SESSION['USERNAME'])){
    header("location:../index.php");
}

if(isset($_POST['logout'])){
    $hybrid->logout();
}

// Fetch data for statistics
$fetch_users = $hybrid->show_users();
$rowCount = $fetch_users->rowCount();

// Fetch leaderboard data
$leaderboard_data = $hybrid->getLeaderboard();
$total_scores = is_array($leaderboard_data) ? count($leaderboard_data) : 0;
$highest_score = 0;
$unique_realms = [];

if(is_array($leaderboard_data) && count($leaderboard_data) > 0) {
    $highest_score = $leaderboard_data[0]['score']; // First item is highest since we order by score DESC
    foreach($leaderboard_data as $entry) {
        if(isset($entry['realm']) && !in_array($entry['realm'], $unique_realms)) {
            $unique_realms[] = $entry['realm'];
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="../../node_modules/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <title>Admin Dashboard</title>
    <style>
        :root {
            --primary: #4361ee;
            --secondary: #3a0ca3;
            --success: #4cc9f0;
            --danger: #f72585;
            --warning: #f8961e;
            --info: #7209b7;
            --light: #f8f9fa;
            --dark: #212529;
            --gray: #6c757d;
            --light-gray: #e9ecef;
        }
        
        * {
            font-family: 'Poppins', sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .dashboard-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            overflow: hidden;
            margin: 0 auto;
            max-width: 1400px;
        }
        
        .dashboard-header {
            background: linear-gradient(to right, var(--primary), var(--secondary));
            color: white;
            padding: 25px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }
        
        .header-left {
            flex: 1;
        }
        
        .welcome-text {
            font-size: 1.8rem;
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .user-badge {
            background: rgba(255,255,255,0.2);
            padding: 8px 20px;
            border-radius: 50px;
            font-weight: 500;
            display: inline-block;
        }
        
        .logout-btn {
            background: var(--danger);
            border: none;
            padding: 10px 25px;
            border-radius: 50px;
            color: white;
            font-weight: 500;
            transition: all 0.3s;
            white-space: nowrap;
        }
        
        .logout-btn:hover {
            background: #d1145a;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(247, 37, 133, 0.3);
        }
        
        /* Statistics Section */
        .statistics-section {
            padding: 30px;
            background: var(--light);
            border-bottom: 1px solid var(--light-gray);
        }
        
        .stats-title {
            font-size: 1.3rem;
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .stats-title i {
            color: var(--primary);
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }
        
        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            display: flex;
            align-items: center;
            gap: 20px;
            transition: all 0.3s;
            border-left: 5px solid var(--primary);
            cursor: pointer;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        
        .stat-card.users {
            border-left-color: var(--primary);
        }
        
        .stat-card.scores {
            border-left-color: var(--info);
        }
        
        .stat-card.high-score {
            border-left-color: var(--warning);
        }
        
        .stat-card.realms {
            border-left-color: var(--success);
        }
        
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            color: white;
        }
        
        .stat-icon.users { background: var(--primary); }
        .stat-icon.scores { background: var(--info); }
        .stat-icon.high-score { background: var(--warning); }
        .stat-icon.realms { background: var(--success); }
        
        .stat-info {
            flex: 1;
        }
        
        .stat-value {
            font-size: 2.2rem;
            font-weight: 700;
            margin: 0;
            color: var(--dark);
            line-height: 1;
        }
        
        .stat-label {
            margin: 5px 0 0;
            color: var(--gray);
            font-size: 0.9rem;
            font-weight: 500;
        }
        
        .stat-trend {
            font-size: 0.8rem;
            padding: 3px 8px;
            border-radius: 10px;
            margin-top: 5px;
            display: inline-block;
        }
        
        .trend-up {
            background: rgba(40, 167, 69, 0.1);
            color: #28a745;
        }
        
        .trend-down {
            background: rgba(220, 53, 69, 0.1);
            color: #dc3545;
        }
        
        /* Navigation */
        .dashboard-nav {
            background: white;
            padding: 20px 30px 0;
            border-bottom: 1px solid var(--light-gray);
        }
        
        .nav-tabs {
            border: none;
            gap: 10px;
        }
        
        .nav-tabs .nav-link {
            border: none;
            color: var(--gray);
            font-weight: 500;
            padding: 12px 25px;
            border-radius: 10px 10px 0 0;
            transition: all 0.3s;
            border-bottom: 3px solid transparent;
        }
        
        .nav-tabs .nav-link.active {
            background: white;
            color: var(--primary);
            border-bottom: 3px solid var(--primary);
            font-weight: 600;
        }
        
        .nav-tabs .nav-link:hover:not(.active) {
            background: rgba(67, 97, 238, 0.05);
            color: var(--primary);
        }
        
        .tab-content {
            padding: 30px;
        }
        
        .section-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid var(--primary);
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .section-title i {
            color: var(--primary);
        }
        
        .table-responsive {
            border-radius: 15px;
            overflow: hidden;
            border: 1px solid var(--light-gray);
        }
        
        .table {
            margin: 0;
        }
        
        .table thead th {
            background: var(--primary);
            color: white;
            border: none;
            padding: 18px 15px;
            font-weight: 500;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
        }
        
        .table tbody td {
            padding: 16px 15px;
            vertical-align: middle;
            border-color: var(--light-gray);
        }
        
        .table tbody tr {
            transition: all 0.3s;
        }
        
        .table tbody tr:hover {
            background: rgba(67, 97, 238, 0.05);
            transform: translateY(-2px);
            box-shadow: 0 3px 10px rgba(0,0,0,0.05);
        }
        
        .badge-realm {
            background: var(--info);
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .badge-realm.forest {
            background: #28a745;
        }
        
        .badge-realm.pinkforest {
            background: #f72585;
        }
        
        .btn-action {
            padding: 8px 15px;
            border-radius: 8px;
            font-weight: 500;
            font-size: 0.85rem;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: all 0.3s;
            border: none;
        }
        
        .btn-delete {
            background: var(--danger);
            color: white;
        }
        
        .btn-delete:hover {
            background: #d1145a;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(247, 37, 133, 0.3);
        }
        
        .btn-update {
            background: var(--warning);
            color: white;
        }
        
        .btn-update:hover {
            background: #e68a00;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(248, 150, 30, 0.3);
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--gray);
        }
        
        .empty-state i {
            font-size: 4rem;
            margin-bottom: 20px;
            color: var(--light-gray);
        }
        
        .empty-state h3 {
            font-weight: 600;
            margin-bottom: 10px;
            color: var(--dark);
        }
        
        .leaderboard-badge {
            width: 40px;
            height: 40px;
            background: gold;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: #333;
            font-size: 1.2rem;
            margin: 0 auto;
        }
        
        .leaderboard-badge.second {
            background: silver;
        }
        
        .leaderboard-badge.third {
            background: #cd7f32;
            color: white;
        }
        
        .quick-actions {
            margin-top: 30px;
            padding: 20px;
            background: var(--light);
            border-radius: 15px;
        }
        
        .action-buttons {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }
        
        .action-btn {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 20px;
            background: white;
            border: 2px solid var(--primary);
            border-radius: 10px;
            color: var(--primary);
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .action-btn:hover {
            background: var(--primary);
            color: white;
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(67, 97, 238, 0.3);
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .dashboard-header {
                flex-direction: column;
                text-align: center;
                padding: 20px;
            }
            
            .header-left {
                text-align: center;
            }
            
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .stat-card {
                flex-direction: column;
                text-align: center;
                padding: 20px;
            }
            
            .stat-info {
                text-align: center;
            }
            
            .nav-tabs {
                flex-direction: column;
            }
            
            .nav-tabs .nav-link {
                border-radius: 10px;
                text-align: center;
            }
        }
        
        @media (max-width: 576px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .action-buttons {
                flex-direction: column;
            }
            
            .action-btn {
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Header -->
        <div class="dashboard-header">
            <div class="header-left">
                <h1 class="welcome-text">Admin Dashboard</h1>
                <div class="user-badge">
                    <i class="fas fa-user-circle me-2"></i>
                    Welcome, <?php echo htmlspecialchars($_SESSION['FNAME']. " " .$_SESSION['LNAME']); ?>
                </div>
            </div>
            <form method="post">
                <button name="logout" class="logout-btn">
                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                </button>
            </form>
        </div>
        
        <!-- Statistics Section - NOW AT THE TOP -->
        <div class="statistics-section">
            <h3 class="stats-title">
                <i class="fas fa-chart-line"></i>Dashboard Overview
            </h3>
            
            <div class="stats-grid">
                <div class="stat-card users">
                    <div class="stat-icon users">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-info">
                        <h3 class="stat-value"><?php echo $rowCount; ?></h3>
                        <p class="stat-label">Total Users</p>
                        <span class="stat-trend trend-up">
                            <i class="fas fa-arrow-up me-1"></i>Active
                        </span>
                    </div>
                </div>
                
                <div class="stat-card scores">
                    <div class="stat-icon scores">
                        <i class="fas fa-gamepad"></i>
                    </div>
                    <div class="stat-info">
                        <h3 class="stat-value"><?php echo $total_scores; ?></h3>
                        <p class="stat-label">Games Played</p>
                        <span class="stat-trend trend-up">
                            <i class="fas fa-chart-line me-1"></i>Tracking
                        </span>
                    </div>
                </div>
                
                <div class="stat-card high-score">
                    <div class="stat-icon high-score">
                        <i class="fas fa-trophy"></i>
                    </div>
                    <div class="stat-info">
                        <h3 class="stat-value"><?php echo $highest_score; ?></h3>
                        <p class="stat-label">Highest Score</p>
                        <span class="stat-trend trend-up">
                            <i class="fas fa-star me-1"></i>Record
                        </span>
                    </div>
                </div>
                
                <div class="stat-card realms">
                    <div class="stat-icon realms">
                        <i class="fas fa-globe"></i>
                    </div>
                    <div class="stat-info">
                        <h3 class="stat-value"><?php echo count($unique_realms); ?></h3>
                        <p class="stat-label">Active Realms</p>
                        <span class="stat-trend trend-up">
                            <i class="fas fa-map me-1"></i>Available
                        </span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Navigation -->
        <div class="dashboard-nav">
            <ul class="nav nav-tabs" id="dashboardTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="users-tab" data-bs-toggle="tab" data-bs-target="#users" type="button" role="tab">
                        <i class="fas fa-users me-2"></i>Users Management
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="leaderboard-tab" data-bs-toggle="tab" data-bs-target="#leaderboard" type="button" role="tab">
                        <i class="fas fa-trophy me-2"></i>Leaderboards
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tools-tab" data-bs-toggle="tab" data-bs-target="#tools" type="button" role="tab">
                        <i class="fas fa-tools me-2"></i>Admin Tools
                    </button>
                </li>
            </ul>
        </div>
        
        <!-- Tab Content -->
        <div class="tab-content">
            <!-- Users Tab -->
            <div class="tab-pane fade show active" id="users" role="tabpanel">
                <div class="section-title">
                    <i class="fas fa-users"></i>Registered Users
                </div>
                
                <?php if($rowCount < 1) { ?>
                    <div class="empty-state">
                        <i class="fas fa-users-slash"></i>
                        <h3>No Registered Users</h3>
                        <p>There are currently no users registered in the system.</p>
                    </div>
                <?php } else { ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th>Username</th>
                                    <th>First Name</th>
                                    <th>Last Name</th>
                                    <th>Created On</th>
                                    <th>Updated On</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $counter = 1;
                                    // Reset pointer since we already fetched for stats
                                    $fetch_users = $hybrid->show_users();
                                    while ($row = $fetch_users->fetch(PDO::FETCH_ASSOC)) {
                                ?>
                                    <tr>
                                        <td class="text-center fw-bold"><?php echo $counter++; ?></td>
                                        <td>
                                            <i class="fas fa-user-circle me-2 text-primary"></i>
                                            <?php echo htmlspecialchars($row['username']); ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($row['firstname']); ?></td>
                                        <td><?php echo htmlspecialchars($row['lastname']); ?></td>
                                        <td>
                                            <i class="far fa-calendar-plus me-2 text-success"></i>
                                            <?php echo htmlspecialchars($row['created_at']); ?>
                                        </td>
                                        <td>
                                            <i class="far fa-calendar-check me-2 text-warning"></i>
                                            <?php echo htmlspecialchars($row['updated_at']); ?>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <a href="update.php?user_id=<?php echo $row['user_id']; ?>" class="btn btn-update">
                                                    <i class="fas fa-edit"></i> Update
                                                </a>
                                                <a href="delete.php?user_id=<?php echo $row['user_id']; ?>" class="btn btn-delete" 
                                                   onclick="return confirm('Are you sure you want to delete this user?');">
                                                    <i class="fas fa-trash"></i> Delete
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                <?php } ?>
                
            
            
            <!-- Leaderboards Tab -->
            <div class="tab-pane fade" id="leaderboard" role="tabpanel">
                <div class="section-title">
                    <br>
                    <i class="fas fa-trophy"></i>Game Leaderboards
                </div>
                
                <?php if(!$leaderboard_data || count($leaderboard_data) === 0) { ?>
                    <div class="empty-state">
                        <i class="fas fa-trophy"></i>
                        <h3>No Leaderboard Data</h3>
                        <p>No game scores have been recorded yet.</p>
                    </div>
                <?php } else { ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th class="text-center">Rank</th>
                                    <th>Player</th>
                                    <th class="text-center">Score</th>
                                    <th class="text-center">Distance</th>
                                    <th class="text-center">Realm</th>
                                    <th class="text-center">Played At</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $rank = 1;
                                    foreach($leaderboard_data as $entry) {
                                ?>
                                    <tr>
                                        <td class="text-center fw-bold">
                                            <?php if($rank == 1): ?>
                                                <div class="leaderboard-badge">🥇</div>
                                            <?php elseif($rank == 2): ?>
                                                <div class="leaderboard-badge second">🥈</div>
                                            <?php elseif($rank == 3): ?>
                                                <div class="leaderboard-badge third">🥉</div>
                                            <?php else: ?>
                                                <span class="badge bg-secondary rounded-pill px-3"><?php echo $rank; ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <i class="fas fa-gamepad me-2 text-info"></i>
                                            User #<?php echo htmlspecialchars($entry['user_id']); ?>
                                        </td>
                                        <td class="text-center fw-bold" style="font-size: 1.2rem;">
                                            <span class="badge bg-primary rounded-pill px-3 py-2">
                                                <?php echo htmlspecialchars($entry['score']); ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <i class="fas fa-running me-1"></i>
                                            <?php echo htmlspecialchars($entry['distance']); ?>m
                                        </td>
                                        <td class="text-center">
                                            <span class="badge-realm <?php echo strtolower(htmlspecialchars($entry['realm'])); ?>">
                                                <i class="fas fa-globe me-1"></i>
                                                <?php echo htmlspecialchars($entry['realm']); ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <i class="far fa-clock me-1 text-muted"></i>
                                            <?php echo date('M d, Y', strtotime($entry['played_at'])); ?>
                                        </td>
                                        <td class="text-center">
                                            <a href="delete_score.php?score_id=<?php echo $entry['score_id']; ?>" 
                                               class="btn btn-delete btn-sm"
                                               onclick="return confirm('Are you sure you want to delete this score?');">
                                                <i class="fas fa-trash"></i> Delete
                                            </a>
                                        </td>
                                    </tr>
                                <?php 
                                        $rank++;
                                    } 
                                ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Leaderboard Quick Actions -->
                    <div class="quick-actions mt-4">
                        <h5 class="mb-3"><i class="fas fa-cog me-2"></i>Leaderboard Management</h5>
                        <div class="action-buttons">
                            <a href="reset_leaderboard.php" class="action-btn" onclick="return confirm('Reset the entire leaderboard?');">
                                <i class="fas fa-redo"></i>
                                <span>Reset Leaderboard</span>
                            </a>
                            <a href="export_leaderboard.php" class="action-btn">
                                <i class="fas fa-file-csv"></i>
                                <span>Export to CSV</span>
                            </a>
                            <a href="filter_leaderboard.php" class="action-btn">
                                <i class="fas fa-filter"></i>
                                <span>Filter Results</span>
                            </a>
                        </div>
                    </div>
                <?php } ?>
            </div>
            
            <!-- Admin Tools Tab -->
            <div class="tab-pane fade" id="tools" role="tabpanel">
                <div class="section-title">
                    <i class="fas fa-tools"></i>Administration Tools
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <div class="card h-100 border-primary">
                            <div class="card-body">
                                <h5 class="card-title text-primary">
                                    <i class="fas fa-database me-2"></i>Database Management
                                </h5>
                                <p class="card-text">Perform database operations and maintenance.</p>
                                <div class="d-grid gap-2">
                                    <a href="backup.php" class="btn btn-outline-primary">
                                        <i class="fas fa-save me-2"></i>Backup Database
                                    </a>
                                    <a href="optimize.php" class="btn btn-outline-primary">
                                        <i class="fas fa-tachometer-alt me-2"></i>Optimize Tables
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-4">
                        <div class="card h-100 border-success">
                            <div class="card-body">
                                <h5 class="card-title text-success">
                                    <i class="fas fa-chart-bar me-2"></i>Analytics & Reports
                                </h5>
                                <p class="card-text">View detailed analytics and generate reports.</p>
                                <div class="d-grid gap-2">
                                    <a href="daily_report.php" class="btn btn-outline-success">
                                        <i class="fas fa-calendar-day me-2"></i>Daily Report
                                    </a>
                                    <a href="user_activity.php" class="btn btn-outline-success">
                                        <i class="fas fa-user-clock me-2"></i>User Activity
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-4">
                        <div class="card h-100 border-warning">
                            <div class="card-body">
                                <h5 class="card-title text-warning">
                                    <i class="fas fa-cogs me-2"></i>System Settings
                                </h5>
                                <p class="card-text">Configure system preferences and settings.</p>
                                <div class="d-grid gap-2">
                                    <a href="game_settings.php" class="btn btn-outline-warning">
                                        <i class="fas fa-gamepad me-2"></i>Game Settings
                                    </a>
                                    <a href="email_settings.php" class="btn btn-outline-warning">
                                        <i class="fas fa-envelope me-2"></i>Email Settings
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-4">
                        <div class="card h-100 border-danger">
                            <div class="card-body">
                                <h5 class="card-title text-danger">
                                    <i class="fas fa-shield-alt me-2"></i>Security
                                </h5>
                                <p class="card-text">Manage security settings and audit logs.</p>
                                <div class="d-grid gap-2">
                                    <a href="audit_logs.php" class="btn btn-outline-danger">
                                        <i class="fas fa-clipboard-list me-2"></i>View Audit Logs
                                    </a>
                                    <a href="security_settings.php" class="btn btn-outline-danger">
                                        <i class="fas fa-lock me-2"></i>Security Settings
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../../node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Tab persistence
            const hash = window.location.hash;
            const tabTrigger = document.querySelector(`[data-bs-target="${hash}"]`);
            
            if (tabTrigger) {
                new bootstrap.Tab(tabTrigger).show();
            }
            
            const tabTriggers = document.querySelectorAll('button[data-bs-toggle="tab"]');
            tabTriggers.forEach(trigger => {
                trigger.addEventListener('click', function() {
                    window.location.hash = this.getAttribute('data-bs-target');
                });
            });
            
            // Auto-refresh statistics cards every 30 seconds
            setInterval(function() {
                // You can implement AJAX refresh here if needed
                console.log('Statistics auto-refresh available');
            }, 30000);
            
            // Add click effects to stat cards
            document.querySelectorAll('.stat-card').forEach(card => {
                card.addEventListener('click', function() {
                    const tabToSwitch = this.classList.contains('users') ? '#users-tab' : 
                                      this.classList.contains('scores') ? '#leaderboard-tab' :
                                      '#tools-tab';
                    
                    const tabElement = document.querySelector(tabToSwitch);
                    if (tabElement) {
                        new bootstrap.Tab(tabElement).show();
                        window.location.hash = tabElement.getAttribute('data-bs-target');
                    }
                });
            });
            
            // Confirmation for all delete actions
            document.querySelectorAll('.btn-delete').forEach(button => {
                if (!button.hasAttribute('onclick')) {
                    button.addEventListener('click', function(e) {
                        if (!confirm('Are you sure you want to delete this item?')) {
                            e.preventDefault();
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>