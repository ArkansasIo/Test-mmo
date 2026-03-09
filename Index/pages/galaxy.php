<?php
/**
 * Galaxy Page - Explore the universe
 */

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$player = new Player($_SESSION['user_id']);
$db = Database::getInstance();

$galaxy = isset($_GET['galaxy']) ? (int)$_GET['galaxy'] : 1;
$system = isset($_GET['system']) ? (int)$_GET['system'] : 1;

// Get all planets in the system
$planets = $db->fetchAll("SELECT p.*, pl.username 
                          FROM planets p 
                          LEFT JOIN players pl ON p.player_id = pl.id 
                          WHERE p.galaxy = ? AND p.system = ? 
                          ORDER BY p.position ASC", [$galaxy, $system]);
?>

<div class="galaxy-page">
    <div class="page-header">
        <h1>Galaxy View</h1>
        <p>Explore the universe and find targets</p>
    </div>
    
    <!-- Galaxy Navigator -->
    <div class="galaxy-navigator" style="background: rgba(10, 10, 30, 0.8); padding: 20px; border-radius: 10px; margin-bottom: 20px; border: 1px solid #4a9eff;">
        <form method="GET" action="" style="display: flex; gap: 15px; align-items: end;">
            <input type="hidden" name="page" value="galaxy">
            
            <div style="flex: 1;">
                <label style="color: #4a9eff; display: block; margin-bottom: 5px;">Galaxy</label>
                <input type="number" name="galaxy" min="1" max="9" value="<?php echo $galaxy; ?>" 
                       style="width: 100%; padding: 10px; background: rgba(10, 10, 30, 0.8); color: #fff; border: 1px solid #4a9eff; border-radius: 5px;">
            </div>
            
            <div style="flex: 1;">
                <label style="color: #4a9eff; display: block; margin-bottom: 5px;">System</label>
                <input type="number" name="system" min="1" max="499" value="<?php echo $system; ?>" 
                       style="width: 100%; padding: 10px; background: rgba(10, 10, 30, 0.8); color: #fff; border: 1px solid #4a9eff; border-radius: 5px;">
            </div>
            
            <button type="submit" class="btn" style="padding: 10px 30px;">View</button>
        </form>
        
        <div style="margin-top: 15px; display: flex; gap: 10px;">
            <a href="?page=galaxy&galaxy=<?php echo $galaxy; ?>&system=<?php echo max(1, $system-1); ?>" class="btn">← Previous</a>
            <a href="?page=galaxy&galaxy=<?php echo $galaxy; ?>&system=<?php echo min(499, $system+1); ?>" class="btn">Next →</a>
        </div>
    </div>
    
    <!-- System View -->
    <div class="system-view" style="background: rgba(10, 10, 30, 0.8); padding: 20px; border-radius: 10px; border: 1px solid #4a9eff;">
        <h2 style="color: #4a9eff; margin-bottom: 15px;">Galaxy <?php echo $galaxy; ?>, System <?php echo $system; ?></h2>
        
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: rgba(74, 158, 255, 0.2);">
                    <th style="padding: 10px; text-align: left; border: 1px solid #4a9eff;">Position</th>
                    <th style="padding: 10px; text-align: left; border: 1px solid #4a9eff;">Planet</th>
                    <th style="padding: 10px; text-align: left; border: 1px solid #4a9eff;">Player</th>
                    <th style="padding: 10px; text-align: left; border: 1px solid #4a9eff;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php for ($pos = 1; $pos <= 15; $pos++): 
                    $planetData = null;
                    foreach ($planets as $p) {
                        if ($p['position'] == $pos) {
                            $planetData = $p;
                            break;
                        }
                    }
                ?>
                <tr style="background: rgba(20, 20, 40, 0.5);">
                    <td style="padding: 10px; border: 1px solid #4a9eff; font-weight: bold; color: #4a9eff;"><?php echo $pos; ?></td>
                    <td style="padding: 10px; border: 1px solid #4a9eff;">
                        <?php if ($planetData): ?>
                            <span style="color: #4a9eff;"><?php echo htmlspecialchars($planetData['name']); ?></span>
                            <br>
                            <small style="color: #888;">Diameter: <?php echo number_format($planetData['diameter']); ?> km</small>
                        <?php else: ?>
                            <span style="color: #555;">Empty Position</span>
                        <?php endif; ?>
                    </td>
                    <td style="padding: 10px; border: 1px solid #4a9eff;">
                        <?php if ($planetData): ?>
                            <span style="color: <?php echo $planetData['player_id'] == $player->getId() ? '#4aff9a' : '#fff'; ?>;">
                                <?php echo htmlspecialchars($planetData['username'] ?? 'Unknown'); ?>
                            </span>
                        <?php else: ?>
                            <span style="color: #555;">-</span>
                        <?php endif; ?>
                    </td>
                    <td style="padding: 10px; border: 1px solid #4a9eff;">
                        <?php if ($planetData): ?>
                            <?php if ($planetData['player_id'] == $player->getId()): ?>
                                <a href="?page=empire&planet=<?php echo $planetData['id']; ?>" class="btn" style="padding: 5px 10px; font-size: 12px;">View</a>
                            <?php else: ?>
                                <a href="?page=fleet&target_galaxy=<?php echo $galaxy; ?>&target_system=<?php echo $system; ?>&target_position=<?php echo $pos; ?>" 
                                   class="btn" style="padding: 5px 10px; font-size: 12px;">Attack</a>
                                <a href="?page=fleet&target_galaxy=<?php echo $galaxy; ?>&target_system=<?php echo $system; ?>&target_position=<?php echo $pos; ?>&mission=spy" 
                                   class="btn" style="padding: 5px 10px; font-size: 12px; background: #9a4aff;">Spy</a>
                            <?php endif; ?>
                        <?php else: ?>
                            <a href="?page=colonize&galaxy=<?php echo $galaxy; ?>&system=<?php echo $system; ?>&position=<?php echo $pos; ?>" 
                               class="btn" style="padding: 5px 10px; font-size: 12px; background: #4aff9a;">Colonize</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endfor; ?>
            </tbody>
        </table>
    </div>
</div>
