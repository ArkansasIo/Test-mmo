<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?php echo GAME_NAME; ?></title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #0a0a1a 0%, #1a1a3a 100%);
            color: #fff;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .login-container {
            background: rgba(20, 20, 40, 0.9);
            border: 2px solid #4a9eff;
            border-radius: 10px;
            padding: 40px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 0 30px rgba(74, 158, 255, 0.3);
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .login-header h1 {
            color: #4a9eff;
            margin: 0 0 10px 0;
            font-size: 28px;
        }
        
        .login-header p {
            color: #aaa;
            margin: 0;
            font-size: 14px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #4a9eff;
            font-weight: bold;
        }
        
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #4a9eff;
            border-radius: 5px;
            background: rgba(10, 10, 30, 0.8);
            color: #fff;
            font-size: 14px;
            box-sizing: border-box;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #6ab0ff;
            box-shadow: 0 0 10px rgba(74, 158, 255, 0.5);
        }
        
        .btn {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 5px;
            background: linear-gradient(135deg, #4a9eff 0%, #2a7eff 100%);
            color: #fff;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn:hover {
            background: linear-gradient(135deg, #6ab0ff 0%, #4a9eff 100%);
            box-shadow: 0 0 20px rgba(74, 158, 255, 0.5);
        }
        
        .error-message {
            background: rgba(255, 50, 50, 0.2);
            border: 1px solid #ff3232;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 20px;
            color: #ff6666;
            text-align: center;
        }
        
        .links {
            margin-top: 20px;
            text-align: center;
        }
        
        .links a {
            color: #4a9eff;
            text-decoration: none;
            margin: 0 10px;
        }
        
        .links a:hover {
            text-decoration: underline;
        }
        
        .dev-bypass {
            margin-top: 20px;
            padding: 15px;
            background: rgba(255, 193, 7, 0.2);
            border: 2px solid #ffc107;
            border-radius: 5px;
            text-align: center;
            animation: blink 2s infinite;
        }
        
        @keyframes blink {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }
        
        .dev-bypass a {
            display: inline-block;
            margin-top: 10px;
            padding: 10px 20px;
            background: #ffc107;
            color: #000;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            transition: all 0.3s;
        }
        
        .dev-bypass a:hover {
            background: #ffca28;
            box-shadow: 0 0 15px rgba(255, 193, 7, 0.5);
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1><?php echo GAME_NAME; ?></h1>
            <p>Enter the Universe</p>
        </div>
        
        <?php if (isset($error)): ?>
            <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Username or Email</label>
                <input type="text" id="username" name="username" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <button type="submit" name="login" class="btn">Login</button>
        </form>
        
        <div class="links">
            <a href="pages/register.php">Create Account</a>
            <a href="#">Forgot Password?</a>
        </div>
        
        <?php if (defined('DEV_MODE') && DEV_MODE): ?>
        <div class="dev-bypass">
            <strong>⚠️ DEV MODE ACTIVE ⚠️</strong>
            <p style="margin: 5px 0; font-size: 12px;">Development bypass enabled</p>
            <a href="dev-bypass.php">🔓 Quick Login Bypass</a>
        </div>
        <?php endif; ?>
    </div>
    
    <?php
    // Handle login
    if (isset($_POST['login'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];
        
        $player = Player::authenticate($username, $password);
        
        if ($player) {
            $_SESSION['user_id'] = $player->getId();
            $_SESSION['player_id'] = $player->getId();
            $_SESSION['username'] = $player->getData('username');
            header('Location: index.php');
            exit;
        } else {
            $error = "Invalid username or password";
        }
    }
    ?>
</body>
</html>
