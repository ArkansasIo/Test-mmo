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
