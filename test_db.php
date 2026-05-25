<?php
session_start();
require_once 'Hybrid_CRUD.php';

echo "<h1>Database Test</h1>";

try {
    $crud = new Hybrid_CRUD();
    
    echo "<h2>1. Testing Database Connection</h2>";
    $conn = $crud->get_connection();
    echo "✓ Database connection successful<br>";
    
    echo "<h2>2. Checking Tables</h2>";
    
    // Check users table
    $stmt = $conn->query("SHOW TABLES LIKE 'users'");
    $usersTable = $stmt->fetch();
    echo $usersTable ? "✓ Users table exists<br>" : "✗ Users table NOT FOUND<br>";
    
    // Check game_scores table
    $stmt = $conn->query("SHOW TABLES LIKE 'game_scores'");
    $scoresTable = $stmt->fetch();
    
    if ($scoresTable) {
        echo "✓ Game scores table exists<br>";
        
        // Show table structure
        $stmt = $conn->query("DESCRIBE game_scores");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<h3>Game Scores Table Structure:</h3>";
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
        foreach ($columns as $col) {
            echo "<tr>";
            echo "<td>{$col['Field']}</td>";
            echo "<td>{$col['Type']}</td>";
            echo "<td>{$col['Null']}</td>";
            echo "<td>{$col['Key']}</td>";
            echo "<td>{$col['Default']}</td>";
            echo "<td>{$col['Extra']}</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Show some sample data
        $stmt = $conn->query("SELECT COUNT(*) as count FROM game_scores");
        $count = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "<br>Total scores in database: " . $count['count'] . "<br>";
        
    } else {
        echo "✗ Game scores table NOT FOUND<br>";
        
        echo "<h3>Creating game_scores table...</h3>";
        $sql = "CREATE TABLE IF NOT EXISTS game_scores (
            score_id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            game_id INT NOT NULL,
            score INT NOT NULL,
            distance INT NOT NULL,
            realm VARCHAR(50) DEFAULT 'forest',
            played_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_user_game (user_id, game_id),
            INDEX idx_score (score DESC),
            INDEX idx_played_at (played_at DESC)
        )";
        
        if ($conn->exec($sql) !== false) {
            echo "✓ Game scores table created successfully!<br>";
        } else {
            echo "✗ Failed to create game_scores table<br>";
        }
    }
    
    echo "<h2>3. Testing Current User</h2>";
    if (isset($_SESSION['USERNAME'])) {
        $username = $_SESSION['USERNAME'];
        echo "Logged in as: " . htmlspecialchars($username) . "<br>";
        
        $user = $crud->get_user_profile($username);
        if ($user) {
            echo "User ID: " . $user['user_id'] . "<br>";
            echo "Username: " . $user['username'] . "<br>";
            echo "Email: " . $user['email'] . "<br>";
        } else {
            echo "✗ Could not find user in database<br>";
        }
    } else {
        echo "✗ Not logged in<br>";
    }
    
    echo "<h2>4. Testing Score Saving</h2>";
    if (isset($_SESSION['USERNAME']) && $user) {
        $testScore = 100;
        $testDistance = 50;
        
        // Check if method exists
        if (method_exists($crud, 'save_game_score')) {
            echo "✓ save_game_score method exists<br>";
            
            $result = $crud->save_game_score($user['user_id'], 1, $testScore, $testDistance, 'test');
            echo $result ? "✓ Test score saved successfully!<br>" : "✗ Failed to save test score<br>";
        } else {
            echo "✗ save_game_score method NOT FOUND in Hybrid_CRUD<br>";
        }
    } else {
        echo "✗ Cannot test - not logged in or user not found<br>";
    }
    
} catch (Exception $e) {
    echo "<h2>Error:</h2>";
    echo "Message: " . $e->getMessage() . "<br>";
    echo "File: " . $e->getFile() . "<br>";
    echo "Line: " . $e->getLine() . "<br>";
}
?>