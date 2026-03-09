# Development & Testing Guide

## Unit Testing

### Setting Up Tests

1. **Install PHPUnit**
```bash
composer require --dev phpunit/phpunit
```

2. **Create test directory structure**
```
tests/
├── Unit/
│   ├── PlayerTest.php
│   ├── PlanetTest.php
│   ├── DatabaseTest.php
│   └── ValidatorTest.php
├── Integration/
│   ├── AuthenticationTest.php
│   ├── FleetCombatTest.php
│   └── ApiEndpointTest.php
└── phpunit.xml
```

### Example Test: Player Class

```php
<?php
namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class PlayerTest extends TestCase {
    private $db;
    private $player;
    
    protected function setUp(): void {
        // Mock database
        $this->db = $this->createMock(Database::class);
        
        // Create player instance
        $this->player = new Player($this->db, 1);
    }
    
    public function testPlayerInitialization() {
        $this->assertNotNull($this->player);
    }
    
    public function testGetResources() {
        $this->db->method('fetchOne')
                 ->willReturn([
                     'credits' => 1000,
                     'minerals' => 2000,
                     'gas' => 500
                 ]);
        
        $resources = $this->player->getResources();
        
        $this->assertEquals(1000, $resources['credits']);
        $this->assertEquals(2000, $resources['minerals']);
    }
    
    public function testDeductResources() {
        $this->db->expects($this->once())
                 ->method('execute');
        
        $result = $this->player->deductResources([
            'credits' => 100,
            'minerals' => 200
        ]);
        
        $this->assertTrue($result);
    }
    
    public function testCanAttackValidation() {
        // Test various scenarios
        $this->assertTrue($this->player->canAttack(5));
    }
}
```

### phpunit.xml Configuration

```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:noNamespaceSchemaLocation="https://schema.phpunit.de/9.5/phpunit.xsd"
         bootstrap="tests/bootstrap.php"
         cacheResultFile="tests/.phpunit.result.cache"
         colors="true"
         beStrictAboutCoverageMetadata="true"
         failOnRisky="true"
         failOnWarning="true"
         verbose="true">
    <testsuites>
        <testsuite name="Unit Tests">
            <directory>tests/Unit</directory>
        </testsuite>
        <testsuite name="Integration Tests">
            <directory>tests/Integration</directory>
        </testsuite>
    </testsuites>
    
    <coverage processUncoveredFiles="true">
        <include>
            <directory suffix=".php">Index/classes</directory>
        </include>
        <exclude>
            <directory>Index/classes/GameEngine.php</directory>
        </exclude>
        <report>
            <html outputDirectory="coverage"/>
            <text outputFile="php://stdout" showUncoveredFiles="true"/>
        </report>
    </coverage>
</phpunit>
```

### Run Tests

```bash
# Run all tests
vendor/bin/phpunit

# Run specific test file
vendor/bin/phpunit tests/Unit/PlayerTest.php

# Run with coverage
vendor/bin/phpunit --coverage-html=coverage

# Run specific test method
vendor/bin/phpunit --filter testGetResources
```

---

## Integration Testing

### Testing Database Operations

```php
<?php
namespace Tests\Integration;

use PHPUnit\Framework\TestCase;

class DatabaseTest extends TestCase {
    private $db;
    
    protected function setUp(): void {
        $this->db = new Database('localhost', 'test_user', 'test_pass', 'scifi_conquest_test');
        $this->db->beginTransaction();
    }
    
    protected function tearDown(): void {
        $this->db->rollback();
    }
    
    public function testPlayerCreation() {
        $query = "INSERT INTO players (username, email, password_hash) VALUES (?, ?, ?)";
        $result = $this->db->execute($query, [
            'testplayer',
            'test@example.com',
            password_hash('password123', PASSWORD_BCRYPT)
        ]);
        
        $this->assertTrue($result);
        
        // Verify insertion
        $player = $this->db->fetchOne(
            "SELECT * FROM players WHERE username = ?",
            ['testplayer']
        );
        
        $this->assertNotNull($player);
        $this->assertEquals('test@example.com', $player['email']);
    }
    
    public function testFleetCreation() {
        $query = "INSERT INTO fleets (owner_id, name, status) VALUES (?, ?, ?)";
        $result = $this->db->execute($query, [1, 'Test Fleet', 'stationed']);
        
        $this->assertTrue($result);
    }
}
```

