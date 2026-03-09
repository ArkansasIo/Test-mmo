<?php
/**
 * GameEngine Class
 * Main game engine that handles core game logic
 */

require_once __DIR__ . '/TaskGenerator.php';

class GameEngine {
    private $db;
    private $currentTime;

    public function __construct() {
        $this->db = Database::getInstance();
        $this->currentTime = time();
    }

    /**
     * Entry point used by web requests and cron tasks.
     */
    public function processAutomatedTasks() {
        $this->runTick();
    }

    /**
     * Process automated tasks like resource production, building completion, etc.
     */
    private function runTick() {
        // Process resource production for all players
        $this->updateResourceProduction();

        // Check for completed buildings
        $this->checkBuildingCompletion();

        // Check for completed research
        $this->checkResearchCompletion();

        // Process fleet movements
        $this->processFleetMovements();
    }

    /**
     * Update resource production for all active players
     */
    private function updateResourceProduction() {
        $sql = "SELECT p.id, p.metal, p.crystal, p.deuterium, p.last_resource_update,
                       COALESCE(SUM(CASE WHEN b.building_type = 'metal_mine' THEN b.level * 30 ELSE 0 END), 0) AS metal_production,
                       COALESCE(SUM(CASE WHEN b.building_type = 'crystal_mine' THEN b.level * 20 ELSE 0 END), 0) AS crystal_production,
                       COALESCE(SUM(CASE WHEN b.building_type = 'deuterium_synthesizer' THEN b.level * 10 ELSE 0 END), 0) AS deuterium_production
                FROM players p
                LEFT JOIN planets pl ON pl.player_id = p.id
                LEFT JOIN buildings b ON b.planet_id = pl.id
                WHERE p.last_activity > ?
                GROUP BY p.id";

        $players = $this->db->fetchAll($sql, [$this->currentTime - 3600]);

        foreach ($players as $player) {
            $lastUpdate = isset($player['last_resource_update']) ? (int)$player['last_resource_update'] : $this->currentTime;
            $timeDiff = max(0, $this->currentTime - $lastUpdate);
            if ($timeDiff === 0) {
                continue;
            }

            $hours = $timeDiff / 3600;

            $metalGained = $player['metal_production'] * $hours * PRODUCTION_MULTIPLIER;
            $crystalGained = $player['crystal_production'] * $hours * PRODUCTION_MULTIPLIER;
            $deuteriumGained = $player['deuterium_production'] * $hours * PRODUCTION_MULTIPLIER;

            $this->db->update('players', [
                'metal' => (int)floor($player['metal'] + $metalGained),
                'crystal' => (int)floor($player['crystal'] + $crystalGained),
                'deuterium' => (int)floor($player['deuterium'] + $deuteriumGained),
                'last_resource_update' => $this->currentTime
            ], 'id = :id', ['id' => $player['id']]);
            
            // Check for low resources and trigger task if needed
            $newMetal = (int)floor($player['metal'] + $metalGained);
            $newCrystal = (int)floor($player['crystal'] + $crystalGained);
            if (($newMetal < 1000 || $newCrystal < 1000) && ($player['metal'] >= 1000 || $player['crystal'] >= 1000)) {
                try {
                    $generator = new TaskGenerator();
                    $lowResource = $newMetal < $newCrystal ? 'metal' : 'crystal';
                    $generator->generateEventTask((int)$player['id'], 'low_resources', [
                        'resource_type' => $lowResource,
                        'current' => $lowResource === 'metal' ? $newMetal : $newCrystal
                    ]);
                } catch (Exception $e) {
                    // Silently fail if task generation fails
                }
            }
        }
    }

