<?php

class AttackTroop {
    // Placeholder properties for Attack Troops
    private $name;
    private $attackPower;
    private $health;

    // Constructor to initialize properties
    public function __construct($name, $attackPower, $health) {
        $this->name = $name;
        $this->attackPower = $attackPower;
        $this->health = $health;
    }

    // Method to get the name of the attack troop
    public function getName() {
        return $this->name;
    }

    // Method to get the attack power
    public function getAttackPower() {
        return $this->attackPower;
    }

    // Method to get the health
    public function getHealth() {
        return $this->health;
    }

    // Placeholder method for attacking
    public function attack($target) {
        // Implement attack logic here
        $target->takeDamage($this->attackPower);
        echo $this->name . " attacks " . $target->getName() . " with power " . $this->attackPower . "\n";
    }

    // Placeholder method for taking damage
    public function takeDamage($damage) {
        // Implement damage logic here
        $this->health -= $damage;
        echo $this->name . " takes " . $damage . " damage and has " . $this->health . " health left\n";
    }
}

?>
