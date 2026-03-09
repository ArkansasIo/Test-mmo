<?php
/**
 * Fleet Page - Manage fleets and send missions
 */

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$player = new Player($_SESSION['user_id']);
$playerData = $player->getData();
$planets = $player->getPlanets();
$currentPlanetId = isset($_GET['planet']) ? $_GET['planet'] : $planets[0]['id'];
$db = Database::getInstance();

// Get planet ships
$planetShips = $db->fetchAll("SELECT * FROM planet_ships WHERE planet_id = ?", [$currentPlanetId]);

// Get active fleet movements
$fleetMovements = $db->fetchAll("SELECT * FROM fleet_movements WHERE player_id = ? AND status IN ('traveling', 'returning')", [$player->getId()]);
?>

<div class="fleet-page">
    <div class="page-header">
        <h1>Fleet Management</h1>
        <p>Manage your fleets and send missions</p>
    </div>
    
    <!-- Active Missions -->
    <?php if (!empty($fleetMovements)): ?>
    <div class="active-missions" style="background: rgba(10, 10, 30, 0.8); padding: 20px; border-radius: 10px; margin-bottom: 20px; border: 1px solid #4a9eff;">
        <h2 style="color: #4a9eff; margin-bottom: 15px;">Active Missions</h2>
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: rgba(74, 158, 255, 0.2);">
                    <th style="padding: 10px; text-align: left; border: 1px solid #4a9eff;">Mission</th>
                    <th style="padding: 10px; text-align: left; border: 1px solid #4a9eff;">From</th>
                    <th style="padding: 10px; text-align: left; border: 1px solid #4a9eff;">To</th>
                    <th style="padding: 10px; text-align: left; border: 1px solid #4a9eff;">Status</th>
                    <th style="padding: 10px; text-align: left; border: 1px solid #4a9eff;">Arrival</th>
                    <th style="padding: 10px; text-align: left; border: 1px solid #4a9eff;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($fleetMovements as $movement): ?>
                <tr>
                    <td style="padding: 10px; border: 1px solid #4a9eff;"><?php echo ucfirst($movement['mission_type']); ?></td>
                    <td style="padding: 10px; border: 1px solid #4a9eff;">[<?php echo $movement['start_galaxy']; ?>:<?php echo $movement['start_system']; ?>:<?php echo $movement['start_position']; ?>]</td>
                    <td style="padding: 10px; border: 1px solid #4a9eff;">[<?php echo $movement['target_galaxy']; ?>:<?php echo $movement['target_system']; ?>:<?php echo $movement['target_position']; ?>]</td>
                    <td style="padding: 10px; border: 1px solid #4a9eff;"><?php echo ucfirst($movement['status']); ?></td>
                    <td style="padding: 10px; border: 1px solid #4a9eff;"><?php echo date('Y-m-d H:i:s', $movement['arrival_time']); ?></td>
                    <td style="padding: 10px; border: 1px solid #4a9eff;">
                        <?php if ($movement['status'] == 'traveling'): ?>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="movement_id" value="<?php echo $movement['id']; ?>">
                            <button type="submit" name="recall_fleet" class="btn btn-danger" style="padding: 5px 10px; font-size: 12px;">Recall</button>
                        </form>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
    
    <!-- Available Ships -->
    <div class="available-ships" style="background: rgba(10, 10, 30, 0.8); padding: 20px; border-radius: 10px; margin-bottom: 20px; border: 1px solid #4a9eff;">
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
        <p style="color: #aaa;">No ships available. Build ships at your shipyard.</p>
        <?php endif; ?>
    </div>
    
    <!-- Send Fleet -->
    <div class="send-fleet" style="background: rgba(10, 10, 30, 0.8); padding: 20px; border-radius: 10px; border: 1px solid #4a9eff;">
        <h2 style="color: #4a9eff; margin-bottom: 15px;">Send Fleet</h2>
        <form method="POST" action="">
            <div style="margin-bottom: 20px;">
                <h3 style="color: #4a9eff; margin-bottom: 10px;">Select Ships</h3>
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 15px;">
                    <?php
                    $shipTypes = ['light_fighter', 'heavy_fighter', 'cruiser', 'battleship', 'small_cargo', 'large_cargo', 
                                'colony_ship', 'recycler', 'espionage_probe', 'bomber', 'destroyer', 'deathstar', 'battlecruiser'];
                    
                    foreach ($shipTypes as $shipType):
                        $available = 0;
                        foreach ($planetShips as $ship) {
                            if ($ship['ship_type'] == $shipType) {
                                $available = $ship['amount'];
                                break;
                            }
                        }
                    ?>
                    <div style="padding: 10px; background: rgba(20, 20, 40, 0.9); border-radius: 5px;">
                        <label style="color: #4a9eff; display: block; margin-bottom: 5px;">
                            <?php echo ucwords(str_replace('_', ' ', $shipType)); ?> (<?php echo $available; ?>)
                        </label>
                        <input type="number" name="ships[<?php echo $shipType; ?>]" min="0" max="<?php echo $available; ?>" 
                               placeholder="0" style="width: 100%; padding: 8px; background: rgba(10, 10, 30, 0.8); color: #fff; border: 1px solid #4a9eff; border-radius: 5px;">
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <div style="margin-bottom: 20px;">
                <h3 style="color: #4a9eff; margin-bottom: 10px;">Target Coordinates</h3>
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px;">
                    <div>
                        <label style="color: #4a9eff; display: block; margin-bottom: 5px;">Galaxy</label>
                        <input type="number" name="target_galaxy" min="1" max="9" required 
                               style="width: 100%; padding: 8px; background: rgba(10, 10, 30, 0.8); color: #fff; border: 1px solid #4a9eff; border-radius: 5px;">
                    </div>
                    <div>
                        <label style="color: #4a9eff; display: block; margin-bottom: 5px;">System</label>
                        <input type="number" name="target_system" min="1" max="499" required 
                               style="width: 100%; padding: 8px; background: rgba(10, 10, 30, 0.8); color: #fff; border: 1px solid #4a9eff; border-radius: 5px;">
                    </div>
                    <div>
                        <label style="color: #4a9eff; display: block; margin-bottom: 5px;">Position</label>
                        <input type="number" name="target_position" min="1" max="15" required 
                               style="width: 100%; padding: 8px; background: rgba(10, 10, 30, 0.8); color: #fff; border: 1px solid #4a9eff; border-radius: 5px;">
                    </div>
                </div>
            </div>
            
            <div style="margin-bottom: 20px;">
                <h3 style="color: #4a9eff; margin-bottom: 10px;">Mission Type</h3>
                <select name="mission_type" required 
                        style="width: 100%; padding: 12px; background: rgba(10, 10, 30, 0.8); color: #fff; border: 1px solid #4a9eff; border-radius: 5px;">
                    <option value="">Select Mission Type</option>
                    <option value="attack">Attack</option>
                    <option value="transport">Transport Resources</option>
                    <option value="colonize">Colonize</option>
                    <option value="spy">Espionage</option>
                    <option value="deploy">Deploy Fleet</option>
                </select>
            </div>
            
            <button type="submit" name="send_fleet" class="btn" style="width: 100%; padding: 15px; font-size: 16px;">
                Send Fleet
            </button>
        </form>
    </div>
</div>

<?php
// Handle fleet send
if (isset($_POST['send_fleet'])) {
    $ships = $_POST['ships'];
    $targetGalaxy = $_POST['target_galaxy'];
    $targetSystem = $_POST['target_system'];
    $targetPosition = $_POST['target_position'];
    $missionType = $_POST['mission_type'];
    
    // Create fleet
    $fleet = new Fleet();
    $fleetId = $fleet->create($player->getId(), $currentPlanetId, $ships);
    
    if ($fleetId) {
        $result = $fleet->sendMission($targetGalaxy, $targetSystem, $targetPosition, $missionType);
        
        if ($result['success']) {
            echo "<script>alert('Fleet sent successfully!'); window.location.href='?page=fleet';</script>";
        } else {
            echo "<script>alert('Error: " . $result['message'] . "');</script>";
        }
    }
}

// Handle fleet recall
if (isset($_POST['recall_fleet'])) {
    $movementId = $_POST['movement_id'];
    $db->update('fleet_movements', ['status' => 'recalled'], 'id = :id', ['id' => $movementId]);
    echo "<script>alert('Fleet recalled!'); window.location.href='?page=fleet';</script>";
}
?>