---

## API Testing

### Using cURL

```bash
# Test registration endpoint
curl -X POST http://localhost/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "username": "testplayer",
    "email": "test@example.com",
    "password": "SecurePass123"
  }'

# Test player action
curl -X POST http://localhost/api/player/attack \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "fleet_id": 1,
    "target_planet": 5
  }'
```

### Using Postman

1. Import example collection:
```json
{
  "info": {
    "name": "Scifi Conquest API",
    "schema": "https://schema.getpostman.com/json/collection/v2.1.0/collection.json"
  },
  "item": [
    {
      "name": "Register User",
      "request": {
        "method": "POST",
        "header": [
          {
            "key": "Content-Type",
            "value": "application/json"
          }
        ],
        "body": {
          "mode": "raw",
          "raw": "{\"username\": \"test\", \"email\": \"test@example.com\", \"password\": \"pass\"}"
        },
        "url": {
          "raw": "http://localhost/api/auth/register",
          "protocol": "http",
          "host": ["localhost"],
          "path": ["api", "auth", "register"]
        }
      }
    }
  ]
}
```

---

## Performance Testing

### Load Testing with Apache Bench

```bash
# Simple load test: 1000 requests, 10 concurrent
ab -n 1000 -c 10 http://localhost/api/game/status

# With custom header
ab -n 1000 -c 10 -H "Authorization: Bearer TOKEN" http://localhost/api/player
```

### Stress Testing

```php
<?php
// stress_test.php
$maxConcurrent = 50;
$totalRequests = 10000;
$endpoint = 'http://localhost/api/game/status';
$token = 'YOUR_TOKEN';

$completed = 0;
$errors = 0;
$startTime = microtime(true);

for ($i = 0; $i < $totalRequests; $i++) {
    $ch = curl_init($endpoint);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Bearer $token"]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    if ($httpCode === 200) {
        $completed++;
    } else {
        $errors++;
    }
    
    curl_close($ch);
    
    if ($i % 100 === 0) {
        echo "Progress: {$i}/{$totalRequests}\n";
    }
}

$duration = microtime(true) - $startTime;

echo "\n=== Load Test Results ===\n";
echo "Total Requests: $totalRequests\n";
echo "Successful: $completed\n";
echo "Failed: $errors\n";
echo "Duration: " . round($duration, 2) . " seconds\n";
echo "Requests/sec: " . round($totalRequests / $duration, 2) . "\n";
```

---

## Database Testing

### Test Database Setup

```sql
-- Create test database
CREATE DATABASE scifi_conquest_test;

-- Grant permissions
GRANT ALL PRIVILEGES ON scifi_conquest_test.* TO 'test_user'@'localhost';

-- Import schema
USE scifi_conquest_test;
SOURCE ../Db/Dbgame.sql;
```

### Seed Test Data

```php
<?php
// tests/Fixtures/TestData.php

class TestData {
    public static function seedTestPlayers($db, $count = 5) {
        for ($i = 1; $i <= $count; $i++) {
            $db->execute(
                "INSERT INTO players (username, email, password_hash) VALUES (?, ?, ?)",
                [
                    "testplayer$i",
                    "testplayer$i@example.com",
                    password_hash('password123', PASSWORD_BCRYPT)
                ]
            );
        }
    }
    
    public static function seedTestPlanets($db, $playerId, $count = 3) {
        for ($i = 1; $i <= $count; $i++) {
            $db->execute(
                "INSERT INTO planets (owner_id, name, galaxy_id, star_id, planet_type, size) 
                 VALUES (?, ?, ?, ?, ?, ?)",
                [$playerId, "Planet$i", 1, 1, 'terrestrial', 'medium']
            );
        }
    }
}
```

---

## Monitoring & Debugging

### Enable Debug Mode

1. Set in `.env`:
```
DEBUG_MODE=true
LOG_LEVEL=debug
```

