<?php
require_once 'Index/config.php';
require_once 'Index/classes/Database.php';

$db = Database::getInstance();
$result = $db->fetchAll('DESCRIBE players');

echo "Players table structure:\n";
echo "========================\n";
foreach($result as $row) {
    echo $row['Field'] . " (" . $row['Type'] . ")" . PHP_EOL;
}
