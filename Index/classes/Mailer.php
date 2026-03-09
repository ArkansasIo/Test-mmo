<?php
/**
 * Email Mailer - Send emails to players
 */
class Mailer {
    private $from = 'noreply@scifi-conquest.local';
    private $fromName = 'Sci-Fi Conquest';
    private $smtpConfig = null;
    
    public function __construct() {
        // Initialize SMTP config if available
        // For now, using PHP mail() function
    }
    
    /**
     * Send verification email
     */
    public function sendVerificationEmail($toEmail, $username, $verificationCode) {
        $subject = 'Verify Your Email - Sci-Fi Conquest: Awakening';
        
        $body = $this->getTemplate('email_verify', [
            'username' => $username,
            'verification_code' => $verificationCode,
            'app_url' => GAME_URL
        ]);
        
        return $this->send($toEmail, $subject, $body);
    }
    
    /**
     * Send password reset email
     */
    public function sendPasswordResetEmail($toEmail, $username, $resetToken) {
        $subject = 'Reset Your Password - Sci-Fi Conquest: Awakening';
        
        $body = $this->getTemplate('email_reset', [
            'username' => $username,
            'reset_token' => $resetToken,
            'reset_url' => GAME_URL . '/Index/pages/reset_password.php?token=' . $resetToken,
            'app_url' => GAME_URL
        ]);
        
        return $this->send($toEmail, $subject, $body);
    }
    
    /**
     * Send welcome email
     */
    public function sendWelcomeEmail($toEmail, $username) {
        $subject = 'Welcome to Sci-Fi Conquest: Awakening!';
        
        $body = $this->getTemplate('email_welcome', [
            'username' => $username,
            'game_url' => GAME_URL,
            'app_url' => GAME_URL
        ]);
        
        return $this->send($toEmail, $subject, $body);
    }
    
    /**
     * Send alliance invitation email
     */
    public function sendAllianceInviteEmail($toEmail, $username, $allianceName, $inviterName) {
        $subject = "Join Alliance: $allianceName";
        
        $body = $this->getTemplate('email_alliance_invite', [
            'username' => $username,
            'alliance_name' => $allianceName,
            'inviter' => $inviterName,
            'game_url' => GAME_URL
        ]);
        
        return $this->send($toEmail, $subject, $body);
    }
    
    /**
     * Send generic email
     */
    public function send($toEmail, $subject, $body, $isHtml = true) {
        $headers = "From: {$this->fromName} <{$this->from}>\r\n";
        $headers .= "Reply-To: {$this->from}\r\n";
        
        if ($isHtml) {
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        } else {
            $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
        }
        
        $headers .= "X-Mailer: Sci-Fi Conquest Engine\r\n";
        
        // In production, use proper email service (PHPMailer, SwiftMailer, etc.)
        $result = mail($toEmail, $subject, $body, $headers);
        
        if ($result) {
            return ['success' => true];
        } else {
            return ['success' => false, 'error' => 'Failed to send email'];
        }
    }
    
    /**
     * Get email template
     */
    private function getTemplate($templateName, $variables = []) {
        $templatePath = __DIR__ . '/../templates/email_' . $templateName . '.php';
        
        if (!file_exists($templatePath)) {
            return $this->getDefaultTemplate($templateName, $variables);
        }
        
        ob_start();
        extract($variables);
        include $templatePath;
        return ob_get_clean();
    }
    
    /**
     * Get default template
     */
    private function getDefaultTemplate($templateName, $variables) {
        $html = "<!DOCTYPE html>\n<html>\n<head>\n<style>\n";
        $html .= "body { font-family: Arial, sans-serif; color: #333; }\n";
        $html .= ".container { max-width: 600px; margin: 0 auto; padding: 20px; }\n";
        $html .= ".header { background-color: #1a1a1a; color: #fff; padding: 20px; text-align: center; }\n";
        $html .= ".content { padding: 20px; background-color: #f9f9f9; }\n";
        $html .= ".footer { text-align: center; padding: 20px; font-size: 12px; color: #999; }\n";
        $html .= "</style>\n</head>\n<body>\n";
        $html .= "<div class='container'>\n";
        $html .= "<div class='header'><h1>" . GAME_NAME . "</h1></div>\n";
        $html .= "<div class='content'>\n";
        $html .= "<p>Hello {$variables['username']},</p>\n";
        $html .= "<p>This is an automated message from " . GAME_NAME . ".</p>\n";
        $html .= "</div>\n";
        $html .= "<div class='footer'>\n";
        $html .= "<p>&copy; " . date('Y') . " " . GAME_NAME . ". All rights reserved.</p>\n";
        $html .= "</div>\n</div>\n</body>\n</html>";
        
        return $html;
    }
}
