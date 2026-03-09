<?php
/**
 * Test Data Generator for Tasks System
 * Generates sample tasks and events for testing
 */

require_once __DIR__ . '/Index/config.php';
require_once __DIR__ . '/Index/classes/Database.php';
require_once __DIR__ . '/Index/classes/Task.php';
require_once __DIR__ . '/Index/classes/Event.php';
require_once __DIR__ . '/Index/classes/TaskGenerator.php';

try {
    $db = Database::getInstance();
    
    // Check if test player exists
    $testPlayer = $db->fetchOne(
        "SELECT id FROM players WHERE username = ? LIMIT 1",
        ['testadmin']
    );
    
    $playerId = null;
    
    if (!$testPlayer) {
        // Create test player
        echo "Creating test player...\n";
        $testPass = password_hash('testpass123', PASSWORD_BCRYPT);
        $now = (int)time();
        
        $playerId = $db->insert('players', [
            'username' => 'testadmin',
            'email' => 'test@admin.local',
            'password' => $testPass,
            'metal' => 5000,
            'crystal' => 3000,
            'deuterium' => 1000,
            'energy' => 10000,
            'is_admin' => 1,
            'is_banned' => 0,
            'created_at' => $now,
            'last_activity' => $now,
            'last_resource_update' => $now
        ]);
        
        echo "✓ Test player created (ID: $playerId)\n";
    } else {
        $playerId = $testPlayer['id'];
        echo "✓ Test player found (ID: $playerId)\n";
    }
    
    // Clear existing test data
    echo "\nClearing existing test tasks and events...\n";
    $db->delete('events', 'player_id = ?', [$playerId]);
    $db->delete('daily_tasks', 'player_id = ?', [$playerId]);
    $db->delete('tasks', 'player_id = ?', [$playerId]);
    echo "✓ Cleared existing data\n";
    
    // Generate tutorial tasks
    echo "\nGenerating tutorial tasks...\n";
    $generator = new TaskGenerator();
    $tasks = $generator->generateTutorialTasks($playerId);
    echo "✓ Generated " . count($tasks) . " tutorial tasks\n";
    
    // Generate daily tasks
    echo "\nGenerating daily tasks...\n";
    $dailyTasks = $generator->generateDailyTasks($playerId);
    echo "✓ Generated " . count($dailyTasks) . " daily tasks\n";
    
    // Generate achievement tasks
    echo "\nGenerating achievement tasks...\n";
    $achievements = $generator->generateAchievementTasks($playerId);
    echo "✓ Generated " . count($achievements) . " achievement tasks\n";
    
    // Create some sample events
    echo "\nCreating sample events...\n";
    $event = new Event();
    
    $sampleEvents = [
        ['fleet_attacked', ['fleet_id' => 1, 'attacker_id' => 2, 'damage' => 5000]],
        ['building_complete', ['planet_id' => 1, 'building_type' => 'metal_mine', 'level' => 5]],
        ['research_complete', ['research_type' => 'weapons', 'level' => 3]],
        ['low_resources', ['resource_type' => 'metal', 'current' => 100]],
    ];
    
    foreach ($sampleEvents as [$type, $data]) {
        $event->create($playerId, $type, $data);
    }
    echo "✓ Created " . count($sampleEvents) . " sample events\n";
    
    // Display task statistics
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "TEST DATA GENERATION COMPLETE\n";
    echo str_repeat("=", 50) . "\n";
    
    $allTasks = $db->fetchAll(
        "SELECT COUNT(*) as count FROM tasks WHERE player_id = ?",
        [$playerId]
    );
    $allEvents = $db->fetchAll(
        "SELECT COUNT(*) as count FROM events WHERE player_id = ?",
        [$playerId]
    );
    
    echo "\nPlayer ID: $playerId\n";
    echo "Username: testadmin\n";
    echo "Password: testpass123\n";
    echo "\nTask Statistics:\n";
    echo "  Total Tasks: " . ($allTasks[0]['count'] ?? 0) . "\n";
    
    $byStatus = $db->fetchAll(
        "SELECT status, COUNT(*) as count FROM tasks WHERE player_id = ? GROUP BY status",
        [$playerId]
    );
    foreach ($byStatus as $row) {
        echo "  - {$row['status']}: {$row['count']}\n";
    }
    
    echo "\nEvents: " . ($allEvents[0]['count'] ?? 0) . "\n";
    
    echo "\n✓ Ready for testing!\n";
    echo "  Access at: http://localhost:8000/Index/index.php?page=tasks\n";
    echo "\n";
    
} catch (Throwable $e) {
    fwrite(STDERR, "Error: " . $e->getMessage() . "\n");
    exit(1);
}
