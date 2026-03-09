<?php

class DefenseTroops {
    // Placeholder properties for Defense Troops
    private $name;
    private $defensePower;
    private $health;

    // Constructor to initialize properties
    public function __construct($name, $defensePower, $health) {
        $this->name = $name;
        $this->defensePower = $defensePower;
        $this->health = $health;
    }

    // Method to get the name of the defense troop
    public function getName() {
        return $this->name;
    }

    // Method to get the defense power
    public function getDefensePower() {
        return $this->defensePower;
    }

    // Method to get the health
    public function getHealth() {
        return $this->health;
    }

    // Placeholder method for defending
    public function defend($damage) {
        // Implement defense logic here
        $this->health -= ($damage - $this->defensePower);
        echo $this->name . " defends and takes " . ($damage - $this->defensePower) . " damage\n";
    }
}

?>
