<?php
class Resource {
    private $db;
    private $playerId;
    
    public function __construct($playerId) {
        $this->db = Database::getInstance();
        $this->playerId = $playerId;
    }
    
    public function getResources() {
        return $this->db->fetchOne("SELECT metal, crystal, deuterium FROM players WHERE id = ?", [$this->playerId]);
    }
    
    public function add($metal = 0, $crystal = 0, $deuterium = 0) {
        $current = $this->getResources();
        $this->db->update("players", ["metal" => $current["metal"] + $metal, "crystal" => $current["crystal"] + $crystal, "deuterium" => $current["deuterium"] + $deuterium], "id = ?", [$this->playerId]);
    }
}