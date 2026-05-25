<?php
class Hybrid_CRUD{
	private $hybrid;

	function __construct(){
		$host = "localhost";
		$user = "root";
		$pass = "";
		$dbname = "clickplay";
		$charset = "utf8mb4";

		try{
			$dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
			$options = [
			PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
			PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
			PDO::ATTR_EMULATE_PREPARES => false
			];
			$connection = new PDO($dsn, $user, $pass, $options);
			$this->hybrid = $connection;
		}
		catch(PDOException $e){
			die("connection failed" . $e->getMessage());
		}		
	}


    public function login($username, $password){
        session_start();

        $login = $this->hybrid->prepare("SELECT * FROM user WHERE username = :username");
        $login->bindParam(':username', $username);
        $login->execute();

        $row = $login->rowCount();

        if($row < 1){
            echo "
                <script>
                    alert('Invalid username or password...');
                    window.location.href = 'index.php';
                </script>
            ";
            return false;
        } else {
            $user = $login->fetch(PDO::FETCH_ASSOC);
            
            if(password_verify($password, $user['password'])){
                if($user['Role'] == 'Admin'){
                    $_SESSION['USERNAME'] = $user['username'];
                    $_SESSION['FNAME'] = $user['firstname'];
                    $_SESSION['LNAME'] = $user['lastname'];
                    $_SESSION['USER_ID'] = $user['user_id'];

                    header("location:admin/index.php");
                    exit();
                }
                else{
                    $_SESSION['USERNAME'] = $user['username'];
                    $_SESSION['FNAME'] = $user['firstname'];						
                    $_SESSION['LNAME'] = $user['lastname'];
                    $_SESSION['USER_ID'] = $user['user_id'];

                    header("location:index.php");
                    exit();
                }
            } else {
                echo "
                    <script>
                        alert('Invalid username or password...');
                        window.location.href = 'index.php';
                    </script>
                ";
                return false;
            }
        }
    }

    /**
     * CREATE USER WITH PASSWORD_HASH
     */
    public function create_user($username, $password, $firstname, $lastname){
        // Hash the password before storing
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $insert = $this->hybrid->prepare("INSERT INTO user(username, password, firstname, lastname, Role) VALUES(:username, :password, :firstname, :lastname, :Role)");
        $insert->execute([
            ':username' => $username,
            ':password' => $hashed_password,
            ':firstname' => $firstname,
            ':lastname' => $lastname,
            ':Role' => 'User'
        ]);
        
        // Get the newly created user ID
        $user_id = $this->hybrid->lastInsertId();
        
        // Start session and log in automatically
        session_start();
        $_SESSION['USERNAME'] = $username;
        $_SESSION['FNAME'] = $firstname;
        $_SESSION['LNAME'] = $lastname;
        $_SESSION['USER_ID'] = $user_id;
        
        header("location:index.php");
        exit();
    }

    /**
     * UPDATE USER PROFILE WITH PASSWORD CHANGE
     */
    public function update_user_profile($username, $firstname, $lastname) {
    try {
        $stmt = $this->hybrid->prepare("UPDATE user SET firstname = :firstname, lastname = :lastname, updated_at = NOW() WHERE username = :username");
        $stmt->execute([
            ':firstname' => $firstname,
            ':lastname' => $lastname,
            ':username' => $username
        ]);
        return $stmt->rowCount() > 0;
    } catch(PDOException $e) {
        error_log("Error updating user profile: " . $e->getMessage());
        return false;
    }
}

/**
 * Update user profile with password change
 */
public function update_user_profile_with_password($username, $firstname, $lastname, $new_password) {
    try {
        // Hash the new password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        
        $stmt = $this->hybrid->prepare("UPDATE user SET firstname = :firstname, lastname = :lastname, password = :password, updated_at = NOW() WHERE username = :username");
        $stmt->execute([
            ':firstname' => $firstname,
            ':lastname' => $lastname,
            ':password' => $hashed_password,
            ':username' => $username
        ]);
        return $stmt->rowCount() > 0;
    } catch(PDOException $e) {
        error_log("Error updating user profile with password: " . $e->getMessage());
        return false;
    }
}
    /**
     * VERIFY CURRENT PASSWORD (for password changes)
     */
    public function verify_current_password($username, $password) {
        try {
            $stmt = $this->hybrid->prepare("SELECT password FROM user WHERE username = :username");
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            $user = $stmt->fetch();
            
            if($user && password_verify($password, $user['password'])) {
                return true;
            }
            return false;
        } catch(PDOException $e) {
            error_log("Error verifying password: " . $e->getMessage());
            return false;
        }
    }

