<?php
/**
 * Empire Page - Overview of player's empire
 */

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$player = new Player($_SESSION['user_id']);
$playerData = $player->getData();
$planets = $player->getPlanets() ?? [];
$currentPlanetId = null;
$currentPlanet = null;
$planetData = null;
$buildings = [];
$production = ['metal' => 0, 'crystal' => 0, 'deuterium' => 0];
$buildingQueue = [];

// Validate planets exist
if (empty($planets)) {
    // No planets - show empty state or error
    $errorMessage = "No planets in your empire. Create a new planet to get started.";
} else {
    // Get the current planet ID from URL or use first planet
    $currentPlanetId = isset($_GET['planet']) ? (int)$_GET['planet'] : $planets[0]['id'];
    
    // Validate planet exists for this player
    $validPlanet = false;
    foreach ($planets as $p) {
        if ($p['id'] == $currentPlanetId) {
            $validPlanet = true;
            break;
        }
    }
    
    if (!$validPlanet) {
        $currentPlanetId = $planets[0]['id'];
    }
    
    // Load planet data
    $currentPlanet = new Planet($currentPlanetId);
    $planetData = $currentPlanet->getData();
    
    if ($planetData) {
        $buildings = $currentPlanet->getBuildings() ?? [];
        $production = $currentPlanet->getProduction() ?? ['metal' => 0, 'crystal' => 0, 'deuterium' => 0];
        
        // Get building queue
        $db = Database::getInstance();
        $buildingQueue = $db->fetchAll("SELECT * FROM building_queue WHERE planet_id = ? ORDER BY completion_time ASC", [$currentPlanetId]) ?? [];
    }
}
?>

