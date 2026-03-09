<?php
/**
 * Achievements & Rewards System
 */
class Achievements {
    private $db;
    
    public function __construct(Database $db) {
        $this->db = $db;
    }
    
    /**
     * Award achievement to player
     */
    public function awardAchievement($playerId, $achievementKey) {
        // Check if already awarded
        $query = "SELECT * FROM player_achievements WHERE player_id = ? AND achievement_key = ?";
        $existing = $this->db->fetchOne($query, [$playerId, $achievementKey]);
        
        if ($existing) {
            return false; // Already awarded
        }
        
        $achievement = $this->getAchievementData($achievementKey);
        if (!$achievement) {
            return false;
        }
        
        // Award achievement
        $query = "INSERT INTO player_achievements (player_id, achievement_key, awarded_at) VALUES (?, ?, NOW())";
        $result = $this->db->execute($query, [$playerId, $achievementKey]);
        
        if ($result) {
            // Award badge if applicable
            if ($achievement['badge_id']) {
                $this->awardBadge($playerId, $achievement['badge_id']);
            }
            
            // Award points/rewards
            if ($achievement['reward_points']) {
                $this->addPlayerPoints($playerId, $achievement['reward_points']);
            }
        }
        
        return $result;
    }
    
    /**
     * Check and auto-award achievements
     */
    public function checkAchievements($playerId) {
        $player = $this->getPlayerData($playerId);
        $achievements = $this->getAllAchievements();
        $awardedCount = 0;
        
        foreach ($achievements as $achievement) {
            if ($this->meetsRequirements($player, $achievement)) {
                if ($this->awardAchievement($playerId, $achievement['key'])) {
                    $awardedCount++;
                }
            }
        }
        
        return $awardedCount;
    }
    
    /**
     * Get player achievements
     */
    public function getPlayerAchievements($playerId) {
        $query = "SELECT 
                    pa.*,
                    a.title,
                    a.description,
                    a.icon,
                    a.reward_points
                  FROM player_achievements pa
                  LEFT JOIN achievements a ON pa.achievement_key = a.key
                  WHERE pa.player_id = ?
                  ORDER BY pa.awarded_at DESC";
        
        return $this->db->fetchAll($query, [$playerId]);
    }
    
    /**
     * Get all achievements
     */
    public function getAllAchievements() {
        $query = "SELECT * FROM achievements WHERE active = 1 ORDER BY category, difficulty";
        return $this->db->fetchAll($query, []);
    }
    
    /**
     * Get achievement data
     */
    private function getAchievementData($achievementKey) {
        $query = "SELECT * FROM achievements WHERE key = ?";
        return $this->db->fetchOne($query, [$achievementKey]);
    }
    
    /**
     * Check if player meets achievement requirements
     */
    private function meetsRequirements($player, $achievement) {
        $requirements = json_decode($achievement['requirements'], true);
        
        foreach ($requirements as $key => $value) {
            if (!isset($player[$key]) || $player[$key] < $value) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Award badge to player
     */
    private function awardBadge($playerId, $badgeId) {
        $query = "INSERT INTO player_badges (player_id, badge_id, awarded_at) 
                  VALUES (?, ?, NOW()) 
                  ON DUPLICATE KEY UPDATE awarded_at = NOW()";
        return $this->db->execute($query, [$playerId, $badgeId]);
    }
    
    /**
     * Add player points
     */
    private function addPlayerPoints($playerId, $points) {
        $query = "UPDATE players SET achievement_points = achievement_points + ? WHERE id = ?";
        return $this->db->execute($query, [$points, $playerId]);
    }
    
    /**
     * Get player data
     */
    private function getPlayerData($playerId) {
        $query = "SELECT * FROM players WHERE id = ?";
        return $this->db->fetchOne($query, [$playerId]);
    }
}

/**
 * Player Settings Manager
 */
class PlayerSettings {
    private $db;
    
    public function __construct(Database $db) {
        $this->db = $db;
    }
    
    /**
     * Get player settings
     */
    public function getSettings($playerId) {
        $query = "SELECT * FROM player_settings WHERE player_id = ?";
        $settings = $this->db->fetchOne($query, [$playerId]);
        
        return $settings ?? $this->getDefaultSettings();
    }
    
    /**
     * Update player settings
     */
    public function updateSettings($playerId, $settings) {
        $query = "INSERT INTO player_settings (player_id, settings, updated_at) 
                  VALUES (?, ?, NOW()) 
                  ON DUPLICATE KEY UPDATE settings = ?, updated_at = NOW()";
        
        $settingsJson = json_encode($settings);
        return $this->db->execute($query, [$playerId, $settingsJson, $settingsJson]);
    }
    
    /**
     * Get single setting
     */
    public function getSetting($playerId, $key) {
        $settings = $this->getSettings($playerId);
        $settingsArray = json_decode($settings['settings'], true);
        
        return $settingsArray[$key] ?? null;
    }
    
    /**
     * Update single setting
     */
    public function updateSetting($playerId, $key, $value) {
        $settings = $this->getSettings($playerId);
        $settingsArray = json_decode($settings['settings'], true) ?? [];
        
        $settingsArray[$key] = $value;
        
        return $this->updateSettings($playerId, $settingsArray);
    }
    
    /**
     * Toggle setting
     */
    public function toggleSetting($playerId, $key) {
        $current = $this->getSetting($playerId, $key);
        return $this->updateSetting($playerId, $key, !$current);
    }
    
    /**
     * Get all preferences for UI
     */
    public function getPreferences($playerId) {
        return [
            'theme' => $this->getSetting($playerId, 'theme') ?? 'dark',
            'language' => $this->getSetting($playerId, 'language') ?? 'en',
            'notifications_enabled' => $this->getSetting($playerId, 'notifications_enabled') ?? true,
            'email_notifications' => $this->getSetting($playerId, 'email_notifications') ?? false,
            'sound_enabled' => $this->getSetting($playerId, 'sound_enabled') ?? true,
            'privacy_level' => $this->getSetting($playerId, 'privacy_level') ?? 'public',
            'auto_save' => $this->getSetting($playerId, 'auto_save') ?? true,
            'animations_enabled' => $this->getSetting($playerId, 'animations_enabled') ?? true
        ];
    }
    
    /**
     * Get default settings
     */
    private function getDefaultSettings() {
        return [
            'player_id' => null,
            'settings' => json_encode([
                'theme' => 'dark',
                'language' => 'en',
                'notifications_enabled' => true,
                'email_notifications' => false,
                'sound_enabled' => true,
                'privacy_level' => 'public',
                'auto_save' => true,
                'animations_enabled' => true
            ]),
            'updated_at' => new DateTime()
        ];
    }
}
