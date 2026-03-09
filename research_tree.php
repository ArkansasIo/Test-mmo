<?php
class ResearchTree {
    private $researched = [];

    public function research($technology) {
        if (!in_array($technology, $this->researched)) {
            $this->researched[] = $technology;
        }
    }

    public function getResearched() {
        return $this->researched;
    }
}

// Example usage
$researchTree = new ResearchTree();
$researchTree->research('Basic Weapons');

$researched = $researchTree->getResearched();
print_r($researched);
?>
