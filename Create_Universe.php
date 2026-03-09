<?php
// Create_Universe.php
// Script to generate a universe structure like OGame

// Database connection
$host = "localhost";
$db_name = "ogame_universe";
$username = "root";
$password = "";
$conn = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);

// Universe settings
$total_galaxies = 5;        // Number of galaxies
$total_solar_systems = 100; // Solar systems per galaxy
$total_planet_slots = 15;   // Planet slots per solar system

// Clear existing data
$conn->exec("TRUNCATE TABLE galaxies");
$conn->exec("TRUNCATE TABLE solar_systems");
$conn->exec("TRUNCATE TABLE planets");

// Generate galaxies
for ($g = 1; $g <= $total_galaxies; $g++) {
    $galaxy_name = "Galaxy $g";
    $query = $conn->prepare("INSERT INTO galaxies (id, name) VALUES (:id, :name)");
    $query->execute(['id' => $g, 'name' => $galaxy_name]);

    // Generate solar systems for the galaxy
    for ($s = 1; $s <= $total_solar_systems; $s++) {
        $solar_system_name = "Solar System $g:$s";
        $query = $conn->prepare("
            INSERT INTO solar_systems (galaxy_id, id, name) 
            VALUES (:galaxy_id, :id, :name)
        ");
        $query->execute([
            'galaxy_id' => $g,
            'id' => $s,
            'name' => $solar_system_name,
        ]);

        // Generate planets for the solar system
        for ($p = 1; $p <= $total_planet_slots; $p++) {
            $planet_name = "Planet $g:$s:$p";
            $size = rand(50, 300); // Random size for the planet
            $resources = json_encode([
                'metal' => rand(1000, 50000),
                'crystal' => rand(1000, 50000),
                'deuterium' => rand(1000, 50000),
            ]);
            $query = $conn->prepare("
                INSERT INTO planets (galaxy_id, solar_system_id, slot, name, size, resources) 
                VALUES (:galaxy_id, :solar_system_id, :slot, :name, :size, :resources)
            ");
            $query->execute([
                'galaxy_id' => $g,
                'solar_system_id' => $s,
                'slot' => $p,
                'name' => $planet_name,
                'size' => $size,
                'resources' => $resources,
            ]);
        }
    }
}

echo "Universe created successfully with $total_galaxies galaxies, $total_solar_systems solar systems per galaxy, and $total_planet_slots planets per solar system!";
?>
