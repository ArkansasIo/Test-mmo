<?php
class Building {
    private $db;
    private $playerId;
    private $planetId;
    
    public function __construct($playerId, $planetId = null) {
        $this->db = Database::getInstance();
        $this->playerId = $playerId;
        $this->planetId = $planetId;
    }
    
    public function getBuildings() {
        return $this->db->fetch("SELECT * FROM buildings WHERE planet_id = ?", [$this->planetId]);
    }
    
    public function buildStructure($type, $level = 1) {
        try {
            $this->db->insert("buildings", ["planet_id" => $this->planetId, "type" => $type, "level" => $level, "completed_at" => time() + (3600 * $level)]);
            return ["success" => true];
        } catch (Exception $e) {
            return ["success" => false];
        }
    }
}