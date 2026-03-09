<?php
/**
 * Player Class
 * Handles player data and operations
 */

class Player {
    private $db;
    private $id;
    private $data;
    
    public function __construct($playerId = null) {
        $this->db = Database::getInstance();
        if ($playerId) {
            $this->id = $playerId;
            $this->loadPlayerData();
        }
    }
    
    /**
     * Load player data from database
     */
    private function loadPlayerData() {
        $sql = "SELECT * FROM players WHERE id = ?";
        $this->data = $this->db->fetchOne($sql, [$this->id]);
    }
    
    /**
     * Create a new player
     */
    public function create($username, $email, $password) {
        // Check if username or email already exists
        if ($this->usernameExists($username)) {
            return ['success' => false, 'message' => 'Username already exists'];
        }
        
        if ($this->emailExists($email)) {
            return ['success' => false, 'message' => 'Email already exists'];
        }
        
        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        
        // Create player
        $playerData = [
            'username' => $username,
            'email' => $email,
            'password' => $hashedPassword,
            'metal' => STARTING_METAL,
            'crystal' => STARTING_CRYSTAL,
            'deuterium' => STARTING_DEUTERIUM,
            'energy' => STARTING_ENERGY,
            'created_at' => time(),
            'last_activity' => time(),
            'last_resource_update' => time()
        ];
        
        $this->id = $this->db->insert('players', $playerData);
        
        if ($this->id) {
            // Create starting planet
            $this->createStartingPlanet();
            $this->loadPlayerData();
            return ['success' => true, 'player_id' => $this->id];
        }
        
        return ['success' => false, 'message' => 'Failed to create player'];
    }
    
    /**
     * Authenticate player
     */
    public static function authenticate($username, $password) {
        $db = Database::getInstance();
        $sql = "SELECT * FROM players WHERE username = ? OR email = ?";
        $player = $db->fetchOne($sql, [$username, $username]);
        
        if ($player && password_verify($password, $player['password'])) {
            // Update last activity
            $db->update('players', [
                'last_activity' => time()
            ], 'id = :id', ['id' => $player['id']]);
            
            return new Player($player['id']);
        }
        
        return null;
    }

    /**
     * Get player row by id for legacy page compatibility.
     */
    public static function getById($playerId) {
        $db = Database::getInstance();
        return $db->fetchOne("SELECT * FROM players WHERE id = ?", [(int)$playerId]);
    }
    
    /**
     * Check if username exists
     */
    private function usernameExists($username) {
        $sql = "SELECT COUNT(*) as count FROM players WHERE username = ?";
        $result = $this->db->fetchOne($sql, [$username]);
        return $result['count'] > 0;
    }
    
    /**
     * Check if email exists
     */
    private function emailExists($email) {
        $sql = "SELECT COUNT(*) as count FROM players WHERE email = ?";
        $result = $this->db->fetchOne($sql, [$email]);
        return $result['count'] > 0;
    }
    
    /**
     * Create starting planet for new player
     */
    private function createStartingPlanet() {
        $planet = new Planet();
        $planet->create($this->id, 'Homeworld', 1, rand(1, 9), rand(1, 9));
    }
    
    /**
     * Get player data
     */
    public function getData($key = null) {
        if ($key) {
            return isset($this->data[$key]) ? $this->data[$key] : null;
        }
        return $this->data;
    }
    
    /**
     * Update player resources
     */
    public function updateResources($metal = 0, $crystal = 0, $deuterium = 0) {
        $currentMetal = $this->data['metal'] + $metal;
        $currentCrystal = $this->data['crystal'] + $crystal;
        $currentDeuterium = $this->data['deuterium'] + $deuterium;
        
        // Ensure resources don't go negative
        $currentMetal = max(0, $currentMetal);
        $currentCrystal = max(0, $currentCrystal);
        $currentDeuterium = max(0, $currentDeuterium);
        
        $this->db->update('players', [
            'metal' => $currentMetal,
            'crystal' => $currentCrystal,
            'deuterium' => $currentDeuterium
        ], 'id = :id', ['id' => $this->id]);
        
        $this->loadPlayerData();
    }
    
    /**
     * Check if player has enough resources
     */
    public function hasResources($metal, $crystal, $deuterium) {
        return $this->data['metal'] >= $metal &&
               $this->data['crystal'] >= $crystal &&
               $this->data['deuterium'] >= $deuterium;
    }
    
    /**
     * Get player planets
     */
    public function getPlanets() {
        $sql = "SELECT * FROM planets WHERE player_id = ? ORDER BY is_capital DESC, id ASC";
        return $this->db->fetchAll($sql, [$this->id]);
    }
    
    /**
     * Get player fleets
     */
    public function getFleets() {
        $sql = "SELECT * FROM fleets WHERE player_id = ?";
        return $this->db->fetchAll($sql, [$this->id]);
    }
    
    /**
     * Get player research levels
     */
    public function getResearch() {
        $sql = "SELECT * FROM research WHERE player_id = ?";
        return $this->db->fetchAll($sql, [$this->id]);
    }
    
    /**
     * Update last activity
     */
    public function updateActivity() {
        $this->db->update('players', [
            'last_activity' => time()
        ], 'id = :id', ['id' => $this->id]);
    }
    
    /**
     * Get player resources
     */
    public function getResources() {
        return [
            'metal' => $this->data['metal'] ?? 0,
            'crystal' => $this->data['crystal'] ?? 0,
            'deuterium' => $this->data['deuterium'] ?? 0,
            'energy' => $this->data['energy'] ?? 0
        ];
    }
    
    /**
     * Get player ID
     */
    public function getId() {
        return $this->id;
    }
}
