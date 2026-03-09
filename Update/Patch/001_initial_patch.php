<?php
function applyPatch($pdo) {
    // Example patch: Add a new column to the players table
    $sql = "ALTER TABLE players ADD COLUMN last_login TIMESTAMP NULL";
    $pdo->exec($sql);

    // Log the applied patch
    $stmt = $pdo->prepare("INSERT INTO applied_patches (patch_name) VALUES ('001_initial_patch')");
    $stmt->execute();
}
?>
