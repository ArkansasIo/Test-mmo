<?php
session_start();
include 'includes/header.php';
include 'classes/Economy.php';

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    echo "<p>You must <a href='index.php?page=login'>log in</a> to manage your economy.</p>";
    include 'includes/footer.php';
    exit();
}

$username = $_SESSION['username'];
$pdo = new PDO('mysql:host=localhost;dbname=ogame', 'root', '');
$stmt = $pdo->prepare("SELECT * FROM players WHERE username = ?");
$stmt->execute([$username]);
$player = $stmt->fetch();
$player_id = $player['id'];

$economy = new Economy();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $metal = $_POST['metal'];
    $crystal = $_POST['crystal'];
    $deuterium = $_POST['deuterium'];
    $economy->tradeResources($player_id, $metal, $crystal, $deuterium);
    header("Location: economy.php");
}
?>

<main>
    <h2>Manage Your Resources</h2>
    <ul>
        <li>Metal: <?php echo $player['metal']; ?></li>
        <li>Crystal: <?php echo $player['crystal']; ?></li>
        <li>Deuterium: <?php echo $player['deuterium']; ?></li>
    </ul>

    <h3>Trade Resources</h3>
    <form action="economy.php" method="post">
        <label for="metal">Metal:</label>
        <input type="number" id="metal" name="metal" required>
        <label for="crystal">Crystal:</label>
        <input type="number" id="crystal" name="crystal" required>
        <label for="deuterium">Deuterium:</label>
        <input type="number" id="deuterium" name="deuterium" required>
        <button type="submit">Trade</button>
    </form>
</main>

<?php
include 'includes/footer.php';
?>
