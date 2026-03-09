<?php
// Database connection
$pdo = new PDO('mysql:host=localhost;dbname=game', 'username', 'password');

// Fetch items from the database
$query = $pdo->query("SELECT * FROM items");
$items = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<h1>In-Game Store</h1>
<ul>
    <?php foreach ($items as $item): ?>
        <li>
            <strong><?php echo htmlspecialchars($item['name']); ?></strong> - 
            Cost: <?php echo $item['cost']; ?> Dark Matter
            <form method="POST" action="purchase.php">
                <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                <button type="submit">Buy</button>
            </form>
        </li>
    <?php endforeach; ?>
</ul>


