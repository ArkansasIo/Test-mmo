<form action="actions.php" method="post">
    <input type="hidden" name="action" value="attack">
    <label for="attacker_id">Attacker ID:</label>
    <input type="text" id="attacker_id" name="attacker_id" required>
    <label for="defender_id">Defender ID:</label>
    <input type="text" id="defender_id" name="defender_id" required>
    <button type="submit">Attack</button>
</form>