    /**
     * CHANGE PASSWORD ONLY
     */
    public function change_password($username, $new_password) {
        try {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            
            $stmt = $this->hybrid->prepare("UPDATE user SET password = :password, updated_at = NOW() WHERE username = :username");
            $stmt->execute([
                ':password' => $hashed_password,
                ':username' => $username
            ]);
            return $stmt->rowCount() > 0;
        } catch(PDOException $e) {
            error_log("Error changing password: " . $e->getMessage());
            return false;
        }
    }

    /**
     * CHECK IF PASSWORD NEEDS REHASHING (for future updates)
     */
    public function needs_rehash($username) {
        try {
            $stmt = $this->hybrid->prepare("SELECT password FROM user WHERE username = :username");
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            $user = $stmt->fetch();
            
            if($user && password_needs_rehash($user['password'], PASSWORD_DEFAULT)) {
                return true;
            }
            return false;
        } catch(PDOException $e) {
            error_log("Error checking rehash: " . $e->getMessage());
            return false;
        }
    }

    /**
     * RESET USER PASSWORD (admin function)
     */
    public function reset_user_password($user_id, $new_password) {
        try {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            
            $stmt = $this->hybrid->prepare("UPDATE user SET password = :password, updated_at = NOW() WHERE user_id = :user_id");
            $stmt->execute([
                ':password' => $hashed_password,
                ':user_id' => $user_id
            ]);
            return $stmt->rowCount() > 0;
        } catch(PDOException $e) {
            error_log("Error resetting password: " . $e->getMessage());
            return false;
        }
    }

		//logout
		public function logout(){
			session_destroy();
			header("location:index.php");
		}

	
		 // ===== NEW PROFILE FUNCTIONS =====

