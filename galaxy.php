<?php
session_start();
include 'includes/header.php';
include 'classes/Universe.php';

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    echo "<p>You must <a href='index.php?page=login'>log in</a> to access the galaxy.</p>";
    include 'includes/footer.php';
    exit();
}

$universe = new Universe();
$galaxies = $universe->getGalaxies();
?>

<main>
    <h2>Galaxies</h2>
    <?php if (count($galaxies) > 0): ?>
        <ul>
            <?php foreach ($galaxies as $galaxy): ?>
                <li>
                    <a href="galaxy.php?galaxy_id=<?php echo $galaxy['id']; ?>"><?php echo htmlspecialchars($galaxy['name']); ?></a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No galaxies available.</p>
    <?php endif; ?>

    <?php if (isset($_GET['galaxy_id'])): ?>
        <?php
        $galaxy_id = $_GET['galaxy_id'];
        $planets = $universe->getPlanetsInGalaxy($galaxy_id);
        ?>
        <section>
            <h3>Planets in Galaxy: <?php echo htmlspecialchars($galaxies[$galaxy_id]['name']); ?></h3>
            <?php if (count($planets) > 0): ?>
                <ul>
                    <?php foreach ($planets as $planet): ?>
                        <li>
                            <?php echo htmlspecialchars($planet['name']); ?> (Owned by Player ID: <?php echo $planet['player_id']; ?>)
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>No planets in this galaxy yet.</p>
            <?php endif; ?>
        </section>
    <?php endif; ?>
</main>

<?php
include 'includes/footer.php';
?>
