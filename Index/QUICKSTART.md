# Scifi Conquest - Quick Start Guide

## Installation & Setup

### 1. Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web Server (Apache, Nginx)
- Composer (optional, for package management)

### 2. Database Setup
```bash
# Import database schema
mysql -u root -p scifi_conquest < Index/Db/Dbgame.sql
mysql -u root -p scifi_conquest < Db/Db.sql
```

### 3. Environment Configuration
Create `.env` in project root:
```
DB_HOST=localhost
DB_USER=game_user
DB_PASSWORD=your_secure_password
DB_NAME=scifi_conquest

LOG_LEVEL=info
LOG_PATH=./logs

CACHE_ENABLED=true
DEBUG_MODE=false

SESSION_TIMEOUT=3600
TIMEZONE=UTC
```

### 4. Directory Permissions
```bash
# Make necessary directories writable
chmod 755 cache logs sessions
chmod 755 Index/classes
```

### 5. Initialize Game Engine
Add to your main `index.php`:
```php
<?php
require_once 'Index/classes/GameEngine.php';

// Game engine is now ready
$db = db();
$cache = cache();
$auth = auth();
```

---

## Quick Examples

### Example 1: User Registration
```php
<?php
require_once 'Index/classes/GameEngine.php';

$validator = validate();
$auth = auth();
$db = db();

// Validate input
$errors = $validator->validateArray($_POST, [
    'username' => 'required|string|min:3|max:20',
    'email' => 'required|email',
    'password' => 'required|string|min:8|max:50'
]);

if (!empty($errors)) {
    http_response_code(422);
    echo json_encode(APIResponse::validationError($errors)->toArray());
    exit;
}

// Check if user exists
$existing = $db->fetchOne(
    "SELECT id FROM players WHERE email = ?", 
    [$_POST['email']]
);

if ($existing) {
    echo json_encode(APIResponse::error('Email already registered')->toArray());
    exit;
}

// Create user
$hash = password_hash($_POST['password'], PASSWORD_BCRYPT);
$db->execute(
    "INSERT INTO players (username, email, password_hash) VALUES (?, ?, ?)",
    [$_POST['username'], $_POST['email'], $hash]
);

// Get user ID
$userId = $db->getLastInsertId();

// Log action
service('Statistics')->recordAction($userId, 'registration');

// Send welcome email
service('NotificationService')->sendEventNotification(
    'player_joined',
    $userId,
    ['player_name' => $_POST['username']]
);

echo json_encode(APIResponse::success('Registration successful', ['id' => $userId])->toArray());
```

### Example 2: Build Structure on Planet
```php
<?php
require_once 'Index/classes/GameEngine.php';

// Verify authentication
if (!auth()->isAuthenticated()) {
    echo json_encode(APIResponse::unauthorized()->toArray());
    exit;
}

$playerId = auth()->getCurrentUser()['player_id'];
$planetId = $_POST['planet_id'];
$buildingType = $_POST['building'];

try {
    $player = new Player(db(), $playerId);
    $planet = new Planet(db(), $planetId);
    
    // Verify ownership
    if ($planet->getOwnerId() !== $playerId) {
        throw new Exception('Not your planet');
    }
    
    // Check resources
    $cost = $planet->getBuildingCost($buildingType);
    if (!$player->hasResources($cost)) {
        throw new Exception('Insufficient resources');
    }
    
    // Deduct resources
    $player->deductResources($cost);
    
    // Build
    $buildingId = $planet->buildBuilding($buildingType);
    
    // Log and notify
    service('Statistics')->recordAction(
        $playerId, 
        'build_structure',
        ['building' => $buildingType, 'planet_id' => $planetId]
    );
    
    service('NotificationService')->sendEventNotification(
        'building_complete',
        $playerId,
        ['building_name' => $buildingType, 'planet_name' => $planet->getName()]
    );
    
    echo json_encode(APIResponse::success('Building started', ['building_id' => $buildingId])->toArray());
    
} catch (Exception $e) {
    logger()->error('Build failed', ['error' => $e->getMessage()]);
    echo json_encode(APIResponse::error($e->getMessage())->toArray());
}
```