<div class="empire-page">
    <div class="page-header">
        <h1>Empire Overview</h1>
        <p>Manage your planets and view your empire's status</p>
    </div>
    
    <!-- Error Message -->
    <?php if (isset($errorMessage)): ?>
    <div style="background: rgba(255, 100, 100, 0.2); border: 2px solid #ff6b6b; padding: 15px; border-radius: 10px; margin-bottom: 20px; color: #ff6b6b;">
        <strong>⚠️ <?php echo $errorMessage; ?></strong>
    </div>
    <?php endif; ?>
    
    <!-- Planet Selector -->
    <?php if (!empty($planets)): ?>
    <div class="planet-selector" style="margin-bottom: 20px;">
        <label style="color: #4a9eff; font-weight: bold;">Select Planet:</label>
        <select onchange="window.location.href='?page=empire&planet=' + this.value" style="padding: 8px; background: rgba(10, 10, 30, 0.8); color: #fff; border: 1px solid #4a9eff; border-radius: 5px;">
            <?php foreach ($planets as $planet): ?>
                <option value="<?php echo $planet['id']; ?>" <?php echo $planet['id'] == $currentPlanetId ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($planet['name'] ?? 'Unnamed'); ?> [<?php echo ($planet['galaxy'] ?? 0); ?>:<?php echo ($planet['system'] ?? 0); ?>:<?php echo ($planet['position'] ?? 0); ?>]
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <?php endif; ?>
    
    <!-- Planet Info -->
    <?php if ($planetData): ?>
    <div class="info-panel" style="background: rgba(10, 10, 30, 0.8); padding: 20px; border-radius: 10px; margin-bottom: 20px; border: 1px solid #4a9eff;">
        <h2 style="color: #4a9eff; margin-bottom: 15px;"><?php echo htmlspecialchars($planetData['name'] ?? 'Unknown Planet'); ?></h2>
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px;">
            <div>
                <strong style="color: #4a9eff;">Coordinates:</strong> 
                [<?php echo ($planetData['galaxy'] ?? 0); ?>:<?php echo ($planetData['system'] ?? 0); ?>:<?php echo ($planetData['position'] ?? 0); ?>]
            </div>
            <div>
                <strong style="color: #4a9eff;">Diameter:</strong> 
                <?php echo number_format($planetData['diameter'] ?? 0); ?> km
            </div>
            <div>
                <strong style="color: #4a9eff;">Temperature:</strong> 
                <?php echo ($planetData['temperature'] ?? 0); ?>°C
            </div>
            <div>
                <strong style="color: #4a9eff;">Fields:</strong> 
                <?php echo ($planetData['fields_used'] ?? 0); ?> / <?php echo ($planetData['fields'] ?? 0); ?>
            </div>
        </div>
    </div>
    
    <!-- Resource Production -->
    <div class="production-panel" style="background: rgba(10, 10, 30, 0.8); padding: 20px; border-radius: 10px; margin-bottom: 20px; border: 1px solid #4a9eff;">
        <h2 style="color: #4a9eff; margin-bottom: 15px;">Resource Production (per hour)</h2>
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px;">
            <div>
                <strong style="color: #4a9eff;">Metal:</strong> 
                +<?php echo number_format($production['metal'] ?? 0); ?>
            </div>
            <div>
                <strong style="color: #4a9eff;">Crystal:</strong> 
                +<?php echo number_format($production['crystal'] ?? 0); ?>
            </div>
            <div>
                <strong style="color: #4a9eff;">Deuterium:</strong> 
                +<?php echo number_format($production['deuterium'] ?? 0); ?>
            </div>
        </div>
    </div>
    
    <!-- Building Queue -->
    <?php if (!empty($buildingQueue)): ?>
    <div class="building-queue" style="background: rgba(10, 10, 30, 0.8); padding: 20px; border-radius: 10px; margin-bottom: 20px; border: 1px solid #4a9eff;">
        <h2 style="color: #4a9eff; margin-bottom: 15px;">Building Queue</h2>
        <?php foreach ($buildingQueue as $item): ?>
            <div style="padding: 10px; background: rgba(20, 20, 40, 0.9); border-radius: 5px; margin-bottom: 10px;">
                <strong style="color: #4a9eff;"><?php echo ucwords(str_replace('_', ' ', $item['building_type'] ?? 'Unknown')); ?></strong>
                (Level <?php echo $item['level'] ?? 0; ?>)
                - Completes in: <?php echo gmdate('H:i:s', max(0, ($item['completion_time'] ?? time()) - time())); ?>
            </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
    
    <!-- Buildings -->
    <div class="buildings-panel" style="background: rgba(10, 10, 30, 0.8); padding: 20px; border-radius: 10px; border: 1px solid #4a9eff;">
        <h2 style="color: #4a9eff; margin-bottom: 15px;">Buildings</h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 15px;">
            <?php
            $buildingTypes = ['metal_mine', 'crystal_mine', 'deuterium_synthesizer', 'solar_plant', 'fusion_reactor', 
                            'robotics_factory', 'shipyard', 'research_lab', 'alliance_depot', 'missile_silo', 'nanite_factory'];
            
            foreach ($buildingTypes as $buildingType):
                $level = 0;
                foreach ($buildings as $building) {
                    if ($building['building_type'] == $buildingType) {
                        $level = $building['level'];
                        break;
                    }
                }
                $nextLevel = $level + 1;
            ?>
            <div style="padding: 15px; background: rgba(20, 20, 40, 0.9); border-radius: 5px; border: 1px solid #4a9eff;">
                <h3 style="color: #4a9eff; margin-bottom: 10px;"><?php echo ucwords(str_replace('_', ' ', $buildingType)); ?></h3>
                <p style="margin-bottom: 10px;">Current Level: <strong><?php echo $level; ?></strong></p>
                <?php if ($level < 30): ?>
                <form method="POST" action="" style="margin-top: 10px;">
                    <input type="hidden" name="building_type" value="<?php echo $buildingType; ?>">
                    <input type="hidden" name="planet_id" value="<?php echo $currentPlanetId; ?>">
                    <button type="submit" name="upgrade_building" class="btn" style="width: 100%;">
                        Upgrade to Level <?php echo $nextLevel; ?>
                    </button>
                </form>
                <?php else: ?>
                <p style="color: #aaa;">Max level reached</p>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php else: ?>
    <div style="background: rgba(74, 158, 255, 0.1); border: 2px solid #4a9eff; padding: 30px; border-radius: 10px; text-align: center;">
        <h2 style="color: #4a9eff; margin-bottom: 10px;">No Planets Available</h2>
        <p style="color: #aaa;">Create your first planet to begin your empire.</p>
    </div>
    <?php endif; ?>
</div>

<?php
// Handle building upgrade
if (isset($_POST['upgrade_building'])) {
    $buildingType = $_POST['building_type'];
    $planetId = $_POST['planet_id'];
    
    $planet = new Planet($planetId);
    $result = $planet->upgradeBuilding($buildingType, $player);
    
    if ($result['success']) {
        echo "<script>alert('" . $result['message'] . "'); window.location.href='?page=empire&planet=$planetId';</script>";
    } else {
        echo "<script>alert('Error: " . $result['message'] . "');</script>";
    }
}
?>
