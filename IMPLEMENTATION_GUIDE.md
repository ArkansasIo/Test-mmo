================================================================================
    IMPLEMENTATION GUIDE & DEVELOPER HANDBOOK
    Sci-Fi Conquest: Awakening - Building the Game
================================================================================

VERSION: 1.0
STATUS: Ready for Development
TARGET: Full implementation in Phase 2-4

================================================================================
SECTION 1: QUICK START FOR DEVELOPERS
================================================================================

You have 3 comprehensive spec documents:

1. GAME_ENGINE_SPECIFICATION.md
   ├─ What pages exist and what they do
   ├─ Game systems & mechanics
   ├─ Database structure overview
   ├─ Class architecture design
   └─ Use for: Understanding overall architecture

2. UI_COMPONENT_SPECIFICATIONS.md
   ├─ How to build UI components
   ├─ CSS classes and styling
   ├─ JavaScript utilities
   ├─ Accessibility requirements
   └─ Use for: Building front-end pages

3. DATABASE_SCHEMA_SPECIFICATION.md
   ├─ Exact table definitions
   ├─ Column types, constraints, indexes
   ├─ Relationships & keys
   ├─ Query optimization
   └─ Use for: Database operations

WORKFLOW: Database → Backend Classes → Frontend Pages

================================================================================
SECTION 2: ADDING A NEW PAGE - STEP BY STEP
================================================================================

Example: Implementing the "Planet Details" page (/pages/planet-details.php)

STEP 1: Read the Specifications
─────────────────────────────────

From GAME_ENGINE_SPECIFICATION.md, find:

"15. PLANET DETAILS PAGE /pages/planet-details.php
    Purpose: Detailed planet information and management
    Sub-sections:
    - Buildings on planet
    - Current production
    - Defense structures
    - Population stats
    - Resource storage/capacity
    - Planet type/characteristics
    - Rename planet
    - Colonization options"

Key Features to implement:
├─ Display all buildings on planet
├─ Show production rates per hour
├─ Defense count display
├─ Resource levels with progress bars
├─ Rename button with modal
└─ Colonization/expansion options

STEP 2: Layout Design (Using UI Components)
────────────────────────────────────────────

From UI_COMPONENT_SPECIFICATIONS.md, build using:

1. PAGE HEADER Component
   <div class="page-header">
       <h1>Planet: [Planet Name]</h1>
       <p>Galaxy 1, System 100, Position 5</p>
   </div>

2. PLANET INFO PANEL
   <div class="card">
       <div class="card-header">
           <h3>Planet Information</h3>
       </div>
       <div class="card-body">
           <div class="card-stat">
               <span class="stat-label">Type:</span>
               <span class="stat-value">Terrestrial</span>
           </div>
           <!-- More stats -->
       </div>
   </div>

3. PRODUCTION CARDS GRID
   <div class="building-grid">
       <div class="building-card">
           <!-- Metal Mine card -->
       </div>
       <!-- More buildings -->
   </div>

4. DEFENSE PANEL
   <div class="info-panel">
       <h2>Defenses</h2>
       <!-- Defense structures list -->
   </div>

STEP 3: Create the PHP File
──────────────────────────

File: Index/pages/planet-details.php

Template:

<?php
/**
 * Planet Details - Display and manage specific planet
 */

// Check authentication
SessionManager::verify();

// Get planet ID from URL
$planet_id = $_GET['planet_id'] ?? 0;
if (!$planet_id) {
    header('Location: empire.php');
    exit;
}

// Load player data
$player = new Player($_SESSION['user_id']);
$planet = new Planet($planet_id);

// Verify ownership
if ($planet->user_id !== $player->id) {
    Logger::log('error', 'Unauthorized planet access', ['player_id' => $player->id, 'planet_id' => $planet_id]);
    die('Access denied');
}

// Get planet details
$planetData = $planet->getData();
$buildings = $planet->getBuildings();
$defenses = $planet->getDefenses();
?>

<!-- Include via menu.php template -->