### Example 3: Attack Another Player
```php
<?php
require_once 'Index/classes/GameEngine.php';

if (!auth()->isAuthenticated()) {
    echo json_encode(APIResponse::unauthorized()->toArray());
    exit;
}

$playerId = auth()->getCurrentUser()['player_id'];
$targetPlanetId = $_POST['target_planet'];

try {
    $player = new Player(db(), $playerId);
    $targetPlanet = new Planet(db(), $targetPlanetId);
    $targetPlayer = $targetPlanet->getOwnerPlayer();
    
    // Check if player can attack
    if (!$player->canAttack($targetPlanetId)) {
        throw new Exception('Cannot attack this target');
    }
    
    // Get fleets
    $attackFleet = $player->getFleet();
    $defenseFleet = $targetPlanet->getDefenseFleet();
    
    if (!$attackFleet->hasShips()) {
        throw new Exception('No ships available');
    }
    
    // Simulate combat
    $combat = new Combat(db());
    $battleResult = $combat->simulateCombat($attackFleet->getId(), $defenseFleet->getId());
    
    // Process results
    if ($battleResult['winner'] === 'attacker') {
        // Calculate loot
        $loot = $targetPlanet->calculateLoot($battleResult);
        $player->addResources($loot);
        
        // Notify target player
        service('NotificationService')->sendEventNotification(
            'attack_incoming',
            $targetPlayer->getId(),
            ['attacker_name' => $player->getUsername(), 'planet_name' => $targetPlanet->getName()]
        );
    }
    
    // Log statistics
    service('Statistics')->recordAction($playerId, 'attack_player', [
        'target' => $targetPlayer->getId(),
        'result' => $battleResult['winner']
    ]);
    
    echo json_encode(APIResponse::success('Battle complete', $battleResult)->toArray());
    
} catch (Exception $e) {
    logger()->error('Attack failed', ['error' => $e->getMessage()]);
    echo json_encode(APIResponse::error($e->getMessage())->toArray());
}
```

### Example 4: Admin Panel - View Server Stats
```php
<?php
require_once 'Index/classes/GameEngine.php';

// Verify admin access
if (!auth()->isAuthenticated() || auth()->getCurrentUser()['role'] !== 'admin') {
    header('Location: /login');
    exit;
}

$adminId = auth()->getCurrentUser()['id'];
$admin = new AdminPanel(db(), $adminId);

// Check permission
if (!$admin->hasPermission('view_server_stats')) {
    die('Access denied');
}

// Get stats
$stats = $admin->getServerStats();
$logs = $admin->getSystemLogs(50, ['level' => 'error', 'days' => 1]);

?>
<div class="admin-panel">
    <h1>Server Statistics</h1>
    
    <div class="stats-grid">
        <div class="stat-card">
            <h3>Total Players</h3>
            <p><?php echo number_format($stats['total_players']); ?></p>
        </div>
        <div class="stat-card">
            <h3>Active Players</h3>
            <p><?php echo number_format($stats['active_players']); ?></p>
        </div>
        <div class="stat-card">
            <h3>Online Now</h3>
            <p><?php echo number_format($stats['online_players']); ?></p>
        </div>
        <div class="stat-card">
            <h3>Banned Players</h3>
            <p><?php echo number_format($stats['banned_players']); ?></p>
        </div>
    </div>
    
    <h2>Recent Errors</h2>
    <table>
        <thead>
            <tr>
                <th>Time</th>
                <th>Action</th>
                <th>Message</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($logs as $log): ?>
            <tr>
                <td><?php echo $log['created_at']; ?></td>
                <td><?php echo $log['action']; ?></td>
                <td><?php echo $log['details']; ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
```

### Example 5: Research Technology
```php
<?php
require_once 'Index/classes/GameEngine.php';

if (!auth()->isAuthenticated()) {
    echo json_encode(APIResponse::unauthorized()->toArray());
    exit;
}

$playerId = auth()->getCurrentUser()['player_id'];
$technologyId = $_POST['technology_id'];

try {
    $player = new Player(db(), $playerId);
    
    // Get tech requirements
    $tech = db()->fetchOne(
        "SELECT * FROM technologies WHERE id = ?",
        [$technologyId]
    );
    
    if (!$tech) {
        throw new Exception('Technology not found');
    }
    
    // Check prerequisites
    $prereqs = json_decode($tech['prerequisites']);
    foreach ($prereqs as $prereqId) {
        if (!$player->hasTechnology($prereqId)) {
            throw new Exception('Missing required technology');
        }
    }
    
    // Check resources
    $cost = json_decode($tech['cost'], true);
    if (!$player->hasResources($cost)) {
        throw new Exception('Insufficient resources');
    }
    
    // Start research
    $researchId = $player->startResearch($technologyId);
    
    // Log action
    service('Statistics')->recordAction($playerId, 'start_research', [
        'technology' => $technologyId
    ]);
    
    echo json_encode(APIResponse::success('Research started', [
        'research_id' => $researchId,
        'completion_time' => $tech['research_time']
    ])->toArray());
    
} catch (Exception $e) {
    echo json_encode(APIResponse::error($e->getMessage())->toArray());
}
```

