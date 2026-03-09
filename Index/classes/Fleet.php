<?php
/**
 * Fleet Class
 * Handles fleet data and operations
 */

class Fleet {
    private $db;
    private $id;
    private $data;
    
    public function __construct($fleetId = null) {
        $this->db = Database::getInstance();
        if ($fleetId) {
            $this->id = $fleetId;
            $this->loadFleetData();
        }
    }
    
    /**
     * Load fleet data from database
     */
    private function loadFleetData() {
        $sql = "SELECT * FROM fleets WHERE id = ?";
        $this->data = $this->db->fetchOne($sql, [$this->id]);
    }
    
    /**
     * Create a new fleet
     */
    public function create($playerId, $planetId, $ships) {
        if (empty($ships) || !is_array($ships)) {
            return false;
        }

        $normalizedShips = [];
        foreach ($ships as $shipType => $amount) {
            $amount = (int)$amount;
            if ($amount > 0) {
                $normalizedShips[$shipType] = $amount;
            }
        }

        if (empty($normalizedShips)) {
            return false;
        }

        // Validate that requested ships exist on the source planet.
        foreach ($normalizedShips as $shipType => $amount) {
            $available = $this->db->fetchOne(
                "SELECT amount FROM planet_ships WHERE planet_id = ? AND ship_type = ?",
                [$planetId, $shipType]
            );
            $availableAmount = $available ? (int)$available['amount'] : 0;
            if ($availableAmount < $amount) {
                return false;
            }
        }

        $fleetData = [
            'player_id' => $playerId,
            'planet_id' => $planetId,
            'created_at' => time()
        ];
        
        $this->id = $this->db->insert('fleets', $fleetData);
        
        if ($this->id) {
            // Add ships to fleet
            foreach ($normalizedShips as $shipType => $amount) {
                $this->db->insert('fleet_ships', [
                    'fleet_id' => $this->id,
                    'ship_type' => $shipType,
                    'amount' => $amount
                ]);

                // Reserve ships by removing them from the origin planet.
                $this->db->query(
                    "UPDATE planet_ships SET amount = amount - ? WHERE planet_id = ? AND ship_type = ?",
                    [$amount, $planetId, $shipType]
                );
            }
            
            $this->loadFleetData();
            return $this->id;
        }
        
        return false;
    }
    
    /**
     * Send fleet on mission
     */
    public function sendMission($targetGalaxy, $targetSystem, $targetPosition, $missionType, $cargo = []) {
        if (!$this->id) {
            return ['success' => false, 'message' => 'Invalid fleet'];
        }

        $originPlanet = $this->db->fetchOne("SELECT galaxy, system, position FROM planets WHERE id = ?", [$this->data['planet_id']]);
        if (!$originPlanet) {
            return ['success' => false, 'message' => 'Origin planet not found'];
        }

        // Calculate travel time
        $distance = $this->calculateDistance(
            (int)$originPlanet['galaxy'],
            (int)$originPlanet['system'],
            (int)$originPlanet['position'],
            $targetGalaxy,
            $targetSystem,
            $targetPosition
        );

        $speed = $this->getFleetSpeed();
        if ($speed <= 0) {
            return ['success' => false, 'message' => 'Fleet has no valid ships'];
        }

        $travelTime = ($distance / $speed) * 3600; // Convert to seconds
        $travelTime = (int)max(60, ceil($travelTime));

        $cargoMetal = isset($cargo['metal']) ? max(0, (int)$cargo['metal']) : 0;
        $cargoCrystal = isset($cargo['crystal']) ? max(0, (int)$cargo['crystal']) : 0;
        $cargoDeuterium = isset($cargo['deuterium']) ? max(0, (int)$cargo['deuterium']) : 0;

        $totalCargo = $cargoMetal + $cargoCrystal + $cargoDeuterium;
        if ($totalCargo > $this->getCargoCapacity()) {
            return ['success' => false, 'message' => 'Cargo exceeds fleet capacity'];
        }

        if ($totalCargo > 0) {
            $player = $this->db->fetchOne("SELECT metal, crystal, deuterium FROM players WHERE id = ?", [$this->data['player_id']]);
            if (!$player || $player['metal'] < $cargoMetal || $player['crystal'] < $cargoCrystal || $player['deuterium'] < $cargoDeuterium) {
                return ['success' => false, 'message' => 'Not enough resources for cargo'];
            }

            $this->db->query(
                "UPDATE players SET metal = metal - ?, crystal = crystal - ?, deuterium = deuterium - ? WHERE id = ?",
                [$cargoMetal, $cargoCrystal, $cargoDeuterium, $this->data['player_id']]
            );
        }
        
        // Create fleet movement
        $movementData = [
            'fleet_id' => $this->id,
            'player_id' => $this->data['player_id'],
            'start_galaxy' => (int)$originPlanet['galaxy'],
            'start_system' => (int)$originPlanet['system'],
            'start_position' => (int)$originPlanet['position'],
            'target_galaxy' => $targetGalaxy,
            'target_system' => $targetSystem,
            'target_position' => $targetPosition,
            'mission_type' => $missionType,
            'departure_time' => time(),
            'arrival_time' => time() + $travelTime,
            'status' => 'traveling',
            'cargo_metal' => $cargoMetal,
            'cargo_crystal' => $cargoCrystal,
            'cargo_deuterium' => $cargoDeuterium
        ];
        
        $movementId = $this->db->insert('fleet_movements', $movementData);
        
        return [
            'success' => true,
            'movement_id' => $movementId,
            'arrival_time' => time() + $travelTime
        ];
    }
    
