<?php
/**
 * Shipyard Page - Build ships and defenses
 */

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$player = new Player($_SESSION['user_id']);
$planets = $player->getPlanets();
$currentPlanetId = isset($_GET['planet']) ? $_GET['planet'] : $planets[0]['id'];
$db = Database::getInstance();

// Get planet ships and defenses
$planetShips = $db->fetchAll("SELECT * FROM planet_ships WHERE planet_id = ?", [$currentPlanetId]);
$planetDefenses = $db->fetchAll("SELECT * FROM planet_defenses WHERE planet_id = ?", [$currentPlanetId]);

// Handle ship building
if (isset($_POST['build_ship'])) {
    require_once '../classes/ShipProduction.php';
    $shipProduction = new ShipProduction();
    
    $shipType = $_POST['ship_type'];
    $amount = (int)$_POST['amount'];
    
    $result = $shipProduction->buildShips($currentPlanetId, $shipType, $amount, $player);
    
    if ($result['success']) {
        echo "<script>alert('" . $result['message'] . "'); window.location.href='?page=shipyard&planet=$currentPlanetId';</script>";
    } else {
        echo "<script>alert('Error: " . $result['message'] . "');</script>";
    }
}

// Handle defense building
if (isset($_POST['build_defense'])) {
    require_once '../classes/ShipProduction.php';
    $shipProduction = new ShipProduction();
    
    $defenseType = $_POST['defense_type'];
    $amount = (int)$_POST['amount'];
    
    $result = $shipProduction->buildDefense($currentPlanetId, $defenseType, $amount, $player);
    
    if ($result['success']) {
        echo "<script>alert('" . $result['message'] . "'); window.location.href='?page=shipyard&planet=$currentPlanetId';</script>";
    } else {
        echo "<script>alert('Error: " . $result['message'] . "');</script>";
    }
}
?>

