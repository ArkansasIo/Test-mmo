<?php
/**
 * Alliance Class
 * Handles alliance operations
 */

class Alliance {
    private $db;
    private $id;
    private $data;
    
    public function __construct($allianceId = null) {
        $this->db = Database::getInstance();
        if ($allianceId) {
            $this->id = $allianceId;
            $this->loadAllianceData();
        }
    }
    
    /**
     * Load alliance data
     */
    private function loadAllianceData() {
        $sql = "SELECT * FROM alliances WHERE id = ?";
        $this->data = $this->db->fetchOne($sql, [$this->id]);
    }
    
    /**
     * Create a new alliance
     */
    public static function create($name, $tag, $founderId, $description = '') {
        $db = Database::getInstance();

        // Check if name or tag already exists
        $nameExists = $db->fetchOne("SELECT COUNT(*) AS count FROM alliances WHERE name = ?", [$name]);
        if ($nameExists && (int)$nameExists['count'] > 0) {
            return false;
        }

        $tagExists = $db->fetchOne("SELECT COUNT(*) AS count FROM alliances WHERE tag = ?", [$tag]);
        if ($tagExists && (int)$tagExists['count'] > 0) {
            return false;
        }

        // Create alliance
        $allianceData = [
            'name' => $name,
            'tag' => $tag,
            'description' => $description,
            'founder_id' => $founderId,
            'created_at' => time()
        ];

        $allianceId = $db->insert('alliances', $allianceData);

        if ($allianceId) {
            self::addMember((int)$allianceId, (int)$founderId, 'leader');
            return (int)$allianceId;
        }

        return false;
    }
    
    /**
     * Check if name exists
     */
    private function nameExists($name) {
        $sql = "SELECT COUNT(*) as count FROM alliances WHERE name = ?";
        $result = $this->db->fetchOne($sql, [$name]);
        return $result['count'] > 0;
    }
    
    /**
     * Check if tag exists
     */
    private function tagExists($tag) {
        $sql = "SELECT COUNT(*) as count FROM alliances WHERE tag = ?";
        $result = $this->db->fetchOne($sql, [$tag]);
        return $result['count'] > 0;
    }
    
    /**
     * Add member to alliance
     */
    public static function addMember($allianceId, $playerId, $rank = 'member') {
        $db = Database::getInstance();
        return $db->insert('alliance_members', [
            'alliance_id' => $allianceId,
            'player_id' => $playerId,
            'rank' => $rank,
            'joined_at' => time()
        ]);
    }
    
    /**
     * Remove member from alliance
     */
    public static function removeMember($allianceId, $playerId) {
        $db = Database::getInstance();
        return $db->delete('alliance_members', 'alliance_id = :alliance_id AND player_id = :player_id', [
            'alliance_id' => $allianceId,
            'player_id' => $playerId
        ]);
    }
    
    /**
     * Get all members
     */
    public static function getMembers($allianceId) {
        $db = Database::getInstance();
        $sql = "SELECT am.*, p.username, p.metal, p.crystal, p.deuterium
                FROM alliance_members am
                JOIN players p ON am.player_id = p.id
                WHERE am.alliance_id = ?
                ORDER BY 
                    CASE am.rank
                        WHEN 'leader' THEN 1
                        WHEN 'vice_leader' THEN 2
                        WHEN 'officer' THEN 3
                        ELSE 4
                    END,
                    am.joined_at ASC";
        return $db->fetchAll($sql, [$allianceId]);
    }
    
    /**
     * Update member rank
     */
    public static function updateMemberRank($allianceId, $playerId, $newRank) {
        $db = Database::getInstance();
        return $db->update('alliance_members', [
            'rank' => $newRank
        ], 'alliance_id = :alliance_id AND player_id = :player_id', [
            'alliance_id' => $allianceId,
            'player_id' => $playerId
        ]);
    }
    
    /**
     * Get alliance data
     */
    public function getData($key = null) {
        if ($key) {
            return isset($this->data[$key]) ? $this->data[$key] : null;
        }
        return $this->data;
    }
    
    /**
     * Update alliance
     */
    public function update($data) {
        $updated = $this->db->update('alliances', $data, 'id = :id', ['id' => $this->id]);
        if ($updated) {
            $this->loadAllianceData();
        }
        return $updated;
    }
    
    /**
     * Delete alliance
     */
    public function delete() {
        return $this->db->delete('alliances', 'id = :id', ['id' => $this->id]);
    }
    
    /**
     * Get member count
     */
    public function getMemberCount() {
        $sql = "SELECT COUNT(*) as count FROM alliance_members WHERE alliance_id = ?";
        $result = $this->db->fetchOne($sql, [$this->id]);
        return $result ? $result['count'] : 0;
    }
    
    /**
     * Check if player is member
     */
    public function isMember($playerId) {
        $sql = "SELECT COUNT(*) as count FROM alliance_members WHERE alliance_id = ? AND player_id = ?";
        $result = $this->db->fetchOne($sql, [$this->id, $playerId]);
        return $result['count'] > 0;
    }
    
    /**
     * Get player rank in alliance
     */
    public function getPlayerRank($playerId) {
        $sql = "SELECT rank FROM alliance_members WHERE alliance_id = ? AND player_id = ?";
        $result = $this->db->fetchOne($sql, [$this->id, $playerId]);
        return $result ? $result['rank'] : null;
    }
    
    /**
     * Get alliance ID
     */
    public function getId() {
        return $this->id;
    }
    
    /**
     * Get all alliances
     */
    public static function getAllAlliances($db) {
        $sql = "SELECT a.*, COUNT(am.id) as member_count
                FROM alliances a
                LEFT JOIN alliance_members am ON a.id = am.alliance_id
                GROUP BY a.id
                ORDER BY member_count DESC, a.created_at DESC";
        return $db->fetchAll($sql);
    }
}
