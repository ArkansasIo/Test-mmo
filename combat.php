<?php
session_start();
include 'includes/header.php';
include 'classes/Combat.php';
include 'classes/Player.php';

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    echo "<p>You must <a href='index.php?page=login'>log in</a> to view combats.</p>";
    include 'includes/footer.php';
    exit();
}

$combat = new Combat();
$combats = $combat->getCombats();
?>

<main>
    <h2>Combat Logs</h2>
    <?php if (count($combats) > 0): ?>
        <ul>
            <?php foreach ($combats as $combat): ?>
                <li>
                    Combat ID: <?php echo $combat['id']; ?> - Attacker ID: <?php echo $combat['attacker_id']; ?> - Defender ID: <?php echo $combat['defender_id']; ?> - Result: <?php echo $combat['result']; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No combats available.</p>
    <?php endif; ?>
</main>

<?php
include 'includes/footer.php';
?>
