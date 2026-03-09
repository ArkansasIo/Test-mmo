<?php
session_start();
$pdo = new PDO('mysql:host=localhost;dbname=game', 'username', 'password');

// Assuming the user is logged in and the session contains their ID
$user_id = $_SESSION['user_id']; // Server-controlled user ID
$item_id = intval($_POST['item_id']); // Sanitize input

// Fetch user balance securely
$user_query = $pdo->prepare("SELECT id, balance FROM users WHERE id = :user_id LIMIT 1");
$user_query->execute(['user_id' => $user_id]);
$user = $user_query->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("Error: User not found.");
}

// Fetch item details securely
$item_query = $pdo->prepare("SELECT id, cost FROM items WHERE id = :item_id LIMIT 1");
$item_query->execute(['item_id' => $item_id]);
$item = $item_query->fetch(PDO::FETCH_ASSOC);

if (!$item) {
    die("Error: Item not found.");
}

// Check if the user has enough balance (server-side validation)
if ($user['balance'] < $item['cost']) {
    die("Error: Insufficient balance.");
}

// Use a transaction to ensure atomic operations
$pdo->beginTransaction();

try {
    // Deduct the cost from the user's balance
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

    // Commit the transaction
    $pdo->commit();
    echo "Purchase successful!";
} catch (Exception $e) {
    // Rollback the transaction on failure
    $pdo->rollBack();
    die("Transaction failed: " . $e->getMessage());
}
?>
