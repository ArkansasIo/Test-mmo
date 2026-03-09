<?php
/**
 * Cron Job Script
 * This script should be run every minute by a cron job or task scheduler
 * 
 * Linux/Mac cron example:
 * * * * * * /usr/bin/php /path/to/scifi-conquest/Index/cron/game_tick.php >> /path/to/logs/cron.log 2>&1
 * 
 * Windows Task Scheduler:
 * Program: C:\php\php.exe
 * Arguments: C:\path\to\scifi-conquest\Index\cron\game_tick.php
 * Schedule: Every 1 minute
 */

// Set time limit to prevent timeouts
set_time_limit(300); // 5 minutes max

// Change to parent directory
chdir(dirname(__DIR__));

require_once '../config.php';
require_once CLASS_PATH . 'Database.php';
require_once CLASS_PATH . 'GameEngine.php';

// Log start time
$startTime = microtime(true);
$logMessage = "[" . date('Y-m-d H:i:s') . "] Starting game tick...\n";

try {
    // Process automated tasks
    $engine = new GameEngine();
    $engine->processAutomatedTasks();
    
    $endTime = microtime(true);
    $executionTime = round($endTime - $startTime, 3);
    
    $logMessage .= "[" . date('Y-m-d H:i:s') . "] Game tick completed in {$executionTime} seconds\n";
    
    // Write to log
    file_put_contents(
        ROOT_PATH . '/logs/cron.log',
        $logMessage,
        FILE_APPEND
    );
    
    echo "Success: Game tick completed\n";
    exit(0);
    
} catch (Exception $e) {
    $logMessage .= "[" . date('Y-m-d H:i:s') . "] ERROR: " . $e->getMessage() . "\n";
    $logMessage .= $e->getTraceAsString() . "\n";
    
    // Write error to log
    file_put_contents(
        ROOT_PATH . '/logs/error.log',
        $logMessage,
        FILE_APPEND
    );
    
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
