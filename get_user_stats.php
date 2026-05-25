<?php
session_start();
require_once 'Hybrid_CRUD.php';

header('Content-Type: application/json');

if (!isset($_GET['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User ID is required']);
    exit;
}

$user_id = intval($_GET['user_id']);

// Create CRUD instance
$crud = new Hybrid_CRUD();

// Get user stats
$stats = $crud->get_user_game_stats($user_id, 1); // 1 = Pixel Dash game ID

if ($stats) {
    echo json_encode([
        'success' => true,
        'high_score' => $stats['high_score'] ?? 0,
        'games_played' => $stats['games_played'] ?? 0,
        'rank' => $stats['rank'] ?? 'N/A'
    ]);
} else {
    echo json_encode([
        'success' => true,
        'high_score' => 0,
        'games_played' => 0,
        'rank' => 'N/A'
    ]);
}
?>