<?php
/**
 * Tasks Page - Player objectives and achievements
 */

if (!defined('CLASS_PATH')) {
    die('Direct access not permitted');
}

if (empty($_SESSION['player_id'])) {
    header('Location: index.php');
    exit;
}

$playerId = (int)$_SESSION['player_id'];
$db = Database::getInstance();

// Ensure Task and Event classes are loaded
require_once CLASS_PATH . 'Task.php';
require_once CLASS_PATH . 'Event.php';
require_once CLASS_PATH . 'TaskGenerator.php';

$task = new Task($playerId);
$event = new Event();

// Handle task actions
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'start_task':
                $taskId = (int)$_POST['task_id'];
                $result = $task->start($taskId);
                $message = $result['success'] ? 'Task started!' : 'Failed to start task';
                break;
                
            case 'complete_task':
                $taskId = (int)$_POST['task_id'];
                $result = $task->complete($taskId);
                if ($result['success']) {
                    $message = "Task completed! Earned " . $result['rewards']['metal'] . " metal and " . $result['rewards']['crystal'] . " crystal!";
                } else {
                    $error = $result['message'] ?? 'Failed to complete task';
                }
                break;
                
            case 'fail_task':
                $taskId = (int)$_POST['task_id'];
                $result = $task->fail($taskId);
                $message = $result['success'] ? 'Task marked as failed' : 'Failed to mark task';
                break;
                
            case 'generate_daily':
                $generator = new TaskGenerator();
                $result = $generator->generateDailyTasks($playerId);
                $message = $result['success'] ? 'Daily tasks generated!' : ($result['message'] ?? 'Failed to generate tasks');
                break;
        }
    }
}

// Get tasks by status
$pendingTasks = $task->getTasks('pending');
$inProgressTasks = $task->getTasks('in_progress');
$completedTasks = $task->getTasks('completed');

$completionRate = $task->getCompletionRate();
$totalRewards = $task->getTotalRewards();
$unreadEvents = $event->getUnreadCount($playerId);

?>
<div class="page-container tasks-page">
    <div class="page-header">
        <h1>Tasks & Objectives</h1>
        <div class="task-stats">
            <div class="stat-box">
                <span class="label">Completion Rate:</span>
                <span class="value"><?php echo $completionRate; ?>%</span>
            </div>
            <div class="stat-box">
                <span class="label">Total Rewards Earned:</span>
                <span class="value">Metal: <?php echo number_format($totalRewards['metal']); ?> | Crystal: <?php echo number_format($totalRewards['crystal']); ?></span>
            </div>
            <div class="stat-box">
                <span class="label">Notifications:</span>
                <span class="value badge"><?php echo $unreadEvents; ?></span>
            </div>
        </div>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>
    
    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <div class="tasks-section">
        <h2>Active Missions (<span class="count"><?php echo count($inProgressTasks); ?></span>)</h2>
        
        <?php if (empty($inProgressTasks)): ?>
            <p class="no-tasks">No active missions. Start a task to begin!</p>
        <?php else: ?>
            <div class="tasks-list">
                <?php foreach ($inProgressTasks as $t): ?>
                    <div class="task-card active">
                        <div class="task-header">
                            <h3><?php echo htmlspecialchars($t['title']); ?></h3>
                            <span class="priority priority-<?php echo $t['priority']; ?>">
                                Priority: <?php echo $t['priority'] == 1 ? 'Low' : ($t['priority'] == 2 ? 'Medium' : 'High'); ?>
                            </span>
                        </div>
                        <p class="task-description"><?php echo htmlspecialchars($t['description']); ?></p>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: <?php echo $t['progress']; ?>%"></div>
                            <span class="progress-text"><?php echo $t['progress']; ?>%</span>
                        </div>
                        <div class="task-rewards">
                            <span>Reward: <strong><?php echo number_format($t['reward_metal']); ?> Metal</strong> + <strong><?php echo number_format($t['reward_crystal']); ?> Crystal</strong></span>
                        </div>
                        <form method="POST" class="task-actions inline">
                            <input type="hidden" name="action" value="complete_task">
                            <input type="hidden" name="task_id" value="<?php echo $t['id']; ?>">
                            <button type="submit" class="btn btn-success">Complete</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="tasks-section">
        <h2>Available Tasks (<span class="count"><?php echo count($pendingTasks); ?></span>)</h2>
        
        <?php if (empty($pendingTasks)): ?>
            <p class="no-tasks">No pending tasks. Great job!</p>
            <form method="POST" class="inline">
                <input type="hidden" name="action" value="generate_daily">
                <button type="submit" class="btn btn-primary">Generate Daily Tasks</button>
            </form>
        <?php else: ?>
            <div class="tasks-list">
                <?php foreach ($pendingTasks as $t): ?>
                    <div class="task-card pending">
                        <div class="task-header">
                            <h3><?php echo htmlspecialchars($t['title']); ?></h3>
                            <span class="category category-<?php echo htmlspecialchars($t['category']); ?>">
                                <?php echo ucfirst($t['category']); ?>
                            </span>
                        </div>
                        <p class="task-description"><?php echo htmlspecialchars($t['description']); ?></p>
                        <div class="task-rewards">
                            <span>Reward: <strong><?php echo number_format($t['reward_metal']); ?> Metal</strong> + <strong><?php echo number_format($t['reward_crystal']); ?> Crystal</strong></span>
                        </div>
                        <form method="POST" class="task-actions inline">
                            <input type="hidden" name="action" value="start_task">
                            <input type="hidden" name="task_id" value="<?php echo $t['id']; ?>">
                            <button type="submit" class="btn btn-primary">Start</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="tasks-section">
        <h2>Completed Missions (<span class="count"><?php echo count($completedTasks); ?></span>)</h2>
        
        <?php if (empty($completedTasks)): ?>
            <p class="no-tasks">No completed tasks yet. Get started!</p>
        <?php else: ?>
            <div class="tasks-list">
                <?php foreach ($completedTasks as $t): ?>
                    <div class="task-card completed">
                        <div class="task-header">
                            <h3><?php echo htmlspecialchars($t['title']); ?> <span class="checkmark">✓</span></h3>
                        </div>
                        <p class="task-description"><?php echo htmlspecialchars($t['description']); ?></p>
                        <div class="task-rewards">
                            <span>Earned: <strong><?php echo number_format($t['reward_metal']); ?> Metal</strong> + <strong><?php echo number_format($t['reward_crystal']); ?> Crystal</strong></span>
                        </div>
                        <div class="completion-time">
                            Completed: <?php echo date('Y-m-d H:i', $t['completed_at']); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
