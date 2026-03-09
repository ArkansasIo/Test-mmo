// Check if the user already owns the item (for unique items)
$check_ownership = $pdo->prepare("SELECT COUNT(*) FROM purchases WHERE user_id = :user_id AND item_id = :item_id");
$check_ownership->execute(['user_id' => $user_id, 'item_id' => $item_id]);
if ($check_ownership->fetchColumn() > 0) {
    die("Error: You already own this item.");
}