    /**
     * Check for completed building constructions
     */
    private function checkBuildingCompletion() {
        $sql = "SELECT * FROM building_queue WHERE completion_time <= ?";
        $completedBuildings = $this->db->fetchAll($sql, [$this->currentTime]);

        foreach ($completedBuildings as $building) {
            // Upsert building level for the completed queue item.
            $this->db->query(
                "INSERT INTO buildings (planet_id, building_type, level)
                 VALUES (?, ?, ?)
                 ON DUPLICATE KEY UPDATE level = VALUES(level)",
                [$building['planet_id'], $building['building_type'], $building['level']]
            );

            // Remove from queue
            $this->db->delete('building_queue', 'id = :id', ['id' => $building['id']]);

            $planetOwner = $this->db->fetchOne("SELECT player_id FROM planets WHERE id = ?", [$building['planet_id']]);
            if ($planetOwner) {
                $playerId = (int)$planetOwner['player_id'];
                $this->createNotification(
                    $playerId,
                    'building',
                    'Building Complete',
                    ucfirst(str_replace('_', ' ', $building['building_type'])) . " reached level " . (int)$building['level'] . "."
                );
                
                // Trigger task generation for building completion
                try {
                    $generator = new TaskGenerator();
                    $generator->generateEventTask($playerId, 'building_complete', [
                        'planet_id' => (int)$building['planet_id'],
                        'building_type' => $building['building_type'],
                        'level' => (int)$building['level']
                    ]);
                } catch (Exception $e) {
                    // Silently fail if task generation fails
                }
            }
        }
    }

    /**
     * Check for completed research
     */
    private function checkResearchCompletion() {
        $sql = "SELECT * FROM research_queue WHERE completion_time <= ?";
        $completedResearch = $this->db->fetchAll($sql, [$this->currentTime]);

        foreach ($completedResearch as $research) {
            // Upsert research level for the completed queue item.
            $this->db->query(
                "INSERT INTO research (player_id, research_type, level)
                 VALUES (?, ?, ?)
                 ON DUPLICATE KEY UPDATE level = VALUES(level)",
                [$research['player_id'], $research['research_type'], $research['level']]
            );

            // Remove from queue
            $this->db->delete('research_queue', 'id = :id', ['id' => $research['id']]);

            $playerId = (int)$research['player_id'];
            $this->createNotification(
                $playerId,
                'research',
                'Research Complete',
                ucfirst(str_replace('_', ' ', $research['research_type'])) . " reached level " . (int)$research['level'] . "."
            );
            
            // Trigger task generation for research completion
            try {
                $generator = new TaskGenerator();
                $generator->generateEventTask($playerId, 'research_complete', [
                    'research_type' => $research['research_type'],
                    'level' => (int)$research['level']
                ]);
            } catch (Exception $e) {
                // Silently fail if task generation fails
            }
        }
    }

    /**
     * Process fleet movements
     */
    private function processFleetMovements() {
        $sql = "SELECT * FROM fleet_movements WHERE arrival_time <= ? AND status IN ('traveling', 'returning', 'recalled')";
        $arrivedFleets = $this->db->fetchAll($sql, [$this->currentTime]);

        foreach ($arrivedFleets as $fleet) {
            if ($fleet['status'] !== 'traveling') {
                $this->completeFleetReturn($fleet);
                continue;
            }

            switch ($fleet['mission_type']) {
                case 'attack':
                    $this->processCombat($fleet);
                    break;
                case 'transport':
                    $this->processTransport($fleet);
                    break;
                case 'colonize':
                    $this->processColonization($fleet);
                    break;
                case 'spy':
                    $this->processSpyMission($fleet);
                    break;
                case 'deploy':
                    $this->scheduleFleetReturn($fleet, 'returning');
                    break;
                default:
                    $this->scheduleFleetReturn($fleet, 'returning');
                    break;
            }
        }
    }

