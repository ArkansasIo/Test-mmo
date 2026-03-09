<?php
/**
 * Session Manager - Handles user authentication and session lifecycle
 */
class SessionManager {
    private $db;
    private $sessionTimeout = 7200; // 2 hours
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Initialize session
     */
    public function init() {
        ini_set('session.gc_maxlifetime', $this->sessionTimeout);
        session_set_cookie_params($this->sessionTimeout);
        
        if (!isset($_SESSION['initialized'])) {
            $_SESSION['initialized'] = time();
            $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
            $_SESSION['ip_address'] = $this->getClientIP();
        }
        
        // Validate session
        if (!$this->validateSession()) {
            $this->destroy();
            return false;
        }
        
        return true;
    }
    
    /**
     * Validate session integrity
     */
    private function validateSession() {
        if (!isset($_SESSION['initialized'])) {
            return false;
        }
        
        // Check timeout
        $sessionAge = time() - $_SESSION['initialized'];
        if ($sessionAge > $this->sessionTimeout) {
            return false;
        }
        
        // Validate user agent (prevent session hijacking)
        if ($_SESSION['user_agent'] !== $_SERVER['HTTP_USER_AGENT']) {
            return false;
        }
        
        // Validate IP (optional, can cause issues with dynamic IPs)
        // if ($_SESSION['ip_address'] !== $this->getClientIP()) {
        //     return false;
        // }
        
        return true;
    }
    
    /**
     * Set user session
     */
    public function setUser($playerId, $username, $isAdmin = false) {
        $_SESSION['player_id'] = (int)$playerId;
        $_SESSION['user_id'] = (int)$playerId;
        $_SESSION['username'] = $username;
        $_SESSION['is_admin'] = (bool)$isAdmin;
        $_SESSION['login_time'] = time();
        
        // Log session activity
        try {
            Logger::log('info', "User $username logged in");
        } catch (Exception $e) {
            // Silently fail
        }
        
        return true;
    }
    
    /**
     * Get current user ID
     */
    public function getPlayerId() {
        return $_SESSION['player_id'] ?? null;
    }
    
    /**
     * Get current username
     */
    public function getUsername() {
        return $_SESSION['username'] ?? null;
    }
    
    /**
     * Check if user is logged in
     */
    public function isLoggedIn() {
        return isset($_SESSION['player_id']) && !empty($_SESSION['player_id']);
    }
    
    /**
     * Check if user is admin
     */
    public function isAdmin() {
        return $_SESSION['is_admin'] ?? false;
    }
    
    /**
     * Refresh session timeout
     */
    public function refresh() {
        $_SESSION['initialized'] = time();
        return true;
    }
    
    /**
     * Destroy session
     */
    public function destroy() {
        if (isset($_SESSION['player_id'])) {
            try {
                Logger::log('info', "User {$_SESSION['username']} logged out");
            } catch (Exception $e) {
                // Silently fail
            }
        }
        
        $_SESSION = [];
        session_destroy();
        return true;
    }
    
    /**
     * Get session duration
     */
    public function getSessionDuration() {
        return isset($_SESSION['initialized']) ? time() - $_SESSION['initialized'] : 0;
    }
    
    /**
     * Get client IP address
     */
    private function getClientIP() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            return trim($ips[0]);
        } else {
            return $_SERVER['REMOTE_ADDR'];
        }
    }
    
    /**
     * Get session info
     */
    public function getInfo() {
        return [
            'player_id' => $this->getPlayerId(),
            'username' => $this->getUsername(),
            'is_admin' => $this->isAdmin(),
            'is_logged_in' => $this->isLoggedIn(),
            'session_duration' => $this->getSessionDuration(),
            'ip_address' => $this->getClientIP()
        ];
    }
}
