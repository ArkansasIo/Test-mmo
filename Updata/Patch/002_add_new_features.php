<?php
function applyPatch($pdo) {
    // Example patch: Add a new table for game events
    $sql = "CREATE TABLE game_events (
        id INT AUTO_INCREMENT PRIMARY KEY,
        event_name VARCHAR(255) NOT NULL,
        event_description TEXT,
        event_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $pdo->exec($sql);

    // Log the applied patch
    $stmt = $pdo->prepare("INSERT INTO applied_patches (patch_name) VALUES ('002_add_new_features')");
    $stmt->execute();
}
?>
