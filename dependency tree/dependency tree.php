<?php

// Function to get building dependencies
function getBuildingDependencies($buildingId) {
    // Create DB connection (assuming MySQLi for simplicity)
    $conn = new mysqli("localhost", "user", "password", "game");

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // SQL query to get dependencies for a building
    $sql = "SELECT b.name FROM dependencies d
            JOIN buildings b ON d.dependency_id = b.id
            WHERE d.item_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $buildingId);
    $stmt->execute();
    $result = $stmt->get_result();

    $dependencies = [];
    while ($row = $result->fetch_assoc()) {
        $dependencies[] = $row['name'];
    }

    $stmt->close();
    $conn->close();

    return $dependencies;
}

// Function to get ships dependencies
function getShipDependencies($shipId) {
    // Similar to getBuildingDependencies function
    // Implementation for ship dependencies goes here...
}

// Function to get technology dependencies
function getTechnologyDependencies($techId) {
    // Similar to getBuildingDependencies function
    // Implementation for technology dependencies goes here...
}

?>