    /**
     * Calculate distance between two coordinates
     */
    private function calculateDistance($g1, $s1, $p1, $g2, $s2, $p2) {
        if ($g1 != $g2) {
            return abs($g1 - $g2) * 20000;
        } elseif ($s1 != $s2) {
            return abs($s1 - $s2) * 5 * 19 + 2700;
        } else {
            return abs($p1 - $p2) * 5 + 1000;
        }
    }
    
    /**
     * Get fleet speed (slowest ship determines fleet speed)
     */
    private function getFleetSpeed() {
        $sql = "SELECT ship_type FROM fleet_ships WHERE fleet_id = ?";
        $ships = $this->db->fetchAll($sql, [$this->id]);
        
        $shipSpeeds = [
            'small_cargo' => 5000,
            'large_cargo' => 7500,
            'light_fighter' => 12500,
            'heavy_fighter' => 10000,
            'cruiser' => 15000,
            'battleship' => 10000,
            'colony_ship' => 2500,
            'recycler' => 2000,
            'espionage_probe' => 100000000,
            'bomber' => 4000,
            'solar_satellite' => 0,
            'destroyer' => 5000,
            'deathstar' => 100,
            'battlecruiser' => 10000
        ];
        
        $minSpeed = 100000000;
        foreach ($ships as $ship) {
            if (isset($shipSpeeds[$ship['ship_type']])) {
                $minSpeed = min($minSpeed, $shipSpeeds[$ship['ship_type']]);
            }
        }
        
        return $minSpeed;
    }
    
    /**
     * Get fleet ships
     */
    public function getShips() {
        $sql = "SELECT * FROM fleet_ships WHERE fleet_id = ?";
        return $this->db->fetchAll($sql, [$this->id]);
    }
    
    /**
     * Get total cargo capacity
     */
    public function getCargoCapacity() {
        $ships = $this->getShips();
        $totalCapacity = 0;
        
        $cargoCapacities = [
            'small_cargo' => 5000,
            'large_cargo' => 25000,
            'light_fighter' => 50,
            'heavy_fighter' => 100,
            'cruiser' => 800,
            'battleship' => 1500,
            'colony_ship' => 7500,
            'recycler' => 20000,
            'espionage_probe' => 5,
            'bomber' => 500,
            'destroyer' => 2000,
            'deathstar' => 1000000,
            'battlecruiser' => 750
        ];
        
        foreach ($ships as $ship) {
            if (isset($cargoCapacities[$ship['ship_type']])) {
                $totalCapacity += $cargoCapacities[$ship['ship_type']] * $ship['amount'];
            }
        }
        
        return $totalCapacity;
    }
    
    /**
     * Get fleet data
     */
    public function getData($key = null) {
        if ($key) {
            return isset($this->data[$key]) ? $this->data[$key] : null;
        }
        return $this->data;
    }
    
    /**
     * Recall fleet
     */
    public function recall() {
        $movement = $this->db->fetchOne("SELECT * FROM fleet_movements WHERE fleet_id = ? AND status = 'traveling' ORDER BY id DESC LIMIT 1", [$this->id]);
        if (!$movement) {
            return false;
        }

        $elapsed = max(60, time() - (int)$movement['departure_time']);
        $sql = "UPDATE fleet_movements
                SET status = 'returning', departure_time = ?, arrival_time = ?
                WHERE id = ?";
        return $this->db->query($sql, [time(), time() + $elapsed, $movement['id']]);
    }
    
    /**
     * Get fleet ID
     */
    public function getId() {
        return $this->id;
    }
}
