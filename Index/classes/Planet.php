<?php
/**
 * Planet Class
 * Handles planet data and operations
 */

class Planet {
    private $db;
    private $id;
    private $data;
    
    public function __construct($planetId = null) {
        $this->db = Database::getInstance();
        if ($planetId) {
            $this->id = $planetId;
            $this->loadPlanetData();
        }
    }
    
    /**
     * Load planet data from database
     */
    private function loadPlanetData() {
        $sql = "SELECT * FROM planets WHERE id = ?";
        $this->data = $this->db->fetchOne($sql, [$this->id]);
    }
    
    /**
     * Create a new planet
     */
    public function create($playerId, $name, $galaxy, $system, $position) {
        // Check if position is already occupied
        if ($this->positionOccupied($galaxy, $system, $position)) {
            return false;
        }
        
        $planetData = [
            'player_id' => $playerId,
            'name' => $name,
            'galaxy' => $galaxy,
            'system' => $system,
            'position' => $position,
            'diameter' => rand(8000, 16000),
            'fields' => rand(150, 300),
            'fields_used' => 0,
            'temperature' => rand(-50, 80),
            'is_capital' => $this->isFirstPlanet($playerId) ? 1 : 0,
            'created_at' => time()
        ];
        
        $this->id = $this->db->insert('planets', $planetData);
        
        if ($this->id) {
            // Create starting buildings
            $this->createStartingBuildings();
            $this->loadPlanetData();
            return $this->id;
        }
        
        return false;
    }
    
    /**
     * Check if position is occupied
     */
    private function positionOccupied($galaxy, $system, $position) {
        $sql = "SELECT COUNT(*) as count FROM planets WHERE galaxy = ? AND system = ? AND position = ?";
        $result = $this->db->fetchOne($sql, [$galaxy, $system, $position]);
        return $result['count'] > 0;
    }
    
    /**
     * Check if this is player's first planet
     */
    private function isFirstPlanet($playerId) {
        $sql = "SELECT COUNT(*) as count FROM planets WHERE player_id = ?";
        $result = $this->db->fetchOne($sql, [$playerId]);
        return $result['count'] == 0;
    }
    
    /**
     * Create starting buildings for new planet
     */
    private function createStartingBuildings() {
        $startingBuildings = [
            ['building_type' => 'metal_mine', 'level' => 1],
            ['building_type' => 'crystal_mine', 'level' => 1],
            ['building_type' => 'solar_plant', 'level' => 1]
        ];
        
        foreach ($startingBuildings as $building) {
            $this->db->insert('buildings', [
                'planet_id' => $this->id,
                'building_type' => $building['building_type'],
                'level' => $building['level']
            ]);
        }
    }
    
    /**
     * Get planet data
     */
    public function getData($key = null) {
        if ($key) {
            return isset($this->data[$key]) ? $this->data[$key] : null;
        }
        return $this->data;
    }
    
    /**
     * Get planet buildings
     */
    public function getBuildings() {
        $sql = "SELECT * FROM buildings WHERE planet_id = ?";
        return $this->db->fetchAll($sql, [$this->id]);
    }
    
    /**
     * Get building level
     */
    public function getBuildingLevel($buildingType) {
        $sql = "SELECT level FROM buildings WHERE planet_id = ? AND building_type = ?";
        $result = $this->db->fetchOne($sql, [$this->id, $buildingType]);
        return $result ? $result['level'] : 0;
    }
    
    /**
     * Start building upgrade
     */
    public function upgradeBuilding($buildingType, $player) {
        $currentLevel = $this->getBuildingLevel($buildingType);
        $nextLevel = $currentLevel + 1;
        
        // Calculate costs
        $costs = $this->calculateBuildingCost($buildingType, $nextLevel);
        
        // Check if player has enough resources
        if (!$player->hasResources($costs['metal'], $costs['crystal'], $costs['deuterium'])) {
            return ['success' => false, 'message' => 'Not enough resources'];
        }
        
        // Check if there's already a building in queue
        if ($this->hasBuildingInQueue()) {
            return ['success' => false, 'message' => 'Building already in progress'];
        }
        
        // Deduct resources
        $player->updateResources(-$costs['metal'], -$costs['crystal'], -$costs['deuterium']);
        
        // Add to building queue
        $completionTime = time() + ($costs['time'] / BUILDING_SPEED_MULTIPLIER);
        
        $this->db->insert('building_queue', [
            'planet_id' => $this->id,
            'building_type' => $buildingType,
            'level' => $nextLevel,
            'start_time' => time(),
            'completion_time' => $completionTime
        ]);
        
        return ['success' => true, 'message' => 'Building upgrade started'];
    }
    
