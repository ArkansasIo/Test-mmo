<?php
class TechnologyTree {
    private $technologies = [];

    public function addTechnology($name, $dependencies = []) {
        $this->technologies[$name] = $dependencies;
    }

    public function getDependencies($name) {
        return isset($this->technologies[$name]) ? $this->technologies[$name] : [];
    }

    public function canResearch($name, $researched) {
        $dependencies = $this->getDependencies($name);
        foreach ($dependencies as $dependency) {
            if (!in_array($dependency, $researched)) {
                return false;
            }
        }
        return true;
    }
}

// Example usage
$techTree = new TechnologyTree();
$techTree->addTechnology('Advanced Weapons', ['Basic Weapons']);
$techTree->addTechnology('Basic Weapons');

$researched = ['Basic Weapons'];
if ($techTree->canResearch('Advanced Weapons', $researched)) {
    echo "You can research Advanced Weapons";
} else {
    echo "You need to research Basic Weapons first";
}
?>
