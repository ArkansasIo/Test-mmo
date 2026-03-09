<?php
/**
 * Notifications Page - View game notifications
 */

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$player = new Player($_SESSION['user_id']);
$db = Database::getInstance();

// Get notifications
$notifications = $db->fetchAll("SELECT * FROM notifications WHERE player_id = ? ORDER BY created_at DESC LIMIT 50", [$player->getId()]);

// Mark all as read
if (isset($_GET['mark_read'])) {
    $db->update('notifications', ['is_read' => 1], 'player_id = :player_id', ['player_id' => $player->getId()]);
    header('Location: ?page=notifications');
    exit;
}
?>

<div class="notifications-page">
    <div class="page-header">
        <h1>Notifications</h1>
        <p>Stay updated with game events</p>
        <?php if (!empty($notifications)): ?>
        <a href="?page=notifications&mark_read=1" class="btn" style="float: right;">Mark All as Read</a>
        <?php endif; ?>
    </div>
    
    <div class="notifications-list" style="background: rgba(10, 10, 30, 0.8); padding: 20px; border-radius: 10px; border: 1px solid #4a9eff;">
        <?php if (!empty($notifications)): ?>
            <?php foreach ($notifications as $notification): ?>
            <div style="padding: 15px; background: rgba(20, 20, 40, <?php echo $notification['is_read'] ? '0.5' : '0.9'; ?>); border-radius: 5px; margin-bottom: 10px; border-left: 3px solid <?php echo $this->getNotificationColor($notification['type']); ?>;">
                <div style="display: flex; justify-content: space-between; align-items: start;">
                    <div style="flex: 1;">
                        <h3 style="color: #4a9eff; margin-bottom: 5px;">
                            <?php echo $this->getNotificationIcon($notification['type']); ?>
                            <?php echo htmlspecialchars($notification['title']); ?>
                        </h3>
                        <p style="margin: 10px 0; color: #ddd;"><?php echo htmlspecialchars($notification['message']); ?></p>
                        <small style="color: #888;"><?php echo date('Y-m-d H:i:s', $notification['created_at']); ?></small>
                    </div>
                    <?php if (!$notification['is_read']): ?>
                    <span style="background: #4a9eff; color: #fff; padding: 5px 10px; border-radius: 5px; font-size: 11px; font-weight: bold;">NEW</span>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div style="text-align: center; padding: 40px; color: #aaa;">
                <p style="font-size: 18px;">No notifications yet</p>
                <p>You'll receive notifications about battles, completed buildings, and more!</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
// Helper functions for notification display
function getNotificationColor($type) {
    $colors = [
        'battle' => '#ff4a4a',
        'building' => '#4a9eff',
        'research' => '#9a4aff',
        'fleet' => '#4aff9a',
        'trade' => '#ffa04a',
        'system' => '#aaa'
    ];
    return isset($colors[$type]) ? $colors[$type] : '#4a9eff';
}

function getNotificationIcon($type) {
    $icons = [
        'battle' => '⚔️',
        'building' => '🏗️',
        'research' => '🔬',
        'fleet' => '🚀',
        'trade' => '💰',
        'system' => '📢'
    ];
    return isset($icons[$type]) ? $icons[$type] . ' ' : '';
}
?>
