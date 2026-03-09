# Scifi Conquest - Game Classes Documentation

## Overview

This document provides comprehensive documentation for all the utility and service classes available in the Scifi Conquest game engine. These classes form the foundation of the game's functionality.

## Core Services

### GameEngine
**File:** `GameEngine.php`

Central service container that manages all game services.

```php
// Initialize the game engine
$engine = GameEngine::getInstance();
$engine->initialize();

// Access services
$db = $engine->getService('database');
$cache = $engine->getService('cache');
$auth = $engine->getService('auth');
```

Or use helper functions:
```php
$db = db();
$cache = cache();
$auth = auth();
$validator = validate();
$logger = logger();
$stats = service('Statistics');
```

---

## Database & ORM

### Database
**File:** `Database.php`

Handles all database operations with prepared statements and security.

```php
$db = db();

// Fetch queries
$user = $db->fetchOne("SELECT * FROM users WHERE id = ?", [1]);
$users = $db->fetchAll("SELECT * FROM users WHERE active = ?", [1]);
$count = $db->fetchColumn("SELECT COUNT(*) FROM users");

// Execute statements
$db->execute("INSERT INTO users (name, email) VALUES (?, ?)", ['John', 'john@email.com']);
$db->execute("UPDATE users SET status = ? WHERE id = ?", ['active', 1]);
$db->execute("DELETE FROM users WHERE id = ?", [1]);

// Transactions
$db->beginTransaction();
try {
    $db->execute("INSERT ...");
    $db->execute("UPDATE ...");
    $db->commit();
} catch (Exception $e) {
    $db->rollback();
}
```

---

## Caching

### Cache
**File:** `Cache.php`

In-memory and filesystem caching system.

```php
$cache = cache();

// Set cache
$cache->set('user_1', $userData, 3600); // 1 hour TTL

// Get cache
$data = $cache->get('user_1');

// Delete cache
$cache->delete('user_1');

// Clear all
$cache->clear();

// Get stats
$stats = $cache->getStats();
```

---

## Authentication & Authorization

### Authentication
**File:** `Authentication.php`

User authentication and session management.

```php
$auth = auth();

// Login user
$result = $auth->login($email, $password);
if ($result) {
    echo "Login successful";
}

// Check if authenticated
if ($auth->isAuthenticated()) {
    $user = $auth->getCurrentUser();
}

// Logout
$auth->logout();

// Check permission
if ($auth->hasPermission('attack_player')) {
    // Allow
}
```

### SessionManager
**File:** `SessionManager.php`

Advanced session handling with security features.

```php
$session = new SessionManager();

// Set session data
$session->set('user_id', 123);
$session->set('player_data', $playerArray);

// Get session data
$userId = $session->get('user_id');

// Check if key exists
if ($session->has('user_id')) {
    // ...
}

// Delete session data
$session->delete('user_id');

// Destroy session
$session->destroy();
```

---

## Validation

### Validator
**File:** `Validator.php`

Input validation and sanitization.

```php
$validator = validate();

// Validate email
if ($validator->isValidEmail($email)) {
    // Valid
}

// Validate URL
if ($validator->isValidUrl($url)) {
    // Valid
}

// Validate integer
if ($validator->isInteger($value)) {
    // Valid
}

// Sanitize input
$clean = $validator->sanitize($inputString);

// Validate array
$rules = [
    'name' => 'required|string|max:50',
    'email' => 'required|email',
    'age' => 'required|integer|min:0|max:150'
];
$errors = $validator->validateArray($_POST, $rules);
if ($errors) {
    // Handle validation errors
}
```

---

## Logging

### Logger
**File:** `Logger.php`

Application logging system.

```php
$logger = logger();

// Log messages at different levels
$logger->info('User logged in', ['user_id' => 123]);
$logger->warning('Resource usage high', ['memory' => '256MB']);
$logger->error('Database connection failed', ['code' => 500]);
$logger->debug('Debug information', ['query' => $sql]);

// Flush logs
$logger->flush();
```

---

## Game-Specific Services

### Player
**File:** `Player.php`

Player management and data handling.

```php
$player = new Player(db(), $playerId);

// Get player data
$data = $player->getData();
$resources = $player->getResources();
$planets = $player->getPlanets();
$fleet = $player->getFleet();

// Update player
$player->updateResources(['credits' => 1000]);
$player->addExperience(50);

// Check player status
if ($player->isActive()) {
    // ...
}
```

### Planet
**File:** `Planet.php`

Planet management.

```php
$planet = new Planet(db(), $planetId);

// Get planet data
$data = $planet->getData();
$buildings = $planet->getBuildings();
$population = $planet->getPopulation();

// Build structure
$planet->buildBuilding($buildingType);

// Update defenses
$planet->updateDefenses($defenseArray);
```

