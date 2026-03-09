<?php
/**
 * Legacy Root Entry Point - Redirect to actual game
 * The real game entry point is in /Index/index.php
 */

// Redirect to proper location
header('Location: /Index/index.php' . (isset($_GET['page']) ? '?page=' . urlencode($_GET['page']) : ''), true, 301);
exit;
?>
