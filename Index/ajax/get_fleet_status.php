<?php
/**
 * AJAX Endpoint - Get Fleet Status
 * Returns active fleet movements
 */

session_start();
require_once '../config.php';
require_once '../classes/Database.php';
require_once '../classes/Player.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Not authenticated']);
    exit;
}

try {
    $db = Database::getInstance();
    $playerId = $_SESSION['user_id'];
    
    $movements = $db->fetchAll("SELECT * FROM fleet_movements 
                                WHERE player_id = ? AND status IN ('traveling', 'returning') 
                                ORDER BY arrival_time ASC", [$playerId]);
    
    $result = [];
    foreach ($movements as $movement) {
        $timeRemaining = max(0, $movement['arrival_time'] - time());
        $result[] = [
            'id' => $movement['id'],
            'mission_type' => $movement['mission_type'],
            'from' => "[{$movement['start_galaxy']}:{$movement['start_system']}:{$movement['start_position']}]",
            'to' => "[{$movement['target_galaxy']}:{$movement['target_system']}:{$movement['target_position']}]",
            'status' => $movement['status'],
            'time_remaining' => $timeRemaining,
            'arrival_time' => $movement['arrival_time']
        ];
    }
    
    echo json_encode([
        'success' => true,
        'fleets' => $result
    ]);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
