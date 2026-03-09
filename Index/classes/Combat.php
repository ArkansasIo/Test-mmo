<?php
/**
 * Combat Class
 * Handles combat calculations and results
 */

class Combat {
    private $db;
    private $attacker;
    private $defender;
    private $attackerFleet;
    private $defenderFleet;
    private $combatReport;
    
    public function __construct() {
        $this->db = Database::getInstance();
        $this->combatReport = [
            'rounds' => [],
            'winner' => null,
            'loot' => ['metal' => 0, 'crystal' => 0, 'deuterium' => 0],
            'debris' => ['metal' => 0, 'crystal' => 0],
            'attacker_losses' => 0,
            'defender_losses' => 0
        ];
    }
    
    /**
     * Initialize combat
     */
    public function initiate($attackerFleetId, $defenderPlanetId) {
        $this->attackerFleet = $this->loadFleetData($attackerFleetId);
        $this->defenderFleet = $this->loadDefenderFleet($defenderPlanetId);
        
        if (empty($this->attackerFleet) || empty($this->defenderFleet)) {
            return null;
        }
        
        return $this->processCombat();
    }
    
    /**
     * Load fleet data for combat
     */
    private function loadFleetData($fleetId) {
        $sql = "SELECT fs.ship_type, fs.amount 
                FROM fleet_ships fs 
                WHERE fs.fleet_id = ?";
        return $this->db->fetchAll($sql, [$fleetId]);
    }
    
    /**
     * Load defender fleet (ships + defenses)
     */
    private function loadDefenderFleet($planetId) {
        $units = [];
        
        // Load ships
        $sql = "SELECT ship_type, amount FROM planet_ships WHERE planet_id = ?";
        $ships = $this->db->fetchAll($sql, [$planetId]);
        $units = array_merge($units, $ships);
        
        // Load defenses
        $sql = "SELECT defense_type as ship_type, amount FROM planet_defenses WHERE planet_id = ?";
        $defenses = $this->db->fetchAll($sql, [$planetId]);
        $units = array_merge($units, $defenses);
        
        return $units;
    }
    
    /**
     * Process combat rounds
     */
    private function processCombat() {
        $maxRounds = 6;
        $round = 1;
        
        while ($round <= $maxRounds) {
            $roundData = [
                'round' => $round,
                'attacker_fire' => 0,
                'defender_fire' => 0,
                'attacker_losses' => [],
                'defender_losses' => []
            ];
            
            // Calculate total firepower
            $attackerPower = $this->calculateTotalPower($this->attackerFleet);
            $defenderPower = $this->calculateTotalPower($this->defenderFleet);
            
            $roundData['attacker_fire'] = $attackerPower;
            $roundData['defender_fire'] = $defenderPower;
            
            // Apply damage
            $this->applyDamage($this->defenderFleet, $attackerPower, $roundData['defender_losses']);
            $this->applyDamage($this->attackerFleet, $defenderPower, $roundData['attacker_losses']);
            
            $this->combatReport['rounds'][] = $roundData;
            
            // Check if combat should end
            if ($this->calculateTotalPower($this->attackerFleet) == 0) {
                $this->combatReport['winner'] = 'defender';
                break;
            }
            
            if ($this->calculateTotalPower($this->defenderFleet) == 0) {
                $this->combatReport['winner'] = 'attacker';
                break;
            }
            
            $round++;
        }
        
        // If no winner after 6 rounds, it's a draw
        if ($this->combatReport['winner'] === null) {
            $this->combatReport['winner'] = 'draw';
        }
        
        // Calculate loot and debris
        $this->calculateLoot();
        $this->calculateDebris();
        
        return $this->combatReport;
    }
    
    /**
     * Calculate total attack power
     */
    private function calculateTotalPower($fleet) {
        $unitStats = $this->getUnitStats();
        $totalPower = 0;
        
        foreach ($fleet as $unit) {
            if (isset($unitStats[$unit['ship_type']])) {
                $totalPower += $unitStats[$unit['ship_type']]['attack'] * $unit['amount'];
            }
        }
        
        return $totalPower;
    }
    
