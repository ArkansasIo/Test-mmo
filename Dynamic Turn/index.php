<?php
session_start();
$user_id = $_SESSION['user_id'];

// Fetch user data
$pdo = new PDO('mysql:host=localhost;dbname=game', 'username', 'password');
$query = $pdo->prepare("SELECT * FROM game_state WHERE user_id = :user_id LIMIT 1");
$query->execute(['user_id' => $user_id]);
$user_game_state = $query->fetch(PDO::FETCH_ASSOC);
$resources = json_decode($user_game_state['resources'], true);
$turns_left = 5; // Example, this could be dynamic

// Handle form submissions for actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Example: process resource collection or battle requests
    // Here you'd call the respective PHP function to handle logic
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Game Dashboard</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Welcome to Your Game Dashboard</h1>
        <nav>
            <a href="index.php">Home</a>
            <a href="battle.php">Battle</a>
            <a href="resources.php">Manage Resources</a>
        </nav>
    </header>

    <main>
        <section class="resources">
            <h2>Your Resources</h2>
            <div class="resource-details">
                <section class="resources">
    <h2>Your Resources</h2>
    <div class="resource-details">
        <p><strong>Metal:</strong> <?php echo $resources['metal']; ?></p>
        <p><strong>Crystal:</strong> <?php echo $resources['crystal']; ?></p>
        <p><strong>Energy:</strong> <?php echo $resources['energy']; ?></p>
        <p><strong>Turns Left:</strong> <?php echo $new_turns; ?></p>
    </div>
</section>
            </div>
        </section>

        <section class="actions">
            <h2>Your Actions</h2>
            <form method="POST" action="index.php">
                <button type="submit" name="action" value="collect_resources">Collect Resources</button>
                <button type="submit" name="action" value="move_units">Move Units</button>
                <button type="submit" name="action" value="start_battle">Start Battle</button>
            </form>
        </section>
    </main>

    <footer>
        <p>&copy; 2025 Your Game Name</p>
    </footer>
</body>
</html>
