<?php
/**
 * Task Generator - Auto-creates tasks based on game events
 */
class TaskGenerator {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Generate tutorial tasks for new players
     */
    public function generateTutorialTasks($playerId) {
        $task = new Task($playerId);
        
        $tasks = [
            ['Build Your First Building', 'Construct a metal mine on your first planet', 'tutorial', 100, 50, Task::PRIORITY_HIGH],
            ['Research First Tech', 'Complete your first technology research', 'tutorial', 150, 100, Task::PRIORITY_HIGH],
            ['Build Your First Ship', 'Construct a light fighter in your shipyard', 'tutorial', 200, 150, Task::PRIORITY_HIGH],
            ['Send Your First Fleet', 'Send a fleet on a transport mission', 'tutorial', 250, 200, Task::PRIORITY_MEDIUM],
            ['Reach Metal Level 5', 'Upgrade your metal mine to level 5', 'progression', 300, 250, Task::PRIORITY_MEDIUM],
        ];
        
        foreach ($tasks as $t) {
            $task->create($t[0], $t[1], $t[2], $t[3], $t[4], $t[5]);
        }
        
        return ['success' => true, 'tasks_created' => count($tasks)];
    }
    
    /**
     * Generate daily tasks
     */
    public function generateDailyTasks($playerId) {
        $task = new Task($playerId);
        
        // Check if already generated today
        $today = date('Y-m-d');
        $existing = $this->db->fetchOne(
            "SELECT id FROM daily_tasks WHERE player_id = ? AND completed_date = ?",
            [$playerId, $today]
        );
        
        if ($existing) {
            return ['success' => false, 'message' => 'Daily tasks already generated'];
        }
        
        $dailyTasks = [
            ['Harvest Resources', 'Collect 50000 metal today', 'daily', 50, 30, Task::PRIORITY_MEDIUM],
            ['Send Trade Fleet', 'Send a trade fleet to a friendly player', 'daily', 75, 50, Task::PRIORITY_LOW],
            ['Research One Tech', 'Complete one technology research', 'daily', 100, 75, Task::PRIORITY_MEDIUM],
            ['Build Defense', 'Build 10 defense structures', 'daily', 125, 100, Task::PRIORITY_LOW],
        ];
        
        $created = [];
        foreach ($dailyTasks as $t) {
            $result = $task->create($t[0], $t[1], $t[2], $t[3], $t[4], $t[5]);
            if ($result['success']) {
                $created[] = $result['task_id'];
            }
        }
        
        // Log daily task generation
        foreach ($created as $taskId) {
            $this->db->insert('daily_tasks', [
                'player_id' => (int)$playerId,
                'task_id' => (int)$taskId,
                'completed_date' => $today
            ]);
        }
        
        return ['success' => true, 'tasks_created' => count($created)];
    }
    
    /**
     * Generate event-driven tasks
     */
    public function generateEventTask($playerId, $event, $data = []) {
        $task = new Task($playerId);
        
        $eventTasks = [
            'fleet_attacked' => ['Defend Your Fleet', 'Send reinforcements to defend your fleet', 'combat', 300, 200, Task::PRIORITY_HIGH],
            'building_complete' => ['Upgrade Next Building', 'Build the next building upgrade', 'progression', 100, 75, Task::PRIORITY_MEDIUM],
            'research_complete' => ['Research Next Tech', 'Continue your research progress', 'progression', 150, 100, Task::PRIORITY_MEDIUM],
            'low_resources' => ['Boost Production', 'Upgrade resource production buildings', 'economy', 200, 150, Task::PRIORITY_MEDIUM],
            'alliance_created' => ['Invite Members', 'Invite 3 players to your alliance', 'social', 250, 200, Task::PRIORITY_LOW],
        ];
        
        if (!isset($eventTasks[$event])) {
            return ['success' => false, 'message' => 'Unknown event'];
        }
        
        $t = $eventTasks[$event];
        return $task->create($t[0], $t[1], $t[2], $t[3], $t[4], $t[5]);
    }
    
    /**
     * Generate achievement tasks
     */
    public function generateAchievementTasks($playerId) {
        $task = new Task($playerId);
        
        $achievements = [
            ['Military Strategist', 'Win 10 combat missions', 'achievement', 500, 400, Task::PRIORITY_LOW],
            ['Trade Master', 'Complete 50 trade missions', 'achievement', 400, 300, Task::PRIORITY_LOW],
            ['Tech Pioneer', 'Research all technologies in one tree', 'achievement', 600, 500, Task::PRIORITY_MEDIUM],
            ['Empire Builder', 'Build 100 buildings across all planets', 'achievement', 700, 600, Task::PRIORITY_LOW],
            ['Fleet Commander', 'Build and control 50 ships', 'achievement', 550, 450, Task::PRIORITY_LOW],
        ];
        
        foreach ($achievements as $a) {
            $task->create($a[0], $a[1], $a[2], $a[3], $a[4], $a[5]);
        }
        
        return ['success' => true, 'achievements_added' => count($achievements)];
    }
    
    /**
     * Auto-complete expired tasks
     */
    public function autoFailExpiredTasks() {
        $oneWeekAgo = time() - (7 * 24 * 3600);
        
        $tasks = $this->db->fetchAll(
            "SELECT id, player_id FROM tasks WHERE status = 'in_progress' AND started_at < ?",
            [$oneWeekAgo]
        );
        
        $failed = 0;
        foreach ($tasks as $t) {
            $taskObj = new Task($t['player_id']);
            $result = $taskObj->fail($t['id']);
            if ($result['success']) $failed++;
        }
        
        return ['success' => true, 'failed_count' => $failed];
    }
}
