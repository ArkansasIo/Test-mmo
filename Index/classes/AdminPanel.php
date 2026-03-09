<?php
/**
 * Admin Panel Utilities
 */
class AdminPanel {
    private $db;
    private $currentAdmin;
    
    public function __construct(Database $db, $adminId = null) {
        $this->db = $db;
        if ($adminId) {
            $this->loadAdmin($adminId);
        }
    }
    
    /**
     * Load admin user
     */
    private function loadAdmin($adminId) {
        $query = "SELECT * FROM admins WHERE id = ? AND active = 1";
        $this->currentAdmin = $this->db->fetchOne($query, [$adminId]);
    }
    
    /**
     * Check admin permission
     */
    public function hasPermission($permission) {
        if (!$this->currentAdmin) {
            return false;
        }
        
        // Super admin has all permissions
        if ($this->currentAdmin['role'] === 'super_admin') {
            return true;
        }
        
        // Check specific permission
        $query = "SELECT * FROM admin_permissions 
                  WHERE admin_id = ? AND permission = ? AND active = 1";
        $result = $this->db->fetchOne($query, [$this->currentAdmin['id'], $permission]);
        
        return $result !== null;
    }
    
    /**
     * Get all admins
     */
    public function getAllAdmins() {
        $query = "SELECT id, username, email, role, active, created_at, last_login 
                  FROM admins ORDER BY created_at DESC";
        return $this->db->fetchAll($query, []);
    }
    
    /**
     * Get system logs
     */
    public function getSystemLogs($limit = 100, $filter = []) {
        $query = "SELECT * FROM system_logs WHERE 1=1";
        $params = [];
        
        if ($filter['level'] ?? null) {
            $query .= " AND level = ?";
            $params[] = $filter['level'];
        }
        
        if ($filter['action'] ?? null) {
            $query .= " AND action LIKE ?";
            $params[] = "%{$filter['action']}%";
        }
        
        if ($filter['days'] ?? null) {
            $query .= " AND created_at > DATE_SUB(NOW(), INTERVAL ? DAY)";
            $params[] = (int)$filter['days'];
        }
        
        $query .= " ORDER BY created_at DESC LIMIT ?";
        $params[] = (int)$limit;
        
        return $this->db->fetchAll($query, $params);
    }
    
    /**
     * Get player moderation history
     */
    public function getPlayerModerationHistory($playerId) {
        $query = "SELECT 
                    m.*,
                    a.username as moderator_name
                  FROM player_moderation m
                  LEFT JOIN admins a ON m.admin_id = a.id
                  WHERE m.player_id = ?
                  ORDER BY m.created_at DESC";
        
        return $this->db->fetchAll($query, [$playerId]);
    }
    
    /**
     * Issue player warning
     */
    public function issueWarning($playerId, $reason, $severity = 'low') {
        $query = "INSERT INTO player_warnings (player_id, reason, severity, admin_id, created_at) 
                  VALUES (?, ?, ?, ?, NOW())";
        
        $result = $this->db->execute($query, [
            $playerId,
            $reason,
            $severity,
            $this->currentAdmin['id']
        ]);
        
        if ($result) {
            $this->logAction("issue_warning", "Player {$playerId} - Reason: {$reason}");
        }
        
        return $result;
    }
    
    /**
     * Ban player
     */
    public function banPlayer($playerId, $reason, $duration = null) {
        $query = "UPDATE players SET status = 'banned', ban_reason = ? WHERE id = ?";
        $params = [$reason, $playerId];
        
        if ($duration) {
            $query = "UPDATE players SET status = 'banned', ban_reason = ?, ban_until = DATE_ADD(NOW(), INTERVAL ? DAY) WHERE id = ?";
            $params = [$reason, (int)$duration, $playerId];
        }
        
        $result = $this->db->execute($query, $params);
        
        if ($result) {
            $this->logAction("ban_player", "Player {$playerId} - Reason: {$reason}");
        }
        
        return $result;
    }
    
    /**
     * Unban player
     */
    public function unbanPlayer($playerId, $reason = null) {
        $query = "UPDATE players SET status = 'active', ban_reason = NULL WHERE id = ?";
        $result = $this->db->execute($query, [$playerId]);
        
        if ($result) {
            $this->logAction("unban_player", "Player {$playerId}" . ($reason ? " - Reason: {$reason}" : ""));
        }
        
        return $result;
    }
    
    /**
     * Get server statistics
     */
    public function getServerStats() {
        return [
            'total_players' => $this->countPlayers(),
            'active_players' => $this->countActivePlayers(),
            'banned_players' => $this->countBannedPlayers(),
            'online_players' => $this->countOnlinePlayers(),
            'total_alliances' => $this->countAlliances(),
            'db_size' => $this->getDatabaseSize(),
            'server_uptime' => $this->getServerUptime()
        ];
    }
    
    /**
     * Clear player cache
     */
    public function clearPlayerCache($playerId = null) {
        $cache = new Cache();
        
        if ($playerId) {
            $cache->delete("player_{$playerId}");
            $cache->delete("player_data_{$playerId}");
        } else {
            $cache->clear();
        }
        
        $this->logAction("clear_cache", $playerId ? "Player {$playerId}" : "All cache");
        
        return true;
    }
    
    /**
     * Log admin action
     */
    public function logAction($action, $details = null) {
        $query = "INSERT INTO admin_logs (admin_id, action, details, ip_address, timestamp) 
                  VALUES (?, ?, ?, ?, NOW())";
        
        return $this->db->execute($query, [
            $this->currentAdmin['id'] ?? 0,
            $action,
            $details,
            $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ]);
    }
    
    /**
     * Private helper methods
     */
    
    private function countPlayers() {
        $query = "SELECT COUNT(*) as count FROM players";
        $result = $this->db->fetchOne($query, []);
        return $result['count'] ?? 0;
    }
    
    private function countActivePlayers() {
        $query = "SELECT COUNT(*) as count FROM players WHERE status = 'active'";
        $result = $this->db->fetchOne($query, []);
        return $result['count'] ?? 0;
    }
    
    private function countBannedPlayers() {
        $query = "SELECT COUNT(*) as count FROM players WHERE status = 'banned'";
        $result = $this->db->fetchOne($query, []);
        return $result['count'] ?? 0;
    }
    
    private function countOnlinePlayers() {
        $query = "SELECT COUNT(*) as count FROM players WHERE last_activity > DATE_SUB(NOW(), INTERVAL 5 MINUTE)";
        $result = $this->db->fetchOne($query, []);
        return $result['count'] ?? 0;
    }
    
    private function countAlliances() {
        $query = "SELECT COUNT(*) as count FROM alliances";
        $result = $this->db->fetchOne($query, []);
        return $result['count'] ?? 0;
    }
    
    private function getDatabaseSize() {
        $query = "SELECT ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) as size_mb 
                  FROM information_schema.TABLES 
                  WHERE table_schema = DATABASE()";
        $result = $this->db->fetchOne($query, []);
        return $result['size_mb'] ?? 0;
    }
    
    private function getServerUptime() {
        $query = "SELECT TIMESTAMPDIFF(HOUR, created_at, NOW()) as uptime_hours FROM game_info LIMIT 1";
        $result = $this->db->fetchOne($query, []);
        return $result['uptime_hours'] ?? 0;
    }
}
