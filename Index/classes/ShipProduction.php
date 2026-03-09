<?php
/**
 * Ship Production Class
 * Handles ship building and production
 */

class ShipProduction {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Build ships
     */
    public function buildShips($planetId, $shipType, $amount, $player) {
        // Get ship costs
        $stats = $this->getShipStats($shipType);
        if (!$stats) {
            return ['success' => false, 'message' => 'Invalid ship type'];
        }
        
        // Calculate total cost
        $totalMetal = $stats['metal'] * $amount;
        $totalCrystal = $stats['crystal'] * $amount;
        $totalDeuterium = $stats['deuterium'] * $amount;
        
        // Check if player has enough resources
        if (!$player->hasResources($totalMetal, $totalCrystal, $totalDeuterium)) {
            return ['success' => false, 'message' => 'Not enough resources'];
        }
        
        // Check if shipyard exists
        $planet = new Planet($planetId);
        $shipyardLevel = $planet->getBuildingLevel('shipyard');
        
        if ($shipyardLevel < 1) {
            return ['success' => false, 'message' => 'Shipyard required'];
        }
        
        // Deduct resources
        $player->updateResources(-$totalMetal, -$totalCrystal, -$totalDeuterium);
        
        // Add ships to planet
        $this->addShipsToPlanet($planetId, $shipType, $amount);
        
        // Log activity
        logActivity($player->getId(), 'build_ships', "Built $amount $shipType on planet $planetId");
        
        return ['success' => true, 'message' => "Built $amount ships"];
    }
    
    /**
     * Add ships to planet
     */
    private function addShipsToPlanet($planetId, $shipType, $amount) {
        $existing = $this->db->fetchOne("SELECT * FROM planet_ships WHERE planet_id = ? AND ship_type = ?", 
                                        [$planetId, $shipType]);
        
        if ($existing) {
            $this->db->update('planet_ships', [
                'amount' => $existing['amount'] + $amount
            ], 'id = :id', ['id' => $existing['id']]);
        } else {
            $this->db->insert('planet_ships', [
                'planet_id' => $planetId,
                'ship_type' => $shipType,
                'amount' => $amount
            ]);
        }
    }
    
    /**
     * Get ship statistics
     */
    private function getShipStats($shipType) {
        $stats = [
            'small_cargo' => ['metal' => 2000, 'crystal' => 2000, 'deuterium' => 0, 'time' => 30],
            'large_cargo' => ['metal' => 6000, 'crystal' => 6000, 'deuterium' => 0, 'time' => 90],
            'light_fighter' => ['metal' => 3000, 'crystal' => 1000, 'deuterium' => 0, 'time' => 60],
            'heavy_fighter' => ['metal' => 6000, 'crystal' => 4000, 'deuterium' => 0, 'time' => 120],
            'cruiser' => ['metal' => 20000, 'crystal' => 7000, 'deuterium' => 2000, 'time' => 300],
            'battleship' => ['metal' => 45000, 'crystal' => 15000, 'deuterium' => 0, 'time' => 600],
            'colony_ship' => ['metal' => 10000, 'crystal' => 20000, 'deuterium' => 10000, 'time' => 900],
            'recycler' => ['metal' => 10000, 'crystal' => 6000, 'deuterium' => 2000, 'time' => 180],
            'espionage_probe' => ['metal' => 0, 'crystal' => 1000, 'deuterium' => 0, 'time' => 10],
            'bomber' => ['metal' => 50000, 'crystal' => 25000, 'deuterium' => 15000, 'time' => 1200],
            'destroyer' => ['metal' => 60000, 'crystal' => 50000, 'deuterium' => 15000, 'time' => 1500],
            'deathstar' => ['metal' => 5000000, 'crystal' => 4000000, 'deuterium' => 1000000, 'time' => 86400],
            'battlecruiser' => ['metal' => 30000, 'crystal' => 40000, 'deuterium' => 15000, 'time' => 900]
        ];
        
        return isset($stats[$shipType]) ? $stats[$shipType] : null;
    }
    
    /**
     * Build defenses
     */
    public function buildDefense($planetId, $defenseType, $amount, $player) {
        // Get defense costs
        $stats = $this->getDefenseStats($defenseType);
        if (!$stats) {
            return ['success' => false, 'message' => 'Invalid defense type'];
        }
        
        // Calculate total cost
        $totalMetal = $stats['metal'] * $amount;
        $totalCrystal = $stats['crystal'] * $amount;
        $totalDeuterium = $stats['deuterium'] * $amount;
        
        // Check if player has enough resources
        if (!$player->hasResources($totalMetal, $totalCrystal, $totalDeuterium)) {
            return ['success' => false, 'message' => 'Not enough resources'];
        }
        
        // Deduct resources
        $player->updateResources(-$totalMetal, -$totalCrystal, -$totalDeuterium);
        
        // Add defenses to planet
        $this->addDefenseToPlanet($planetId, $defenseType, $amount);
        
        return ['success' => true, 'message' => "Built $amount defenses"];
    }
    
    /**
     * Add defenses to planet
     */
    private function addDefenseToPlanet($planetId, $defenseType, $amount) {
        $existing = $this->db->fetchOne("SELECT * FROM planet_defenses WHERE planet_id = ? AND defense_type = ?", 
                                        [$planetId, $defenseType]);
        
        if ($existing) {
            $this->db->update('planet_defenses', [
                'amount' => $existing['amount'] + $amount
            ], 'id = :id', ['id' => $existing['id']]);
        } else {
            $this->db->insert('planet_defenses', [
                'planet_id' => $planetId,
                'defense_type' => $defenseType,
                'amount' => $amount
            ]);
        }
    }
    
    /**
     * Get defense statistics
     */
    private function getDefenseStats($defenseType) {
        $stats = [
            'rocket_launcher' => ['metal' => 2000, 'crystal' => 0, 'deuterium' => 0],
            'light_laser' => ['metal' => 1500, 'crystal' => 500, 'deuterium' => 0],
            'heavy_laser' => ['metal' => 6000, 'crystal' => 2000, 'deuterium' => 0],
            'gauss_cannon' => ['metal' => 20000, 'crystal' => 15000, 'deuterium' => 2000],
            'ion_cannon' => ['metal' => 2000, 'crystal' => 6000, 'deuterium' => 0],
            'plasma_turret' => ['metal' => 50000, 'crystal' => 50000, 'deuterium' => 30000],
            'small_shield_dome' => ['metal' => 10000, 'crystal' => 10000, 'deuterium' => 0],
            'large_shield_dome' => ['metal' => 50000, 'crystal' => 50000, 'deuterium' => 0]
        ];
        
        return isset($stats[$defenseType]) ? $stats[$defenseType] : null;
    }
}
