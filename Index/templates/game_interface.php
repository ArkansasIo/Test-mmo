<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo GAME_NAME; ?></title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #0a0a1a 0%, #1a1a3a 100%);
            color: #fff;
            min-height: 100vh;
        }
        
        .navbar {
            background: rgba(20, 20, 40, 0.95);
            border-bottom: 2px solid #4a9eff;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(74, 158, 255, 0.3);
        }
        
        .navbar-brand {
            color: #4a9eff;
            font-size: 24px;
            font-weight: bold;
            text-decoration: none;
        }
        
        .navbar-menu {
            display: flex;
            gap: 20px;
            list-style: none;
        }
        
        .navbar-menu a {
            color: #fff;
            text-decoration: none;
            padding: 8px 15px;
            border-radius: 5px;
            transition: all 0.3s;
        }
        
        .navbar-menu a:hover {
            background: rgba(74, 158, 255, 0.2);
            color: #4a9eff;
        }
        
        .navbar-user {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .resource-display {
            display: flex;
            gap: 15px;
            background: rgba(10, 10, 30, 0.8);
            padding: 8px 15px;
            border-radius: 5px;
            border: 1px solid #4a9eff;
        }
        
        .resource-item {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .resource-label {
            color: #4a9eff;
            font-weight: bold;
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .page-header {
            background: rgba(20, 20, 40, 0.9);
            border: 2px solid #4a9eff;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .page-header h1 {
            color: #4a9eff;
            margin-bottom: 10px;
        }
        
        .content-area {
            background: rgba(20, 20, 40, 0.9);
            border: 2px solid #4a9eff;
            border-radius: 10px;
            padding: 20px;
            min-height: 500px;
        }
        
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            background: linear-gradient(135deg, #4a9eff 0%, #2a7eff 100%);
            color: #fff;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
        }
        
        .btn:hover {
            background: linear-gradient(135deg, #6ab0ff 0%, #4a9eff 100%);
            box-shadow: 0 0 15px rgba(74, 158, 255, 0.5);
        }
        
        .btn-danger {
            background: linear-gradient(135deg, #ff4a4a 0%, #ff2a2a 100%);
        }
        
        .btn-danger:hover {
            background: linear-gradient(135deg, #ff6a6a 0%, #ff4a4a 100%);
            box-shadow: 0 0 15px rgba(255, 74, 74, 0.5);
        }
    </style>
</head>
<body>
    <?php
    // Load player data
    $player = new Player($_SESSION['user_id']);
    $playerData = $player->getData();
    $planets = $player->getPlanets();
    $currentPlanet = isset($_SESSION['current_planet']) ? $_SESSION['current_planet'] : $planets[0]['id'];
    ?>
    
    <nav class="navbar">
        <a href="index.php" class="navbar-brand"><?php echo GAME_NAME; ?></a>
        
        <ul class="navbar-menu">
            <li><a href="index.php?page=empire">Empire</a></li>
            <li><a href="index.php?page=shipyard">Shipyard</a></li>
            <li><a href="index.php?page=fleet">Fleet</a></li>
            <li><a href="index.php?page=research">Research</a></li>
            <li><a href="index.php?page=galaxy">Galaxy</a></li>
            <li><a href="index.php?page=rankings">Rankings</a></li>
            <li><a href="index.php?page=alliance">Alliance</a></li>
            <li><a href="index.php?page=marketplace">Marketplace</a></li>
            <li><a href="index.php?page=messages">Messages</a></li>
            <?php if ($playerData['is_admin']): ?>
            <li><a href="index.php?page=admin" style="color: #ff4aff;">Admin</a></li>
            <?php endif; ?>
        </ul>
        
        <div class="navbar-user">
            <div class="resource-display">
                <div class="resource-item">
                    <span class="resource-label">Metal:</span>
                    <span id="metal"><?php echo number_format($playerData['metal']); ?></span>
                </div>
                <div class="resource-item">
                    <span class="resource-label">Crystal:</span>
                    <span id="crystal"><?php echo number_format($playerData['crystal']); ?></span>
                </div>
                <div class="resource-item">
                    <span class="resource-label">Deuterium:</span>
                    <span id="deuterium"><?php echo number_format($playerData['deuterium']); ?></span>
                </div>
            </div>
            <span><?php echo htmlspecialchars($playerData['username']); ?></span>
            <a href="?action=logout" class="btn btn-danger">Logout</a>
        </div>
    </nav>
    
    <div class="container">
        <?php
        // Handle logout
        if (isset($_GET['action']) && $_GET['action'] == 'logout') {
            session_destroy();
            header('Location: index.php');
            exit;
        }
        ?>
        
        <div class="content-area">
            <?php
            // This is where the page content from index.php switch statement will be included
            ?>
        </div>
    </div>
    
    <script>
        // Auto-refresh resources every 30 seconds
        setInterval(function() {
            fetch('ajax/get_resources.php')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('metal').textContent = Number(data.metal).toLocaleString();
                    document.getElementById('crystal').textContent = Number(data.crystal).toLocaleString();
                    document.getElementById('deuterium').textContent = Number(data.deuterium).toLocaleString();
                });
        }, 30000);
    </script>
</body>
</html>
