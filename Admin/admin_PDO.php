<?php

	class admin_PDO{
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

		public function create_user($username, $password, $firstname, $lastname){
		$insert = $this->hybrid->prepare("INSERT INTO user(username, password, firstname, lastname, Role ) VALUES(:username, :password, :firstname, :lastname, :Role)");
		$insert->execute([
			':username' => $username,
			':password' => $password,
			':firstname' => $firstname,
			':lastname' => $lastname,
			':lastname' => 'User'
		]);
		return $insert;
		}

		 public function show_users() {
        $sql = "SELECT user_id, username, firstname, lastname, Role, created_at, updated_at FROM user WHERE Role = 'User' ORDER BY created_at DESC";
        $stmt = $this->hybrid->prepare($sql);
        $stmt->execute();
        return $stmt;
    }
    
    /**
     * RESET USER PASSWORD (admin function)
     */
    public function reset_user_password($user_id, $new_password) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        
        $sql = "UPDATE user SET password = ?, updated_at = NOW() WHERE user_id = ?";
        $stmt = $this->hybrid->prepare($sql);
        return $stmt->execute([$hashed_password, $user_id]);
    }
    
    /**
     * DELETE USER
     */
    public function delete_user($user_id) {
        // First delete related records (optional, based on your foreign key constraints)
        $sql1 = "DELETE FROM game_scores WHERE user_id = ?";
        $stmt1 = $this->hybrid->prepare($sql1);
        $stmt1->execute([$user_id]);
        
        // Then delete the user
        $sql2 = "DELETE FROM user WHERE user_id = ?";
        $stmt2 = $this->hybrid->prepare($sql2);
        return $stmt2->execute([$user_id]);
    }
    
    /**
     * GET USER BY ID
     */
    public function get_user_by_id($user_id) {
        $sql = "SELECT user_id, username, firstname, lastname, Role, created_at, updated_at FROM user WHERE user_id = ?";
        $stmt = $this->hybrid->prepare($sql);
        $stmt->execute([$user_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * GET LEADERBOARD DATA
     */
  

		//logout
		public function logout(){
			session_destroy();

			header("location:../index.php");
		}

		public function getLeaderboard() {
    $sql = "SELECT * FROM game_scores ORDER BY score DESC LIMIT 50";
    $stmt = $this->hybrid->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
public function deleteScore($score_id) {
    $sql = "DELETE FROM game_scores WHERE score_id = ?";
    $stmt = $this->hybrid->prepare($sql);
    return $stmt->execute([$score_id]);
}
public function show_selected_user($user_id){
			$fetch = $this->hybrid->prepare("SELECT * FROM user WHERE user_id = :user_id ");
			$fetch->execute([
				':user_id' => $user_id
			]);
			return $fetch;
		}

		public function update_user($user_id, $username, $firstname, $lastname, $updated_at){
			$update = $this->hybrid->prepare("UPDATE user 
				SET username = :username,  
					firstname = :firstname,
					lastname = :lastname,
					updated_at = :updated_at
				WHERE user_id = :user_id");
			$update->execute([
				':user_id' => $user_id,
				':username'=> $username,
				':firstname'=> $firstname,
				':lastname'=> $lastname,
				':updated_at'=> $updated_at
			]);
			return $update;
		}

	}// end of class <_)-----
?>