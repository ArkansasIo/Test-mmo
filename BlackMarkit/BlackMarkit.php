<?php
// BlackMarkit.php
// Black Market trading system for Galactic Empires

// Database connection
$host = "localhost";
$db_name = "ogame_universe";
$username = "root";
$password = "";
$conn = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);

// Handle actions
$action = $_GET['action'] ?? null;

switch ($action) {
    case 'list': // List available trades/items
        listBlackMarketItems($conn);
        break;

    case 'buy': // Buy an item
        $item_id = $_GET['item_id'] ?? null;
        $player_id = $_GET['player_id'] ?? null;
        if ($item_id && $player_id) {
            buyItem($conn, $item_id, $player_id);
        } else {
            echo "Invalid parameters for buying an item.";
        }
        break;

    case 'sell': // Sell an item
        $player_id = $_POST['player_id'] ?? null;
        $item_name = $_POST['item_name'] ?? null;
        $price = $_POST['price'] ?? null;
        $quantity = $_POST['quantity'] ?? null;
        if ($player_id && $item_name && $price && $quantity) {
            sellItem($conn, $player_id, $item_name, $price, $quantity);
        } else {
            echo "Invalid parameters for selling an item.";
        }
        break;

    default:
        echo "Welcome to the Black Market! Use ?action=list to see items.";
        break;
}

// List available trades/items
function listBlackMarketItems($conn) {
    $query = $conn->query("SELECT * FROM black_market");
    $items = $query->fetchAll(PDO::FETCH_ASSOC);

    echo "<h1>Black Market Items</h1>";
    foreach ($items as $item) {
        echo "<p>";
        echo "Item: {$item['name']}<br>";
        echo "Price: {$item['price']} credits<br>";
        echo "Quantity: {$item['quantity']}<br>";
        echo "<a href='?action=buy&item_id={$item['id']}&player_id=1'>Buy</a>";
        echo "</p>";
    }
}

// Buy an item
function buyItem($conn, $item_id, $player_id) {
    // Fetch item details
    $query = $conn->prepare("SELECT * FROM black_market WHERE id = :id");
    $query->execute(['id' => $item_id]);
    $item = $query->fetch(PDO::FETCH_ASSOC);

    if (!$item) {
        echo "Item not found.";
        return;
    }

    // Fetch player credits
    $query = $conn->prepare("SELECT credits FROM players WHERE id = :id");
    $query->execute(['id' => $player_id]);
    $player = $query->fetch(PDO::FETCH_ASSOC);

    if (!$player || $player['credits'] < $item['price']) {
        echo "Not enough credits to buy this item.";
        return;
    }

    // Deduct credits and reduce item quantity
    $conn->beginTransaction();
    $conn->prepare("UPDATE players SET credits = credits - :price WHERE id = :id")
         ->execute(['price' => $item['price'], 'id' => $player_id]);

    $conn->prepare("UPDATE black_market SET quantity = quantity - 1 WHERE id = :id")
         ->execute(['id' => $item_id]);

    $conn->commit();

    echo "Item purchased successfully!";
}

// Sell an item
function sellItem($conn, $player_id, $item_name, $price, $quantity) {
    $query = $conn->prepare("
        INSERT INTO black_market (seller_id, name, price, quantity) 
        VALUES (:seller_id, :name, :price, :quantity)
    ");
    $query->execute([
        'seller_id' => $player_id,
        'name' => $item_name,
        'price' => $price,
        'quantity' => $quantity,
    ]);

    echo "Item listed on the Black Market.";
}
?>
