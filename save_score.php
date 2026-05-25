<?php
session_start();

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include files
require_once 'Hybrid_CRUD.php';

// Check if extended class exists
$useExtended = file_exists('Hybrid_CRUD_Extended.php');
if ($useExtended) {
    require_once 'Hybrid_CRUD_Extended.php';
    $crud = new Hybrid_CRUD_Extended();
} else {
    $crud = new Hybrid_CRUD();
}

header('Content-Type: application/json');

// Log for debugging
error_log("Save score request received. Using extended class: " . ($useExtended ? 'YES' : 'NO'));

// Check request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Get POST data
$json = file_get_contents('php://input');
$data = json_decode($json, true);

error_log("Received data: " . print_r($data, true));

// Validate
if (!$data || !isset($data['user_id'], $data['game_id'], $data['score'], $data['distance'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

$user_id = intval($data['user_id']);
$game_id = intval($data['game_id']);
$score = intval($data['score']);
$distance = intval($data['distance']);
$realm = $data['realm'] ?? 'forest';

error_log("Attempting to save score: user_id=$user_id, score=$score, distance=$distance, realm=$realm");

// Direct database approach - simpler and more reliable
try {
    // Get connection
    $conn = null;
    
    if (method_exists($crud, 'get_connection')) {
        $conn = $crud->get_connection();
    } elseif (property_exists($crud, 'conn') && $crud->conn instanceof PDO) {
        $conn = $crud->conn;
    } elseif (property_exists($crud, 'connection') && $crud->connection instanceof PDO) {
        $conn = $crud->connection;
    }
    
    if (!$conn) {
        // Create direct connection
        $host = 'localhost';
        $dbname = 'clickplay';
        $username = 'root';
        $password = '';
        
        $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    
    // Check if table exists
    $tableCheck = $conn->query("SHOW TABLES LIKE 'game_scores'")->fetch();
    if (!$tableCheck) {
        // Create table
        $createTable = "CREATE TABLE game_scores (
            score_id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            game_id INT NOT NULL DEFAULT 1,
            score INT NOT NULL,
            distance INT NOT NULL,
            realm VARCHAR(50) DEFAULT 'forest',
            played_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_user_game (user_id, game_id)
        )";
        $conn->exec($createTable);
        error_log("Created game_scores table");
    }
    
    // Insert score
    $sql = "INSERT INTO game_scores (user_id, game_id, score, distance, realm, played_at) 
            VALUES (?, ?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $result = $stmt->execute([$user_id, $game_id, $score, $distance, $realm]);
    
    error_log("Insert result: " . ($result ? 'SUCCESS' : 'FAILED'));
    
    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Score saved successfully']);
    } else {
        $errorInfo = $stmt->errorInfo();
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $errorInfo[2]]);
    }
    
} catch (Exception $e) {
    error_log("Exception in save_score.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>