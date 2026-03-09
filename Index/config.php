<?php
/**
 * Configuration File
 * Main configuration settings for the game
 */

// CRITICAL: Session & Timezone MUST be set before any output
// ============================================================
// Session Configuration (MUST be first)
define('SESSION_LIFETIME', 7200); // 2 hours
ini_set('session.gc_maxlifetime', SESSION_LIFETIME);

// Set session save path
if (!ini_get('session.save_path')) {
    $sessionPath = dirname(__DIR__) . '/sessions';
    if (is_dir($sessionPath) && is_writable($sessionPath)) {
        ini_set('session.save_path', $sessionPath);
    }
}

// Timezone (must be before date operations)
date_default_timezone_set('UTC');

// Development Mode (SET TO FALSE IN PRODUCTION!)
// WARNING: Enables login bypass and debug features - INSECURE!
define('DEV_MODE', true);

// Error Reporting (set to 0 in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'scifi_conquest');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Game Configuration
define('GAME_NAME', 'Sci-Fi Conquest: Awakening');
define('GAME_VERSION', '1.0.0');
define('GAME_URL', 'http://localhost:8000');

// Resource Starting Values
define('STARTING_METAL', 500);
define('STARTING_CRYSTAL', 500);
define('STARTING_DEUTERIUM', 0);
define('STARTING_ENERGY', 0);

// Turn Configuration
define('TURN_DURATION', 3600); // 1 hour in seconds
define('MAX_TURNS_STORAGE', 24); // Maximum turns that can be stored

// Path Configuration
define('ROOT_PATH', dirname(__DIR__));
define('CLASS_PATH', ROOT_PATH . '/Index/classes/');
define('TEMPLATE_PATH', ROOT_PATH . '/Index/templates/');
define('INCLUDE_PATH', ROOT_PATH . '/Index/includes/');
define('PAGE_PATH', ROOT_PATH . '/Index/pages/');

// Security
define('SALT', 'scifi_conquest_salt_' . md5(__DIR__));
define('ENCRYPTION_KEY', 'your_encryption_key_here');

// Game Balance
define('BUILDING_SPEED_MULTIPLIER', 1.0);
define('RESEARCH_SPEED_MULTIPLIER', 1.0);
define('PRODUCTION_MULTIPLIER', 1.0);

// Admin Settings
define('ADMIN_EMAIL', 'admin@scificonquest.com');
define('ENABLE_REGISTRATION', true);
define('MAINTENANCE_MODE', false);
