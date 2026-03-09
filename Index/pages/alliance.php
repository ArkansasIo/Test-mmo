<?php
/**
 * Alliance Management Page
 * View, create, join, and manage alliances
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!defined('CLASS_PATH')) {
    require_once dirname(__DIR__) . '/config.php';
}

require_once CLASS_PATH . 'Database.php';
require_once CLASS_PATH . 'Player.php';
require_once CLASS_PATH . 'Alliance.php';
require_once INCLUDE_PATH . 'helpers.php';

if (!isset($_SESSION['player_id'])) {
    header('Location: index.php');
    exit;
}

$db = Database::getInstance();
$player = Player::getById($_SESSION['player_id']);
$message = '';
$error = '';

// Get player's alliance info
$playerAlliance = $db->fetchOne("
    SELECT a.*, am.rank
    FROM alliance_members am
    JOIN alliances a ON a.id = am.alliance_id
    WHERE am.player_id = ?
", [$_SESSION['player_id']]);

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'create':
            if ($playerAlliance) {
                $error = 'You are already in an alliance';
            } else {
                $name = trim($_POST['name'] ?? '');
                $tag = trim($_POST['tag'] ?? '');
                $description = trim($_POST['description'] ?? '');
                
                if (empty($name) || empty($tag)) {
                    $error = 'Name and tag are required';
                } elseif (strlen($tag) > 8) {
                    $error = 'Tag must be 8 characters or less';
                } else {
                    $allianceId = Alliance::create($name, $tag, $_SESSION['player_id'], $description);
                    if ($allianceId) {
                        $message = 'Alliance created successfully!';
                        header('Location: alliance.php');
                        exit;
                    } else {
                        $error = 'Failed to create alliance. Name or tag may already be taken.';
                    }
                }
            }
            break;
            
        case 'join':
            if ($playerAlliance) {
                $error = 'You are already in an alliance';
            } else {
                $allianceId = intval($_POST['alliance_id'] ?? 0);
                if ($allianceId) {
                    $success = Alliance::addMember($allianceId, $_SESSION['player_id']);
                    if ($success) {
                        $message = 'Joined alliance successfully!';
                        header('Location: alliance.php');
                        exit;
                    } else {
                        $error = 'Failed to join alliance';
                    }
                }
            }
            break;
            
        case 'leave':
            if ($playerAlliance) {
                if ($playerAlliance['rank'] === 'leader') {
                    // Check if there are other members
                    $members = Alliance::getMembers($playerAlliance['id']);
                    if (count($members) > 1) {
                        $error = 'Leaders cannot leave alliance with members. Transfer leadership first or disband.';
                    } else {
                        // Disband alliance
                        $db->delete('alliances', 'id = ?', [$playerAlliance['id']]);
                        $message = 'Alliance disbanded';
                        header('Location: alliance.php');
                        exit;
                    }
                } else {
                    $success = Alliance::removeMember($playerAlliance['id'], $_SESSION['player_id']);
                    if ($success) {
                        $message = 'Left alliance successfully';
                        header('Location: alliance.php');
                        exit;
                    }
                }
            }
            break;
            
        case 'kick':
            if ($playerAlliance && in_array($playerAlliance['rank'], ['leader', 'vice_leader'])) {
                $targetPlayerId = intval($_POST['target_player_id'] ?? 0);
                if ($targetPlayerId && $targetPlayerId != $_SESSION['player_id']) {
                    $success = Alliance::removeMember($playerAlliance['id'], $targetPlayerId);
                    if ($success) {
                        $message = 'Player kicked from alliance';
                    } else {
                        $error = 'Failed to kick player';
                    }
                }
            }
            break;
            
        case 'promote':
        case 'demote':
            if ($playerAlliance && $playerAlliance['rank'] === 'leader') {
                $targetPlayerId = intval($_POST['target_player_id'] ?? 0);
                $newRank = $_POST['new_rank'] ?? '';
                
                if ($targetPlayerId && in_array($newRank, ['member', 'officer', 'vice_leader'])) {
                    $success = Alliance::updateMemberRank($playerAlliance['id'], $targetPlayerId, $newRank);
                    if ($success) {
                        $message = 'Member rank updated';
                    } else {
                        $error = 'Failed to update rank';
                    }
                }
            }
            break;
            
        case 'update':
            if ($playerAlliance && in_array($playerAlliance['rank'], ['leader', 'vice_leader'])) {
                $description = trim($_POST['description'] ?? '');
                $success = $db->update('alliances', [
                    'description' => $description
                ], 'id = ?', [$playerAlliance['id']]);
                
                if ($success) {
                    $message = 'Alliance description updated';
                    header('Location: alliance.php');
                    exit;
                }
            }
            break;
    }
}

// Get all alliances for browsing
$allAlliances = $db->fetchAll("
    SELECT a.*, 
           COUNT(am.player_id) as member_count,
           p.username as leader_name
    FROM alliances a
    LEFT JOIN alliance_members am ON am.alliance_id = a.id
    LEFT JOIN players p ON p.id = a.leader_id
    GROUP BY a.id
    ORDER BY member_count DESC, a.name ASC
");

?>

<div class="page-header">
    <h1>Alliance Management</h1>
</div>

<?php if ($message): ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<?php if ($playerAlliance): ?>
    <!-- Player is in an alliance -->
    <div class="card">
        <h2>[<?php echo htmlspecialchars($playerAlliance['tag']); ?>] <?php echo htmlspecialchars($playerAlliance['name']); ?></h2>
        <p><strong>Your Rank:</strong> <?php echo htmlspecialchars(ucfirst($playerAlliance['rank'])); ?></p>
        
        <div class="section">
            <h3>Description</h3>
            <?php if (in_array($playerAlliance['rank'], ['leader', 'vice_leader'])): ?>
                <form method="POST" action="">
                    <input type="hidden" name="action" value="update">
                    <textarea name="description" rows="4" style="width: 100%;"><?php echo htmlspecialchars($playerAlliance['description']); ?></textarea>
                    <button type="submit" class="btn btn-primary">Update Description</button>
                </form>
            <?php else: ?>
                <p><?php echo nl2br(htmlspecialchars($playerAlliance['description'] ?: 'No description')); ?></p>
            <?php endif; ?>
        </div>
        
        <div class="section">
            <h3>Members</h3>
            <?php
            $members = Alliance::getMembers($playerAlliance['id']);
            ?>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Player</th>
                        <th>Rank</th>
                        <th>Joined</th>
                        <?php if (in_array($playerAlliance['rank'], ['leader', 'vice_leader'])): ?>
                            <th>Actions</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($members as $member): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($member['username']); ?></td>
                            <td><?php echo htmlspecialchars(ucfirst($member['rank'])); ?></td>
                            <td><?php echo formatTime($member['joined_at']); ?></td>
                            <?php if (in_array($playerAlliance['rank'], ['leader', 'vice_leader']) && $member['player_id'] != $_SESSION['player_id']): ?>
                                <td>
                                    <?php if ($playerAlliance['rank'] === 'leader'): ?>
                                        <form method="POST" action="" style="display: inline;">
                                            <input type="hidden" name="action" value="promote">
                                            <input type="hidden" name="target_player_id" value="<?php echo $member['player_id']; ?>">
                                            <select name="new_rank">
                                                <option value="member" <?php echo $member['rank'] === 'member' ? 'selected' : ''; ?>>Member</option>
                                                <option value="officer" <?php echo $member['rank'] === 'officer' ? 'selected' : ''; ?>>Officer</option>
                                                <option value="vice_leader" <?php echo $member['rank'] === 'vice_leader' ? 'selected' : ''; ?>>Vice Leader</option>
                                            </select>
                                            <button type="submit" class="btn btn-small">Update</button>
                                        </form>
                                    <?php endif; ?>
                                    
                                    <form method="POST" action="" style="display: inline;">
                                        <input type="hidden" name="action" value="kick">
                                        <input type="hidden" name="target_player_id" value="<?php echo $member['player_id']; ?>">
                                        <button type="submit" class="btn btn-small btn-danger" onclick="return confirm('Kick this player?')">Kick</button>
                                    </form>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <div class="section">
            <form method="POST" action="" onsubmit="return confirm('Are you sure you want to leave this alliance?')">
                <input type="hidden" name="action" value="leave">
                <button type="submit" class="btn btn-danger">Leave Alliance</button>
            </form>
        </div>
    </div>
    
<?php else: ?>
    <!-- Player is not in an alliance -->
    
    <div class="card">
        <h2>Create Alliance</h2>
        <form method="POST" action="">
            <input type="hidden" name="action" value="create">
            
            <div class="form-group">
                <label>Alliance Name</label>
                <input type="text" name="name" required maxlength="50">
            </div>
            
            <div class="form-group">
                <label>Alliance Tag (Max 8 characters)</label>
                <input type="text" name="tag" required maxlength="8">
            </div>
            
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" rows="4"></textarea>
            </div>
            
            <button type="submit" class="btn btn-primary">Create Alliance</button>
        </form>
    </div>
    
    <div class="card">
        <h2>Browse Alliances</h2>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Tag</th>
                    <th>Name</th>
                    <th>Leader</th>
                    <th>Members</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($allAlliances)): ?>
                    <tr>
                        <td colspan="5" style="text-align: center;">No alliances available</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($allAlliances as $alliance): ?>
                        <tr>
                            <td>[<?php echo htmlspecialchars($alliance['tag']); ?>]</td>
                            <td><?php echo htmlspecialchars($alliance['name']); ?></td>
                            <td><?php echo htmlspecialchars($alliance['leader_name']); ?></td>
                            <td><?php echo $alliance['member_count']; ?></td>
                            <td>
                                <form method="POST" action="">
                                    <input type="hidden" name="action" value="join">
                                    <input type="hidden" name="alliance_id" value="<?php echo $alliance['id']; ?>">
                                    <button type="submit" class="btn btn-small">Join</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<style>
.section {
    margin-top: 20px;
    padding-top: 20px;
    border-top: 1px solid #4a9eff;
}

.section h3 {
    color: #4a9eff;
    margin-bottom: 15px;
}

.btn-small {
    padding: 5px 10px;
    font-size: 12px;
}

.btn-danger {
    background: linear-gradient(135deg, #ff4a4a 0%, #ff2a2a 100%);
}

.btn-danger:hover {
    background: linear-gradient(135deg, #ff6666 0%, #ff4a4a 100%);
}

.alert {
    padding: 15px;
    border-radius: 5px;
    margin-bottom: 20px;
}

.alert-success {
    background: rgba(74, 255, 154, 0.2);
    border: 1px solid #4aff9a;
    color: #4aff9a;
}

.alert-error {
    background: rgba(255, 74, 74, 0.2);
    border: 1px solid #ff4a4a;
    color: #ff6666;
}
</style>

<?php include INCLUDE_PATH . 'footer.php'; ?>
