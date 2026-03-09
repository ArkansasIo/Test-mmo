<?php

class Universe {
    private $pdo;

    public function __construct() {
        $this->pdo = new PDO('mysql:host=localhost;dbname=ogame', 'root', '');
    }

    public function getGalaxies() {
        $stmt = $this->pdo->query("SELECT * FROM galaxies");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPlanetsInGalaxy($galaxy_id) {
        $stmt = $this->pdo->prepare("SELECT * FROM planets WHERE galaxy_id = ?");
        $stmt->execute([$galaxy_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createPlanet($name, $player_id, $galaxy_id) {
        $stmt = $this->pdo->prepare("INSERT INTO planets (name, player_id, galaxy_id, metal, crystal, deuterium) VALUES (?, ?, ?, 1000, 1000, 1000)");
        $stmt->execute([$name, $player_id, $galaxy_id]);
    }
}
?>
