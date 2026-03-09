<?php
/**
 * Event class for game notifications
 */
class Event {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Create event notification
     */
    public function create($playerId, $type, $data) {
        try {
            $this->db->insert('events', [
                'player_id' => (int)$playerId,
                'type' => $type,
                'data' => json_encode($data),
                'created_at' => time(),
                'read' => 0
            ]);
            return ['success' => true];
        } catch (Exception $e) {
            return ['success' => false];
        }
    }
    
    /**
     * Get player events
     */
    public function getEvents($playerId, $limit = 50, $unreadOnly = false) {
        $query = "SELECT * FROM events WHERE player_id = ?";
        $params = [(int)$playerId];
        
        if ($unreadOnly) {
            $query .= " AND `read` = 0";
        }
        
        $query .= " ORDER BY created_at DESC LIMIT ?";
        $params[] = (int)$limit;
        
        return $this->db->fetchAll($query, $params);
    }
    
    /**
     * Mark event as read
     */
    public function markRead($eventId) {
        try {
            $this->db->update('events', ['read' => 1], 'id = ?', [(int)$eventId]);
            return ['success' => true];
        } catch (Exception $e) {
            return ['success' => false];
        }
    }
    
    /**
     * Mark all events as read
     */
    public function markAllRead($playerId) {
        try {
            $this->db->update('events', ['read' => 1], 'player_id = ? AND `read` = 0', [(int)$playerId]);
            return ['success' => true];
        } catch (Exception $e) {
            return ['success' => false];
        }
    }
    
    /**
     * Get unread count
     */
    public function getUnreadCount($playerId) {
        $result = $this->db->fetchOne(
            "SELECT COUNT(*) as count FROM events WHERE player_id = ? AND `read` = 0",
            [(int)$playerId]
        );
        return (int)($result['count'] ?? 0);
    }
}
