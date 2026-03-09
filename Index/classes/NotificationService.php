<?php
/**
 * Email & Notification Service
 */
class NotificationService {
    private $db;
    private $smtpConfig;
    
    public function __construct(Database $db, $smtpConfig = null) {
        $this->db = $db;
        $this->smtpConfig = $smtpConfig ?? $this->getDefaultConfig();
    }
    
    /**
     * Send email notification
     */
    public function sendEmail($to, $subject, $message, $htmlContent = null) {
        $headers = "From: " . $this->smtpConfig['from_email'] . "\r\n";
        $headers .= "Content-Type: " . ($htmlContent ? "text/html" : "text/plain") . "; charset=UTF-8\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
        
        $result = mail($to, $subject, $htmlContent ?? $message, $headers);
        
        // Log notification
        $this->logNotification($to, 'email', $subject, $result);
        
        return $result;
    }
    
    /**
     * Send in-game notification
     */
    public function sendInGameNotification($playerId, $title, $message, $type = 'info', $actionUrl = null) {
        $query = "INSERT INTO notifications (player_id, title, message, type, action_url, created_at) 
                  VALUES (?, ?, ?, ?, ?, NOW())";
        
        $params = [
            $playerId,
            $title,
            $message,
            $type,
            $actionUrl
        ];
        
        return $this->db->execute($query, $params);
    }
    
    /**
     * Send bulk notification
     */
    public function sendBulkNotification($playerIds, $title, $message, $type = 'info') {
        $query = "INSERT INTO notifications (player_id, title, message, type, created_at) 
                  VALUES ";
        
        $values = [];
        $params = [];
        
        foreach ($playerIds as $playerId) {
            $values[] = "(?, ?, ?, ?, NOW())";
            $params[] = $playerId;
            $params[] = $title;
            $params[] = $message;
            $params[] = $type;
        }
        
        $query .= implode(", ", $values);
        
        return $this->db->execute($query, $params);
    }
    
    /**
     * Get unread notifications
     */
    public function getUnreadNotifications($playerId) {
        $query = "SELECT * FROM notifications 
                  WHERE player_id = ? AND is_read = FALSE 
                  ORDER BY created_at DESC";
        
        return $this->db->fetchAll($query, [$playerId]);
    }
    
    /**
     * Mark notification as read
     */
    public function markAsRead($notificationId) {
        $query = "UPDATE notifications SET is_read = TRUE WHERE id = ?";
        return $this->db->execute($query, [$notificationId]);
    }
    
    /**
     * Mark all as read for player
     */
    public function markAllAsRead($playerId) {
        $query = "UPDATE notifications SET is_read = TRUE WHERE player_id = ?";
        return $this->db->execute($query, [$playerId]);
    }
    
    /**
     * Delete notification
     */
    public function deleteNotification($notificationId) {
        $query = "DELETE FROM notifications WHERE id = ?";
        return $this->db->execute($query, [$notificationId]);
    }
    
    /**
     * Clear old notifications
     */
    public function clearOldNotifications($days = 30) {
        $query = "DELETE FROM notifications 
                  WHERE created_at < DATE_SUB(NOW(), INTERVAL ? DAY)";
        return $this->db->execute($query, [$days]);
    }
    
    /**
     * Send event-based notification
     */
    public function sendEventNotification($event, $playerId, $metadata = []) {
        $templates = $this->getEventTemplates();
        
        if (!isset($templates[$event])) {
            return false;
        }
        
        $template = $templates[$event];
        $message = $this->parseTemplate($template['message'], $metadata);
        $title = $template['title'] ?? 'Game Notification';
        $type = $template['type'] ?? 'info';
        
        return $this->sendInGameNotification($playerId, $title, $message, $type);
    }
    
    /**
     * Log notification
     */
    private function logNotification($recipient, $type, $subject, $success) {
        $query = "INSERT INTO notification_log (recipient, type, subject, success, timestamp) 
                  VALUES (?, ?, ?, ?, NOW())";
        
        $this->db->execute($query, [
            $recipient,
            $type,
            $subject,
            $success ? 1 : 0
        ]);
    }
    
    /**
     * Get event templates
     */
    private function getEventTemplates() {
        return [
            'player_joined' => [
                'title' => 'Welcome to Game',
                'message' => 'Welcome {player_name}! Get started with your first colony.',
                'type' => 'success'
            ],
            'building_complete' => [
                'title' => 'Building Complete',
                'message' => 'Your {building_name} has been completed on {planet_name}!',
                'type' => 'success'
            ],
            'attack_incoming' => [
                'title' => 'ALERT: Attack Incoming',
                'message' => '{attacker_name} is attacking {planet_name}!',
                'type' => 'danger'
            ],
            'research_complete' => [
                'title' => 'Research Complete',
                'message' => 'Your research in {technology_name} is complete!',
                'type' => 'success'
            ],
            'fleet_arrival' => [
                'title' => 'Fleet Arrival',
                'message' => 'Your fleet has arrived at {destination}',
                'type' => 'info'
            ]
        ];
    }
    
    /**
     * Parse template with metadata
     */
    private function parseTemplate($template, $metadata) {
        foreach ($metadata as $key => $value) {
            $template = str_replace('{' . $key . '}', $value, $template);
        }
        return $template;
    }
    
    /**
     * Get default SMTP configuration
     */
    private function getDefaultConfig() {
        return [
            'from_email' => 'noreply@scifi-conquest.game',
            'from_name' => 'Scifi Conquest',
            'smtp_host' => $_ENV['SMTP_HOST'] ?? 'localhost',
            'smtp_port' => $_ENV['SMTP_PORT'] ?? 25,
            'smtp_user' => $_ENV['SMTP_USER'] ?? '',
            'smtp_pass' => $_ENV['SMTP_PASS'] ?? ''
        ];
    }
}
