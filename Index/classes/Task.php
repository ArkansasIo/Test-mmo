<?php
/**
 * Task Manager - Handles player tasks and goals
 */
class Task {
    private $db;
    private $playerId;
    
    const STATUS_PENDING = 'pending';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';
    
    const PRIORITY_LOW = 1;
    const PRIORITY_MEDIUM = 2;
    const PRIORITY_HIGH = 3;
    
    public function __construct($playerId) {
        $this->db = Database::getInstance();
        $this->playerId = (int)$playerId;
    }
    
    /**
     * Get all player tasks
     */
    public function getTasks($status = null) {
        $query = "SELECT * FROM tasks WHERE player_id = ?";
        $params = [$this->playerId];
        
        if ($status) {
            $query .= " AND status = ?";
            $params[] = $status;
        }
        
        $query .= " ORDER BY priority DESC, created_at ASC";
        return $this->db->fetchAll($query, $params);
    }
    
    /**
     * Create new task
     */
    public function create($title, $description = '', $category = 'general', $reward_metal = 0, $reward_crystal = 0, $priority = self::PRIORITY_MEDIUM) {
        try {
            $taskId = $this->db->insert('tasks', [
                'player_id' => $this->playerId,
                'title' => $title,
                'description' => $description,
                'category' => $category,
                'status' => self::STATUS_PENDING,
                'priority' => $priority,
                'reward_metal' => (int)$reward_metal,
                'reward_crystal' => (int)$reward_crystal,
                'progress' => 0,
                'created_at' => time()
            ]);
            return ['success' => true, 'task_id' => $taskId];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    /**
     * Start task
     */
    public function start($taskId) {
        try {
            $this->db->update('tasks', [
                'status' => self::STATUS_IN_PROGRESS,
                'started_at' => time()
            ], 'id = ? AND player_id = ?', [$taskId, $this->playerId]);
            
            return ['success' => true];
        } catch (Exception $e) {
            return ['success' => false];
        }
    }
    
    /**
     * Update task progress
     */
    public function updateProgress($taskId, $progress) {
        if ($progress < 0) $progress = 0;
        if ($progress > 100) $progress = 100;
        
        try {
            $this->db->update('tasks', [
                'progress' => (int)$progress
            ], 'id = ? AND player_id = ?', [$taskId, $this->playerId]);
            
            return ['success' => true];
        } catch (Exception $e) {
            return ['success' => false];
        }
    }
    
    /**
     * Complete task and award rewards
     */
    public function complete($taskId) {
        $task = $this->db->fetchOne(
            "SELECT * FROM tasks WHERE id = ? AND player_id = ?",
            [$taskId, $this->playerId]
        );
        
        if (!$task) {
            return ['success' => false, 'message' => 'Task not found'];
        }
        
        try {
            // Update task status
            $this->db->update('tasks', [
                'status' => self::STATUS_COMPLETED,
                'completed_at' => time(),
                'progress' => 100
            ], 'id = ?', [$taskId]);
            
            // Award resources
            $player = new Player($this->playerId);
            if ($task['reward_metal'] > 0 || $task['reward_crystal'] > 0) {
                $resource = new Resource($this->playerId);
                $resource->add($task['reward_metal'], $task['reward_crystal'], 0);
            }
            
            // Create event notification
            $event = new Event();
            $event->create($this->playerId, 'task_completed', [
                'task_id' => $taskId,
                'task_title' => $task['title'],
                'rewards' => ['metal' => $task['reward_metal'], 'crystal' => $task['reward_crystal']]
            ]);
            
            return ['success' => true, 'rewards' => ['metal' => $task['reward_metal'], 'crystal' => $task['reward_crystal']]];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    /**
     * Fail task
     */
    public function fail($taskId) {
        try {
            $this->db->update('tasks', [
                'status' => self::STATUS_FAILED,
                'failed_at' => time()
            ], 'id = ? AND player_id = ?', [$taskId, $this->playerId]);
            
            return ['success' => true];
        } catch (Exception $e) {
            return ['success' => false];
        }
    }
    
    /**
     * Get tasks by category
     */
    public function getByCategory($category, $status = null) {
        $query = "SELECT * FROM tasks WHERE player_id = ? AND category = ?";
        $params = [$this->playerId, $category];
        
        if ($status) {
            $query .= " AND status = ?";
            $params[] = $status;
        }
        
        return $this->db->fetchAll($query, $params);
    }
    
    /**
     * Get high-priority tasks
     */
    public function getHighPriority() {
        return $this->db->fetchAll(
            "SELECT * FROM tasks WHERE player_id = ? AND priority >= ? AND status IN (?, ?) ORDER BY created_at ASC",
            [$this->playerId, self::PRIORITY_HIGH, self::STATUS_PENDING, self::STATUS_IN_PROGRESS]
        );
    }
    
    /**
     * Get task completion rate
     */
    public function getCompletionRate() {
        $total = $this->db->fetchOne(
            "SELECT COUNT(*) as count FROM tasks WHERE player_id = ?",
            [$this->playerId]
        );
        
        $completed = $this->db->fetchOne(
            "SELECT COUNT(*) as count FROM tasks WHERE player_id = ? AND status = ?",
            [$this->playerId, self::STATUS_COMPLETED]
        );
        
        if ($total['count'] == 0) return 0;
        return round(($completed['count'] / $total['count']) * 100, 2);
    }
    
    /**
     * Get total rewards earned
     */
    public function getTotalRewards() {
        $result = $this->db->fetchOne(
            "SELECT SUM(reward_metal) as metal, SUM(reward_crystal) as crystal FROM tasks WHERE player_id = ? AND status = ?",
            [$this->playerId, self::STATUS_COMPLETED]
        );
        
        return [
            'metal' => (int)($result['metal'] ?? 0),
            'crystal' => (int)($result['crystal'] ?? 0)
        ];
    }
}
