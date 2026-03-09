<?php
/**
 * Main Navigation Menus - Top Bar and Left Sidebar
 * OGame-style interface for Sci-Fi Conquest: Awakening
 */

$currentPage = $_GET['page'] ?? 'empire';
$player = new Player($_SESSION['user_id']);
$playerData = $player->getData();
$resources = $player->getResources();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo GAME_NAME; ?> - Game</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Courier New', monospace;
            background: linear-gradient(135deg, #0a0a1a 0%, #1a1a3a 100%);
            color: #fff;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        /* ==================== TOP NAVBAR ==================== */
        .top-navbar {
            background: linear-gradient(to right, rgba(10, 10, 30, 0.98), rgba(20, 20, 50, 0.98));
            border-bottom: 3px solid #4a9eff;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 4px 15px rgba(74, 158, 255, 0.5);
            position: sticky;
            top: 0;
            z-index: 1000;
            height: 70px;
        }
        
        /* Brand Logo */
        .navbar-brand {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 0 25px;
            color: #4a9eff;
            font-size: 22px;
            font-weight: bold;
            text-decoration: none;
            border-right: 2px solid #4a9eff;
            height: 100%;
        }
        
        .navbar-brand:hover {
            color: #7ab8ff;
        }
        
        .brand-logo {
            width: 40px;
            height: 40px;
            background: radial-gradient(circle at 30% 30%, #7ab8ff, #2a6eff);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 18px;
        }
        
        /* Main Menu Sections */
        .navbar-main-menu {
            display: flex;
            list-style: none;
            gap: 0;
            flex: 1;
            height: 100%;
            align-items: center;
        }
        
        .navbar-main-menu > li {
            position: relative;
            height: 100%;
            display: flex;
            align-items: center;
        }
        
        .navbar-main-menu > li > a {
            color: #fff;
            text-decoration: none;
            padding: 0 20px;
            height: 100%;
            display: flex;
            align-items: center;
            transition: all 0.3s;
            border-bottom: 3px solid transparent;
            font-weight: 600;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .navbar-main-menu > li > a:hover {
            background: rgba(74, 158, 255, 0.1);
            color: #4a9eff;
            border-bottom-color: #4a9eff;
        }
        
        .navbar-main-menu > li.active > a {
            background: rgba(74, 158, 255, 0.15);
            color: #4a9eff;
            border-bottom-color: #4a9eff;
        }
        
        /* Dropdown Menus */
        .dropdown-menu {
            position: absolute;
            top: 100%;
            left: 0;
            background: rgba(10, 10, 30, 0.98);
            border: 2px solid #4a9eff;
            border-top: none;
            list-style: none;
            min-width: 200px;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.8);
        }
        
        .navbar-main-menu > li:hover .dropdown-menu {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }
        
        .dropdown-menu li a {
            display: block;
            color: #fff;
            text-decoration: none;
            padding: 12px 20px;
            border-bottom: 1px solid rgba(74, 158, 255, 0.2);
            transition: all 0.3s;
            font-size: 12px;
        }
        
        .dropdown-menu li:last-child a {
            border-bottom: none;
        }
        
        .dropdown-menu li a:hover {
            background: rgba(74, 158, 255, 0.2);
            color: #4a9eff;
            padding-left: 25px;
        }
        
        /* User Info & Resources */
        .navbar-right {
            display: flex;
            align-items: center;
            gap: 20px;
            padding: 0 25px;
            height: 100%;
        }
        
        .resource-display {
            display: flex;
            gap: 20px;
            background: rgba(20, 20, 40, 0.8);
            padding: 10px 15px;
            border-radius: 8px;
            border: 1px solid rgba(74, 158, 255, 0.3);
        }
        
        .resource-item {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 12px;
        }
        
        .resource-label {
            color: #4a9eff;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .resource-value {
            color: #7ab8ff;
            font-weight: bold;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 0 15px;
            border-left: 1px solid rgba(74, 158, 255, 0.3);
        }
        
        .user-info span {
            font-size: 13px;
            color: #fff;
        }
        
        .user-info strong {
            color: #4a9eff;
        }
        
        .user-menu-btn {
            width: 35px;
            height: 35px;
            background: linear-gradient(135deg, #4a9eff, #2a7fff);
            border: none;
            border-radius: 50%;
            color: #fff;
            cursor: pointer;
            font-size: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
        }
        
        .user-menu-btn:hover {
            box-shadow: 0 0 10px rgba(74, 158, 255, 0.6);
        }
        
        /* ==================== LEFT SIDEBAR ==================== */
        .game-container {
            display: flex;
            flex: 1;
            gap: 0;
        }
        
        .left-sidebar {
            width: 280px;
            background: linear-gradient(to right, rgba(10, 10, 30, 0.95), rgba(15, 15, 35, 0.95));
            border-right: 2px solid #4a9eff;
            padding: 0;
            overflow-y: auto;
            max-height: calc(100vh - 70px);
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.5);
        }
        
        .sidebar-section {
            border-bottom: 1px solid rgba(74, 158, 255, 0.2);
            padding: 0;
        }
        
        .sidebar-section:first-child {
            border-top: 1px solid rgba(74, 158, 255, 0.2);
        }
        
        .sidebar-title {
            background: rgba(74, 158, 255, 0.1);
            color: #4a9eff;
            padding: 12px 15px;
            font-weight: bold;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-left: 3px solid #4a9eff;
            cursor: pointer;
            transition: all 0.3s;
            user-select: none;
        }
        
        .sidebar-title:hover {
            background: rgba(74, 158, 255, 0.2);
            padding-left: 18px;
        }
        
        .sidebar-title.collapsed::after {
            content: " ▶";
        }
        
        .sidebar-title.expanded::after {
            content: " ▼";
        }
        
        .sidebar-menu {
            list-style: none;
            padding: 0;
            overflow: hidden;
            max-height: 500px;
            transition: max-height 0.3s;
        }
        
        .sidebar-menu.collapsed {
            max-height: 0;
        }
        
        .sidebar-menu li {
            border-top: 1px solid rgba(74, 158, 255, 0.1);
        }
        
        .sidebar-menu li:first-child {
            border-top: none;
        }
        
        .sidebar-menu a {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #fff;
            text-decoration: none;
            padding: 12px 15px;
            transition: all 0.3s;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .sidebar-menu a:hover {
            background: rgba(74, 158, 255, 0.15);
            color: #4a9eff;
            padding-left: 20px;
            border-left: 3px solid #4a9eff;
        }
        
        .sidebar-menu li.active a {
            background: rgba(74, 158, 255, 0.2);
            color: #4a9eff;
            border-left: 3px solid #4a9eff;
            padding-left: 12px;
            font-weight: bold;
        }
        
        .menu-icon {
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
        }
        
        .menu-label {
            flex: 1;
        }
        
        /* Content Area */
        .main-content {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
            max-height: calc(100vh - 70px);
        }
        
        /* Scrollbar Styling */
        .left-sidebar::-webkit-scrollbar,
        .main-content::-webkit-scrollbar {
            width: 8px;
        }
        
        .left-sidebar::-webkit-scrollbar-track,
        .main-content::-webkit-scrollbar-track {
            background: rgba(10, 10, 30, 0.5);
        }
        
        .left-sidebar::-webkit-scrollbar-thumb,
        .main-content::-webkit-scrollbar-thumb {
            background: rgba(74, 158, 255, 0.3);
            border-radius: 4px;
        }
        
        .left-sidebar::-webkit-scrollbar-thumb:hover,
        .main-content::-webkit-scrollbar-thumb:hover {
            background: rgba(74, 158, 255, 0.6);
        }
        
        /* Responsive */
        @media (max-width: 1024px) {
            .left-sidebar {
                width: 220px;
            }
        }
        
        @media (max-width: 768px) {
            .navbar-main-menu {
                display: none;
            }
            
            .left-sidebar {
                position: fixed;
                left: 0;
                top: 70px;
                height: calc(100vh - 70px);
                z-index: 999;
                transform: translateX(-100%);
                transition: transform 0.3s;
            }
            
            .left-sidebar.open {
                transform: translateX(0);
            }
        }
    </style>
    <script>
        function toggleSidebar(event) {
            event.preventDefault();
            const sidebar = document.querySelector('.left-sidebar');
            sidebar.classList.toggle('open');
        }
        
        function toggleSidebarSection(element) {
            const menu = element.nextElementSibling;
            element.classList.toggle('collapsed');
            element.classList.toggle('expanded');
            menu.classList.toggle('collapsed');
            
            // Save state to localStorage
            const sectionName = element.textContent.trim();
            const isExpanded = element.classList.contains('expanded');
            localStorage.setItem('sidebar-' + sectionName, isExpanded);
        }
        
        function initSidebarState() {
            const titles = document.querySelectorAll('.sidebar-title');
            titles.forEach(title => {
                const sectionName = title.textContent.trim();
                const isExpanded = localStorage.getItem('sidebar-' + sectionName) === 'true';
                
                if (!isExpanded) {
                    title.classList.add('collapsed');
                    title.nextElementSibling.classList.add('collapsed');
                } else {
                    title.classList.add('expanded');
                }
            });
        }
        
        function setActiveMenu() {
            const currentPage = new URLSearchParams(window.location.search).get('page') || 'empire';
            const links = document.querySelectorAll('.sidebar-menu a, .navbar-main-menu a');
            links.forEach(link => {
                if (link.getAttribute('href').includes('page=' + currentPage)) {
                    link.closest('li')?.classList.add('active');
                } else {
                    link.closest('li')?.classList.remove('active');
                }
            });
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            initSidebarState();
            setActiveMenu();
        });
    </script>
</head>
<body>

<!-- TOP NAVBAR -->
<nav class="top-navbar">
    <!-- Brand -->
    <a href="?page=empire" class="navbar-brand">
        <div class="brand-logo">⚔️</div>
        <span><?php echo GAME_NAME; ?></span>
    </a>
    
    <!-- Main Menu -->
    <ul class="navbar-main-menu">
        <li <?php echo $currentPage === 'empire' ? 'class="active"' : ''; ?>>
            <a href="?page=empire">◈ Empire</a>
        </li>
        
        <li>
            <a href="#">🌍 Resources</a>
            <ul class="dropdown-menu">
                <li><a href="?page=empire">Overview</a></li>
                <li><a href="#">Resource Production</a></li>
                <li><a href="#">Storage</a></li>
                <li><a href="#">Trade Market</a></li>
            </ul>
        </li>
        
        <li>
            <a href="#">⚙️ Buildings</a>
            <ul class="dropdown-menu">
                <li><a href="#">Build Queue</a></li>
                <li><a href="#">Available Buildings</a></li>
                <li><a href="#">Building Levels</a></li>
                <li><a href="#">Demolish</a></li>
            </ul>
        </li>
        
        <li <?php echo $currentPage === 'research' ? 'class="active"' : ''; ?>>
            <a href="?page=research">🔬 Research</a>
        </li>
        
        <li>
            <a href="#">🛸 Fleet</a>
            <ul class="dropdown-menu">
                <li><a href="?page=shipyard">Shipyard</a></li>
                <li><a href="?page=fleet">Fleet</a></li>
                <li><a href="#">Expeditions</a></li>
                <li><a href="#">Movements</a></li>
            </ul>
        </li>
        
        <li>
            <a href="#">⚔️ Combat</a>
            <ul class="dropdown-menu">
                <li><a href="#">Espionage</a></li>
                <li><a href="#">Attack</a></li>
                <li><a href="#">Defend</a></li>
                <li><a href="#">Battle Reports</a></li>
            </ul>
        </li>
        
        <li>
            <a href="#">🌐 Diplomacy</a>
            <ul class="dropdown-menu">
                <li><a href="?page=alliance">Alliance</a></li>
                <li><a href="#">Trade Agreement</a></li>
                <li><a href="#">Messenger</a></li>
                <li><a href="#">Diplomacy</a></li>
            </ul>
        </li>
        
        <li>
            <a href="#">📊 Info</a>
            <ul class="dropdown-menu">
                <li><a href="?page=rankings">Rankings</a></li>
                <li><a href="?page=galaxy">Galaxy</a></li>
                <li><a href="#">Statistics</a></li>
                <li><a href="#">Forum</a></li>
            </ul>
        </li>
    </ul>
    
    <!-- Right Section -->
    <div class="navbar-right">
        <!-- Resources Display -->
        <div class="resource-display">
            <div class="resource-item">
                <span class="resource-label">Metal:</span>
                <span class="resource-value"><?php echo number_format($resources['metal'] ?? 500); ?></span>
            </div>
            <div class="resource-item">
                <span class="resource-label">Crystal:</span>
                <span class="resource-value"><?php echo number_format($resources['crystal'] ?? 500); ?></span>
            </div>
            <div class="resource-item">
                <span class="resource-label">Deuterium:</span>
                <span class="resource-value"><?php echo number_format($resources['deuterium'] ?? 100); ?></span>
            </div>
            <div class="resource-item">
                <span class="resource-label">Energy:</span>
                <span class="resource-value"><?php echo number_format($resources['energy'] ?? 1000); ?></span>
            </div>
        </div>
        
        <!-- User Info -->
        <div class="user-info">
            <span>Player: <strong><?php echo htmlspecialchars($playerData['username'] ?? 'Player'); ?></strong></span>
            <button class="user-menu-btn" title="User Menu">⚙️</button>
        </div>
    </div>
</nav>

<!-- MAIN LAYOUT -->
<div class="game-container">
    <!-- LEFT SIDEBAR -->
    <aside class="left-sidebar" id="sidebar">
        <!-- OVERVIEW SECTION -->
        <div class="sidebar-section">
            <div class="sidebar-title expanded">📊 Overview</div>
            <ul class="sidebar-menu">
                <li <?php echo $currentPage === 'empire' ? 'class="active"' : ''; ?>>
                    <a href="?page=empire">
                        <span class="menu-icon">◈</span>
                        <span class="menu-label">Empire</span>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <span class="menu-icon">📍</span>
                        <span class="menu-label">Planets</span>
                    </a>
                </li>
                <li>
                    <a href="#?page=tasks">
                        <span class="menu-icon">📋</span>
                        <span class="menu-label">Tasks</span>
                    </a>
                </li>
                <li>
                    <a href="#?page=notifications">
                        <span class="menu-icon">🔔</span>
                        <span class="menu-label">Notifications</span>
                    </a>
                </li>
            </ul>
        </div>
        
        <!-- RESOURCES SECTION -->
        <div class="sidebar-section">
            <div class="sidebar-title expanded">🌍 Resources</div>
            <ul class="sidebar-menu">
                <li>
                    <a href="#">
                        <span class="menu-icon">📦</span>
                        <span class="menu-label">Storage</span>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <span class="menu-icon">⚡</span>
                        <span class="menu-label">Production</span>
                    </a>
                </li>
                <li>
                    <a href="?page=marketplace">
                        <span class="menu-icon">💰</span>
                        <span class="menu-label">Marketplace</span>
                    </a>
                </li>
            </ul>
        </div>
        
        <!-- CONSTRUCTION SECTION -->
        <div class="sidebar-section">
            <div class="sidebar-title expanded">⚙️ Construction</div>
            <ul class="sidebar-menu">
                <li>
                    <a href="#">
                        <span class="menu-icon">🏗️</span>
                        <span class="menu-label">Buildings</span>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <span class="menu-icon">📜</span>
                        <span class="menu-label">Build Queue</span>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <span class="menu-icon">🗑️</span>
                        <span class="menu-label">Demolish</span>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <span class="menu-icon">🛡️</span>
                        <span class="menu-label">Defense</span>
                    </a>
                </li>
            </ul>
        </div>
        
        <!-- TECHNOLOGY SECTION -->
        <div class="sidebar-section">
            <div class="sidebar-title expanded">🔬 Technology</div>
            <ul class="sidebar-menu">
                <li <?php echo $currentPage === 'research' ? 'class="active"' : ''; ?>>
                    <a href="?page=research">
                        <span class="menu-icon">📚</span>
                        <span class="menu-label">Research</span>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <span class="menu-icon">🔍</span>
                        <span class="menu-label">Tech Tree</span>
                    </a>
                </li>
            </ul>
        </div>
        
        <!-- FLEET SECTION -->
        <div class="sidebar-section">
            <div class="sidebar-title expanded">🛸 Fleet</div>
            <ul class="sidebar-menu">
                <li <?php echo $currentPage === 'shipyard' ? 'class="active"' : ''; ?>>
                    <a href="?page=shipyard">
                        <span class="menu-icon">🛠️</span>
                        <span class="menu-label">Shipyard</span>
                    </a>
                </li>
                <li <?php echo $currentPage === 'fleet' ? 'class="active"' : ''; ?>>
                    <a href="?page=fleet">
                        <span class="menu-icon">✈️</span>
                        <span class="menu-label">Fleet</span>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <span class="menu-icon">🚀</span>
                        <span class="menu-label">Movements</span>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <span class="menu-icon">🗺️</span>
                        <span class="menu-label">Expeditions</span>
                    </a>
                </li>
            </ul>
        </div>
        
        <!-- COMBAT SECTION -->
        <div class="sidebar-section">
            <div class="sidebar-title expanded">⚔️ Combat</div>
            <ul class="sidebar-menu">
                <li>
                    <a href="#">
                        <span class="menu-icon">🎯</span>
                        <span class="menu-label">Attack</span>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <span class="menu-icon">🛡️</span>
                        <span class="menu-label">Defense</span>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <span class="menu-icon">🕵️</span>
                        <span class="menu-label">Espionage</span>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <span class="menu-icon">📋</span>
                        <span class="menu-label">Battle Reports</span>
                    </a>
                </li>
            </ul>
        </div>
        
        <!-- DIPLOMACY SECTION -->
        <div class="sidebar-section">
            <div class="sidebar-title expanded">🤝 Diplomacy</div>
            <ul class="sidebar-menu">
                <li <?php echo $currentPage === 'alliance' ? 'class="active"' : ''; ?>>
                    <a href="?page=alliance">
                        <span class="menu-icon">⚜️</span>
                        <span class="menu-label">Alliance</span>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <span class="menu-icon">✉️</span>
                        <span class="menu-label">Messages</span>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <span class="menu-icon">📜</span>
                        <span class="menu-label">Diplomacy</span>
                    </a>
                </li>
            </ul>
        </div>
        
        <!-- INFORMATION SECTION -->
        <div class="sidebar-section">
            <div class="sidebar-title expanded">ℹ️ Information</div>
            <ul class="sidebar-menu">
                <li <?php echo $currentPage === 'rankings' ? 'class="active"' : ''; ?>>
                    <a href="?page=rankings">
                        <span class="menu-icon">🏆</span>
                        <span class="menu-label">Rankings</span>
                    </a>
                </li>
                <li <?php echo $currentPage === 'galaxy' ? 'class="active"' : ''; ?>>
                    <a href="?page=galaxy">
                        <span class="menu-icon">🌌</span>
                        <span class="menu-label">Galaxy</span>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <span class="menu-icon">📊</span>
                        <span class="menu-label">Statistics</span>
                    </a>
                </li>
            </ul>
        </div>
        
        <!-- ACCOUNT SECTION -->
        <div class="sidebar-section">
            <div class="sidebar-title expanded">👤 Account</div>
            <ul class="sidebar-menu">
                <li>
                    <a href="#">
                        <span class="menu-icon">⚙️</span>
                        <span class="menu-label">Settings</span>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <span class="menu-icon">🔐</span>
                        <span class="menu-label">Security</span>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <span class="menu-icon">❓</span>
                        <span class="menu-label">Help</span>
                    </a>
                </li>
                <li>
                    <a href="?action=logout" style="color: #ff6b6b;">
                        <span class="menu-icon">🚪</span>
                        <span class="menu-label">Logout</span>
                    </a>
                </li>
            </ul>
        </div>
    </aside>
    
    <!-- MAIN CONTENT AREA -->
    <main class="main-content">
        <?php
        // Include the appropriate page content
        switch ($currentPage) {
            case 'empire':
                include PAGE_PATH . 'empire.php';
                break;
            case 'shipyard':
                include PAGE_PATH . 'shipyard.php';
                break;
            case 'fleet':
                include PAGE_PATH . 'fleet.php';
                break;
            case 'research':
                include PAGE_PATH . 'research.php';
                break;
            case 'alliance':
                include PAGE_PATH . 'alliance.php';
                break;
            case 'galaxy':
                include PAGE_PATH . 'galaxy.php';
                break;
            case 'rankings':
                include PAGE_PATH . 'rankings.php';
                break;
            case 'marketplace':
                include PAGE_PATH . 'marketplace.php';
                break;
            default:
                include PAGE_PATH . 'empire.php';
        }
        ?>
    </main>
</div>

</body>
</html>
