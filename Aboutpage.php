<?php
// Start the session for any user-specific data if needed
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Galactic Empires</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS file -->
</head>
<body>
    <header>
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="about.php" class="active">About</a></li>
                <li><a href="game.php">Game</a></li>
                <li><a href="contact.php">Contact</a></li>
            </ul>
        </nav>
    </header>

    <section class="about">
        <h1>About Galactic Empires</h1>
        <p>Welcome to <strong>Galactic Empires</strong>, a massive multiplayer online real-time strategy (MMORTS) game where you will build your intergalactic empire, manage resources, engage in strategic battles, and form alliances to dominate the galaxy.</p>

        <h2>Game Overview</h2>
        <p>Galactic Empires offers an immersive experience with complex systems that challenge your strategic thinking and planning. As a commander, you'll oversee resource management, technology research, fleet building, and diplomacy to outsmart your opponents. With turn-based mechanics, every decision you make matters in the race to control the universe.</p>

        <h2>Key Features</h2>
        <ul>
            <li><strong>Empire Building:</strong> Develop and expand your empire across various planets, harnessing resources to fuel your growth.</li>
            <li><strong>Fleet Management:</strong> Build and command powerful fleets to protect your empire or conquer others.</li>
            <li><strong>Diplomacy & Alliances:</strong> Engage in politics with other players through alliances, treaties, and trade.</li>
            <li><strong>Real-Time Combat:</strong> Participate in large-scale, turn-based space battles with detailed combat mechanics.</li>
            <li><strong>Research & Technology:</strong> Unlock advanced technologies to enhance your empire's capabilities and give you the edge in battle.</li>
        </ul>

        <h2>Gameplay Mechanics</h2>
        <p>The game operates on a turn-based system, where each turn represents a fixed amount of time in the game universe. During your turn, you will manage your empire, make decisions about your fleet movements, trade, and complete research. The key to success lies in careful planning and resource management as you engage with other players and form your strategies.</p>

        <h2>Get Involved</h2>
        <p>Are you ready to lead your empire to victory? Join Galactic Empires today, and become part of an exciting universe where strategy, diplomacy, and military power combine in a quest for intergalactic dominance.</p>

        <h2>Contact Us</h2>
        <p>If you have any questions or need support, feel free to reach out to our team via our <a href="contact.php">Contact Page</a>.</p>
    </section>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> Galactic Empires. All rights reserved.</p>
    </footer>
</body>
</html>
