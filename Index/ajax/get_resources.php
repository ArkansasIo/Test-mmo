<?php
/**
 * AJAX Endpoint - Get Resources
 * Returns current player resources
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
    $player = new Player($_SESSION['user_id']);
    $data = $player->getData();
    
    echo json_encode([
        'success' => true,
        'metal' => floor($data['metal']),
        'crystal' => floor($data['crystal']),
        'deuterium' => floor($data['deuterium']),
        'energy' => floor($data['energy'])
    ]);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