### Fleet
**File:** `Fleet.php`

Fleet management and combat.

```php
$fleet = new Fleet(db(), $fleetId);

// Get fleet data
$data = $fleet->getData();
$ships = $fleet->getShips();

// Move fleet
$fleet->moveTo($targetPlanetId, $arrivalTime);

// Attack
$result = $fleet->attack($targetFleetId);
```

### Combat
**File:** `Combat.php`

Combat simulation and calculations.

```php
$combat = new Combat(db());

// Start combat
$battle = $combat->simulateCombat($attackerFleetId, $defenderFleetId);

// Get battle results
$winner = $battle['winner'];
$casualties = $battle['casualties'];
$loot = $battle['loot'];
```

### Research
**File:** (Technology tree.php)

Technology research system.

```php
// Research a technology
$player->research($technologyId);

// Get research status
$research = $player->getResearch();

// Check if technology available
if ($player->hasTechnology($technologyId)) {
    // Can use this tech
}
```

---

## Statistics & Analytics

### Statistics
**File:** `Statistics.php`

Game-wide analytics and tracking.

```php
$stats = service('Statistics');

// Record action
$stats->recordAction($playerId, 'attack_player', ['target' => $targetId]);

// Get player stats
$playerStats = $stats->getPlayerStats($playerId);

// Get top players
$topPlayers = $stats->getTopPlayers(10);

// Get game statistics
$gameStats = $stats->getGameStats();

// Get stats by period
$dailyStats = $stats->getStatsByPeriod('2024-01-01', '2024-12-31', 'daily');

// Action breakdown
$breakdown = $stats->getActionBreakdown();
```

---

## Admin Features

### AdminPanel
**File:** `AdminPanel.php`

Administrator tools and moderation.

```php
$admin = new AdminPanel(db(), $adminId);

// Check permission
if ($admin->hasPermission('manage_players')) {
    // ...
}

// Get admins
$admins = $admin->getAllAdmins();

// Get system logs
$logs = $admin->getSystemLogs(100, ['level' => 'error', 'days' => 7]);

// Get player history
$history = $admin->getPlayerModerationHistory($playerId);

// Issue warning
$admin->issueWarning($playerId, 'Spam behavior', 'medium');

// Ban player
$admin->banPlayer($playerId, 'Rule violation', 30); // 30 days

// Unban player
$admin->unbanPlayer($playerId, 'Appeal accepted');

// Get server stats
$stats = $admin->getServerStats();

// Clear cache
$admin->clearPlayerCache($playerId);

// Log action
$admin->logAction('ban_player', 'Banned for 30 days');
```

---

## Achievements & Settings

### Achievements
**File:** `Achievements.php`

Player achievements system.

```php
$achievements = service('Achievements');

// Award achievement
$achievements->awardAchievement($playerId, 'first_planet');

// Check and auto-award
$count = $achievements->checkAchievements($playerId);

// Get player achievements
$playerAchievements = $achievements->getPlayerAchievements($playerId);

// Get all achievements
$allAchievements = $achievements->getAllAchievements();
```

### PlayerSettings
**File:** `Achievements.php`

Player preferences and settings.

```php
$settings = new PlayerSettings(db());

// Get all settings
$allSettings = $settings->getSettings($playerId);

// Get single setting
$theme = $settings->getSetting($playerId, 'theme');

// Update setting
$settings->updateSetting($playerId, 'theme', 'light');

// Toggle setting
$settings->toggleSetting($playerId, 'notifications_enabled');

// Get all preferences
$prefs = $settings->getPreferences($playerId);
```

---

## Notifications & Communication

### NotificationService
**File:** `NotificationService.php`

Email and in-game notifications.

```php
$notifier = service('NotificationService');

// Send email
$notifier->sendEmail('user@email.com', 'Subject', 'Message', $htmlContent);

// Send in-game notification
$notifier->sendInGameNotification($playerId, 'Title', 'Message', 'success', '/page/url');

// Send bulk notification
$notifier->sendBulkNotification([$playerId1, $playerId2], 'Title', 'Message');

// Get unread
$unread = $notifier->getUnreadNotifications($playerId);

// Mark as read
$notifier->markAsRead($notificationId);

// Clear old notifications
$notifier->clearOldNotifications(30); // Older than 30 days
```

---

## API Responses

### APIResponse
**File:** `APIResponse.php`

Standardized API response format.