    /**
     * Apply damage to fleet
     */
    private function applyDamage(&$fleet, $totalDamage, &$losses) {
        $unitStats = $this->getUnitStats();
        
        foreach ($fleet as &$unit) {
            if ($totalDamage <= 0) break;
            
            if (!isset($unitStats[$unit['ship_type']])) continue;
            
            $unitShield = $unitStats[$unit['ship_type']]['shield'];
            $unitArmor = $unitStats[$unit['ship_type']]['armor'];
            $unitTotal = $unitShield + $unitArmor;
            
            $unitsDestroyed = 0;
            
            while ($unit['amount'] > 0 && $totalDamage > 0) {
                // Damage exceeds shield + armor, unit is destroyed
                if ($totalDamage >= $unitTotal) {
                    $unitsDestroyed++;
                    $unit['amount']--;
                    $totalDamage -= $unitTotal;
                } else {
                    // Partial damage - 30% chance to destroy unit
                    if (rand(1, 100) <= 30) {
                        $unitsDestroyed++;
                        $unit['amount']--;
                    }
                    $totalDamage = 0;
                }
            }
            
            if ($unitsDestroyed > 0) {
                $losses[] = [
                    'type' => $unit['ship_type'],
                    'amount' => $unitsDestroyed
                ];
            }
        }
        
        // Remove units with 0 amount
        $fleet = array_filter($fleet, function($unit) {
            return $unit['amount'] > 0;
        });
    }
    
    /**
     * Get unit statistics
     */
    private function getUnitStats() {
        return [
            // Ships
            'small_cargo' => ['attack' => 5, 'shield' => 10, 'armor' => 400],
            'large_cargo' => ['attack' => 5, 'shield' => 25, 'armor' => 1200],
            'light_fighter' => ['attack' => 50, 'shield' => 10, 'armor' => 400],
            'heavy_fighter' => ['attack' => 150, 'shield' => 25, 'armor' => 1000],
            'cruiser' => ['attack' => 400, 'shield' => 50, 'armor' => 2700],
            'battleship' => ['attack' => 1000, 'shield' => 200, 'armor' => 6000],
            'colony_ship' => ['attack' => 50, 'shield' => 100, 'armor' => 3000],
            'recycler' => ['attack' => 1, 'shield' => 10, 'armor' => 1600],
            'espionage_probe' => ['attack' => 0, 'shield' => 0, 'armor' => 100],
            'bomber' => ['attack' => 1000, 'shield' => 500, 'armor' => 7500],
            'destroyer' => ['attack' => 2000, 'shield' => 500, 'armor' => 11000],
            'deathstar' => ['attack' => 200000, 'shield' => 50000, 'armor' => 900000],
            'battlecruiser' => ['attack' => 700, 'shield' => 400, 'armor' => 7000],
            
            // Defenses
            'rocket_launcher' => ['attack' => 80, 'shield' => 20, 'armor' => 200],
            'light_laser' => ['attack' => 100, 'shield' => 25, 'armor' => 200],
            'heavy_laser' => ['attack' => 250, 'shield' => 100, 'armor' => 800],
            'gauss_cannon' => ['attack' => 1100, 'shield' => 200, 'armor' => 3500],
            'ion_cannon' => ['attack' => 150, 'shield' => 500, 'armor' => 800],
            'plasma_turret' => ['attack' => 3000, 'shield' => 300, 'armor' => 10000],
            'small_shield_dome' => ['attack' => 1, 'shield' => 2000, 'armor' => 2000],
            'large_shield_dome' => ['attack' => 1, 'shield' => 10000, 'armor' => 10000]
        ];
    }
    
    /**
     * Calculate loot
     */
    private function calculateLoot() {
        // Attacker can loot up to 50% of available resources
        // This is simplified - actual implementation would check planet resources
        $this->combatReport['loot'] = [
            'metal' => 0,
            'crystal' => 0,
            'deuterium' => 0
        ];
    }
    
    /**
     * Calculate debris field
     */
    private function calculateDebris() {
        // 30% of destroyed ships become debris
        $totalDebris = ($this->combatReport['attacker_losses'] + $this->combatReport['defender_losses']) * 0.3;
        
        $this->combatReport['debris'] = [
            'metal' => floor($totalDebris * 0.6),
            'crystal' => floor($totalDebris * 0.4)
        ];
    }
    
    /**
     * Get combat report
     */
    public function getReport() {
        return $this->combatReport;
    }
    
    /**
     * Save combat report to database
     */
    public function saveCombatReport($attackerId, $defenderId) {
        $reportData = [
            'attacker_id' => $attackerId,
            'defender_id' => $defenderId,
            'combat_data' => json_encode($this->combatReport),
            'winner' => $this->combatReport['winner'],
            'created_at' => time()
        ];
        
        return $this->db->insert('combat_reports', $reportData);
    }
}