    /**
     * Get user profile by username
     */
    public function get_user_profile($username) {
        try {
            $stmt = $this->hybrid->prepare("SELECT * FROM user WHERE username = :username");
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            return $stmt->fetch();
        } catch(PDOException $e) {
            error_log("Error getting user profile: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update user profile (name only)
     */
   

    /**
     * Update user profile with password change
     */
 

    /**
     * Verify current password
     */
    

    /**
     * Get user's game statistics (if you have a games table)
     * This is a template function - modify based on your actual database structure
     */
    public function get_user_game_stats($user_id) {
        try {
            // Example query - adjust based on your actual tables
            $stmt = $this->hybrid->prepare("
                SELECT 
                    COUNT(*) as total_games,
                    SUM(score) as total_score,
                    MAX(score) as highest_score,
                    MIN(score) as lowest_score,
                    AVG(score) as average_score
                FROM games 
                WHERE user_id = :user_id
            ");
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
            return $stmt->fetch();
        } catch(PDOException $e) {
            error_log("Error getting game stats: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Check if username exists (for validation)
     */
    public function username_exists($username) {
        try {
            $stmt = $this->hybrid->prepare("SELECT COUNT(*) as count FROM user WHERE username = :username");
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            $result = $stmt->fetch();
            return $result['count'] > 0;
        } catch(PDOException $e) {
            error_log("Error checking username: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get user's recent activities (if you have an activities table)
     * This is a template function - modify based on your actual database structure
     */
    public function get_user_recent_activities($user_id, $limit = 10) {
        try {
            $stmt = $this->hybrid->prepare("
                SELECT * FROM user_activities 
                WHERE user_id = :user_id 
                ORDER BY created_at DESC 
                LIMIT :limit
            ");
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch(PDOException $e) {
            error_log("Error getting user activities: " . $e->getMessage());
            return [];
        }
    }
	  // ===== LEADERBOARD FUNCTIONS =====

    /**
     * Get all games for dropdown
     */
    public function get_all_games() {
        try {
            $stmt = $this->hybrid->prepare("SELECT * FROM games ORDER BY game_name");
            $stmt->execute();
            return $stmt->fetchAll();
        } catch(PDOException $e) {
            error_log("Error getting games: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get leaderboard rankings for a specific game
     */
   

    /**
     * Get overall leaderboard across all games
     */
    

    /**
     * Get user's position in leaderboard for a specific game
     */
    public function get_user_game_rank($user_id, $game_id) {
        try {
            $stmt = $this->hybrid->prepare("
                SELECT 
                    position,
                    score
                FROM (
                    SELECT 
                        user_id,
                        score,
                        ROW_NUMBER() OVER (ORDER BY score DESC) as position
                    FROM leaderboards
                    WHERE game_id = :game_id
                ) ranked
                WHERE user_id = :user_id
            ");
            $stmt->bindParam(':game_id', $game_id);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
            return $stmt->fetch();
        } catch(PDOException $e) {
            error_log("Error getting user rank: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get recent games/scores for a user
     */
    public function get_user_recent_scores($user_id, $limit = 10) {
        try {
            $stmt = $this->hybrid->prepare("
                SELECT 
                    l.*,
                    g.game_name,
                    g.game_desc
                FROM leaderboards l
                JOIN games g ON l.game_id = g.game_id
                WHERE l.user_id = :user_id
                ORDER BY l.leaderboards_id DESC
                LIMIT :limit
            ");
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch(PDOException $e) {
            error_log("Error getting user recent scores: " . $e->getMessage());
            return [];
        }
    }

 
    

    /**
     * Update leaderboard ranks for a game (re-rank based on scores)
     */
    private function update_leaderboard_ranks($game_id) {
        try {
            $stmt = $this->hybrid->prepare("
                UPDATE leaderboards l
                JOIN (
                    SELECT 
                        leaderboards_id,
                        ROW_NUMBER() OVER (ORDER BY score DESC, leaderboards_id ASC) as new_rank
                    FROM leaderboards
                    WHERE game_id = :game_id
                ) ranked ON l.leaderboards_id = ranked.leaderboards_id
                SET l.rank = ranked.new_rank
                WHERE l.game_id = :game_id
            ");
            $stmt->bindParam(':game_id', $game_id);
            $stmt->execute();
            return true;
        } catch(PDOException $e) {
            error_log("Error updating ranks: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get game details by ID
     */
    public function get_game_by_id($game_id) {
        try {
            $stmt = $this->hybrid->prepare("SELECT * FROM games WHERE game_id = :game_id");
            $stmt->bindParam(':game_id', $game_id);
            $stmt->execute();
            return $stmt->fetch();
        } catch(PDOException $e) {
            error_log("Error getting game: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Search users in leaderboard
     */
    public function search_leaderboard($game_id, $search_term) {
        try {
            $stmt = $this->hybrid->prepare("
                SELECT 
                    l.*,
                    u.username,
                    u.firstname,
                    u.lastname,
                    CONCAT(u.firstname, ' ', u.lastname) as full_name,
                    g.game_name
                FROM leaderboards l
                JOIN user u ON l.user_id = u.user_id
                JOIN games g ON l.game_id = g.game_id
                WHERE l.game_id = :game_id 
                AND (u.username LIKE :search OR u.firstname LIKE :search OR u.lastname LIKE :search)
                ORDER BY l.score DESC
            ");
            $search_param = "%{$search_term}%";
            $stmt->bindParam(':game_id', $game_id);
            $stmt->bindParam(':search', $search_param);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch(PDOException $e) {
            error_log("Error searching leaderboard: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get player count in leaderboard
     */
    public function get_leaderboard_player_count($game_id = null) {
        try {
            if ($game_id) {
                $stmt = $this->hybrid->prepare("
                    SELECT COUNT(DISTINCT user_id) as player_count 
                    FROM leaderboards 
                    WHERE game_id = :game_id
                ");
                $stmt->bindParam(':game_id', $game_id);
            } else {
                $stmt = $this->hybrid->prepare("
                    SELECT COUNT(DISTINCT user_id) as player_count 
                    FROM leaderboards
                ");
            }
            $stmt->execute();
            $result = $stmt->fetch();
            return $result['player_count'] ?? 0;
        } catch(PDOException $e) {
            error_log("Error getting player count: " . $e->getMessage());
            return 0;
        }
    }

   
    public function get_user_all_scores($user_id) {
        try {
            $stmt = $this->hybrid->prepare("
                SELECT 
                    l.*,
                    g.game_name,
                    g.game_desc
                FROM leaderboards l
                JOIN games g ON l.game_id = g.game_id
                WHERE l.user_id = :user_id
                ORDER BY l.game_id, l.score DESC
            ");
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch(PDOException $e) {
            error_log("Error getting user scores: " . $e->getMessage());
            return [];
        }
    }
	public function save_game_score($user_id, $game_id, $score, $distance, $realm = 'forest') {
    try {
        $conn = $this->get_connection();
        
        $sql = "INSERT INTO game_scores (user_id, game_id, score, distance, realm, played_at) 
                VALUES (?, ?, ?, ?, ?, NOW())";
        
        $stmt = $conn->prepare($sql);
        $result = $stmt->execute([$user_id, $game_id, $score, $distance, $realm]);
        
        if (!$result) {
            error_log("Failed to save score: " . implode(", ", $stmt->errorInfo()));
        }
        
        return $result;
    } catch (Exception $e) {
        error_log("Error in save_game_score: " . $e->getMessage());
        return false;
    }
}


public function get_user_by_id($user_id) {
    $conn = $this->get_connection();
    $sql = "SELECT * FROM user WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$user_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

public function get_game_leaderboard($game_id, $limit = 100) {
    try {
        $stmt = $this->hybrid->prepare("
            SELECT 
                gs.*,
                u.username,
                u.firstname,
                u.lastname,
                CONCAT(u.firstname, ' ', u.lastname) as full_name,
                ROW_NUMBER() OVER (ORDER BY gs.score DESC) as rank
            FROM game_scores gs
            JOIN user u ON gs.user_id = u.user_id
            WHERE gs.game_id = :game_id
            GROUP BY gs.user_id  -- Get only the highest score per user
            ORDER BY MAX(gs.score) DESC
            LIMIT :limit
        ");
        $stmt->bindParam(':game_id', $game_id);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    } catch(PDOException $e) {
        error_log("Error getting leaderboard: " . $e->getMessage());
        return [];
    }
}

/**
 * Get overall leaderboard across all games
 */
public function get_overall_leaderboard($limit = 100) {
    try {
        $stmt = $this->hybrid->prepare("
            SELECT 
                u.user_id,
                u.username,
                u.firstname,
                u.lastname,
                CONCAT(u.firstname, ' ', u.lastname) as full_name,
                COUNT(DISTINCT gs.score_id) as games_played,
                COALESCE(MAX(gs.score), 0) as total_score,
                COALESCE(AVG(gs.score), 0) as average_score
            FROM user u
            LEFT JOIN game_scores gs ON u.user_id = gs.user_id
            WHERE u.Role = 'User'
            GROUP BY u.user_id, u.username, u.firstname, u.lastname
            ORDER BY MAX(gs.score) DESC, COUNT(gs.score_id) DESC
            LIMIT :limit
        ");
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    } catch(PDOException $e) {
        error_log("Error getting overall leaderboard: " . $e->getMessage());
        return [];
    }
}

/**
 * Get top players for a specific game
 */
public function get_top_players($game_id, $limit = 10) {
    try {
        $stmt = $this->hybrid->prepare("
            SELECT 
                u.username,
                CONCAT(u.firstname, ' ', u.lastname) as full_name,
                u.user_id,
                MAX(gs.score) as score,
                ROW_NUMBER() OVER (ORDER BY MAX(gs.score) DESC) as rank
            FROM game_scores gs
            JOIN user u ON gs.user_id = u.user_id
            WHERE gs.game_id = :game_id
            GROUP BY gs.user_id
            ORDER BY MAX(gs.score) DESC
            LIMIT :limit
        ");
        $stmt->bindParam(':game_id', $game_id);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    } catch(PDOException $e) {
        error_log("Error getting top players: " . $e->getMessage());
        return [];
    }
}

/**
 * Add a new score to leaderboard
 */
public function add_leaderboard_score($user_id, $game_id, $score) {
    try {
        // Insert into game_scores table
        $stmt = $this->hybrid->prepare("
            INSERT INTO game_scores (user_id, game_id, score, distance, realm, played_at) 
            VALUES (:user_id, :game_id, :score, 0, 'forest', NOW())
        ");
        $stmt->execute([
            ':user_id' => $user_id,
            ':game_id' => $game_id,
            ':score' => $score
        ]);
        
        return $this->hybrid->lastInsertId();
    } catch(PDOException $e) {
        error_log("Error adding leaderboard score: " . $e->getMessage());
        return false;
    }
}

/**
 * Get user's best score for a game
 */
public function get_user_best_score($user_id, $game_id) {
    try {
        $stmt = $this->hybrid->prepare("
            SELECT MAX(score) as best_score
            FROM game_scores
            WHERE user_id = :user_id AND game_id = :game_id
        ");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':game_id', $game_id);
        $stmt->execute();
        return $stmt->fetch();
    } catch(PDOException $e) {
        error_log("Error getting best score: " . $e->getMessage());
        return false;
    }
}

} // end of class

?>