<?php
session_start();
include 'includes/header.php';
include 'classes/Player.php';
include 'classes/Planet.php';

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    echo "<p>You must <a href='index.php?page=login'>log in</a> to access your planets.</p>";
    include 'includes/footer.php';
    exit();
}

$username = $_SESSION['username'];
$pdo = new PDO('mysql:host=localhost;dbname=ogame', 'root', '');
$stmt = $pdo->prepare("SELECT * FROM players WHERE username = ?");
$stmt->execute([$username]);
$player = $stmt->fetch();
$player_id = $player['id'];

$stmt = $pdo->prepare("SELECT * FROM planets WHERE player_id = ?");
$stmt->execute([$player_id]);
$planets = $stmt->fetchAll();
?>

<main>
    <h2>Your Planets</h2>
    <?php if (count($planets) > 0): ?>
        <ul>
            <?php foreach ($planets as $planet): ?>
                <li>
                    <?php echo htmlspecialchars($planet['name']); ?> - Metal: <?php echo $planet['metal']; ?>, Crystal: <?php echo $planet['crystal']; ?>, Deuterium: <?php echo $planet['deuterium']; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>You do not have any planets yet.</p>
    <?php endif; ?>

    <h3>Create a New Planet</h3>
    <form action="actions.php" method="post">
        <input type="hidden" name="action" value="create_planet">
        <input type="hidden" name="player_id" value="<?php echo $player_id; ?>">
        <label for="name">Planet Name:</label>
        <input type="text" id="name" name="name" required>
        <label for="galaxy_id">Galaxy ID:</label>
        <input type="number" id="galaxy_id" name="galaxy_id" required>
        <button type="submit">Create Planet</button>
    </form>
</main>

<?php
include 'includes/footer.php';
?>
