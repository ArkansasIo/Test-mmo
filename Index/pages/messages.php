<?php
/**
 * Messages Page - Send and receive messages
 */

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$player = new Player($_SESSION['user_id']);
$db = Database::getInstance();

// Handle send message
if (isset($_POST['send_message'])) {
    $recipient = $_POST['recipient'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];
    
    // Find recipient
    $recipientData = $db->fetchOne("SELECT id FROM players WHERE username = ?", [$recipient]);
    
    if ($recipientData) {
        $db->insert('messages', [
            'sender_id' => $player->getId(),
            'recipient_id' => $recipientData['id'],
            'subject' => $subject,
            'message' => $message,
            'created_at' => time(),
            'is_read' => 0
        ]);
        
        echo "<script>alert('Message sent!'); window.location.href='?page=messages';</script>";
    } else {
        echo "<script>alert('Player not found!');</script>";
    }
}

// Handle delete message
if (isset($_GET['delete'])) {
    $messageId = $_GET['delete'];
    $db->delete('messages', 'id = :id AND recipient_id = :player_id', ['id' => $messageId, 'player_id' => $player->getId()]);
    header('Location: ?page=messages');
    exit;
}

// Handle mark as read
if (isset($_GET['read'])) {
    $messageId = $_GET['read'];
    $db->update('messages', ['is_read' => 1], 'id = :id AND recipient_id = :player_id', ['id' => $messageId, 'player_id' => $player->getId()]);
}

