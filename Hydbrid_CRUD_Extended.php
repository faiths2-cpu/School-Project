<?php
// Hybrid_CRUD_Extended.php
// This extends the original Hybrid_CRUD class

// Require the original class
require_once __DIR__ . '/Hybrid_CRUD.php';

class Hybrid_CRUD_Extended extends Hybrid_CRUD {
    
    // Override or add the missing method
    public function get_connection() {
        // First, try to call parent if it exists
        if (method_exists(parent::class, 'get_connection')) {
            return parent::get_connection();
        }
        
        // Try to find connection property
        if (property_exists($this, 'conn') && $this->conn instanceof PDO) {
            return $this->conn;
        }
        
        if (property_exists($this, 'connection') && $this->connection instanceof PDO) {
            return $this->connection;
        }
        
        if (property_exists($this, 'db') && $this->db instanceof PDO) {
            return $this->db;
        }
        
        if (property_exists($this, 'pdo') && $this->pdo instanceof PDO) {
            return $this->pdo;
        }
        
        // Last resort: check if there's a method to get connection
        if (method_exists($this, 'getConnection')) {
            return $this->getConnection();
        }
        
        if (method_exists($this, 'getConnectionToDatabase')) {
            return $this->getConnectionToDatabase();
        }
        
        // If all else fails, create a new connection
        try {
            // Update these with your database credentials
            $host = 'localhost';
            $dbname = 'database2';
            $username = 'root';
            $password = '';
            
            return new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", 
                          $username, $password);
        } catch (PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage());
            return null;
        }
    }
    
    // Method to get user by ID
    public function get_user_by_id($user_id) {
        try {
            $conn = $this->get_connection();
            if (!$conn) {
                return false;
            }
            
            $sql = "SELECT * FROM users WHERE user_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$user_id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error in get_user_by_id: " . $e->getMessage());
            return false;
        }
    }
    
    // Method to save game score
    public function save_game_score($user_id, $game_id, $score, $distance, $realm = 'forest') {
        try {
            $conn = $this->get_connection();
            if (!$conn) {
                return false;
            }
            
            $sql = "INSERT INTO game_scores (user_id, game_id, score, distance, realm, played_at) 
                    VALUES (?, ?, ?, ?, ?, NOW())";
            $stmt = $conn->prepare($sql);
            return $stmt->execute([$user_id, $game_id, $score, $distance, $realm]);
        } catch (Exception $e) {
            error_log("Error in save_game_score: " . $e->getMessage());
            return false;
        }
    }
    
    // Method to get user game stats
    public function get_user_game_stats($user_id, $game_id) {
        try {
            $conn = $this->get_connection();
            if (!$conn) {
                return ['high_score' => 0, 'games_played' => 0, 'rank' => 1];
            }
            
            // Get stats
            $sql = "SELECT COALESCE(MAX(score), 0) as high_score, 
                           COALESCE(COUNT(*), 0) as games_played 
                    FROM game_scores 
                    WHERE user_id = ? AND game_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$user_id, $game_id]);
            $stats = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$stats) {
                $stats = ['high_score' => 0, 'games_played' => 0];
            }
            
            // Get rank
            $sql2 = "SELECT COUNT(DISTINCT user_id) + 1 as rank 
                     FROM game_scores 
                     WHERE game_id = ? AND score > ?";
            $stmt2 = $conn->prepare($sql2);
            $stmt2->execute([$game_id, $stats['high_score']]);
            $rank = $stmt2->fetch(PDO::FETCH_ASSOC);
            
            $stats['rank'] = $rank['rank'] ?? 1;
            
            return $stats;
        } catch (Exception $e) {
            error_log("Error in get_user_game_stats: " . $e->getMessage());
            return ['high_score' => 0, 'games_played' => 0, 'rank' => 1];
        }
    }
}
?>