---

## API Endpoints Structure

### Authentication
- `POST /api/auth/login` - Login user
- `POST /api/auth/register` - Register new user
- `POST /api/auth/logout` - Logout user
- `POST /api/auth/refresh-token` - Refresh auth token

### Player
- `GET /api/player` - Get player profile
- `POST /api/player/update` - Update player data
- `GET /api/player/resources` - Get resource status
- `POST /api/player/settings` - Update player settings

### Games
- `GET /api/game/status` - Get game status
- `GET /api/game/universe` - Get universe data
- `POST /api/game/action` - Execute game action

### Fleet
- `GET /api/fleet` - Get player fleets
- `POST /api/fleet/create` - Create new fleet
- `POST /api/fleet/move` - Move fleet
- `POST /api/fleet/attack` - Attack with fleet

### Buildings
- `GET /api/buildings` - Get building list
- `POST /api/building/build` - Build structure
- `POST /api/building/upgrade` - Upgrade building

### Research
- `GET /api/research` - Get research data
- `POST /api/research/start` - Start research

### Admin
- `GET /api/admin/stats` - Get server statistics
- `GET /api/admin/logs` - Get system logs
- `POST /api/admin/player/ban` - Ban player
- `POST /api/admin/player/warn` - Warn player
- `POST /api/admin/cache/clear` - Clear cache

---

## Directory Structure

```
scifi-Conquest-Awakening/
├── Index/
│   ├── classes/              # Core classes
│   │   ├── GameEngine.php   # Service container
│   │   ├── Database.php     # Database handler
│   │   ├── Player.php       # Player class
│   │   ├── Planet.php       # Planet class
│   │   ├── Fleet.php        # Fleet class
│   │   └── ... (other classes)
│   ├── api/                  # API endpoint files
│   ├── pages/               # Game pages
│   ├── includes/            # Helper includes
│   ├── cache/               # Cached data (writable)
│   ├── CLASSES_DOCUMENTATION.md  # Classes reference
│   └── config.php           # Main config
├── Db/
│   ├── Db.sql              # Database schema
│   └── Config.php          # Database config
├── logs/                    # Application logs (writable)
├── assets/                  # Static assets
├── .env                     # Environment config
└── README.md
```

---

## Common Tasks

### Task: Setup New Game Server
1. Install PHP and MySQL
2. Create database and import schema
3. Set environment variables in `.env`
4. Run migrations/updates if any
5. Set proper file permissions
6. Test game engine initialization
7. Run background tasks (cron jobs)

### Task: Add New Game Feature
1. Create new class in `classes/` directory
2. Add database tables if needed
3. Update relevant existing classes to integrate
4. Create API endpoints for feature
5. Add logging/statistics tracking
6. Write documentation
7. Test thoroughly

### Task: Deploy to Production
1. Set `DEBUG_MODE=false` in `.env`
2. Set `LOG_LEVEL=error` in `.env`
3. Update database backups
4. Disable development endpoints
5. Enable HTTPS
6. Set strong admin passwords
7. Setup automated backups

---

## Troubleshooting

### Problem: Database connection fails
**Solution:**
1. Check credentials in `.env`
2. Verify MySQL is running
3. Check firewall rules
4. Verify database was created
5. Check user permissions

### Problem: Classes not loading
**Solution:**
1. Verify `GameEngine.php` is being required
2. Check file permissions
3. Verify class file names match
4. Check syntax errors: `php -l filename.php`

### Problem: Cache not working
**Solution:**
1. Check `cache/` directory exists
2. Verify directory is writable
3. Check `CACHE_ENABLED=true` in `.env`
4. Clear cache manually

### Problem: Slow database queries
**Solution:**
1. Add indexes to frequently queried columns
2. Use `EXPLAIN` to analyze queries
3. Enable query caching
4. Monitor with logger

---

## Next Steps

1. Review `CLASSES_DOCUMENTATION.md` for detailed API reference
2. Check example implementations above
3. Run initial tests and setup background tasks
4. Configure admin panel
5. Setup player registration and authentication
6. Start game server
7. Monitor logs and statistics

---

**Last Updated:** 2024
**Version:** 1.0
