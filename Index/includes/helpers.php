<?php
/**
 * Helper Functions
 * Utility functions used throughout the application
 */

/**
 * Format number with suffixes (K, M, B, T)
 */
function formatNumber($number) {
    if ($number >= 1000000000000) {
        return number_format($number / 1000000000000, 2) . 'T';
    } elseif ($number >= 1000000000) {
        return number_format($number / 1000000000, 2) . 'B';
    } elseif ($number >= 1000000) {
        return number_format($number / 1000000, 2) . 'M';
    } elseif ($number >= 1000) {
        return number_format($number / 1000, 2) . 'K';
    }
    return number_format($number);
}

/**
 * Format time duration
 */
function formatTime($seconds) {
    $days = floor($seconds / 86400);
    $hours = floor(($seconds % 86400) / 3600);
    $minutes = floor(($seconds % 3600) / 60);
    $secs = $seconds % 60;
    
    $parts = [];
    if ($days > 0) $parts[] = $days . 'd';
    if ($hours > 0) $parts[] = $hours . 'h';
    if ($minutes > 0) $parts[] = $minutes . 'm';
    if ($secs > 0 || empty($parts)) $parts[] = $secs . 's';
    
    return implode(' ', $parts);
}

/**
 * Sanitize input
 */
function sanitize($input) {
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}

/**
 * Redirect to page
 */
function redirect($page, $params = []) {
    $url = 'index.php?page=' . $page;
    foreach ($params as $key => $value) {
        $url .= '&' . urlencode($key) . '=' . urlencode($value);
    }
    header('Location: ' . $url);
    exit;
}

/**
 * Check if player is admin
 */
function isAdmin($playerId) {
    $db = Database::getInstance();
    $player = $db->fetchOne("SELECT is_admin FROM players WHERE id = ?", [$playerId]);
    return $player && $player['is_admin'] == 1;
}

/**
 * Log activity
 */
function logActivity($playerId, $action, $details = '') {
    $db = Database::getInstance();
    $db->insert('activity_log', [
        'player_id' => $playerId,
        'action' => $action,
        'details' => $details,
        'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '',
        'created_at' => time()
    ]);
}

/**
 * Send notification to player
 */
function sendNotification($playerId, $type, $title, $message) {
    $db = Database::getInstance();
    return $db->insert('notifications', [
        'player_id' => $playerId,
        'type' => $type,
        'title' => $title,
        'message' => $message,
        'is_read' => 0,
        'created_at' => time()
    ]);
}

/**
 * Send message between players
 */
function sendMessage($senderId, $recipientId, $subject, $message) {
    $db = Database::getInstance();
    return $db->insert('messages', [
        'sender_id' => $senderId,
        'recipient_id' => $recipientId,
        'subject' => $subject,
        'message' => $message,
        'is_read' => 0,
        'created_at' => time()
    ]);
}

/**
 * Calculate distance between coordinates
 */
function calculateDistance($g1, $s1, $p1, $g2, $s2, $p2) {
    if ($g1 != $g2) {
        return abs($g1 - $g2) * 20000;
    } elseif ($s1 != $s2) {
        return abs($s1 - $s2) * 5 * 19 + 2700;
    } else {
        return abs($p1 - $p2) * 5 + 1000;
    }
}

/**
 * Get ship stats
 */