<div class="main-content">
    
    <!-- PAGE HEADER -->
    <div class="page-header">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h1><?php echo htmlspecialchars($planetData['name']); ?></h1>
                <p>Galaxy <?php echo $planetData['galaxy']; ?>, 
                   System <?php echo $planetData['system']; ?>, 
                   Position <?php echo $planetData['position']; ?></p>
            </div>
            <button class="btn btn-secondary" onclick="openRenameModal()">Rename</button>
        </div>
    </div>

    <!-- PLANET INFO SECTION -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Planet Information</h3>
            <span class="badge badge-info"><?php echo $planetData['type']; ?></span>
        </div>
        <div class="card-body">
            <div class="card-stat">
                <span class="stat-label">Type:</span>
                <span class="stat-value"><?php echo ucfirst($planetData['type']); ?></span>
            </div>
            <div class="card-stat">
                <span class="stat-label">Temperature:</span>
                <span class="stat-value"><?php echo $planetData['temperature']; ?>°C</span>
            </div>
            <div class="card-stat">
                <span class="stat-label">Diameter:</span>
                <span class="stat-value"><?php echo number_format($planetData['diameter']); ?> km</span>
            </div>
            <div class="card-stat">
                <span class="stat-label">Created:</span>
                <span class="stat-value"><?php echo date('Y-m-d', strtotime($planetData['created_at'])); ?></span>
            </div>
        </div>
    </div>

    <!-- PRODUCTION SECTION -->
    <div class="production-panel">
        <h2>Production Overview</h2>
        <div class="production-grid">
            <div class="production-item">
                <span class="prod-icon">⛏️</span>
                <span class="prod-label">Metal/hour</span>
                <span class="prod-value"><?php echo number_format($planetData['metal_production'], 2); ?></span>
            </div>
            <div class="production-item">
                <span class="prod-icon">💎</span>
                <span class="prod-label">Crystal/hour</span>
                <span class="prod-value"><?php echo number_format($planetData['crystal_production'], 2); ?></span>
            </div>
            <div class="production-item">
                <span class="prod-icon">🧪</span>
                <span class="prod-label">Deuterium/hour</span>
                <span class="prod-value"><?php echo number_format($planetData['deuterium_production'], 2); ?></span>
            </div>
            <div class="production-item">
                <span class="prod-icon">⚡</span>
                <span class="prod-label">Energy</span>
                <span class="prod-value"><?php echo $planetData['energy_production']; ?> units</span>
            </div>
        </div>
    </div>

    <!-- RESOURCES SECTION -->
    <div class="info-panel">
        <h2>Resources</h2>
        <div class="resource-breakdown">
            <div class="resource-row">
                <span class="res-label">Metal:</span>
                <div class="progress">
                    <div class="progress-bar progress-primary" 
                         style="width: <?php echo ($planetData['current_metal'] / $planetData['metal_storage'] * 100); ?>%">
                        <span class="progress-label">
                            <?php echo number_format($planetData['current_metal'], 0); ?> 
                            / <?php echo number_format($planetData['metal_storage'], 0); ?>
                        </span>
                    </div>
                </div>
            </div>
            <div class="resource-row">
                <span class="res-label">Crystal:</span>
                <div class="progress">
                    <div class="progress-bar progress-primary" 
                         style="width: <?php echo ($planetData['current_crystal'] / $planetData['crystal_storage'] * 100); ?>%">
                        <span class="progress-label">
                            <?php echo number_format($planetData['current_crystal'], 0); ?> 
                            / <?php echo number_format($planetData['crystal_storage'], 0); ?>
                        </span>
                    </div>
                </div>
            </div>
            <div class="resource-row">
                <span class="res-label">Deuterium:</span>
                <div class="progress">
                    <div class="progress-bar progress-primary" 
                         style="width: <?php echo ($planetData['current_deuterium'] / $planetData['deuterium_storage'] * 100); ?>%">
                        <span class="progress-label">
                            <?php echo number_format($planetData['current_deuterium'], 0); ?> 
                            / <?php echo number_format($planetData['deuterium_storage'], 0); ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- BUILDINGS SECTION -->
    <div class="buildings-panel">
        <h2>Buildings</h2>
        <div class="building-grid">
            <?php foreach ($buildings as $building): ?>
                <div class="building-card">
                    <div class="building-icon"><?php echo getBuildingIcon($building['type_id']); ?></div>
                    <div class="building-name"><?php echo $building['name']; ?></div>
                    <div class="building-level">Level <?php echo $building['level']; ?></div>
                    <button class="btn btn-sm btn-primary" 
                            onclick="showBuildingDetails(<?php echo $building['id']; ?>)">
                        Details
                    </button>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- DEFENSE SECTION -->
    <div class="defense-panel">
        <h2>Defenses</h2>
        <div class="defense-list">
            <?php if (count($defenses) > 0): ?>
                <table class="defense-table">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Quantity</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($defenses as $defense): ?>
                            <tr>
                                <td><?php echo $defense['name']; ?></td>
                                <td><?php echo $defense['quantity']; ?></td>
                                <td><span class="badge <?php echo getBadgeClass($defense['status']); ?>">
                                    <?php echo ucfirst($defense['status']); ?>
                                </span></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="no-data">No defenses constructed</p>
            <?php endif; ?>
        </div>
    </div>