    /**
     * Process combat between fleets
     */
    private function processCombat($fleet) {
        $targetPlanet = $this->findPlanetByCoordinates(
            (int)$fleet['target_galaxy'],
            (int)$fleet['target_system'],
            (int)$fleet['target_position']
        );

        if (!$targetPlanet) {
            $this->scheduleFleetReturn($fleet, 'returning');
            return;
        }

        $combat = new Combat();
        $report = $combat->initiate((int)$fleet['fleet_id'], (int)$targetPlanet['id']);
        if ($report !== null) {
            $combat->saveCombatReport((int)$fleet['player_id'], (int)$targetPlanet['player_id']);
            $attackerId = (int)$fleet['player_id'];
            $defenderId = (int)$targetPlanet['player_id'];
            
            $this->createNotification($attackerId, 'combat', 'Combat Report', 'Your fleet engaged enemy defenses.');
            $this->createNotification($defenderId, 'combat', 'Under Attack', 'Your planet was attacked.');
            
            // Trigger task generation for combat events
            try {
                $generator = new TaskGenerator();
                // Attacker task
                $generator->generateEventTask($attackerId, 'fleet_attacked', [
                    'fleet_id' => (int)$fleet['fleet_id'],
                    'target_planet' => (int)$targetPlanet['id'],
                    'defended' => false
                ]);
                // Defender task
                $generator->generateEventTask($defenderId, 'fleet_attacked', [
                    'fleet_id' => (int)$fleet['fleet_id'],
                    'planet_id' => (int)$targetPlanet['id'],
                    'defended' => true
                ]);
            } catch (Exception $e) {
                // Silently fail if task generation fails
            }
        }

        $this->scheduleFleetReturn($fleet, 'returning');
    }

    /**
     * Process resource transport
     */
    private function processTransport($fleet) {
        $targetPlanet = $this->findPlanetByCoordinates(
            (int)$fleet['target_galaxy'],
            (int)$fleet['target_system'],
            (int)$fleet['target_position']
        );

        if ($targetPlanet) {
            $this->db->query(
                "UPDATE players
                 SET metal = metal + ?, crystal = crystal + ?, deuterium = deuterium + ?
                 WHERE id = ?",
                [
                    (int)$fleet['cargo_metal'],
                    (int)$fleet['cargo_crystal'],
                    (int)$fleet['cargo_deuterium'],
                    (int)$targetPlanet['player_id']
                ]
            );

            $this->createNotification(
                (int)$targetPlanet['player_id'],
                'fleet',
                'Resources Delivered',
                'Incoming transport arrived at your planet.'
            );
        }

        $this->db->update('fleet_movements', [
            'cargo_metal' => 0,
            'cargo_crystal' => 0,
            'cargo_deuterium' => 0
        ], 'id = :id', ['id' => $fleet['id']]);

        $this->scheduleFleetReturn($fleet, 'returning');
    }

    /**
     * Process colonization mission
     */
    private function processColonization($fleet) {
        $existingPlanet = $this->findPlanetByCoordinates(
            (int)$fleet['target_galaxy'],
            (int)$fleet['target_system'],
            (int)$fleet['target_position']
        );

        if (!$existingPlanet) {
            $planetName = 'Colony ' . (int)$fleet['target_galaxy'] . ':' . (int)$fleet['target_system'] . ':' . (int)$fleet['target_position'];
            $this->db->insert('planets', [
                'player_id' => (int)$fleet['player_id'],
                'name' => $planetName,
                'galaxy' => (int)$fleet['target_galaxy'],
                'system' => (int)$fleet['target_system'],
                'position' => (int)$fleet['target_position'],
                'diameter' => rand(8000, 16000),
                'fields' => rand(150, 300),
                'fields_used' => 0,
                'temperature' => rand(-50, 80),
                'is_capital' => 0,
                'created_at' => $this->currentTime
            ]);

            $this->createNotification(
                (int)$fleet['player_id'],
                'colonization',
                'Colonization Complete',
                'A new colony was established successfully.'
            );
        }

        $this->scheduleFleetReturn($fleet, 'returning');
    }

