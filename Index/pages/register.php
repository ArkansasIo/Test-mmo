<?php
/**
 * Registration Page
 * Allows new players to create accounts
 */

if (!defined('CLASS_PATH')) {
    require_once __DIR__ . '/../config.php';
}
require_once CLASS_PATH . 'Database.php';
require_once CLASS_PATH . 'Player.php';
require_once CLASS_PATH . 'Planet.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    // Validation
    if (empty($username) || empty($email) || empty($password)) {
        $error = 'All fields are required';
    } elseif (strlen($username) < 3 || strlen($username) > 20) {
        $error = 'Username must be between 3 and 20 characters';
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $error = 'Username can only contain letters, numbers, and underscores';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email address';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters';
    } elseif ($password !== $confirmPassword) {
        $error = 'Passwords do not match';
    } else {
        try {
            $db = Database::getInstance();
            
            // Check if username exists
            $existingUser = $db->fetchOne(
                "SELECT id FROM players WHERE username = ?",
                [$username]
            );
            
            if ($existingUser) {
                $error = 'Username already taken';
            } else {
                // Check if email exists
                $existingEmail = $db->fetchOne(
                    "SELECT id FROM players WHERE email = ?",
                    [$email]
                );
                
                if ($existingEmail) {
                    $error = 'Email already registered';
                } else {
                    // Create player (includes starter planet via Player class).
                    $player = new Player();
                    $result = $player->create($username, $email, $password);

                    if (!empty($result['success'])) {
                        $success = 'Account created successfully! You can now login.';
                    } else {
                        $error = $result['message'] ?? 'Failed to create account. Please try again.';
                    }
                }
            }
        } catch (Exception $e) {
            $error = 'Registration failed: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - <?php echo GAME_NAME; ?></title>
    <link rel="stylesheet" href="../../css/style.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #0a0a1a 0%, #1a1a3a 100%);
        }
        
        .register-container {
            background: rgba(20, 20, 40, 0.95);
            border: 2px solid #4a9eff;
            border-radius: 10px;
            padding: 40px;
            width: 100%;
            max-width: 450px;
            box-shadow: 0 0 30px rgba(74, 158, 255, 0.3);
        }
        
        .register-container h1 {
            color: #4a9eff;
            text-align: center;
            margin-bottom: 10px;
        }
        
        .register-container .subtitle {
            text-align: center;
            color: #8ab4f8;
            margin-bottom: 30px;
            font-size: 14px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            color: #4a9eff;
            margin-bottom: 8px;
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
            box-shadow: 0 0 10px rgba(74, 158, 255, 0.3);
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
        
        .error {
            background: rgba(255, 74, 74, 0.2);
            border: 1px solid #ff4a4a;
            border-radius: 5px;
            padding: 12px;
            margin-bottom: 20px;
            color: #ff6666;
            text-align: center;
        }
        
        .success {
            background: rgba(74, 255, 154, 0.2);
            border: 1px solid #4aff9a;
            border-radius: 5px;
            padding: 12px;
            margin-bottom: 20px;
            color: #4aff9a;
            text-align: center;
        }
        
        .login-link {
            text-align: center;
            margin-top: 20px;
            color: #8ab4f8;
        }
        
        .login-link a {
            color: #4a9eff;
            text-decoration: none;
            font-weight: bold;
        }
        
        .login-link a:hover {
            text-decoration: underline;
        }
        
        .requirements {
            background: rgba(74, 158, 255, 0.1);
            border: 1px solid #4a9eff;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 20px;
            font-size: 12px;
            color: #8ab4f8;
        }
        
        .requirements h3 {
            color: #4a9eff;
            margin-top: 0;
            margin-bottom: 10px;
            font-size: 14px;
        }
        
        .requirements ul {
            margin: 0;
            padding-left: 20px;
        }
        
        .requirements li {
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <h1><?php echo GAME_NAME; ?></h1>
        <p class="subtitle">Create Your Account</p>
        
        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success">
                <?php echo htmlspecialchars($success); ?>
                <br><br>
                <a href="../index.php" class="btn">Go to Login</a>
            </div>
        <?php else: ?>
            <div class="requirements">
                <h3>Requirements:</h3>
                <ul>
                    <li>Username: 3-20 characters (letters, numbers, underscores)</li>
                    <li>Password: Minimum 6 characters</li>
                    <li>Valid email address</li>
                </ul>
            </div>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" 
                           value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"
                           required minlength="3" maxlength="20" pattern="[a-zA-Z0-9_]+">
                </div>
                
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" 
                           value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                           required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required minlength="6">
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required minlength="6">
                </div>
                
                <button type="submit" class="btn">Create Account</button>
            </form>
            
            <div class="login-link">
                Already have an account? <a href="../index.php">Login here</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
