<?php
class Building {
    private $name;
    private $requiredTechnologies;

    public function __construct($name, $requiredTechnologies = []) {
        $this->name = $name;
        $this->requiredTechnologies = $requiredTechnologies;
    }

    public function getName() {
        return $this->name;
    }

    public function getRequiredTechnologies() {
        return $this->requiredTechnologies;
    }

    public function canBuild($researchedTechnologies) {
        foreach ($this->requiredTechnologies as $tech) {
            if (!in_array($tech, $researchedTechnologies)) {
                return false;
            }
        }
        return true;
    }
}

// Example usage
$building = new Building('Research Lab', ['Advanced Weapons']);
$researched = ['Basic Weapons', 'Advanced Weapons'];

if ($building->canBuild($researched)) {
    echo "You can build the Research Lab";
} else {
    echo "You need more research to build the Research Lab";
}
?>
