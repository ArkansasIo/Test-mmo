<?php
// header.php - Navigation Toolbar

// Get the current page or set default
$page = isset($_GET['page']) ? $_GET['page'] : 'empire'; 

// Function to set active class for the current page
function setActive($current_page, $menu_item) {
    return $current_page == $menu_item ? 'active' : '';
}
?>

<div id="toolbar" class="toolbar">
    <ul>
        <li class="<?= setActive($page, 'empire') ?>"><a href="index.php?page=empire">Empire</a></li>
        <li class="<?= setActive($page, 'fleet') ?>"><a href="index.php?page=fleet">Fleet Management</a></li>
        <li class="<?= setActive($page, 'marketplace') ?>"><a href="index.php?page=marketplace">Marketplace</a></li>
        <li class="<?= setActive($page, 'research') ?>"><a href="index.php?page=research">Research</a></li>
        <li class="<?= setActive($page, 'notifications') ?>"><a href="index.php?page=notifications">Notifications</a></li>
        <li class="<?= setActive($page, 'messages') ?>"><a href="index.php?page=messages">Messages</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</div>
