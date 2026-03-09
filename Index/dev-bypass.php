<?php
/**
 * DEVELOPMENT BYPASS TOOL
 * 
 * ⚠️  WARNING: THIS IS EXTREMELY INSECURE! ⚠️
 * 
 * This page allows instant login as any user without password.
 * ONLY use in local development environment!
 * 
 * TO DISABLE: Set DEV_MODE = false in config.php
 */

require_once 'config.php';
session_start();

// Security check - only allow in DEV_MODE
if (!defined('DEV_MODE') || !DEV_MODE) {
    http_response_code(403);
    die('Dev bypass disabled. Enable DEV_MODE in config.php to use this feature.');
}

require_once CLASS_PATH . 'Database.php';

$db = Database::getInstance();
$message = '';
$users = [];

// Handle quick login
if (isset($_GET['login_as'])) {
    $userId = (int)$_GET['login_as'];
    if ($userId > 0) {
        $_SESSION['player_id'] = $userId;
        $_SESSION['user_id'] = $userId;
        $_SESSION['dev_bypass'] = true;
        header('Location: index.php');
        exit;
    }
}

// Handle create test user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_test_user'])) {
    $username = 'test_' . time();
    $email = $username . '@test.local';
    $password = password_hash('test123', PASSWORD_BCRYPT);
    
    try {
        $result = $db->execute(
            "INSERT INTO players (username, email, password_hash, status, created_at, last_activity) 
             VALUES (?, ?, ?, 'active', NOW(), NOW())",
            [$username, $email, $password]
        );
        
        if ($result) {
            $userId = $db->getLastInsertId();
            
            // Initialize resources
            $db->execute(
                "INSERT INTO player_resources (player_id, metals, crystals, deuterium, energy, last_update) 
                 VALUES (?, ?, ?, ?, ?, NOW())",
                [$userId, STARTING_METAL, STARTING_CRYSTAL, STARTING_DEUTERIUM, STARTING_ENERGY]
            );
            
            $message = "✅ Test user created: $username (ID: $userId) - Password: test123";
        }
    } catch (Exception $e) {
        $message = "❌ Error: " . $e->getMessage();
    }
}

// Fetch all users
try {
    $users = $db->fetchAll("SELECT id, username, email, status, created_at, last_login FROM players ORDER BY id DESC LIMIT 50");
} catch (Exception $e) {
    $message = "Database error: " . $e->getMessage();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🔓 Dev Login Bypass - Scifi Conquest</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .warning {
            background: #ff6b6b;
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.8; }
        }
        
        .warning h1 {
            font-size: 24px;
            margin-bottom: 10px;
        }
        
        .warning p {
            margin: 5px 0;
        }
        
        .card {
            background: white;
            border-radius: 10px;
            padding: 30px;
            margin-bottom: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        
        .card h2 {
            color: #333;
            margin-bottom: 20px;
            border-bottom: 2px solid #667eea;
            padding-bottom: 10px;
        }
        
        .message {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            background: #4CAF50;
            color: white;
        }
        
        .message.error {
            background: #f44336;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        th {
            background: #667eea;
            color: white;
            padding: 12px;
            text-align: left;
            font-weight: 600;
        }
        
        td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
        }
        
        tr:hover {
            background: #f5f5f5;
        }
        
        .btn {
            background: #667eea;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            font-size: 14px;
            transition: background 0.3s;
        }
        
        .btn:hover {
            background: #5568d3;
        }
        
        .btn-success {
            background: #4CAF50;
        }
        
        .btn-success:hover {
            background: #45a049;
        }
        
        .btn-danger {
            background: #f44336;
        }
        
        .btn-small {
            padding: 6px 12px;
            font-size: 12px;
        }
        
        .status-active {
            color: #4CAF50;
            font-weight: bold;
        }
        
        .status-inactive {
            color: #f44336;
        }
        
        .actions {
            margin-bottom: 20px;
        }
        
        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #667eea;
            text-decoration: none;
        }
        
        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="warning">
            <h1>⚠️ DEVELOPMENT BYPASS TOOL ⚠️</h1>
            <p>🔓 This page allows instant login as ANY user without password verification!</p>
            <p>🚨 This is EXTREMELY INSECURE and should NEVER be enabled in production!</p>
            <p>💻 Current Mode: <strong>DEV_MODE = <?php echo DEV_MODE ? 'ENABLED' : 'DISABLED'; ?></strong></p>
            <p>🛑 To disable: Set <code>DEV_MODE = false</code> in config.php</p>
        </div>
        
        <?php if ($message): ?>
            <div class="message <?php echo strpos($message, '❌') !== false ? 'error' : ''; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        
        <div class="card">
            <h2>🚀 Quick Actions</h2>
            <div class="actions">
                <form method="POST" style="display: inline;">
                    <button type="submit" name="create_test_user" class="btn btn-success">
                        ➕ Create Test User
                    </button>
                </form>
                <a href="index.php" class="btn" style="margin-left: 10px;">🏠 Go to Game</a>
            </div>
        </div>
        
        <div class="card">
            <h2>👥 Available Users (Click to Login)</h2>
            
            <?php if (empty($users)): ?>
                <p style="color: #999; padding: 20px;">No users found. Create a test user above or register normally.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Last Login</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['id']); ?></td>
                            <td><strong><?php echo htmlspecialchars($user['username']); ?></strong></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td class="status-<?php echo $user['status']; ?>">
                                <?php echo htmlspecialchars($user['status']); ?>
                            </td>
                            <td><?php echo htmlspecialchars($user['created_at'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($user['last_login'] ?? 'Never'); ?></td>
                            <td>
                                <a href="?login_as=<?php echo $user['id']; ?>" class="btn btn-small">
                                    🔓 Login as this user
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
        
        <div class="card">
            <h2>📝 Usage Instructions</h2>
            <ol style="padding-left: 20px; line-height: 1.8;">
                <li>Click "Create Test User" to generate a new test account</li>
                <li>Click "🔓 Login as this user" next to any user to instantly login</li>
                <li>You can also use the URL parameter: <code>index.php?dev_login=USER_ID</code></li>
                <li>Test credentials for created users: <strong>Password: test123</strong></li>
            </ol>
            
            <h3 style="margin-top: 20px; color: #f44336;">🛑 Security Notice</h3>
            <p style="margin-top: 10px;">
                Before deploying to production, ensure <code>DEV_MODE</code> is set to <code>false</code> in 
                <code>Index/config.php</code>. This will completely disable this bypass page.
            </p>
        </div>
        
        <a href="index.php" class="back-link">← Back to Login</a>
    </div>
</body>
</html>
