<?php
/**
 * Rankings/Leaderboard Page
 * Displays top players by various metrics
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!defined('CLASS_PATH')) {
    require_once dirname(__DIR__) . '/config.php';
}

require_once CLASS_PATH . 'Database.php';
require_once CLASS_PATH . 'Player.php';
require_once INCLUDE_PATH . 'helpers.php';

if (!isset($_SESSION['player_id'])) {
    header('Location: index.php');
    exit;
}

$db = Database::getInstance();
$rankingType = $_GET['type'] ?? 'points';
$limit = 100;

// Get current player info
$player = Player::getById($_SESSION['player_id']);

// Ranking queries based on type
$rankings = [];
$title = '';
$description = '';

switch ($rankingType) {
    case 'economy':
        $title = 'Economy Rankings';
        $description = 'Top players by total resources';
        $rankings = $db->fetchAll("
            SELECT p.id, p.username, 
                   SUM(pl.metal + pl.crystal + pl.deuterium) as total_resources,
                   COUNT(pl.id) as planet_count
            FROM players p
            LEFT JOIN planets pl ON pl.player_id = p.id
            WHERE p.is_admin = 0
            GROUP BY p.id
            ORDER BY total_resources DESC
            LIMIT ?
        ", [$limit]);
        break;
        
    case 'fleet':
        $title = 'Fleet Rankings';
        $description = 'Top players by fleet power';
        $rankings = $db->fetchAll("
            SELECT p.id, p.username,
                   COALESCE(SUM(
                       CASE ps.ship_type
                           WHEN 'small_cargo' THEN ps.amount * 4
                           WHEN 'large_cargo' THEN ps.amount * 12
                           WHEN 'light_fighter' THEN ps.amount * 50
                           WHEN 'heavy_fighter' THEN ps.amount * 150
                           WHEN 'cruiser' THEN ps.amount * 400
                           WHEN 'battleship' THEN ps.amount * 1000
                           WHEN 'battlecruiser' THEN ps.amount * 700
                           WHEN 'destroyer' THEN ps.amount * 2000
                           WHEN 'bomber' THEN ps.amount * 1000
                           WHEN 'espionage_probe' THEN ps.amount
                           WHEN 'recycler' THEN ps.amount * 16
                           WHEN 'colony_ship' THEN ps.amount * 10
                           WHEN 'deathstar' THEN ps.amount * 50000
                           ELSE 0
                       END
                   ), 0) as fleet_power
            FROM players p
            LEFT JOIN planets pl ON pl.player_id = p.id
            LEFT JOIN planet_ships ps ON ps.planet_id = pl.id
            WHERE p.is_admin = 0
            GROUP BY p.id
            HAVING fleet_power > 0
            ORDER BY fleet_power DESC
            LIMIT ?
        ", [$limit]);
        break;
        
    case 'research':
        $title = 'Research Rankings';
        $description = 'Top players by research level';
        $rankings = $db->fetchAll("
            SELECT p.id, p.username,
                   COALESCE(SUM(r.level), 0) as total_research
            FROM players p
            LEFT JOIN research r ON r.player_id = p.id
            WHERE p.is_admin = 0
            GROUP BY p.id
            HAVING total_research > 0
            ORDER BY total_research DESC
            LIMIT ?
        ", [$limit]);
        break;
        
    case 'military':
        $title = 'Military Rankings';
        $description = 'Top players by military strength (fleet + defense)';
        $rankings = $db->fetchAll("
            SELECT p.id, p.username,
                   (
                       COALESCE(SUM(
                           CASE ps.ship_type
                               WHEN 'small_cargo' THEN ps.amount * 4
                               WHEN 'large_cargo' THEN ps.amount * 12
                               WHEN 'light_fighter' THEN ps.amount * 50
                               WHEN 'heavy_fighter' THEN ps.amount * 150
                               WHEN 'cruiser' THEN ps.amount * 400
                               WHEN 'battleship' THEN ps.amount * 1000
                               WHEN 'battlecruiser' THEN ps.amount * 700
                               WHEN 'destroyer' THEN ps.amount * 2000
                               WHEN 'bomber' THEN ps.amount * 1000
                               WHEN 'deathstar' THEN ps.amount * 50000
                               ELSE 0
                           END
                       ), 0) +
                       COALESCE(SUM(
                           CASE pd.defense_type
                               WHEN 'rocket_launcher' THEN pd.amount * 20
                               WHEN 'light_laser' THEN pd.amount * 25
                               WHEN 'heavy_laser' THEN pd.amount * 100
                               WHEN 'gauss_cannon' THEN pd.amount * 200
                               WHEN 'ion_cannon' THEN pd.amount * 500
                               WHEN 'plasma_turret' THEN pd.amount * 3000
                               WHEN 'small_shield_dome' THEN pd.amount * 2000
                               WHEN 'large_shield_dome' THEN pd.amount * 10000
                               ELSE 0
                           END
                       ), 0)
                   ) as military_power
            FROM players p
            LEFT JOIN planets pl ON pl.player_id = p.id
            LEFT JOIN planet_ships ps ON ps.planet_id = pl.id
            LEFT JOIN planet_defenses pd ON pd.planet_id = pl.id
            WHERE p.is_admin = 0
            GROUP BY p.id
            HAVING military_power > 0
            ORDER BY military_power DESC
            LIMIT ?
        ", [$limit]);
        break;
        
    case 'alliance':
        $title = 'Alliance Rankings';
        $description = 'Top alliances by total member points';
        $rankings = $db->fetchAll("
            SELECT a.id, a.name, a.tag,
                   COUNT(am.player_id) as member_count,
                   SUM(
                       (SELECT SUM(metal + crystal + deuterium) 
                        FROM planets WHERE player_id = am.player_id)
                   ) as total_resources
            FROM alliances a
            LEFT JOIN alliance_members am ON am.alliance_id = a.id
            GROUP BY a.id
            HAVING member_count > 0
            ORDER BY total_resources DESC
            LIMIT ?
        ", [$limit]);
        break;
        
    default: // 'points'
        $title = 'Overall Rankings';
        $description = 'Top players by total points';
        $rankings = $db->fetchAll("
            SELECT p.id, p.username,
                   (
                       (COALESCE(p.metal + p.crystal + p.deuterium, 0) / 1000) +
                       (COALESCE((SELECT SUM(r.level) FROM research r WHERE r.player_id = p.id), 0) * 400)
                   ) as total_points
            FROM players p
            WHERE p.is_admin = 0
            ORDER BY total_points DESC
            LIMIT ?
        ", [$limit]);
        break;
}

?>

<div class="page-header">
    <h1><?php echo $title; ?></h1>
    <p><?php echo $description; ?></p>
</div>

<div class="ranking-tabs">
    <a href="?type=points" class="tab <?php echo $rankingType === 'points' ? 'active' : ''; ?>">Overall</a>
    <a href="?type=economy" class="tab <?php echo $rankingType === 'economy' ? 'active' : ''; ?>">Economy</a>
    <a href="?type=fleet" class="tab <?php echo $rankingType === 'fleet' ? 'active' : ''; ?>">Fleet</a>
    <a href="?type=military" class="tab <?php echo $rankingType === 'military' ? 'active' : ''; ?>">Military</a>
    <a href="?type=research" class="tab <?php echo $rankingType === 'research' ? 'active' : ''; ?>">Research</a>
    <a href="?type=alliance" class="tab <?php echo $rankingType === 'alliance' ? 'active' : ''; ?>">Alliances</a>
</div>

<div class="card">
    <table class="data-table">
        <thead>
            <tr>
                <th>Rank</th>
                <th><?php echo $rankingType === 'alliance' ? 'Alliance' : 'Player'; ?></th>
                <?php if ($rankingType === 'alliance'): ?>
                    <th>Tag</th>
                    <th>Members</th>
                    <th>Total Resources</th>
                <?php else: ?>
                    <?php if ($rankingType === 'economy'): ?>
                        <th>Planets</th>
                        <th>Total Resources</th>
                    <?php elseif ($rankingType === 'fleet'): ?>
                        <th>Fleet Power</th>
                    <?php elseif ($rankingType === 'research'): ?>
                        <th>Research Levels</th>
                    <?php elseif ($rankingType === 'military'): ?>
                        <th>Military Power</th>
                    <?php else: ?>
                        <th>Total Points</th>
                    <?php endif; ?>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($rankings)): ?>
                <tr>
                    <td colspan="4" style="text-align: center; padding: 20px;">No rankings available yet</td>
                </tr>
            <?php else: ?>
                <?php 
                $rank = 1;
                foreach ($rankings as $entry): 
                    $isCurrentPlayer = ($rankingType !== 'alliance' && $entry['id'] == $_SESSION['player_id']);
                ?>
                    <tr <?php echo $isCurrentPlayer ? 'class="highlight"' : ''; ?>>
                        <td><strong><?php echo $rank; ?></strong></td>
                        <td>
                            <?php 
                            if ($rankingType === 'alliance') {
                                echo htmlspecialchars($entry['name']);
                            } else {
                                echo htmlspecialchars($entry['username']);
                                if ($isCurrentPlayer) echo ' <span style="color: #4aff9a;">(You)</span>';
                            }
                            ?>
                        </td>
                        <?php if ($rankingType === 'alliance'): ?>
                            <td>[<?php echo htmlspecialchars($entry['tag']); ?>]</td>
                            <td><?php echo number_format($entry['member_count']); ?></td>
                            <td><?php echo formatNumber($entry['total_resources']); ?></td>
                        <?php else: ?>
                            <?php if ($rankingType === 'economy'): ?>
                                <td><?php echo number_format($entry['planet_count']); ?></td>
                                <td><?php echo formatNumber($entry['total_resources']); ?></td>
                            <?php elseif ($rankingType === 'fleet'): ?>
                                <td><?php echo formatNumber($entry['fleet_power']); ?></td>
                            <?php elseif ($rankingType === 'research'): ?>
                                <td><?php echo number_format($entry['total_research']); ?></td>
                            <?php elseif ($rankingType === 'military'): ?>
                                <td><?php echo formatNumber($entry['military_power']); ?></td>
                            <?php else: ?>
                                <td><?php echo formatNumber($entry['total_points']); ?></td>
                            <?php endif; ?>
                        <?php endif; ?>
                    </tr>
                <?php 
                    $rank++;
                endforeach; 
                ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<style>
.ranking-tabs {
    display: flex;
    gap: 10px;
    margin-bottom: 20px;
    flex-wrap: wrap;
}

.ranking-tabs .tab {
    padding: 10px 20px;
    background: rgba(20, 20, 40, 0.8);
    border: 1px solid #4a9eff;
    border-radius: 5px;
    color: #4a9eff;
    text-decoration: none;
    transition: all 0.3s;
}

.ranking-tabs .tab:hover {
    background: rgba(74, 158, 255, 0.2);
    box-shadow: 0 0 10px rgba(74, 158, 255, 0.3);
}

.ranking-tabs .tab.active {
    background: linear-gradient(135deg, #4a9eff 0%, #2a7eff 100%);
    color: #fff;
}

.highlight {
    background: rgba(74, 255, 154, 0.1) !important;
}

.data-table td:nth-child(1) {
    text-align: center;
    font-weight: bold;
    color: #4a9eff;
}
</style>

