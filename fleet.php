<?php
session_start();
include 'includes/header.php';
include 'classes/Player.php';
include 'classes/Fleet.php';

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    echo "<p>You must <a href='index.php?page=login'>log in</a> to manage your fleets.</p>";
    include 'includes/footer.php';
    exit();
}

$username = $_SESSION['username'];
$pdo = new PDO('mysql:host=localhost;dbname=ogame', 'root', '');
$stmt = $pdo->prepare("SELECT * FROM players WHERE username = ?");
$stmt->execute([$username]);
$player = $stmt->fetch();
$player_id = $player['id'];

$stmt = $pdo->prepare("SELECT * FROM fleets WHERE player_id = ?");
$stmt->execute([$player_id]);
$fleets = $stmt->fetchAll();
?>

<main>
    <h2>Your Fleets</h2>
    <?php if (count($fleets) > 0): ?>
        <ul>
            <?php foreach ($fleets as $fleet): ?>
                <li>
                    Fleet ID: <?php echo $fleet['id']; ?> - Ships: <?php echo $fleet['ships']; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>You do not have any fleets yet.</p>
    <?php endif; ?>

    <h3>Create a New Fleet</h3>
    <form action="actions.php" method="post">
        <input type="hidden" name="action" value="create_fleet">
        <input type="hidden" name="player_id" value="<?php echo $player_id; ?>">
        <label for="planet_id">Planet ID:</label>
        <input type="number" id="planet_id" name="planet_id" required>
        <label for="ships">Number of Ships:</label>
        <input type="number" id="ships" name="ships" required>
        <button type="submit">Create Fleet</button>
    </form>
</main>

<?php
include 'includes/footer.php';
?>