</div>

<!-- MODALS -->

<!-- Rename Planet Modal -->
<div class="modal" id="renameModal">
    <div class="modal-backdrop"></div>
    <div class="modal-content">
        <div class="modal-header">
            <h2>Rename Planet</h2>
            <button class="modal-close" onclick="closeModal('renameModal')">×</button>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label class="form-label">New Planet Name</label>
                <input type="text" id="planetName" class="form-control" 
                       placeholder="Enter new name" value="<?php echo htmlspecialchars($planetData['name']); ?>">
                <small class="form-text">Max 100 characters</small>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('renameModal')">Cancel</button>
            <button class="btn btn-primary" onclick="renamePlanet()">Rename</button>
        </div>
    </div>
</div>

<!-- JavaScript -->
<script>
function openRenameModal() {
    document.getElementById('renameModal').classList.add('active');
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.remove('active');
}

function renamePlanet() {
    const newName = document.getElementById('planetName').value;
    if (!newName || newName.length === 0) {
        showAlert('error', 'Planet name cannot be empty');
        return;
    }
    if (newName.length > 100) {
        showAlert('error', 'Planet name too long (max 100 chars)');
        return;
    }

    // Send AJAX request
    fetch('/Index/api/planet-rename.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            planet_id: <?php echo $planet_id; ?>,
            name: newName
        })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showAlert('success', 'Planet renamed successfully');
            setTimeout(() => location.reload(), 1500);
        } else {
            showAlert('error', data.message || 'Failed to rename planet');
        }
    })
    .catch(err => showAlert('error', 'Request failed: ' + err));
}

function showBuildingDetails(buildingId) {
    // TODO: Implement building details modal
    showAlert('info', 'Building details - ' + buildingId);
}
</script>

STEP 4: Create Backend API (if needed)
──────────────────────────────────────

File: Index/api/planet-rename.php

<?php
SessionManager::verify();
$player = new Player($_SESSION['user_id']);

$data = json_decode(file_get_contents('php://input'), true);
$planet_id = $data['planet_id'] ?? 0;
$name = trim($data['name'] ?? '');

// Validate
if (!$planet_id || strlen($name) === 0 || strlen($name) > 100) {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit;
}

// Check ownership
$planet = new Planet($planet_id);
if ($planet->user_id !== $player->id) {
    Logger::log('error', 'Unauthorized planet rename', ['planet_id' => $planet_id]);
    echo json_encode(['success' => false, 'message' => 'Access denied']);
    exit;
}

