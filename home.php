<?php
session_start();
include 'includes/header.php';

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    echo "<p>You must <a href='index.php?page=login'>log in</a> to access the game.</p>";
    include 'includes/footer.php';
    exit();
}

// Fetch player data
$username = $_SESSION['username'];
$pdo = new PDO('mysql:host=localhost;dbname=ogame', 'root', '');
$stmt = $pdo->prepare("SELECT * FROM players WHERE username = ?");
$stmt->execute([$username]);
$player = $stmt->fetch();

// Fetch player's planets
$player_id = $player['id'];
$stmt = $pdo->prepare("SELECT * FROM planets WHERE player_id = ?");
$stmt->execute([$player_id]);
$planets = $stmt->fetchAll();
?>

<main><h2>Welcome to scifi-conquest-game Online</h2>
    <h2>Welcome, <?php echo htmlspecialchars($username); ?>!</h2>
    <p>Here is an overview of your resources and planets.</p>

    <section>
        <h3>Resources</h3>
        <ul>
               <li>Turns: <?php echo $player['Turns']; ?></li>
               <li>Cridits: <?php echo $player['Cridits']; ?></li>
            <li>Metal: <?php echo $player['metal']; ?></li>
            <li>Crystal: <?php echo $player['crystal']; ?></li>
            <li>Deuterium: <?php echo $player['deuterium']; ?></li>
        </ul>
    </section>

    <section>
        <h3>Your Planets</h3>
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
    </section>

    <section>
        <h3>Actions</h3>
        <ul>
            <li><a href="index.php?page=build_planet">Build a new planet</a></li>
            <li><a href="index.php?page=manage_fleet">Manage your fleet</a></li>
            <li><a href="index.php?page=attack">Attack another player</a></li>
        </ul>
    </section>
</main>

<?php
include 'includes/footer.php';
?>