    /**
     * Process spy mission
     */
    private function processSpyMission($fleet) {
        $targetPlanet = $this->findPlanetByCoordinates(
            (int)$fleet['target_galaxy'],
            (int)$fleet['target_system'],
            (int)$fleet['target_position']
        );

        if ($targetPlanet) {
            $targetPlayer = $this->db->fetchOne(
                "SELECT username, metal, crystal, deuterium FROM players WHERE id = ?",
                [(int)$targetPlanet['player_id']]
            );

            if ($targetPlayer) {
                $message = sprintf(
                    "Spy report for [%d:%d:%d] (%s): M %d, C %d, D %d",
                    (int)$targetPlanet['galaxy'],
                    (int)$targetPlanet['system'],
                    (int)$targetPlanet['position'],
                    $targetPlayer['username'],
                    (int)$targetPlayer['metal'],
                    (int)$targetPlayer['crystal'],
                    (int)$targetPlayer['deuterium']
                );

                $this->createNotification((int)$fleet['player_id'], 'spy', 'Espionage Report', $message);
            }
        }

        $this->scheduleFleetReturn($fleet, 'returning');
    }

    private function findPlanetByCoordinates($galaxy, $system, $position) {
        return $this->db->fetchOne(
            "SELECT * FROM planets WHERE galaxy = ? AND system = ? AND position = ? LIMIT 1",
            [$galaxy, $system, $position]
        );
    }

    private function scheduleFleetReturn($fleet, $status) {
        $flightTime = max(60, (int)$fleet['arrival_time'] - (int)$fleet['departure_time']);

        $this->db->update('fleet_movements', [
            'status' => $status,
            'departure_time' => $this->currentTime,
            'arrival_time' => $this->currentTime + $flightTime
        ], 'id = :id', ['id' => $fleet['id']]);
    }

    private function completeFleetReturn($fleet) {
        $originPlanet = $this->db->fetchOne("SELECT id, player_id FROM planets WHERE id = (SELECT planet_id FROM fleets WHERE id = ?)", [(int)$fleet['fleet_id']]);

        if ($originPlanet) {
            $fleetShips = $this->db->fetchAll("SELECT ship_type, amount FROM fleet_ships WHERE fleet_id = ?", [(int)$fleet['fleet_id']]);
            foreach ($fleetShips as $ship) {
                $this->db->query(
                    "INSERT INTO planet_ships (planet_id, ship_type, amount)
                     VALUES (?, ?, ?)
                     ON DUPLICATE KEY UPDATE amount = amount + VALUES(amount)",
                    [(int)$originPlanet['id'], $ship['ship_type'], (int)$ship['amount']]
                );
            }

            $cargoMetal = (int)$fleet['cargo_metal'];
            $cargoCrystal = (int)$fleet['cargo_crystal'];
            $cargoDeuterium = (int)$fleet['cargo_deuterium'];
            if ($cargoMetal > 0 || $cargoCrystal > 0 || $cargoDeuterium > 0) {
                $this->db->query(
                    "UPDATE players SET metal = metal + ?, crystal = crystal + ?, deuterium = deuterium + ? WHERE id = ?",
                    [$cargoMetal, $cargoCrystal, $cargoDeuterium, (int)$originPlanet['player_id']]
                );
            }

            $this->createNotification((int)$originPlanet['player_id'], 'fleet', 'Fleet Returned', 'One of your fleets has returned to base.');
        }

        $this->db->delete('fleet_movements', 'id = :id', ['id' => $fleet['id']]);
        $this->db->delete('fleet_ships', 'fleet_id = :fleet_id', ['fleet_id' => $fleet['fleet_id']]);
        $this->db->delete('fleets', 'id = :id', ['id' => $fleet['fleet_id']]);
    }

    private function createNotification($playerId, $type, $title, $message) {
        $this->db->insert('notifications', [
            'player_id' => $playerId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'is_read' => 0,
            'created_at' => $this->currentTime
        ]);
    }

    /**
     * Get game statistics
     */
    public function getGameStats() {
        $stats = [];
        $stats['total_players'] = $this->db->fetchOne("SELECT COUNT(*) as count FROM players")['count'];
        $stats['online_players'] = $this->db->fetchOne("SELECT COUNT(*) as count FROM players WHERE last_activity > ?", [$this->currentTime - 900])['count'];
        $stats['total_planets'] = $this->db->fetchOne("SELECT COUNT(*) as count FROM planets")['count'];
        $stats['total_fleets'] = $this->db->fetchOne("SELECT COUNT(*) as count FROM fleets")['count'];
        return $stats;
    }
}
