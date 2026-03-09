<?php
/**
 * Authentication & User Session Management
 * Handles login, registration, and session management
 */
class Authentication {
    private $db;
    private $sessionTimeout = 3600;
    
    public function __construct(Database $db) {
        $this->db = $db;
        $this->sessionTimeout = defined('SESSION_LIFETIME') ? SESSION_LIFETIME : 3600;
        
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    /**
     * Login user with email and password
     */
    public function login($email, $password) {
        try {
            // Validate input
            if (empty($email) || empty($password)) {
                return ['success' => false, 'error' => 'Email and password required'];
            }
            
            // Get user from database
            $user = $this->db->fetchOne(
                "SELECT id, username, email, password_hash, status FROM players WHERE email = ?",
                [$email]
            );
            
            if (!$user) {
                return ['success' => false, 'error' => 'Invalid email or password'];
            }
            
            // Check account status
            if ($user['status'] === 'banned') {
                return ['success' => false, 'error' => 'This account has been banned'];
            }
            
            if ($user['status'] === 'suspended') {
                return ['success' => false, 'error' => 'This account is suspended'];
            }
            
            // Verify password
            if (!password_verify($password, $user['password_hash'])) {
                // Log failed attempt
                $this->logFailedLogin($email);
                return ['success' => false, 'error' => 'Invalid email or password'];
            }
            
            // Set session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['login_time'] = time();
            $_SESSION['last_activity'] = time();
            
            // Update last login
            $this->db->execute(
                "UPDATE players SET last_login = NOW(), last_activity = NOW() WHERE id = ?",
                [$user['id']]
            );
            
            return ['success' => true, 'user_id' => $user['id']];
            
        } catch (Exception $e) {
            Logger::log('error', 'Login error: ' . $e->getMessage(), ['email' => $email]);
            return ['success' => false, 'error' => 'Login failed. Please try again.'];
        }
    }
    
    /**
     * Register new user
     */
    public function register($username, $email, $password, $passwordConfirm) {
        try {
            // Validate input
            $errors = [];
            
            if (empty($username)) {
                $errors['username'] = 'Username required';
            } elseif (strlen($username) < 3) {
                $errors['username'] = 'Username must be at least 3 characters';
            } elseif (strlen($username) > 20) {
                $errors['username'] = 'Username must be 20 characters or less';
            }
            
            if (empty($email)) {
                $errors['email'] = 'Email required';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = 'Invalid email format';
            }
            
            if (empty($password)) {
                $errors['password'] = 'Password required';
            } elseif (strlen($password) < 8) {
                $errors['password'] = 'Password must be at least 8 characters';
            }
            
            if ($password !== $passwordConfirm) {
                $errors['password_confirm'] = 'Passwords do not match';
            }
            
            if (!empty($errors)) {
                return ['success' => false, 'errors' => $errors];
            }
            
            // Check if username exists
            $existing = $this->db->fetchOne(
                "SELECT id FROM players WHERE username = ?",
                [$username]
            );
            
            if ($existing) {
                return ['success' => false, 'errors' => ['username' => 'Username already taken']];
            }
            
            // Check if email exists
            $existing = $this->db->fetchOne(
                "SELECT id FROM players WHERE email = ?",
                [$email]
            );
            
            if ($existing) {
                return ['success' => false, 'errors' => ['email' => 'Email already registered']];
            }
            
            // Hash password
            $passwordHash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
            
            // Create user
            $result = $this->db->execute(
                "INSERT INTO players (username, email, password_hash, status, created_at, last_activity) 
                 VALUES (?, ?, ?, 'active', NOW(), NOW())",
                [$username, $email, $passwordHash]
            );
            
            if ($result) {
                $userId = $this->db->getLastInsertId();
                
                // Initialize player resources
                $this->initializePlayerResources($userId);
                
                Logger::log('info', 'New user registered', ['username' => $username, 'email' => $email]);
                
                return ['success' => true, 'user_id' => $userId, 'message' => 'Registration successful'];
            } else {
                return ['success' => false, 'error' => 'Registration failed. Please try again.'];
            }
            
        } catch (Exception $e) {
            Logger::log('error', 'Registration error: ' . $e->getMessage());
            return ['success' => false, 'error' => 'Registration failed. Please try again.'];
        }
    }
    
    /**
     * Logout user
     */
    public function logout() {
        if (isset($_SESSION['user_id'])) {
            $userId = $_SESSION['user_id'];
            Logger::log('info', 'User logged out', ['user_id' => $userId]);
        }
        
        session_destroy();
        return true;
    }
    
    /**
     * Check if user is authenticated
     */
    public function isAuthenticated() {
        if (!isset($_SESSION['user_id'])) {
            return false;
        }
        
        // Check session timeout
        if (isset($_SESSION['login_time'])) {
            if (time() - $_SESSION['login_time'] > $this->sessionTimeout) {
                $this->logout();
                return false;
            }
        }
        
        // Update last activity
        $_SESSION['last_activity'] = time();
        
        return true;
    }
    
    /**
     * Get current user
     */
    public function getCurrentUser() {
        if (!$this->isAuthenticated()) {
            return null;
        }
        
        return [
            'id' => $_SESSION['user_id'],
            'username' => $_SESSION['username'],
            'email' => $_SESSION['email']
        ];
    }
    
    /**
     * Get current user ID
     */
    public function getCurrentUserId() {
        if (!$this->isAuthenticated()) {
            return null;
        }
        
        return $_SESSION['user_id'];
    }
    
    /**
     * Check if user has permission
     */
    public function hasPermission($permission) {
        if (!$this->isAuthenticated()) {
            return false;
        }
        
        // Get user role from database
        $user = $this->db->fetchOne(
            "SELECT role FROM players WHERE id = ?",
            [$_SESSION['user_id']]
        );
        
        if (!$user) {
            return false;
        }
        
        // Admin has all permissions
        if ($user['role'] === 'admin' || $user['role'] === 'super_admin') {
            return true;
        }
        
        // Check specific permission
        // This would be expanded based on your permission system
        return false;
    }
    
    /**
     * Check if user is admin
     */
    public function isAdmin() {
        if (!$this->isAuthenticated()) {
            return false;
        }
        
        $user = $this->db->fetchOne(
            "SELECT role FROM players WHERE id = ?",
            [$_SESSION['user_id']]
        );
        
        return $user && ($user['role'] === 'admin' || $user['role'] === 'super_admin');
    }
    
    /**
     * Verify email with code
     */
    public function verifyEmail($userId, $verificationCode) {
        try {
            // In production, store verification codes in database
            // For now, simple implementation
            
            $result = $this->db->execute(
                "UPDATE players SET email_verified = 1 WHERE id = ?",
                [$userId]
            );
            
            return ['success' => $result, 'message' => $result ? 'Email verified' : 'Verification failed'];
            
        } catch (Exception $e) {
            Logger::log('error', 'Email verification error: ' . $e->getMessage(), ['user_id' => $userId]);
            return ['success' => false, 'error' => 'Verification failed'];
        }
    }
    
    /**
     * Request password reset
     */
    public function requestPasswordReset($email) {
        try {
            $user = $this->db->fetchOne(
                "SELECT id, username FROM players WHERE email = ?",
                [$email]
            );
            
            if (!$user) {
                // Don't reveal if email exists
                return ['success' => true, 'message' => 'If email exists, reset link will be sent'];
            }
            
            // Generate reset token
            $token = bin2hex(random_bytes(32));
            $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            // Store reset token
            $this->db->execute(
                "INSERT INTO password_resets (user_id, token, expires_at) VALUES (?, ?, ?) 
                 ON DUPLICATE KEY UPDATE token = ?, expires_at = ?",
                [$user['id'], $token, $expiresAt, $token, $expiresAt]
            );
            
            Logger::log('info', 'Password reset requested', ['user_id' => $user['id']]);
            
            return ['success' => true, 'message' => 'Password reset link sent to email'];
            
        } catch (Exception $e) {
            Logger::log('error', 'Password reset error: ' . $e->getMessage(), ['email' => $email]);
            return ['success' => false, 'error' => 'Password reset failed'];
        }
    }
    
    /**
     * Reset password with token
     */
    public function resetPassword($token, $newPassword, $confirmPassword) {
        try {
            if (empty($token) || empty($newPassword)) {
                return ['success' => false, 'error' => 'Invalid reset request'];
            }
            
            if ($newPassword !== $confirmPassword) {
                return ['success' => false, 'error' => 'Passwords do not match'];
            }
            
            if (strlen($newPassword) < 8) {
                return ['success' => false, 'error' => 'Password must be at least 8 characters'];
            }
            
            // Get reset token
            $reset = $this->db->fetchOne(
                "SELECT user_id FROM password_resets WHERE token = ? AND expires_at > NOW()",
                [$token]
            );
            
            if (!$reset) {
                return ['success' => false, 'error' => 'Invalid or expired reset link'];
            }
            
            // Hash new password
            $passwordHash = password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => 12]);
            
            // Update password
            $this->db->execute(
                "UPDATE players SET password_hash = ? WHERE id = ?",
                [$passwordHash, $reset['user_id']]
            );
            
            // Delete reset token
            $this->db->execute(
                "DELETE FROM password_resets WHERE user_id = ?",
                [$reset['user_id']]
            );
            
            Logger::log('info', 'Password reset completed', ['user_id' => $reset['user_id']]);
            
            return ['success' => true, 'message' => 'Password reset successful. Please login.'];
            
        } catch (Exception $e) {
            Logger::log('error', 'Password reset error: ' . $e->getMessage());
            return ['success' => false, 'error' => 'Password reset failed'];
        }
    }
    
    /**
     * Initialize player resources on registration
     */
    private function initializePlayerResources($userId) {
        try {
            $startingMetals = defined('STARTING_METAL') ? STARTING_METAL : 500;
            $startingCrystal = defined('STARTING_CRYSTAL') ? STARTING_CRYSTAL : 500;
            $startingDeuterium = defined('STARTING_DEUTERIUM') ? STARTING_DEUTERIUM : 0;
            
            $this->db->execute(
                "INSERT INTO player_resources (player_id, metals, crystals, deuterium, last_update) 
                 VALUES (?, ?, ?, ?, NOW())",
                [$userId, $startingMetals, $startingCrystal, $startingDeuterium]
            );
            
        } catch (Exception $e) {
            Logger::log('error', 'Error initializing player resources: ' . $e->getMessage(), ['user_id' => $userId]);
        }
    }
    
    /**
     * Log failed login attempt
     */
    private function logFailedLogin($email) {
        try {
            $this->db->execute(
                "INSERT INTO login_attempts (email, ip_address, attempt_time) VALUES (?, ?, NOW())",
                [$email, $_SERVER['REMOTE_ADDR'] ?? 'unknown']
            );
        } catch (Exception $e) {
            // Silently fail, don't disrupt normal flow
        }
    }
}
