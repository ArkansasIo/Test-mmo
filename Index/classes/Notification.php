<?php
/**
 * Notification System - Send notifications to players
 */
class Notification {
    private $db;
    
    const TYPE_SYSTEM = 'system';
    const TYPE_ACHIEVEMENT = 'achievement';
    const TYPE_ALERT = 'alert';
    const TYPE_MESSAGE = 'message';
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Create notification
     */
    public function create($playerId, $type, $title, $message, $data = []) {
        try {
            $this->db->insert('notifications', [
                'player_id' => (int)$playerId,
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'data' => json_encode($data),
                'is_read' => 0,
                'created_at' => time()
            ]);
            
            return ['success' => true];
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    /**
     * Get notifications
     */
    public function getNotifications($playerId, $limit = 50, $unreadOnly = false) {
        $query = "SELECT * FROM notifications WHERE player_id = ?";
        $params = [(int)$playerId];
        
        if ($unreadOnly) {
            $query .= " AND is_read = 0";
        }
        
        $query .= " ORDER BY created_at DESC LIMIT ?";
        $params[] = (int)$limit;
        
        return $this->db->fetchAll($query, $params);
    }
    
    /**
     * Get unread count
     */
    public function getUnreadCount($playerId) {
        $result = $this->db->fetchOne(
            "SELECT COUNT(*) as count FROM notifications WHERE player_id = ? AND is_read = 0",
            [(int)$playerId]
        );
        
        return (int)($result['count'] ?? 0);
    }
    
    /**
     * Mark notification as read
     */
    public function markRead($notificationId, $playerId) {
        try {
            $this->db->update('notifications', 
                ['is_read' => 1],
                'id = ? AND player_id = ?',
                [$notificationId, $playerId]
            );
            
            return ['success' => true];
        } catch (Exception $e) {
            return ['success' => false];
        }
    }
    
    /**
     * Mark all notifications as read
     */
    public function markAllRead($playerId) {
        try {
            $this->db->update('notifications',
                ['is_read' => 1],
                'player_id = ? AND is_read = 0',
                [$playerId]
            );
            
            return ['success' => true];
        } catch (Exception $e) {
            return ['success' => false];
        }
    }
    
    /**
     * Delete notification
     */
    public function delete($notificationId, $playerId) {
        try {
            $this->db->delete('notifications',
                'id = ? AND player_id = ?',
                [$notificationId, $playerId]
            );
            
            return ['success' => true];
        } catch (Exception $e) {
            return ['success' => false];
        }
    }
    
    /**
     * Clear old notifications
     */
    public function clearOldNotifications($daysOld = 30) {
        try {
            $cutoffTime = time() - ($daysOld * 86400);
            
            $this->db->delete('notifications',
                'created_at < ?',
                [$cutoffTime]
            );
            
            return ['success' => true];
        } catch (Exception $e) {
            return ['success' => false];
        }
    }
}
