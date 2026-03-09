<?php
session_start();
$pdo = new PDO('mysql:host=localhost;dbname=game', 'username', 'password');

// Assuming user is logged in
$user_id = $_SESSION['user_id']; // Fetch from session
$item_id = $_POST['item_id'];

// Fetch user balance
$user_query = $pdo->prepare("SELECT balance FROM users WHERE id = :user_id");
$user_query->execute(['user_id' => $user_id]);
$user = $user_query->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("User not found.");
}

// Fetch item details
$item_query = $pdo->prepare("SELECT * FROM items WHERE id = :item_id");
$item_query->execute(['item_id' => $item_id]);
$item = $item_query->fetch(PDO::FETCH_ASSOC);

if (!$item) {
    die("Item not found.");
}

// Check if user has enough balance
if ($user['balance'] < $item['cost']) {
    die("Not enough Dark Matter!");
}

// Deduct item cost from user's balance
$pdo->beginTransaction();
try {
    $update_balance = $pdo->prepare("UPDATE users SET balance = balance - :cost WHERE id = :user_id");
    $update_balance->execute([
        'cost' => $item['cost'],
        'user_id' => $user_id,
    ]);

    // Log the purchase
    $log_purchase = $pdo->prepare("INSERT INTO purchases (user_id, item_id, date) VALUES (:user_id, :item_id, NOW())");
    $log_purchase->execute([
        'user_id' => $user_id,
        'item_id' => $item_id,
    ]);

    $pdo->commit();
    echo "Purchase successful!";
} catch (Exception $e) {
    $pdo->rollBack();
    die("Transaction failed: " . $e->getMessage());
}
?>
