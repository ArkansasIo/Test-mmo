<?php

class Ship {
    private $pdo;

    public function __construct() {
        $this->pdo = new PDO('mysql:host=localhost;dbname=ogame', 'root', '');
    }

    public function createShip($name, $type, $player_id, $fleet_id) {
        $stmt = $this->pdo->prepare("INSERT INTO ships (name, type, player_id, fleet_id) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $type, $player_id, $fleet_id]);
    }

    public function getShips($fleet_id) {
        $stmt = $this->pdo->prepare("SELECT * FROM ships WHERE fleet_id = ?");
        $stmt->execute([$fleet_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addWeapon($ship_id, $name, $damage, $range) {
        $stmt = $this->pdo->prepare("INSERT INTO weapons (name, damage, range, ship_id) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $damage, $range, $ship_id]);
    }

    public function getWeapons($ship_id) {
        $stmt = $this->pdo->prepare("SELECT * FROM weapons WHERE ship_id = ?");
        $stmt->execute([$ship_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
