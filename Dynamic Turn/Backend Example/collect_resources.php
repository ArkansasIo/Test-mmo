<?php
session_start();
$user_id = $_SESSION['user_id'];

$pdo = new PDO('mysql:host=localhost;dbname=game', 'username', 'password');
$query = $pdo->prepare("SELECT * FROM game_state WHERE user_id = :user_id LIMIT 1");
$query->execute(['user_id' => $user_id]);
$user_game_state = $query->fetch(PDO::FETCH_ASSOC);

// Simulate collecting resources
$resources = json_decode($user_game_state['resources'], true);
$resources['metal'] += 100;  // Collect 100 metal
$resources['crystal'] += 50; // Collect 50 crystal

// Update resources in the database
$update_query = $pdo->prepare("UPDATE game_state SET resources = :resources WHERE user_id = :user_id");
$update_query->execute(['resources' => json_encode($resources), 'user_id' => $user_id]);

// Respond with the updated resources
echo json_encode(['success' => true, 'resources' => $resources]);
?>
