<?php
require 'config/config.php';

// Include database connection
$pdo = new PDO('mysql:host=localhost;dbname=ogame', 'root', '');

// Get applied patches
$appliedPatches = [];
$stmt = $pdo->query("SELECT patch_name FROM applied_patches");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $appliedPatches[] = $row['patch_name'];
}

// Get all patch files
$patchFiles = glob('patches/*.php');

foreach ($patchFiles as $patchFile) {
    $patchName = basename($patchFile, '.php');

    // Check if the patch has already been applied
    if (!in_array($patchName, $appliedPatches)) {
        // Apply the patch
        require $patchFile;
        applyPatch($pdo);
        echo "Applied patch: $patchName\n";
    } else {
        echo "Patch $patchName already applied.\n";
    }
}
?>