2. Create `index.php` wrapper:
```php
<?php
if (defined('DEBUG_MODE') && DEBUG_MODE) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

require_once 'Index/classes/GameEngine.php';
```

### Query Debugging

```php
<?php
// Enable query logging
$db = db();

// Query with logging
$query = "SELECT * FROM players WHERE id = ?";
$startTime = microtime(true);

$result = $db->fetchOne($query, [1]);

$endTime = microtime(true);
$duration = $endTime - $startTime;

logger()->debug("Query executed in {$duration}ms", [
    'query' => $query,
    'duration' => $duration,
    'result_count' => count($result ?? [])
]);
```

### Performance Profiling

```php
<?php
// Simple profiler
class Profiler {
    private static $timers = [];
    
    public static function start($name) {
        self::$timers[$name] = microtime(true);
    }
    
    public static function end($name) {
        if (!isset(self::$timers[$name])) {
            return false;
        }
        
        $duration = microtime(true) - self::$timers[$name];
        unset(self::$timers[$name]);
        
        logger()->debug("$name took " . round($duration * 1000, 2) . "ms");
        
        return $duration;
    }
}

// Usage
Profiler::start('player_load');
$player = new Player(db(), 1);
Profiler::end('player_load');

Profiler::start('resource_fetch');
$resources = $player->getResources();
Profiler::end('resource_fetch');
```

---

## Continuous Integration

### GitHub Actions Workflow

```yaml
# .github/workflows/tests.yml
name: Tests

on: [push, pull_request]

jobs:
  test:
    runs-on: ubuntu-latest
    
    services:
      mysql:
        image: mysql:5.7
        env:
          MYSQL_ROOT_PASSWORD: root
          MYSQL_DATABASE: scifi_conquest_test
        options: >-
          --health-cmd="mysqladmin ping"
          --health-interval=10s
          --health-timeout=5s
          --health-retries=3
        ports:
          - 3306:3306
    
    steps:
      - uses: actions/checkout@v2
      
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.0'
          extensions: mysql, pdo_mysql
      
      - name: Install dependencies
        run: composer install
      
      - name: Create test database
        run: |
          mysql -h 127.0.0.1 -u root -proot -e "CREATE DATABASE scifi_conquest_test"
          mysql -h 127.0.0.1 -u root -proot scifi_conquest_test < Db/Dbgame.sql
      
      - name: Run tests
        run: vendor/bin/phpunit
        env:
          DB_HOST: 127.0.0.1
          DB_USER: root
          DB_PASSWORD: root
          DB_NAME: scifi_conquest_test
```

---

## Manual Testing Checklist

### Login Flow
- [ ] Register new account
- [ ] Verify email (if enabled)
- [ ] Login with correct credentials
- [ ] Reject login with wrong password
- [ ] Handle suspended/banned accounts
- [ ] Session persistence across pages
- [ ] Logout functionality

### Game Play
- [ ] Create planets/colonies
- [ ] Build structures
- [ ] Manage resources
- [ ] Attack other players
- [ ] Research technologies
- [ ] Join alliance
- [ ] Send/receive messages

### Admin Functions
- [ ] View server statistics
- [ ] Manage player accounts
- [ ] Issue warnings
- [ ] Ban/unban players
- [ ] View system logs
- [ ] Clear caches
- [ ] Generate reports

### Performance
- [ ] Load times < 2 seconds
- [ ] Database queries log
- [ ] Cache hit rates
- [ ] Memory usage stable
- [ ] No memory leaks over time

---

## Common Issues & Fixes

### Issue: Database Connection Fails
```bash
# Check MySQL is running
sudo service mysql status

# Verify credentials
mysql -u user -p -h localhost

# Check user permissions
mysql -u root -p
> GRANT ALL PRIVILEGES ON scifi_conquest.* TO 'game_user'@'localhost';
```

### Issue: Classes Not Found
```php
// Ensure class files exist and are named correctly
// Class name should match filename exactly
// PlayerClass.php → class PlayerClass
```

### Issue: Cache Not Working
```bash
# Verify cache directory
ls -la cache/

# Set permissions
chmod 755 cache/
chmod 777 cache/

# Clear cache
rm -rf cache/*
```

---

**Last Updated:** 2024
**Version:** 1.0
