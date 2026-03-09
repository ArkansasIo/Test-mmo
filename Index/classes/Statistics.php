<?php
/**
 * Statistics Handler - Track game metrics and analytics
 */
class Statistics {
    private $db;
    
    public function __construct(Database $db) {
        $this->db = $db;
    }
    
    /**
     * Record player action
     */
    public function recordAction($playerId, $action, $metadata = []) {
        $query = "INSERT INTO game_statistics (player_id, action, metadata, timestamp) 
                  VALUES (?, ?, ?, NOW())";
        
        $params = [
            $playerId,
            $action,
            json_encode($metadata)
        ];
        
        return $this->db->execute($query, $params);
    }
    
    /**
     * Get player statistics
     */
    public function getPlayerStats($playerId) {
        $query = "SELECT 
                    COUNT(*) as total_actions,
                    COUNT(DISTINCT DATE(timestamp)) as active_days,
                    MAX(timestamp) as last_action
                  FROM game_statistics 
                  WHERE player_id = ?";
        
        return $this->db->fetchOne($query, [$playerId]);
    }
    
    /**
     * Get top players by action count
     */
    public function getTopPlayers($limit = 10) {
        $query = "SELECT 
                    player_id,
                    COUNT(*) as total_actions,
                    MAX(timestamp) as last_action
                  FROM game_statistics 
                  GROUP BY player_id 
                  ORDER BY total_actions DESC 
                  LIMIT ?";
        
        return $this->db->fetchAll($query, [$limit]);
    }
    
    /**
     * Get game-wide statistics
     */
    public function getGameStats() {
        return [
            'total_players' => $this->getTotalPlayers(),
            'active_players' => $this->getActivePlayers(),
            'total_actions' => $this->getTotalActions(),
            'avg_actions_per_player' => $this->getAverageActionsPerPlayer(),
            'most_common_action' => $this->getMostCommonAction()
        ];
    }
    
    /**
     * Get statistics for time period
     */
    public function getStatsByPeriod($startDate, $endDate, $period = 'daily') {
        $dateFormat = match($period) {
            'hourly' => '%Y-%m-%d %H:00:00',
            'daily' => '%Y-%m-%d',
            'weekly' => '%Y-W%u',
            'monthly' => '%Y-%m',
            default => '%Y-%m-%d'
        };
        
        $query = "SELECT 
                    DATE_FORMAT(timestamp, ?) as period,
                    COUNT(*) as action_count,
                    COUNT(DISTINCT player_id) as unique_players
                  FROM game_statistics 
                  WHERE timestamp BETWEEN ? AND ?
                  GROUP BY period 
                  ORDER BY period";
        
        return $this->db->fetchAll($query, [$dateFormat, $startDate, $endDate]);
    }
    
    /**
     * Get action breakdown
     */
    public function getActionBreakdown() {
        $query = "SELECT 
                    action,
                    COUNT(*) as count,
                    ROUND(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM game_statistics), 2) as percentage
                  FROM game_statistics 
                  GROUP BY action 
                  ORDER BY count DESC";
        
        return $this->db->fetchAll($query, []);
    }
    
    /**
     * Private helper methods
     */
    
    private function getTotalPlayers() {
        $query = "SELECT COUNT(DISTINCT player_id) as total FROM game_statistics";
        $result = $this->db->fetchOne($query, []);
        return $result['total'] ?? 0;
    }
    
    private function getActivePlayers() {
        $query = "SELECT COUNT(DISTINCT player_id) as active 
                  FROM game_statistics 
                  WHERE timestamp > DATE_SUB(NOW(), INTERVAL 24 HOUR)";
        $result = $this->db->fetchOne($query, []);
        return $result['active'] ?? 0;
    }
    
    private function getTotalActions() {
        $query = "SELECT COUNT(*) as total FROM game_statistics";
        $result = $this->db->fetchOne($query, []);
        return $result['total'] ?? 0;
    }
    
    private function getAverageActionsPerPlayer() {
        $query = "SELECT AVG(action_count) as average 
                  FROM (SELECT player_id, COUNT(*) as action_count 
                        FROM game_statistics 
                        GROUP BY player_id) as player_actions";
        $result = $this->db->fetchOne($query, []);
        return round($result['average'] ?? 0, 2);
    }
    
    private function getMostCommonAction() {
        $query = "SELECT action, COUNT(*) as count 
                  FROM game_statistics 
                  GROUP BY action 
                  ORDER BY count DESC 
                  LIMIT 1";
        $result = $this->db->fetchOne($query, []);
        return $result['action'] ?? null;
    }
}
