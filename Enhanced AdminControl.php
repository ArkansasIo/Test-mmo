<?php
session_start();

// Check if the user is an admin
function isAdmin() {
    return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == true;
}

if (!isAdmin()) {
    die("Access denied. You must be an admin to access this page.");
}

// Include necessary files
include 'classes/Player.php';
include 'classes/Planet.php';
include 'classes/Fleet.php';
include 'classes/Combat.php';

$pdo = new PDO('mysql:host=localhost;dbname=ogame', 'root', '');

function getPlayers($pdo) {
    $stmt = $pdo->query("SELECT * FROM players");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getPlanets($pdo) {
    $stmt = $pdo->query("SELECT * FROM planets");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getFleets($pdo) {
    $stmt = $pdo->query("SELECT * FROM fleets");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getCombats($pdo) {
    $stmt = $pdo->query("SELECT * FROM combats");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Handle admin actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];

    switch ($action) {
        case 'delete_player':
            $player_id = $_POST['player_id'];
            $stmt = $pdo->prepare("DELETE FROM players WHERE id = ?");
            $stmt->execute([$player_id]);
            break;
        case 'edit_player':
            $player_id = $_POST['player_id'];
            $metal = $_POST['metal'];
            $crystal = $_POST['crystal'];
            $deuterium = $_POST['deuterium'];
            $stmt = $pdo->prepare("UPDATE players SET metal = ?, crystal = ?, deuterium = ? WHERE id = ?");
            $stmt->execute([$metal, $crystal, $deuterium, $player_id]);
            break;
        case 'delete_planet':
            $planet_id = $_POST['planet_id'];
            $stmt = $pdo->prepare("DELETE FROM planets WHERE id = ?");
            $stmt->execute([$planet_id]);
            break;
        case 'edit_planet':
            $planet_id = $_POST['planet_id'];
            $metal = $_POST['metal'];
            $crystal = $_POST['crystal'];
            $deuterium = $_POST['deuterium'];
            $stmt = $pdo->prepare("UPDATE planets SET metal = ?, crystal = ?, deuterium = ? WHERE id = ?");
            $stmt->execute([$metal, $crystal, $deuterium, $planet_id]);
            break;
        case 'delete_fleet':
            $fleet_id = $_POST['fleet_id'];
            $stmt = $pdo->prepare("DELETE FROM fleets WHERE id = ?");
            $stmt->execute([$fleet_id]);
            break;
        case 'edit_fleet':
            $fleet_id = $_POST['fleet_id'];
            $ships = $_POST['ships'];
            $stmt = $pdo->prepare("UPDATE fleets SET ships = ? WHERE id = ?");
            $stmt->execute([$ships, $fleet_id]);
            break;
        // Add more admin actions as needed
    }
}

// Fetch updated data
$players = getPlayers($pdo);
$planets = getPlanets($pdo);
$fleets = getFleets($pdo);
$combats = getCombats($pdo);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Control Panel</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <header>
        <h1>Admin Control Panel</h1>
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="AdminControl.php">Admin Panel</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section>
            <h2>Manage Players</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Metal</th>
                        <th>Crystal</th>
                        <th>Deuterium</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($players as $player): ?>
                    <tr>
                        <td><?php echo $player['id']; ?></td>
                        <td><?php echo $player['username']; ?></td>
                        <td><?php echo $player['email']; ?></td>
                        <td><?php echo $player['metal']; ?></td>
                        <td><?php echo $player['crystal']; ?></td>
                        <td><?php echo $player['deuterium']; ?></td>
                        <td>
                            <form action="AdminControl.php" method="post" style="display:inline;">
                                <input type="hidden" name="action" value="delete_player">
                                <input type="hidden" name="player_id" value="<?php echo $player['id']; ?>">
                                <button type="submit">Delete</button>
                            </form>
                            <form action="AdminControl.php" method="post" style="display:inline;">
                                <input type="hidden" name="action" value="edit_player">
                                <input type="hidden" name="player_id" value="<?php echo $player['id']; ?>">
                                <input type="number" name="metal" value="<?php echo $player['metal']; ?>" required>
                                <input type="number" name="crystal" value="<?php echo $player['crystal']; ?>" required>
                                <input type="number" name="deuterium" value="<?php echo $player['deuterium']; ?>" required>
                                <button type="submit">Edit</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>

        <section>
            <h2>Manage Planets</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Player ID</th>
                        <th>Metal</th>
                        <th>Crystal</th>
                        <th>Deuterium</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($planets as $planet): ?>
                    <tr>
                        <td><?php echo $planet['id']; ?></td>
                        <td><?php echo $planet['name']; ?></td>
                        <td><?php echo $planet['player_id']; ?></td>
                        <td><?php echo $planet['metal']; ?></td>
                        <td><?php echo $planet['crystal']; ?></td>
                        <td><?php echo $planet['deuterium']; ?></td>
                        <td>
                            <form action="AdminControl.php" method="post" style="display:inline;">
                                <input type="hidden" name="action" value="delete_planet">
                                <input type="hidden" name="planet_id" value="<?php echo $planet['id']; ?>">
                                <button type="submit">Delete</button>
                            </form>
                            <form action="AdminControl.php" method="post" style="display:inline;">
                                <input type="hidden" name="action" value="edit_planet">
                                <input type="hidden" name="planet_id" value="<?php echo $planet['id']; ?>">
                                <input type="number" name="metal" value="<?php echo $planet['metal']; ?>" required>
                                <input type="number" name="crystal" value="<?php echo $planet['crystal']; ?>" required>
                                <input type="number" name="deuterium" value="<?php echo $planet['deuterium']; ?>" required>
                                <button type="submit">Edit</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>

        <section>
            <h2>Manage Fleets</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Player ID</th>
                        <th>Planet ID</th>
                        <th>Ships</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($fleets as $fleet): ?>
                    <tr>
                        <td><?php echo $fleet['id']; ?></td>
                        <td><?php echo $fleet['player_id']; ?></td>
                        <td><?php echo $fleet['planet_id']; ?></td>
                        <td><?php echo $fleet['ships']; ?></td>
                        <td>
                            <form action="AdminControl.php" method="post" style="display:inline;">
                                <input type="hidden" name="action" value="delete_fleet">
                                <input type="hidden" name="fleet_id" value="<?php echo $fleet['id']; ?>">
                                <button type="submit">Delete</button>
                            </form>
                            <form action="AdminControl.php" method="post" style="display:inline;">
                                <input type="hidden" name="action" value="edit_fleet">
                                <input type="hidden" name="fleet_id" value="<?php echo $fleet['id']; ?>">
                                <input type="number" name="ships" value="<?php echo $fleet['ships']; ?>" required>
                                <button type="submit">Edit</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>

        <section>
            <h2>Combat Logs</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Attacker ID</th>
                        <th>Defender ID</th>
                        <th>Result</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($combats as $combat): ?>
                    <tr>
                        <td><?php echo $combat['id']; ?></td>
                        <td><?php echo $combat['attacker_id']; ?></td>
                        <td><?php echo $combat['defender_id']; ?></td>
                        <td><?php echo $combat['result']; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>

        <!-- Add a section for managing universe settings if needed -->
        <!-- <section>
            <h2>Manage Universe</h2>
            <!-- Universe management forms and actions -->
        <!-- </section> -->
    </main>

    <footer>
        <p>&copy; 2025 Ogame MMORPG</p>
    </footer>
</body>
</html>
