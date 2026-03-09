<?php
// Start the session to get the logged-in user's ID
session_start();
$user_id = $_SESSION['user_id']; // Assuming the user is logged in

// Database connection
$pdo = new PDO('mysql:host=localhost;dbname=game', 'username', 'password');

// Fetch user's game state
$query = $pdo->prepare("SELECT * FROM game_state WHERE user_id = :user_id LIMIT 1");
$query->execute(['user_id' => $user_id]);
$user_game_state = $query->fetch(PDO::FETCH_ASSOC);

// Check if the user exists in the database
if (!$user_game_state) {
    die("User not found in the game.");
}

// Get the last turn time (when the player took their last turn)
$last_turn_time = strtotime($user_game_state['last_turn_time']); // Convert to timestamp
$current_time = time(); // Current server time

// Set a maximum number of turns per hour (e.g., 5 turns)
$max_turns_per_hour = 5;

// Calculate the time difference in seconds
$time_diff = $current_time - $last_turn_time;

// Calculate how many turns should be regenerated
$turns_regenerated = floor($time_diff / 3600) * $max_turns_per_hour; // Turns per hour

// Get the current number of turns the user has (you can store this in the DB)
$current_turns = $user_game_state['turns'];

// Calculate new total turns
$new_turns = min($current_turns + $turns_regenerated, $max_turns_per_hour); // Cap the turns at the max per hour

// If the player has new turns, update the database
if ($new_turns > $current_turns) {
    $update_query = $pdo->prepare("UPDATE game_state SET turns = :turns, last_turn_time = NOW() WHERE user_id = :user_id");
    $update_query->execute(['turns' => $new_turns, 'user_id' => $user_id]);
}

// Output the number of turns left
echo "You have $new_turns turns left.";
?>