.tasks-page {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

.page-header {
    background: linear-gradient(135deg, #1a3a52 0%, #0d1f2d 100%);
    border: 2px solid #4a9eff;
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 30px;
    color: #fff;
}

.task-stats {
    display: flex;
    gap: 20px;
    margin-top: 15px;
    flex-wrap: wrap;
}

.stat-box {
    background: rgba(0,0,0,0.3);
    padding: 15px;
    border-radius: 5px;
    border-left: 3px solid #4a9eff;
}

.tasks-section {
    margin-bottom: 40px;
}

.tasks-section h2 {
    color: #4a9eff;
    border-bottom: 2px solid #4a9eff;
    padding-bottom: 10px;
    margin-bottom: 20px;
}

.tasks-list {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 20px;
}

.task-card {
    background: rgba(20, 30, 50, 0.9);
    border: 2px solid #333;
    border-radius: 8px;
    padding: 20px;
    transition: all 0.3s ease;
}

.task-card:hover {
    border-color: #4a9eff;
    box-shadow: 0 0 20px rgba(74, 158, 255, 0.3);
}

.task-card.active {
    border-color: #ff9800;
    background: rgba(255, 152, 0, 0.1);
}

.task-card.completed {
    border-color: #4caf50;
    background: rgba(76, 175, 80, 0.1);
}

.task-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
}

.task-header h3 {
    margin: 0;
    color: #fff;
    font-size: 18px;
}

.priority, .category {
    display: inline-block;
    padding: 5px 10px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: bold;
}

.priority-1 { background: #2196F3; color: #fff; }
.priority-2 { background: #ff9800; color: #fff; }
.priority-3 { background: #f44336; color: #fff; }

.category-tutorial { background: #9c27b0; color: #fff; }
.category-progression { background: #2196F3; color: #fff; }
.category-daily { background: #4caf50; color: #fff; }
.category-achievement { background: #ffeb3b; color: #000; }
.category-social { background: #9c27b0; color: #fff; }
.category-combat { background: #f44336; color: #fff; }
.category-economy { background: #ff9800; color: #fff; }
.category-general { background: #607d8b; color: #fff; }

.task-description {
    color: #bbb;
    margin: 10px 0;
    font-size: 14px;
}

.progress-bar {
    background: #000;
    height: 20px;
    border-radius: 4px;
    overflow: hidden;
    margin: 15px 0;
    position: relative;
}

.progress-fill {
    background: linear-gradient(90deg, #4caf50, #81c784);
    height: 100%;
    transition: width 0.3s ease;
}

.progress-text {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: #fff;
    font-weight: bold;
    font-size: 12px;
}

.task-rewards {
    background: rgba(74, 158, 255, 0.2);
    padding: 10px;
    border-radius: 4px;
    margin: 10px 0;
    font-size: 13px;
    border-left: 3px solid #4a9eff;
}

.task-actions {
    display: flex;
    gap: 10px;
    margin-top: 15px;
}

.btn {
    padding: 8px 15px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-weight: bold;
    transition: all 0.3s ease;
}

.btn-primary {
    background: #4a9eff;
    color: #fff;
}

.btn-primary:hover {
    background: #2874d0;
}

.btn-success {
    background: #4caf50;
    color: #fff;
}

.btn-success:hover {
    background: #2e7d32;
}

.checkmark {
    color: #4caf50;
    font-weight: bold;
}

.no-tasks {
    color: #888;
    text-align: center;
    padding: 30px;
    font-style: italic;
}

.completion-time {
    color: #999;
    font-size: 12px;
    margin-top: 10px;
}

.alert {
    padding: 15px;
    border-radius: 4px;
    margin-bottom: 20px;
}

.alert-success {
    background: rgba(76, 175, 80, 0.2);
    color: #4caf50;
    border-left: 4px solid #4caf50;
}

.alert-danger {
    background: rgba(244, 67, 54, 0.2);
    color: #f44336;
    border-left: 4px solid #f44336;
}

.badge {
    display: inline-block;
    background: #f44336;
    color: #fff;
    padding: 5px 10px;
    border-radius: 20px;
    font-weight: bold;
    font-size: 12px;
}

.count {
    color: #ff9800;
    font-weight: bold;
}
</style>

<?php include TEMPLATE_PATH . 'game_interface.php'; ?>
