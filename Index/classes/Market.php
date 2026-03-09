<?php
class Market {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function getListings() {
        return $this->db->fetch("SELECT * FROM market_listings WHERE active = 1 LIMIT 100");
    }
    
    public function createListing($playerId, $resource, $qty, $price) {
        try {
            $this->db->insert("market_listings", ["player_id" => $playerId, "resource_type" => $resource, "quantity" => $qty, "price_per_unit" => $price, "active" => 1, "created_at" => time()]);
            return ["success" => true];
        } catch (Exception $e) {
            return ["success" => false];
        }
    }
}