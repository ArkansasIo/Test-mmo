// Example: Get dependencies for a specific building (e.g., Building ID = 1)
$buildingId = 1;
$dependencies = getBuildingDependencies($buildingId);

// Output the dependencies
if (count($dependencies) > 0) {
    echo "Building ID $buildingId has the following dependencies:\n";
    foreach ($dependencies as $dep) {
        echo "- $dep\n";
    }
} else {
    echo "Building ID $buildingId has no dependencies.\n";
}
