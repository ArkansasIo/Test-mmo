<?php
// Start the session for any user-specific data if needed
session_start();

// Assuming user is logged in and their data is stored in session
$userName = isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest';
$resources = isset($_SESSION['resources']) ? $_SESSION['resources'] : ['metal' => 10000, 'crystal' => 5000, 'deuterium' => 3000]; // Example resources
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galactic Empires</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS file -->
    <script src="scripts.js"></script> <!-- Link to your JavaScript file for dynamic content -->
</head>
<body>
    <header>
        <nav>
            <ul>
                <li><a href="index.php" class="active">Dashboard</a></li>
                <li><a href="buildings.php">Buildings</a></li>
                <li><a href="fleet.php">Fleet</a></li>
                <li><a href="research.php">Research</a></li>
                <li><a href="messages.php">Messages</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
        <div class="user-info">
            <p>Welcome, <?php echo htmlspecialchars($userName); ?></p>
        </div>
    </header>

    <section class="resources">
        <h2>Your Resources</h2>
        <div class="resource-container">
            <div class="resource-item">
                <img src="metal_icon.png" alt="Metal">
                <span>Metal: <?php echo $resources['metal']; ?> units</span>
            </div>
            <div class="resource-item">
                <img src="crystal_icon.png" alt="Crystal">
                <span>Crystal: <?php echo $resources['crystal']; ?> units</span>
            </div>
            <div class="resource-item">
                <img src="deuterium_icon.png" alt="Deuterium">
                <span>Deuterium: <?php echo $resources['deuterium']; ?> units</span>
            </div>
        </div>
    </section>

    <section class="actions">
        <h2>Available Actions</h2>
        <div class="action-container">
            <div class="action-item">
                <a href="build.php"><button class="action-btn">Build Structures</button></a>
                <p>Upgrade your empire's buildings and enhance your resource production.</p>
            </div>
            <div class="action-item">
                <a href="fleet_movement.php"><button class="action-btn">Manage Fleet</button></a>
                <p>Send your fleet to explore, attack, or defend other planets.</p>
            </div>
            <div class="action-item">
                <a href="research_lab.php"><button class="action-btn">Research Technologies</button></a>
                <p>Unlock new technologies to gain an advantage in battle.</p>
            </div>
        </div>
    </section>

    <section class="news">
        <h2>Galactic News</h2>
        <ul>
            <li>Planetary Defense Update: New shields available for fleet protection.</li>
            <li>New Fleet Commanding System: Fleet management has been improved.</li>
            <li>Galaxy Expansion: Explore new galaxies and uncover hidden resources!</li>
        </ul>
    </section>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> Galactic Empires. All rights reserved.</p>
    </footer>
</body>
</html>