<div class="shipyard-page">
    <div class="page-header">
        <h1>Shipyard</h1>
        <p>Build ships and defenses</p>
    </div>
    
    <!-- Available Ships -->
    <div class="ship-hangar" style="background: rgba(10, 10, 30, 0.8); padding: 20px; border-radius: 10px; margin-bottom: 20px; border: 1px solid #4a9eff;">
        <h2 style="color: #4a9eff; margin-bottom: 15px;">Available Ships</h2>
        <?php if (!empty($planetShips)): ?>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 15px;">
            <?php foreach ($planetShips as $ship): ?>
            <div style="padding: 15px; background: rgba(20, 20, 40, 0.9); border-radius: 5px; border: 1px solid #4a9eff; text-align: center;">
                <h3 style="color: #4a9eff; margin-bottom: 10px;"><?php echo ucwords(str_replace('_', ' ', $ship['ship_type'])); ?></h3>
                <p style="font-size: 24px; font-weight: bold; margin: 10px 0;"><?php echo number_format($ship['amount']); ?></p>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <p style="color: #aaa;">No ships built yet.</p>
        <?php endif; ?>
    </div>
    
    <!-- Build Ships -->
    <div class="build-ships" style="background: rgba(10, 10, 30, 0.8); padding: 20px; border-radius: 10px; margin-bottom: 20px; border: 1px solid #4a9eff;">
        <h2 style="color: #4a9eff; margin-bottom: 15px;">Build Ships</h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 15px;">
            <?php
            $shipTypes = [
                'small_cargo' => ['metal' => 2000, 'crystal' => 2000, 'deuterium' => 0],
                'large_cargo' => ['metal' => 6000, 'crystal' => 6000, 'deuterium' => 0],
                'light_fighter' => ['metal' => 3000, 'crystal' => 1000, 'deuterium' => 0],
                'heavy_fighter' => ['metal' => 6000, 'crystal' => 4000, 'deuterium' => 0],
                'cruiser' => ['metal' => 20000, 'crystal' => 7000, 'deuterium' => 2000],
                'battleship' => ['metal' => 45000, 'crystal' => 15000, 'deuterium' => 0],
                'colony_ship' => ['metal' => 10000, 'crystal' => 20000, 'deuterium' => 10000],
                'recycler' => ['metal' => 10000, 'crystal' => 6000, 'deuterium' => 2000],
                'espionage_probe' => ['metal' => 0, 'crystal' => 1000, 'deuterium' => 0],
                'bomber' => ['metal' => 50000, 'crystal' => 25000, 'deuterium' => 15000],
                'destroyer' => ['metal' => 60000, 'crystal' => 50000, 'deuterium' => 15000],
                'battlecruiser' => ['metal' => 30000, 'crystal' => 40000, 'deuterium' => 15000]
            ];
            
            foreach ($shipTypes as $shipType => $cost):
            ?>
            <div style="padding: 15px; background: rgba(20, 20, 40, 0.9); border-radius: 5px; border: 1px solid #4a9eff;">
                <h3 style="color: #4a9eff; margin-bottom: 10px;"><?php echo ucwords(str_replace('_', ' ', $shipType)); ?></h3>
                <p style="font-size: 12px; color: #aaa; margin-bottom: 10px;">
                    Cost: <?php echo number_format($cost['metal']); ?> M, 
                    <?php echo number_format($cost['crystal']); ?> C, 
                    <?php echo number_format($cost['deuterium']); ?> D
                </p>
                <form method="POST" action="">
                    <input type="hidden" name="ship_type" value="<?php echo $shipType; ?>">
                    <input type="number" name="amount" min="1" value="1" 
                           style="width: 100%; padding: 8px; margin-bottom: 10px; background: rgba(10, 10, 30, 0.8); color: #fff; border: 1px solid #4a9eff; border-radius: 5px;">
                    <button type="submit" name="build_ship" class="btn" style="width: 100%; padding: 8px; font-size: 12px;">
                        Build
                    </button>
                </form>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <!-- Available Defenses -->
    <div class="defense-hangar" style="background: rgba(10, 10, 30, 0.8); padding: 20px; border-radius: 10px; margin-bottom: 20px; border: 1px solid #4a9eff;">
        <h2 style="color: #4a9eff; margin-bottom: 15px;">Planet Defenses</h2>
        <?php if (!empty($planetDefenses)): ?>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 15px;">
            <?php foreach ($planetDefenses as $defense): ?>
            <div style="padding: 15px; background: rgba(20, 20, 40, 0.9); border-radius: 5px; border: 1px solid #4a9eff; text-align: center;">
                <h3 style="color: #4a9eff; margin-bottom: 10px;"><?php echo ucwords(str_replace('_', ' ', $defense['defense_type'])); ?></h3>
                <p style="font-size: 24px; font-weight: bold; margin: 10px 0;"><?php echo number_format($defense['amount']); ?></p>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <p style="color: #aaa;">No defenses built yet.</p>
        <?php endif; ?>
    </div>
    
    <!-- Build Defenses -->
    <div class="build-defenses" style="background: rgba(10, 10, 30, 0.8); padding: 20px; border-radius: 10px; border: 1px solid #4a9eff;">
        <h2 style="color: #4a9eff; margin-bottom: 15px;">Build Defenses</h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 15px;">
            <?php
            $defenseTypes = [
                'rocket_launcher' => ['metal' => 2000, 'crystal' => 0, 'deuterium' => 0],
                'light_laser' => ['metal' => 1500, 'crystal' => 500, 'deuterium' => 0],
                'heavy_laser' => ['metal' => 6000, 'crystal' => 2000, 'deuterium' => 0],
                'gauss_cannon' => ['metal' => 20000, 'crystal' => 15000, 'deuterium' => 2000],
                'ion_cannon' => ['metal' => 2000, 'crystal' => 6000, 'deuterium' => 0],
                'plasma_turret' => ['metal' => 50000, 'crystal' => 50000, 'deuterium' => 30000],
                'small_shield_dome' => ['metal' => 10000, 'crystal' => 10000, 'deuterium' => 0],
                'large_shield_dome' => ['metal' => 50000, 'crystal' => 50000, 'deuterium' => 0]
            ];
            
            foreach ($defenseTypes as $defenseType => $cost):
            ?>
            <div style="padding: 15px; background: rgba(20, 20, 40, 0.9); border-radius: 5px; border: 1px solid #4a9eff;">
                <h3 style="color: #4a9eff; margin-bottom: 10px;"><?php echo ucwords(str_replace('_', ' ', $defenseType)); ?></h3>
                <p style="font-size: 12px; color: #aaa; margin-bottom: 10px;">
                    Cost: <?php echo number_format($cost['metal']); ?> M, 
                    <?php echo number_format($cost['crystal']); ?> C, 
                    <?php echo number_format($cost['deuterium']); ?> D
                </p>
                <form method="POST" action="">
                    <input type="hidden" name="defense_type" value="<?php echo $defenseType; ?>">
                    <input type="number" name="amount" min="1" value="1" 
                           style="width: 100%; padding: 8px; margin-bottom: 10px; background: rgba(10, 10, 30, 0.8); color: #fff; border: 1px solid #4a9eff; border-radius: 5px;">
                    <button type="submit" name="build_defense" class="btn" style="width: 100%; padding: 8px; font-size: 12px;">
                        Build
                    </button>
                </form>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