// Get inbox messages
$inbox = $db->fetchAll("SELECT m.*, p.username as sender_name 
                        FROM messages m 
                        JOIN players p ON m.sender_id = p.id 
                        WHERE m.recipient_id = ? 
                        ORDER BY m.created_at DESC LIMIT 50", [$player->getId()]);

// Get sent messages
$sent = $db->fetchAll("SELECT m.*, p.username as recipient_name 
                       FROM messages m 
                       JOIN players p ON m.recipient_id = p.id 
                       WHERE m.sender_id = ? 
                       ORDER BY m.created_at DESC LIMIT 50", [$player->getId()]);

$activeTab = isset($_GET['tab']) ? $_GET['tab'] : 'inbox';
$viewMessage = isset($_GET['view']) ? $db->fetchOne("SELECT m.*, p.username as sender_name 
                                                      FROM messages m 
                                                      JOIN players p ON m.sender_id = p.id 
                                                      WHERE m.id = ? AND m.recipient_id = ?", 
                                                     [$_GET['view'], $player->getId()]) : null;

// Mark as read if viewing
if ($viewMessage && !$viewMessage['is_read']) {
    $db->update('messages', ['is_read' => 1], 'id = :id', ['id' => $viewMessage['id']]);
}
?>

<div class="messages-page">
    <div class="page-header">
        <h1>Messages</h1>
        <p>Communicate with other players</p>
    </div>
    
    <?php if ($viewMessage): ?>
    <!-- View Message -->
    <div class="message-view" style="background: rgba(10, 10, 30, 0.8); padding: 20px; border-radius: 10px; margin-bottom: 20px; border: 1px solid #4a9eff;">
        <a href="?page=messages" class="btn" style="margin-bottom: 15px; display: inline-block;">← Back to Inbox</a>
        
        <div style="background: rgba(20, 20, 40, 0.9); padding: 20px; border-radius: 5px;">
            <div style="margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px solid #4a9eff;">
                <h2 style="color: #4a9eff; margin-bottom: 10px;"><?php echo htmlspecialchars($viewMessage['subject']); ?></h2>
                <div style="display: flex; justify-content: space-between; color: #aaa; font-size: 14px;">
                    <span>From: <strong style="color: #4a9eff;"><?php echo htmlspecialchars($viewMessage['sender_name']); ?></strong></span>
                    <span><?php echo date('Y-m-d H:i:s', $viewMessage['created_at']); ?></span>
                </div>
            </div>
            
            <div style="color: #ddd; line-height: 1.6;">
                <?php echo nl2br(htmlspecialchars($viewMessage['message'])); ?>
            </div>
            
            <div style="margin-top: 20px;">
                <a href="?page=messages&compose=1&recipient=<?php echo urlencode($viewMessage['sender_name']); ?>&subject=RE: <?php echo urlencode($viewMessage['subject']); ?>" 
                   class="btn">Reply</a>
                <a href="?page=messages&delete=<?php echo $viewMessage['id']; ?>" 
                   class="btn btn-danger" 
                   onclick="return confirm('Delete this message?')">Delete</a>
            </div>
        </div>
    </div>
    
    <?php elseif (isset($_GET['compose'])): ?>
    <!-- Compose Message -->
    <div class="compose-message" style="background: rgba(10, 10, 30, 0.8); padding: 20px; border-radius: 10px; border: 1px solid #4a9eff;">
        <h2 style="color: #4a9eff; margin-bottom: 15px;">Compose Message</h2>
        <form method="POST" action="">
            <div style="margin-bottom: 15px;">
                <label style="color: #4a9eff; display: block; margin-bottom: 5px;">To (Username):</label>
                <input type="text" name="recipient" value="<?php echo isset($_GET['recipient']) ? htmlspecialchars($_GET['recipient']) : ''; ?>" required 
                       style="width: 100%; padding: 10px; background: rgba(10, 10, 30, 0.8); color: #fff; border: 1px solid #4a9eff; border-radius: 5px;">
            </div>
            
            <div style="margin-bottom: 15px;">
                <label style="color: #4a9eff; display: block; margin-bottom: 5px;">Subject:</label>
                <input type="text" name="subject" value="<?php echo isset($_GET['subject']) ? htmlspecialchars($_GET['subject']) : ''; ?>" required 
                       style="width: 100%; padding: 10px; background: rgba(10, 10, 30, 0.8); color: #fff; border: 1px solid #4a9eff; border-radius: 5px;">
            </div>
            
            <div style="margin-bottom: 15px;">
                <label style="color: #4a9eff; display: block; margin-bottom: 5px;">Message:</label>
                <textarea name="message" rows="10" required 
                          style="width: 100%; padding: 10px; background: rgba(10, 10, 30, 0.8); color: #fff; border: 1px solid #4a9eff; border-radius: 5px; resize: vertical;"></textarea>
            </div>
            
            <div>
                <button type="submit" name="send_message" class="btn">Send Message</button>
                <a href="?page=messages" class="btn btn-danger">Cancel</a>
            </div>
        </form>
    </div>
    
    <?php else: ?>
    <!-- Message List -->
    <div style="margin-bottom: 20px;">
        <a href="?page=messages&compose=1" class="btn">Compose New Message</a>
    </div>
    
    <div class="message-tabs" style="margin-bottom: 20px;">
        <a href="?page=messages&tab=inbox" 
           class="btn" 
           style="<?php echo $activeTab == 'inbox' ? 'background: #4a9eff;' : 'background: rgba(74, 158, 255, 0.3);'; ?>">
            Inbox (<?php echo count(array_filter($inbox, function($m) { return !$m['is_read']; })); ?>)
        </a>
        <a href="?page=messages&tab=sent" 
           class="btn" 
           style="<?php echo $activeTab == 'sent' ? 'background: #4a9eff;' : 'background: rgba(74, 158, 255, 0.3);'; ?>">
            Sent
        </a>
    </div>
    
    <div class="messages-list" style="background: rgba(10, 10, 30, 0.8); padding: 20px; border-radius: 10px; border: 1px solid #4a9eff;">
        <?php 
        $messages = $activeTab == 'inbox' ? $inbox : $sent;
        
        if (!empty($messages)): ?>
            <?php foreach ($messages as $msg): ?>
            <div style="padding: 15px; background: rgba(20, 20, 40, <?php echo !$msg['is_read'] && $activeTab == 'inbox' ? '1' : '0.5'; ?>); border-radius: 5px; margin-bottom: 10px; border-left: 3px solid <?php echo !$msg['is_read'] && $activeTab == 'inbox' ? '#4a9eff' : '#333'; ?>; cursor: pointer;" 
                 onclick="window.location.href='?page=messages&view=<?php echo $msg['id']; ?>'">
                <div style="display: flex; justify-content: space-between; align-items: start;">
                    <div style="flex: 1;">
                        <h3 style="color: #4a9eff; margin-bottom: 5px;"><?php echo htmlspecialchars($msg['subject']); ?></h3>
                        <p style="color: #aaa; font-size: 14px;">
                            <?php echo $activeTab == 'inbox' ? 'From: ' . htmlspecialchars($msg['sender_name']) : 'To: ' . htmlspecialchars($msg['recipient_name']); ?>
                        </p>
                        <small style="color: #888;"><?php echo date('Y-m-d H:i:s', $msg['created_at']); ?></small>
                    </div>
                    <?php if (!$msg['is_read'] && $activeTab == 'inbox'): ?>
                    <span style="background: #4a9eff; color: #fff; padding: 5px 10px; border-radius: 5px; font-size: 11px; font-weight: bold;">NEW</span>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div style="text-align: center; padding: 40px; color: #aaa;">
                <p style="font-size: 18px;">No messages</p>
            </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>
