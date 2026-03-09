<?php

class UntrainedUnits {
    // Placeholder properties for Untrained Units
    private $name;
    private $potential;

    // Constructor to initialize properties
    public function __construct($name, $potential) {
        $this->name = $name;
        $this->potential = $potential;
    }

    // Method to get the name of the untrained unit
    public function getName() {
        return $this->name;
    }

    // Method to get the potential
    public function getPotential() {
        return $this->potential;
    }

    // Placeholder method for training
    public function train() {
        // Implement training logic here
        echo $this->name . " is being trained to unlock potential " . $this->potential . "\n";
    }
}

?>
