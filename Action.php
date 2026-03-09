<?php
include 'classes/Player.php';
include 'classes/Fleet.php';
include 'classes/Combat.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];

    switch ($action) {
        case 'attack':
            $attackerId = $_POST['attacker_id'];
            $defenderId = $_POST['defender_id'];

            $attacker = new Fleet($attackerId);
            $defender = new Fleet($defenderId);

            $combat = new Combat($attacker, $defender);
            $result = $combat->battle();

            echo "Combat Result: " . $result;
            break;
        
        // Additional actions can be handled here
    }
}
?>
