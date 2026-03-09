<?php
class Defense {
    private $db;
    private $playerId;
    private $planetId;
    
    public function __construct($playerId, $planetId = null) {
        $this->db = Database::getInstance();
        $this->playerId = $playerId;
        $this->planetId = $planetId;
    }
    
    public function getDefenses() {
        return $this->db->fetch("SELECT * FROM defenses WHERE player_id = ? AND planet_id = ?", [$this->playerId, $this->planetId]);
    }
    
    public function build($type, $qty) {
        try {
            $this->db->insert("defenses", ["player_id" => $this->playerId, "planet_id" => $this->planetId, "type" => $type, "quantity" => $qty, "created_at" => time()]);
            return ["success" => true];
        } catch (Exception $e) {
            return ["success" => false];
        }
    }
}