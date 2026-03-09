<?php
/**
 * Create Test User Script
 * Quick script to create a test user for development
 */

require_once 'Index/config.php';
require_once CLASS_PATH . 'Database.php';

$db = Database::getInstance();

// Generate unique username
$username = 'testuser_' . time();
$email = $username . '@test.local';
$password = 'test123';
$passwordHash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

try {
    echo "Creating test user...\n";
    
    // Create user
    $currentTime = time();
    $db->execute(
        "INSERT INTO players (username, email, password, metal, crystal, deuterium, energy, is_admin, is_banned, created_at, last_activity, last_resource_update) 
         VALUES (?, ?, ?, ?, ?, ?, ?, 0, 0, ?, ?, ?)",
        [$username, $email, $passwordHash, STARTING_METAL, STARTING_CRYSTAL, STARTING_DEUTERIUM, STARTING_ENERGY, $currentTime, $currentTime, $currentTime]
    );
    
    $userId = $db->getLastInsertId();
    
    if ($userId) {
        echo "✅ User created successfully!\n\n";
        echo "==========================================\n";
        echo "Username: $username\n";
        echo "Email:    $email\n";
        echo "Password: $password\n";
        echo "User ID:  $userId\n";
        echo "==========================================\n\n";
        
        echo "✅ Resources initialized (in player record)!\n\n";
        
        // Create home planet using live schema columns
        try {
            echo "Creating home planet...\n";
            $planetName = "Homeworld";
            $galaxy = 1;
            $system = rand(1, 100);
            $position = rand(1, 15);
            $temperature = 25;
            $diameter = 12800;
            $fields = 163;
            $fieldsUsed = 0;
            $isCapital = 1;

            $planetInsertResult = $db->execute(
                "INSERT INTO planets (player_id, name, galaxy, system, position, diameter, fields, fields_used, temperature, is_capital, created_at)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
                [$userId, $planetName, $galaxy, $system, $position, $diameter, $fields, $fieldsUsed, $temperature, $isCapital, $currentTime]
            );

            if ($planetInsertResult !== false) {
                $planetId = $db->getLastInsertId();
                echo "✅ Home planet created at [$galaxy:$system:$position] (Planet ID: $planetId)\n\n";
            } else {
                echo "⚠️  Planet creation failed (insert returned false).\n\n";
            }
        } catch (Exception $e) {
            echo "⚠️  Note: Could not create planet: " . $e->getMessage() . "\n\n";
        }
        
        echo "==========================================\n";
        echo "🎮 READY TO PLAY!\n";
        echo "==========================================\n\n";
        
        if (defined('DEV_MODE') && DEV_MODE) {
            echo "Quick Login URLs:\n";
            echo "- Dev Bypass: http://localhost:8000/Index/dev-bypass.php\n";
            echo "- Direct Login: http://localhost:8000/Index/index.php?dev_login=$userId\n\n";
        } else {
            echo "Login at: http://localhost:8000/Index/index.php\n";
            echo "Use credentials above\n\n";
        }
        
    } else {
        echo "❌ Error: Failed to create user\n";
        exit(1);
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
