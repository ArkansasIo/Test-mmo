<?php
/**
 * Research Page - Manage technology research
 */

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$player = new Player($_SESSION['user_id']);
$research = $player->getResearch();
$db = Database::getInstance();

// Get research queue
$researchQueue = $db->fetchAll("SELECT * FROM research_queue WHERE player_id = ? ORDER BY completion_time ASC", [$player->getId()]);
?>

<div class="research-page">
    <div class="page-header">
        <h1>Research</h1>
        <p>Advance your technology to gain an edge over your enemies</p>
    </div>
    
    <!-- Research Queue -->
    <?php if (!empty($researchQueue)): ?>
    <div class="research-queue" style="background: rgba(10, 10, 30, 0.8); padding: 20px; border-radius: 10px; margin-bottom: 20px; border: 1px solid #4a9eff;">
        <h2 style="color: #4a9eff; margin-bottom: 15px;">Research in Progress</h2>
        <?php foreach ($researchQueue as $item): ?>
            <div style="padding: 10px; background: rgba(20, 20, 40, 0.9); border-radius: 5px; margin-bottom: 10px;">
                <strong style="color: #4a9eff;"><?php echo ucwords(str_replace('_', ' ', $item['research_type'])); ?></strong>
                (Level <?php echo $item['level']; ?>)
                - Completes in: <?php echo gmdate('H:i:s', $item['completion_time'] - time()); ?>
            </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
    
    <!-- Available Research -->
    <div class="research-list" style="background: rgba(10, 10, 30, 0.8); padding: 20px; border-radius: 10px; border: 1px solid #4a9eff;">
        <h2 style="color: #4a9eff; margin-bottom: 15px;">Available Technologies</h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 15px;">
            <?php
            $researchTypes = [
                'energy_technology' => 'Increases energy production efficiency',
                'laser_technology' => 'Required for laser weapons and energy systems',
                'ion_technology' => 'Enables ion cannons and advanced weapons',
                'hyperspace_technology' => 'Increases fleet speed',
                'plasma_technology' => 'Unlocks plasma weapons',
                'combustion_drive' => 'Basic ship propulsion',
                'impulse_drive' => 'Advanced ship propulsion',
                'hyperspace_drive' => 'Fastest ship propulsion',
                'espionage_technology' => 'Improves spy probe effectiveness',
                'computer_technology' => 'Allows more fleet slots',
                'astrophysics' => 'Increases available planet slots',
                'intergalactic_research_network' => 'Links research facilities',
                'graviton_technology' => 'Required for Death Star',
                'weapons_technology' => 'Increases ship attack power',
                'shielding_technology' => 'Increases ship shield strength',
                'armor_technology' => 'Increases ship armor'
            ];
            
            foreach ($researchTypes as $type => $description):
                $currentLevel = 0;
                foreach ($research as $r) {
                    if ($r['research_type'] == $type) {
                        $currentLevel = $r['level'];
                        break;
                    }
                }
                $nextLevel = $currentLevel + 1;
            ?>
            <div style="padding: 15px; background: rgba(20, 20, 40, 0.9); border-radius: 5px; border: 1px solid #4a9eff;">
                <h3 style="color: #4a9eff; margin-bottom: 10px;"><?php echo ucwords(str_replace('_', ' ', $type)); ?></h3>
                <p style="color: #aaa; font-size: 13px; margin-bottom: 10px;"><?php echo $description; ?></p>
                <p style="margin-bottom: 10px;">Current Level: <strong><?php echo $currentLevel; ?></strong></p>
                <?php if (empty($researchQueue)): ?>
                <form method="POST" action="" style="margin-top: 10px;">
                    <input type="hidden" name="research_type" value="<?php echo $type; ?>">
                    <button type="submit" name="start_research" class="btn" style="width: 100%;">
                        Research Level <?php echo $nextLevel; ?>
                    </button>
                </form>
                <?php else: ?>
                <p style="color: #aaa; font-size: 12px;">Research lab busy</p>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php
// Handle research start
if (isset($_POST['start_research'])) {
    $researchType = $_POST['research_type'];
    
    // Calculate costs and time (simplified)
    $baseCost = ['metal' => 1000, 'crystal' => 500, 'deuterium' => 100];
    $currentLevel = 0;
    foreach ($research as $r) {
        if ($r['research_type'] == $researchType) {
            $currentLevel = $r['level'];
            break;
        }
    }
    $nextLevel = $currentLevel + 1;
    $multiplier = pow(2, $nextLevel - 1);
    
    $metalCost = $baseCost['metal'] * $multiplier;
    $crystalCost = $baseCost['crystal'] * $multiplier;
    $deuteriumCost = $baseCost['deuterium'] * $multiplier;
    
    if ($player->hasResources($metalCost, $crystalCost, $deuteriumCost)) {
        $player->updateResources(-$metalCost, -$crystalCost, -$deuteriumCost);
        
        $completionTime = time() + (3600 * $multiplier);
        
        $db->insert('research_queue', [
            'player_id' => $player->getId(),
            'research_type' => $researchType,
            'level' => $nextLevel,
            'start_time' => time(),
            'completion_time' => $completionTime
        ]);
        
        echo "<script>alert('Research started!'); window.location.href='?page=research';</script>";
    } else {
        echo "<script>alert('Not enough resources!');</script>";
    }
}
?>
