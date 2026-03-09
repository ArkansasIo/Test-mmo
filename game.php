<?php
require_once 'technology_tree.php';
require_once 'research_tree.php';
require_once 'build_building.php';

$techTree = new TechnologyTree();
$researchTree = new ResearchTree();
$techTree->addTechnology('Advanced Weapons', ['Basic Weapons']);
$techTree->addTechnology('Basic Weapons');

$building = new Building('Research Lab', ['Advanced Weapons']);

// Research Basic Weapons
$researchTree->research('Basic Weapons');

// Check if we can research Advanced Weapons
if ($techTree->canResearch('Advanced Weapons', $researchTree->getResearched())) {
    $researchTree->research('Advanced Weapons');
    echo "Advanced Weapons researched\n";
}

// Check if we can build the Research Lab
if ($building->canBuild($researchTree->getResearched())) {
    echo "You can build the Research Lab\n";
} else {
    echo "You need more research to build the Research Lab\n";
}
?>
