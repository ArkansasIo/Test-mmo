<?php
class Player {
    private $id;
    private $username;
    private $resources;

    public function __construct($id) {
        $this->id = $id;
        $this->loadPlayerData();
    }

    private function loadPlayerData() {
        // Load player data from the database
        $pdo = new PDO('mysql:host=localhost;dbname=ogame', 'root', '');
        $stmt = $pdo->prepare("SELECT * FROM players WHERE id = ?");
        $stmt->execute([$this->id]);
        $player = $stmt->fetch();

        $this->username = $player['username'];
        $this->resources = [
            'metal' => $player['metal'],
            'crystal' => $player['crystal'],
            'deuterium' => $player['deuterium']
        ];
    }

    public function getUsername() {
        return $this->username;
    }

    public function getResources() {
        return $this->resources;
    }

    public function setResources($resources) {
        $this->resources = $resources;
        // Update resources in the database
        $pdo = new PDO('mysql:host=localhost;dbname=ogame', 'root', '');
        $stmt = $pdo->prepare("UPDATE players SET metal = ?, crystal = ?, deuterium = ? WHERE id = ?");
        $stmt->execute([$resources['metal'], $resources['crystal'], $resources['deuterium'], $this->id]);
    }

    // Additional methods for player actions can be added here
}
?>