```php
// Success response
$response = APIResponse::success('Player created', ['id' => 123]);
$response->send();

// Error response
$response = APIResponse::error('Something went wrong', [], 500);
$response->send();

// Validation error
$errors = ['email' => 'Invalid email', 'age' => 'Must be 18+'];
$response = APIResponse::validationError($errors);
$response->send();

// Pagination
$response = APIResponse::success('Player list', $players);
$response->withPagination($page, $limit, $total);
$response->send();

// Unauthorized
$response = APIResponse::unauthorized('Please login');
$response->send();

// Not found
$response = APIResponse::notFound('Player not found');
$response->send();
```

---

## Utility Classes

### Task & TaskGenerator
**File:** `Task.php`, `TaskGenerator.php`

Background task management.

```php
$taskGen = new TaskGenerator(db());

// Create tasks
$taskGen->generateUpkeepTasks();
$taskGen->generateProductionTasks();

// Execute tasks
$executed = $taskGen->executeScheduledTasks();
```

### Mailer
**File:** `Mailer.php`

Email template handling.

```php
$mailer = new Mailer();

// Send templated email
$mailer->sendTemplate('welcome', 'user@email.com', [
    'name' => 'John',
    'activation_link' => 'https://...'
]);
```

---

## Integration Examples

### Complete Player Login
```php
require 'classes/GameEngine.php';

$auth = auth();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    if ($auth->login($email, $password)) {
        $user = $auth->getCurrentUser();
        
        // Log action
        service('Statistics')->recordAction($user['id'], 'login');
        
        // Get player data
        $player = new Player(db(), $user['player_id']);
        $playerData = $player->getData();
        
        // Set cache
        cache()->set("player_data_{$user['player_id']}", $playerData, 300);
        
        // Redirect
        header('Location: /game/dashboard');
    } else {
        $error = "Login failed";
    }
}
```

### Creating an API Endpoint
```php
require 'classes/GameEngine.php';

header('Content-Type: application/json');

// Validate authentication
if (!auth()->isAuthenticated()) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    die;
}

try {
    $user = auth()->getCurrentUser();
    $player = new Player(db(), $user['player_id']);
    
    // Validate input
    $validator = validate();
    $errors = $validator->validateArray($_POST, [
        'resource' => 'required|string',
        'amount' => 'required|integer|min:0'
    ]);
    
    if ($errors) {
        echo json_encode(APIResponse::validationError($errors)->toArray());
        die;
    }
    
    // Process request
    $resource = $_POST['resource'];
    $amount = (int)$_POST['amount'];
    
    $player->addResource($resource, $amount);
    
    // Log action
    service('Statistics')->recordAction($user['player_id'], 'add_resource', [
        'resource' => $resource,
        'amount' => $amount
    ]);
    
    echo json_encode(
        APIResponse::success('Resource added', ['new_total' => $player->getResource($resource)])
                   ->toArray()
    );
} catch (Exception $e) {
    logger()->error('API error', ['message' => $e->getMessage()]);
    echo json_encode(APIResponse::error('Server error', [$e->getMessage()], 500)->toArray());
}
```

---

## Best Practices

1. **Always use prepared statements** - Use the `?` placeholder in SQL queries and pass parameters separately.

2. **Use the service container** - Always access services through the GameEngine for consistency.

3. **Log important actions** - Use the Logger and Statistics service to track game events.

4. **Cache frequently accessed data** - Use the Cache service for player data and static information.

5. **Validate all input** - Use the Validator service before processing user input.

6. **Use transactions** - Wrap related database operations in transactions to maintain data integrity.

7. **Handle exceptions** - Use try-catch blocks and log errors appropriately.

8. **Use helper functions** - Use `db()`, `cache()`, etc., instead of `GameEngine::getInstance()->getService()`.

---

## Configuration

Configuration is managed through environment variables. Create a `.env` file in the game root:

```
DB_HOST=localhost
DB_USER=game_user
DB_PASSWORD=secure_password
DB_NAME=scifi_conquest

LOG_LEVEL=info
LOG_PATH=/path/to/logs

CACHE_ENABLED=true
DEBUG_MODE=false

SESSION_TIMEOUT=3600
TIMEZONE=UTC

SMTP_HOST=mail.example.com
SMTP_PORT=587
SMTP_USER=your_email@example.com
SMTP_PASS=your_password
```

---

## Troubleshooting

**Q: GameEngine initialization fails**
A: Check database connection settings and ensure all required tables exist.

**Q: Cache not working**
A: Ensure the `cache` directory exists and is writable by the PHP process.

**Q: Validation errors not showing**
A: Check that form fields match the validation rules and validate before processing.

**Q: Notifications not sending**
A: Check SMTP configuration and email headers in `NotificationService.php`.

---

## Version Information

- **Game Version:** 1.0
- **PHP Version Required:** 7.4+
- **Last Updated:** 2024