    /**
     * Check if planet has building in queue
     */
    private function hasBuildingInQueue() {
        $sql = "SELECT COUNT(*) as count FROM building_queue WHERE planet_id = ?";
        $result = $this->db->fetchOne($sql, [$this->id]);
        return $result['count'] > 0;
    }
    
    /**
     * Calculate building cost
     */
    private function calculateBuildingCost($buildingType, $level) {
        $baseCosts = [
            'metal_mine' => ['metal' => 60, 'crystal' => 15, 'deuterium' => 0, 'time' => 60],
            'crystal_mine' => ['metal' => 48, 'crystal' => 24, 'deuterium' => 0, 'time' => 60],
            'deuterium_synthesizer' => ['metal' => 225, 'crystal' => 75, 'deuterium' => 0, 'time' => 90],
            'solar_plant' => ['metal' => 75, 'crystal' => 30, 'deuterium' => 0, 'time' => 60],
            'fusion_reactor' => ['metal' => 900, 'crystal' => 360, 'deuterium' => 180, 'time' => 300],
            'robotics_factory' => ['metal' => 400, 'crystal' => 120, 'deuterium' => 200, 'time' => 120],
            'shipyard' => ['metal' => 400, 'crystal' => 200, 'deuterium' => 100, 'time' => 180],
            'research_lab' => ['metal' => 200, 'crystal' => 400, 'deuterium' => 200, 'time' => 150],
            'alliance_depot' => ['metal' => 20000, 'crystal' => 40000, 'deuterium' => 0, 'time' => 600],
            'missile_silo' => ['metal' => 20000, 'crystal' => 20000, 'deuterium' => 1000, 'time' => 600],
            'nanite_factory' => ['metal' => 1000000, 'crystal' => 500000, 'deuterium' => 100000, 'time' => 3600],
            'terraformer' => ['metal' => 0, 'crystal' => 50000, 'deuterium' => 100000, 'time' => 1800],
            'space_dock' => ['metal' => 200, 'crystal' => 0, 'deuterium' => 50, 'time' => 90]
        ];
        
        if (!isset($baseCosts[$buildingType])) {
            return ['metal' => 0, 'crystal' => 0, 'deuterium' => 0, 'time' => 0];
        }
        
        $base = $baseCosts[$buildingType];
        $multiplier = pow(2, $level - 1);
        
        return [
            'metal' => floor($base['metal'] * $multiplier),
            'crystal' => floor($base['crystal'] * $multiplier),
            'deuterium' => floor($base['deuterium'] * $multiplier),
            'time' => floor($base['time'] * $multiplier)
        ];
    }
    
    /**
     * Get resource production per hour
     */
    public function getProduction() {
        $buildings = $this->getBuildings();
        $production = ['metal' => 0, 'crystal' => 0, 'deuterium' => 0, 'energy' => 0];
        
        foreach ($buildings as $building) {
            switch ($building['building_type']) {
                case 'metal_mine':
                    $production['metal'] += $building['level'] * 30;
                    break;
                case 'crystal_mine':
                    $production['crystal'] += $building['level'] * 20;
                    break;
                case 'deuterium_synthesizer':
                    $production['deuterium'] += $building['level'] * 10;
                    break;
                case 'solar_plant':
                    $production['energy'] += $building['level'] * 20;
                    break;
            }
        }
        
        return $production;
    }
    
    /**
     * Get planet ID
     */
    public function getId() {
        return $this->id;
    }
}