function getShipStats($shipType) {
    $stats = [
        'small_cargo' => ['metal' => 2000, 'crystal' => 2000, 'deuterium' => 0, 'cargo' => 5000, 'speed' => 5000],
        'large_cargo' => ['metal' => 6000, 'crystal' => 6000, 'deuterium' => 0, 'cargo' => 25000, 'speed' => 7500],
        'light_fighter' => ['metal' => 3000, 'crystal' => 1000, 'deuterium' => 0, 'cargo' => 50, 'speed' => 12500],
        'heavy_fighter' => ['metal' => 6000, 'crystal' => 4000, 'deuterium' => 0, 'cargo' => 100, 'speed' => 10000],
        'cruiser' => ['metal' => 20000, 'crystal' => 7000, 'deuterium' => 2000, 'cargo' => 800, 'speed' => 15000],
        'battleship' => ['metal' => 45000, 'crystal' => 15000, 'deuterium' => 0, 'cargo' => 1500, 'speed' => 10000],
        'colony_ship' => ['metal' => 10000, 'crystal' => 20000, 'deuterium' => 10000, 'cargo' => 7500, 'speed' => 2500],
        'recycler' => ['metal' => 10000, 'crystal' => 6000, 'deuterium' => 2000, 'cargo' => 20000, 'speed' => 2000],
        'espionage_probe' => ['metal' => 0, 'crystal' => 1000, 'deuterium' => 0, 'cargo' => 5, 'speed' => 100000000],
        'bomber' => ['metal' => 50000, 'crystal' => 25000, 'deuterium' => 15000, 'cargo' => 500, 'speed' => 4000],
        'destroyer' => ['metal' => 60000, 'crystal' => 50000, 'deuterium' => 15000, 'cargo' => 2000, 'speed' => 5000],
        'deathstar' => ['metal' => 5000000, 'crystal' => 4000000, 'deuterium' => 1000000, 'cargo' => 1000000, 'speed' => 100],
        'battlecruiser' => ['metal' => 30000, 'crystal' => 40000, 'deuterium' => 15000, 'cargo' => 750, 'speed' => 10000]
    ];
    
    return isset($stats[$shipType]) ? $stats[$shipType] : null;
}

/**
 * Get defense stats
 */
function getDefenseStats($defenseType) {
    $stats = [
        'rocket_launcher' => ['metal' => 2000, 'crystal' => 0, 'deuterium' => 0],
        'light_laser' => ['metal' => 1500, 'crystal' => 500, 'deuterium' => 0],
        'heavy_laser' => ['metal' => 6000, 'crystal' => 2000, 'deuterium' => 0],
        'gauss_cannon' => ['metal' => 20000, 'crystal' => 15000, 'deuterium' => 2000],
        'ion_cannon' => ['metal' => 2000, 'crystal' => 6000, 'deuterium' => 0],
        'plasma_turret' => ['metal' => 50000, 'crystal' => 50000, 'deuterium' => 30000],
        'small_shield_dome' => ['metal' => 10000, 'crystal' => 10000, 'deuterium' => 0],
        'large_shield_dome' => ['metal' => 50000, 'crystal' => 50000, 'deuterium' => 0]
    ];
    
    return isset($stats[$defenseType]) ? $stats[$defenseType] : null;
}

/**
 * Check if maintenance mode is active
 */
function isMaintenanceMode() {
    return defined('MAINTENANCE_MODE') && MAINTENANCE_MODE === true;
}

/**
 * Generate random planet name
 */
function generatePlanetName() {
    $prefixes = ['Alpha', 'Beta', 'Gamma', 'Delta', 'Epsilon', 'Zeta', 'Eta', 'Theta', 'Iota', 'Kappa'];
    $suffixes = ['Prime', 'Minor', 'Major', 'Secundus', 'Tertius', 'Quartus', 'Quintus'];
    $names = ['Centauri', 'Proxima', 'Kepler', 'Gliese', 'Sirius', 'Vega', 'Altair', 'Rigel', 'Betelgeuse', 'Aldebaran'];
    
    return $prefixes[array_rand($prefixes)] . ' ' . $names[array_rand($names)] . ' ' . $suffixes[array_rand($suffixes)];
}

/**
 * Validate coordinates
 */
function validateCoordinates($galaxy, $system, $position) {
    return $galaxy >= 1 && $galaxy <= 9 &&
           $system >= 1 && $system <= 499 &&
           $position >= 1 && $position <= 15;
}

/**
 * Get player rank
 */
function getPlayerRank($playerId) {
    $db = Database::getInstance();
    $result = $db->fetchOne("SELECT COUNT(*) + 1 as rank 
                             FROM players 
                             WHERE (metal + crystal + deuterium) > 
                                   (SELECT metal + crystal + deuterium FROM players WHERE id = ?)", 
                            [$playerId]);
    return $result ? $result['rank'] : 0;
}

/**
 * Format coordinates
 */
function formatCoordinates($galaxy, $system, $position) {
    return "[$galaxy:$system:$position]";
}