// Update planet name
try {
    $planet->rename($name);
    Logger::log('info', 'Planet renamed', ['planet_id' => $planet_id, 'old_name' => $planet->name, 'new_name' => $name]);
    echo json_encode(['success' => true, 'message' => 'Planet renamed']);
} catch (Exception $e) {
    Logger::log('error', 'Planet rename failed', ['error' => $e->getMessage()]);
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
?>

STEP 5: Update Navigation (menu.php)
─────────────────────────────────────

Add to LEFT SIDEBAR menu:

// In Index/templates/menu.php, add to PLANETS section:
<a href="?page=planet-details&planet_id=<?php echo $planet['id']; ?>">
    <span class="menu-icon">🌍</span>
    <span class="menu-label"><?php echo $planet['name']; ?></span>
</a>

STEP 6: Testing
───────────────

Manual Testing:
✅ Visit /Index/index.php?page=planet-details&planet_id=1
✅ Verify all planet data displays
✅ Test rename functionality
✅ Test responsive design (mobile)
✅ Check console for JS errors
✅ Verify database queries work

Unit Testing (PHP):
<?php
// In tests/PlanetDetailsTest.php
class PlanetDetailsTest {
    public function testPageLoads() {
        $page = file_get_contents('/Index/pages/planet-details.php');
        $this->assertNotEmpty($page);
    }
    
    public function testPlanetDataRetrieved() {
        $planet = new Planet(1);
        $data = $planet->getData();
        $this->assertNotNull($data);
    }
}
?>

================================================================================
SECTION 3: ADDING A NEW GAME SYSTEM - EXAMPLE: COMBAT
================================================================================

The Battle System needs:

1. CLASS STRUCTURE
─────────────────

File: Index/classes/Battle.php

<?php
class Battle {
    private $attacker_fleet;
    private $defender_fleet;
    private $planet;
    private $rounds = 0;
    private $max_rounds = 10;
    private $combatlog = [];

    public function __construct(Fleet $attacker, Fleet $defender, Planet $planet) {
        $this->attacker_fleet = $attacker;
        $this->defender_fleet = $defender;
        $this->planet = $planet;
    }

    public function simulate() {
        while ($this->rounds < $this->max_rounds && $this->continue_battle()) {
            $this->executeRound();
            $this->rounds++;
        }
        return $this->getResult();
    }

    private function executeRound() {
        // Calculate damage for this round
        $attacker_damage = $this->calculateDamage($this->attacker_fleet, $this->defender_fleet);
        $defender_damage = $this->calculateDamage($this->defender_fleet, $this->attacker_fleet);

        // Apply damage
        $this->applyDamage($this->attacker_fleet, $defender_damage);
        $this->applyDamage($this->defender_fleet, $attacker_damage);

        // Log round
        $this->addCombatLog("Round {$this->rounds}: ATK dmg={$attacker_damage}, DEF dmg={$defender_damage}");
    }

    private function calculateDamage(Fleet $attacker, Fleet $defender) {
        $power = $attacker->calculateCombatPower();
        $randomFactor = 0.8 + (rand(0, 40) / 100); // 0.8 - 1.2
        $baseDamage = $power * $randomFactor;

        // Apply armor reduction
        $armorBonus = $defender->getArmorBonus(); // From armor tech
        $finalDamage = max(0, $baseDamage - $armorBonus);

        return $finalDamage;
    }

    public function getResult() {
        return [
            'attacker_losses' => $this->attacker_fleet->getDestroyedShips(),
            'defender_losses' => $this->defender_fleet->getDestroyedShips(),
            'plunder' => $this->calculatePlunder(),
            'debris' => $this->calculateDebris(),
            'winner' => $this->determineWinner(),
            'report' => implode("\n", $this->combatlog)
        ];
    }
}
?>

2. DATABASE TABLES
──────────────────

Tables needed:
├─ battles (battle records)
├─ battle_damage (round-by-round damage)
└─ battle_report (detailed reports for players)

Already defined in DATABASE_SCHEMA_SPECIFICATION.md

3. PAGE IMPLEMENTATION
──────────────────────

File: Index/pages/battle-simulator.php
├─ Display attacker fleet selector
├─ Display defender fleet selector
├─ [Simulate] button
├─ Show predicted outcome
└─ Display casualty estimates

4. API ENDPOINT
────────────────

File: Index/api/battle-simulate.php
├─ POST: attacker_fleet_id, defender_fleet_id
├─ Call: new Battle(...)->simulate()
├─ Return: JSON with result
└─ Don't modify data (simulation only)

================================================================================
SECTION 4: COMMON PATTERNS & UTILITIES
================================================================================

DATA ACCESS PATTERN (DAO Pattern):

// Getting user resources
$player = new Player($user_id);
$resources = $player->getResources();
// Returns: ['metal' => 1000, 'crystal' => 500, 'deuterium' => 100]

// Add resources
$player->addResources(500, 250, 50);

// Remove resources  
$player->removeResources(100, 50, 10);

TEMPLATE PATTERN (for pages):

<?php header('Content-Type: text/html; charset=utf-8'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- SEO, meta tags -->
</head>
<body>
    <?php include 'templates/menu.php'; ?>
    
    <div class="main-content">
        <!-- Page content -->
    </div>
</body>
</html>

ERROR HANDLING PATTERN:

try {
    $result = $someObject->doAction();
    Logger::log('info', 'Action completed', ['result' => $result]);
} catch (ValidateException $e) {
    Logger::log('warning', 'Validation failed', ['error' => $e->getMessage()]);
    echo "Validation error: " . htmlspecialchars($e->getMessage());
} catch (Exception $e) {
    Logger::log('error', 'Unexpected error', ['error' => $e->getMessage(), 'stack' => $e->getTraceAsString()]);
    echo "An error occurred. Please try again.";
}

JSON API PATTERN:

<?php
header('Content-Type: application/json');

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Validate input
    if (!isset($data['required_field'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Missing field']);
        exit;
    }

    // Process
    $result = processData($data);

    // Return success
    http_response_code(200);
    echo json_encode(['success' => true, 'data' => $result]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>

================================================================================
SECTION 5: QUALITY CHECKLIST
================================================================================

Before committing any new page/feature:

FUNCTIONALITY:
☑ Feature works as specified
☑ All user inputs validated
☑ Database queries work
☑ Error handling implemented
☑ Edge cases handled (0 resources, max level, etc.)

CODE QUALITY:
☑ Follows naming conventions
☑ Comments on complex logic
☑ No hardcoded values (use constants)
☑ SQL injection protected (parameterized queries)
☑ No PHP warnings/errors
☑ Logging added for important actions

SECURITY:
☑ Authentication checked
☑ Authorization verified (ownership/permissions)
☑ XSS protection (htmlspecialchars, escaping)
☑ CSRF tokens on forms (if not AJAX)
☑ Rate limiting on APIs (if applicable)
☑ No sensitive data in logs

USABILITY:
☑ Mobile responsive
☑ Clear user feedback (success, error msgs)
☑ Accessible (WCAG AA compliant)
☑ Performance (< 2s load, <100ms response)
☑ Keyboard navigation works
☑ Intuitive UI/UX

DOCUMENTATION:
☑ Code comments explain "why" not "what"
☑ Function docblocks complete
☑ README updated (if file structure changed)
☑ Game spec updated (if mechanics changed)

================================================================================
SECTION 6: DEPLOYMENT CHECKLIST
================================================================================

Before deploying to production:

CODE REVIEW:
☑ Another developer reviewed code
☑ No security vulnerabilities
☑ No breaking changes
☑ Database migrations tested

TESTING:
☑ All new features work
☑ No regression in existing features
☑ Cross-browser tested
☑ Mobile tested
☑ Load testing passed (if critical)

DOCUMENTATION:
☑ Game documentation updated
☑ API documentation updated
☑ Deployment notes created
☑ Rollback plan documented

DEPLOYMENT:
☑ Backup created
☑ Database migrated
☑ Code deployed
☑ Cache cleared
☑ Tests run in production
☑ Monitor for errors (24h observation)

ROLLBACK:
☑ If critical errors: Restore from backup
☑ If minor bugs: Deploy patches
☑ Log all issues discovered

================================================================================
SECTION 7: GETTING HELP
================================================================================

Resource Files:

1. GAME_ENGINE_SPECIFICATION.md
   Use when: "What is the [page/system] supposed to do?"
   Find: Detailed descriptions, formulas, mechanics

2. UI_COMPONENT_SPECIFICATIONS.md
   Use when: "How do I build the UI?"
   Find: CSS classes, component structure, examples

3. DATABASE_SCHEMA_SPECIFICATION.md
   Use when: "What tables/fields do I need?"
   Find: Table structure, keys, relationships

4. Code Examples:
   Look in: Index/pages/empire.php (working page)
   Look in: Index/classes/Player.php (working class)

5. Logger Output:
   Check: Logs/game.log (for recent errors/actions)
   Use: Logger::log('info', 'message', ['context_data']);

================================================================================
NEXT STEPS FOR DEVELOPMENT
================================================================================

PHASE 2 - PRIORITY (Ready to implement):

1. Planet Details Page (/pages/planet-details.php)
   ├─ Time estimate: 6-8 hours
   └─ Dependencies: Planet class (exists)

2. Buildings Page (/pages/buildings.php)
   ├─ Time estimate: 12-15 hours
   ├─ Dependencies: Building class, production calc
   └─ Complexity: Medium (build queue management)

3. Espionage Page (/pages/espionage.php)
   ├─ Time estimate: 10-12 hours
   ├─ Dependencies: Fleet class, new Spy mechanics
   └─ Complexity: High (intel reports)

4. Defense Report Page (/pages/defense-report.php)
   ├─ Time estimate: 8-10 hours
   ├─ Dependencies: Battle class
   └─ Complexity: Medium

5. Settings Page (/pages/settings.php)
   ├─ Time estimate: 10-12 hours
   ├─ Dependencies: SessionManager updates
   └─ Complexity: Low-Medium

PHASE 3 - ADVANCED (After Phase 2):

- Battle Simulator (prediction system)
- Alliance war system
- Marketplace with negotiations
- Real-time production tick
- Achievement system

PHASE 4 - POLISH (Final):

- Performance optimization
- Mobile app compatibility
- Analytics & monitoring
- Load testing at scale
- Community features

================================================================================
