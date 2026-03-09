<?php
/**
 * AJAX Endpoint - Get Building Queue
 * Returns current building queue status
 */

session_start();
require_once '../config.php';
require_once '../classes/Database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Not authenticated']);
    exit;
}

$planetId = isset($_GET['planet_id']) ? (int)$_GET['planet_id'] : 0;

if (!$planetId) {
    echo json_encode(['error' => 'Planet ID required']);
    exit;
}

try {
    $db = Database::getInstance();
    $queue = $db->fetchAll("SELECT * FROM building_queue WHERE planet_id = ? ORDER BY completion_time ASC", [$planetId]);
    
    $result = [];
    foreach ($queue as $item) {
        $timeRemaining = max(0, $item['completion_time'] - time());
        $result[] = [
            'id' => $item['id'],
            'building_type' => $item['building_type'],
            'level' => $item['level'],
            'time_remaining' => $timeRemaining,
            'completion_time' => $item['completion_time']
        ];
    }
    
    echo json_encode([
        'success' => true,
        'queue' => $result
    ]);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
