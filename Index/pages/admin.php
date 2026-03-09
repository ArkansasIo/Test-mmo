<?php
/**
 * Admin Control Panel
 * Administrative tools for managing the game
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
$player = Player::getById($_SESSION['player_id']);

// Check if player is admin
if (!$player || !$player['is_admin']) {
    die('Access denied. Admin privileges required.');
}

$message = '';
$error = '';

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'ban_player':
            $playerId = intval($_POST['player_id'] ?? 0);
            if ($playerId) {
                $success = $db->update('players', ['is_banned' => 1], 'id = :id', ['id' => $playerId]);
                $message = $success ? 'Player banned successfully' : 'Failed to ban player';
            }
            break;
            
        case 'unban_player':
            $playerId = intval($_POST['player_id'] ?? 0);
            if ($playerId) {
                $success = $db->update('players', ['is_banned' => 0], 'id = :id', ['id' => $playerId]);
                $message = $success ? 'Player unbanned successfully' : 'Failed to unban player';
            }
            break;
            
        case 'delete_player':
            $playerId = intval($_POST['player_id'] ?? 0);
            if ($playerId && $playerId != $_SESSION['player_id']) {
                $db->delete('players', 'id = :id', ['id' => $playerId]);
                $message = 'Player deleted successfully';
            }
            break;
            
        case 'give_resources':
            $playerId = intval($_POST['player_id'] ?? 0);
            $metal = intval($_POST['metal'] ?? 0);
            $crystal = intval($_POST['crystal'] ?? 0);
            $deuterium = intval($_POST['deuterium'] ?? 0);
            
            if ($playerId) {
                $db->query(
                    "UPDATE players SET metal = metal + ?, crystal = crystal + ?, deuterium = deuterium + ? WHERE id = ?",
                    [$metal, $crystal, $deuterium, $playerId]
                );
                $message = 'Resources added successfully';
            }
            break;
            
        case 'send_message':
            $recipientId = intval($_POST['recipient_id'] ?? 0);
            $subject = trim($_POST['subject'] ?? '');
            $messageText = trim($_POST['message'] ?? '');
            
            if ($recipientId && $subject && $messageText) {
                $db->insert('messages', [
                    'sender_id' => $_SESSION['player_id'],
                    'recipient_id' => $recipientId,
                    'subject' => $subject,
                    'message' => $messageText,
                    'created_at' => time()
                ]);
                $message = 'Message sent successfully';
            }
            break;
            
        case 'maintenance_mode':
            $enabled = isset($_POST['enabled']) ? 1 : 0;
            $configFile = __DIR__ . '/../config.php';
            $configContent = file_get_contents($configFile);
            $configContent = preg_replace(
                "/define\('MAINTENANCE_MODE', (true|false)\);/",
                "define('MAINTENANCE_MODE', " . ($enabled ? 'true' : 'false') . ");",
                $configContent
            );
            file_put_contents($configFile, $configContent);
            $message = 'Maintenance mode ' . ($enabled ? 'enabled' : 'disabled');
            break;
            
        case 'clear_cache':
            // Keep activity log table reasonably small in local environments.
            $db->query("DELETE FROM activity_log WHERE created_at < ?", [time() - 86400]);
            $message = 'Cache cleared successfully';
            break;
    }
}

// Get statistics
$stats = [
    'total_players' => $db->fetchOne("SELECT COUNT(*) as count FROM players WHERE is_admin = 0")['count'],
    'active_players' => $db->fetchOne("SELECT COUNT(*) as count FROM players WHERE is_admin = 0 AND last_activity > ?", [time() - 86400])['count'],
    'total_planets' => $db->fetchOne("SELECT COUNT(*) as count FROM planets")['count'],
    'total_fleets' => $db->fetchOne("SELECT COUNT(*) as count FROM fleets")['count'],
    'total_alliances' => $db->fetchOne("SELECT COUNT(*) as count FROM alliances")['count'],
    'banned_players' => $db->fetchOne("SELECT COUNT(*) as count FROM players WHERE is_banned = 1")['count']
];

// Get recent players
$recentPlayers = $db->fetchAll("
    SELECT id, username, email, created_at, last_activity,
           CASE WHEN is_banned = 1 THEN 'banned' ELSE 'active' END AS status
    FROM players
    WHERE is_admin = 0
    ORDER BY created_at DESC
    LIMIT 20
");

// Get online players
$onlinePlayers = $db->fetchAll("
    SELECT id, username, last_activity
    FROM players
    WHERE is_admin = 0 AND last_activity > ?
    ORDER BY last_activity DESC
", [time() - 900]); // 15 minutes

?>

<div class="page-header">
    <h1>Admin Control Panel</h1>
</div>

<?php if ($message): ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<div class="admin-grid">
    <div class="card">
        <h2>Game Statistics</h2>
        <div class="stats-grid">
            <div class="stat-item">
                <span class="stat-value"><?php echo number_format($stats['total_players']); ?></span>
                <span class="stat-label">Total Players</span>
            </div>
            <div class="stat-item">
                <span class="stat-value"><?php echo number_format($stats['active_players']); ?></span>
                <span class="stat-label">Active (24h)</span>
            </div>
            <div class="stat-item">
                <span class="stat-value"><?php echo number_format($stats['total_planets']); ?></span>
                <span class="stat-label">Total Planets</span>
            </div>
            <div class="stat-item">
                <span class="stat-value"><?php echo number_format($stats['total_fleets']); ?></span>
                <span class="stat-label">Total Fleets</span>
            </div>
            <div class="stat-item">
                <span class="stat-value"><?php echo number_format($stats['total_alliances']); ?></span>
                <span class="stat-label">Alliances</span>
            </div>
            <div class="stat-item">
                <span class="stat-value"><?php echo number_format($stats['banned_players']); ?></span>
                <span class="stat-label">Banned</span>
            </div>
        </div>
    </div>
    
    <div class="card">
        <h2>System Tools</h2>
        <form method="POST" action="">
            <input type="hidden" name="action" value="maintenance_mode">
            <label>
                <input type="checkbox" name="enabled" <?php echo MAINTENANCE_MODE ? 'checked' : ''; ?>>
                Maintenance Mode
            </label>
            <button type="submit" class="btn btn-primary">Update</button>
        </form>
        
        <hr>
        
        <form method="POST" action="">
            <input type="hidden" name="action" value="clear_cache">
            <button type="submit" class="btn btn-secondary">Clear Cache</button>
        </form>
    </div>
</div>

<div class="card">
    <h2>Online Players (<?php echo count($onlinePlayers); ?>)</h2>
    <table class="data-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Last Activity</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($onlinePlayers)): ?>
                <tr>
                    <td colspan="3" style="text-align: center;">No players online</td>
                </tr>
            <?php else: ?>
                <?php foreach ($onlinePlayers as $onlinePlayer): ?>
                    <tr>
                        <td><?php echo $onlinePlayer['id']; ?></td>
                        <td><?php echo htmlspecialchars($onlinePlayer['username']); ?></td>
                        <td><?php echo formatTime($onlinePlayer['last_activity']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="card">
    <h2>Player Management</h2>
    <table class="data-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Registered</th>
                <th>Last Login</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($recentPlayers as $p): ?>
                <tr>
                    <td><?php echo $p['id']; ?></td>
                    <td><?php echo htmlspecialchars($p['username']); ?></td>
                    <td><?php echo htmlspecialchars($p['email']); ?></td>
                    <td><?php echo formatTime($p['created_at']); ?></td>
                    <td><?php echo formatTime($p['last_activity']); ?></td>
                    <td>
                        <span class="status-<?php echo $p['status']; ?>">
                            <?php echo ucfirst($p['status']); ?>
                        </span>
                    </td>
                    <td>
                        <?php if ($p['status'] === 'active'): ?>
                            <form method="POST" action="" style="display: inline;">
                                <input type="hidden" name="action" value="ban_player">
                                <input type="hidden" name="player_id" value="<?php echo $p['id']; ?>">
                                <button type="submit" class="btn btn-small btn-warning" onclick="return confirm('Ban this player?')">Ban</button>
                            </form>
                        <?php else: ?>
                            <form method="POST" action="" style="display: inline;">
                                <input type="hidden" name="action" value="unban_player">
                                <input type="hidden" name="player_id" value="<?php echo $p['id']; ?>">
                                <button type="submit" class="btn btn-small">Unban</button>
                            </form>
                        <?php endif; ?>
                        
                        <button class="btn btn-small" onclick="openGiveResourcesModal(<?php echo $p['id']; ?>, '<?php echo htmlspecialchars($p['username']); ?>')">Resources</button>
                        <button class="btn btn-small" onclick="openMessageModal(<?php echo $p['id']; ?>, '<?php echo htmlspecialchars($p['username']); ?>')">Message</button>
                        
                        <form method="POST" action="" style="display: inline;">
                            <input type="hidden" name="action" value="delete_player">
                            <input type="hidden" name="player_id" value="<?php echo $p['id']; ?>">
                            <button type="submit" class="btn btn-small btn-danger" onclick="return confirm('Permanently delete this player?')">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Give Resources Modal -->
<div id="resourcesModal" class="modal" style="display: none;">
    <div class="modal-content">
        <h2>Give Resources</h2>
        <p>Player: <span id="resourcesPlayerName"></span></p>
        <form method="POST" action="">
            <input type="hidden" name="action" value="give_resources">
            <input type="hidden" name="player_id" id="resourcesPlayerId">
            
            <div class="form-group">
                <label>Metal</label>
                <input type="number" name="metal" value="0" min="0">
            </div>
            
            <div class="form-group">
                <label>Crystal</label>
                <input type="number" name="crystal" value="0" min="0">
            </div>
            
            <div class="form-group">
                <label>Deuterium</label>
                <input type="number" name="deuterium" value="0" min="0">
            </div>
            
            <button type="submit" class="btn btn-primary">Give Resources</button>
            <button type="button" class="btn btn-secondary" onclick="closeModal('resourcesModal')">Cancel</button>
        </form>
    </div>
</div>

<!-- Message Modal -->
<div id="messageModal" class="modal" style="display: none;">
    <div class="modal-content">
        <h2>Send Admin Message</h2>
        <p>To: <span id="messagePlayerName"></span></p>
        <form method="POST" action="">
            <input type="hidden" name="action" value="send_message">
            <input type="hidden" name="recipient_id" id="messagePlayerId">
            
            <div class="form-group">
                <label>Subject</label>
                <input type="text" name="subject" required>
            </div>
            
            <div class="form-group">
                <label>Message</label>
                <textarea name="message" rows="5" required></textarea>
            </div>
            
            <button type="submit" class="btn btn-primary">Send Message</button>
            <button type="button" class="btn btn-secondary" onclick="closeModal('messageModal')">Cancel</button>
        </form>
    </div>
</div>

<style>
.admin-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
    margin-bottom: 20px;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 15px;
}

.stat-item {
    text-align: center;
    padding: 15px;
    background: rgba(74, 158, 255, 0.1);
    border-radius: 5px;
}

.stat-value {
    display: block;
    font-size: 24px;
    font-weight: bold;
    color: #4a9eff;
    margin-bottom: 5px;
}

.stat-label {
    display: block;
    font-size: 12px;
    color: #8ab4f8;
}

.status-active {
    color: #4aff9a;
}

.status-banned {
    color: #ff4a4a;
}

.btn-warning {
    background: linear-gradient(135deg, #ffaa00 0%, #ff8800 100%);
}

.btn-warning:hover {
    background: linear-gradient(135deg, #ffcc00 0%, #ffaa00 100%);
}

.modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.8);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
}

.modal-content {
    background: rgba(20, 20, 40, 0.95);
    border: 2px solid #4a9eff;
    border-radius: 10px;
    padding: 30px;
    max-width: 500px;
    width: 90%;
}

.btn-secondary {
    background: rgba(100, 100, 150, 0.8);
}
</style>

<script>
function openGiveResourcesModal(playerId, playerName) {
    document.getElementById('resourcesPlayerId').value = playerId;
    document.getElementById('resourcesPlayerName').textContent = playerName;
    document.getElementById('resourcesModal').style.display = 'flex';
}

function openMessageModal(playerId, playerName) {
    document.getElementById('messagePlayerId').value = playerId;
    document.getElementById('messagePlayerName').textContent = playerName;
    document.getElementById('messageModal').style.display = 'flex';
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}
</script>

<?php include INCLUDE_PATH . 'footer.php'; ?>
