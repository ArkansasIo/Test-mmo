<?php
// setup_info.php for rts turnbase mmorpg-inspired project

// Database configuration
$host = 'localhost';  // Database host (usually localhost)
$dbname = 'game_database';  // Database name
$username = 'root';  // Database username
$password = '';  // Database password (leave empty if no password is set)

try {
    // Establish a connection using PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected to the OGame database successfully.";
} catch (PDOException $e) {
    // Handle connection error
    echo "Error connecting to the database: " . $e->getMessage();
    exit;
}

// Game settings (these could be adjusted as per game design)
define('GAME_NAME', 'Galactic Empires');
define('MAX_PLAYERS', 1000);  // Maximum number of players in the game
define('MAX_PLANETS', 10);  // Maximum number of planets a player can own
define('STARTING_RESOURCES', 1000);  // Starting resources for new players (e.g., metal, crystal, deuterium)

// Resource production rates (example)
define('METAL_PRODUCTION_RATE', 5);  // Resources produced per minute (metal)
define('CRYSTAL_PRODUCTION_RATE', 3);  // Resources produced per minute (crystal)
define('DEUTERIUM_PRODUCTION_RATE', 2);  // Resources produced per minute (deuterium)

// Game-specific constants (e.g., for fleet management)
define('MAX_FLEET_SIZE', 100);  // Maximum number of ships in a fleet

// Time settings
define('TICK_TIME', 60);  // Game tick time (in seconds), used for resource production and other time-dependent activities

// Path to game assets (e.g., images, scripts)
define('GAME_ASSETS_PATH', '/path/to/assets');

// Include other configuration files
include_once('config.php');  // If there are additional configuration files

// Check if the game is in maintenance mode
define('MAINTENANCE_MODE', false);  // Set to true for maintenance

// Security settings
define('SESSION_TIMEOUT', 3600);  // Session timeout in seconds (1 hour)
define('ENCRYPTION_KEY', 'your_secure_encryption_key_here');  // Key for encrypting sensitive data (e.g., passwords)

// Environment configuration
define('ENVIRONMENT', 'production');  // Can be 'development' or 'production'

// Debugging mode (showing detailed errors in development)
if (ENVIRONMENT == 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

?>
