<?php
/**
 * Main Entry Point - index.php
 * Handles authentication and page routing
 */

require_once 'config.php';
session_start();
require_once CLASS_PATH . 'Database.php';
require_once CLASS_PATH . 'Player.php';
require_once CLASS_PATH . 'Planet.php';
require_once CLASS_PATH . 'Fleet.php';
require_once CLASS_PATH . 'Combat.php';
require_once CLASS_PATH . 'Alliance.php';
require_once CLASS_PATH . 'GameEngine.php';
require_once CLASS_PATH . 'Task.php';
require_once CLASS_PATH . 'Event.php';

$error = '';
$page = $_GET['page'] ?? 'empire';
$publicPages = ['register'];

// DEV MODE: Quick login bypass
if (defined('DEV_MODE') && DEV_MODE && isset($_GET['dev_login'])) {
    $devUserId = (int)($_GET['dev_login']);
    if ($devUserId > 0) {
        $_SESSION['player_id'] = $devUserId;
        $_SESSION['user_id'] = $devUserId;
        $_SESSION['dev_bypass'] = true;
        header('Location: index.php');
        exit;
    }
}

// Handle login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    $player = Player::authenticate($username, $password);
    
    if ($player) {
        $_SESSION['player_id'] = $player->getId();
        $_SESSION['user_id'] = $player->getId();
        header('Location: index.php');
        exit;
    } else {
        $error = "Invalid username or password";
    }
}

// Handle logout
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_destroy();
    header('Location: index.php');
    exit;
}

// Support both session keys used across legacy pages.
$sessionPlayerId = $_SESSION['user_id'] ?? ($_SESSION['player_id'] ?? null);

// Public pages are reachable without authentication.
if (!$sessionPlayerId && in_array($page, $publicPages, true)) {
    include PAGE_PATH . $page . '.php';
    exit;
}

// Check if user is logged in
if (!$sessionPlayerId) {
    include TEMPLATE_PATH . 'login.php';
    exit;
}

$_SESSION['player_id'] = (int)$sessionPlayerId;
$_SESSION['user_id'] = (int)$sessionPlayerId;

// Update last login time
$db = Database::getInstance();
$db->update('players', ['last_activity' => time()], 'id = :id', ['id' => (int)$sessionPlayerId]);

// Process game engine tasks
$engine = new GameEngine();
$engine->processAutomatedTasks();

// Load the main game interface with menus and page routing
include TEMPLATE_PATH . 'menu.php';


