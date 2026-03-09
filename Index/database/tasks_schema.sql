-- Sci-Fi Conquest Tasks & Events Schema Extension

-- Tasks table for player objectives
CREATE TABLE IF NOT EXISTS tasks (
    id INT PRIMARY KEY AUTO_INCREMENT,
    player_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    category VARCHAR(50) DEFAULT 'general',
    status VARCHAR(20) DEFAULT 'pending',
    priority INT DEFAULT 2,
    progress INT DEFAULT 0,
    reward_metal INT DEFAULT 0,
    reward_crystal INT DEFAULT 0,
    reward_deuterium INT DEFAULT 0,
    started_at BIGINT,
    completed_at BIGINT,
    failed_at BIGINT,
    created_at BIGINT NOT NULL,
    FOREIGN KEY (player_id) REFERENCES players(id) ON DELETE CASCADE,
    INDEX idx_player_status (player_id, status),
    INDEX idx_priority (priority),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Events table for game notifications
CREATE TABLE IF NOT EXISTS events (
    id INT PRIMARY KEY AUTO_INCREMENT,
    player_id INT NOT NULL,
    type VARCHAR(50) NOT NULL,
    data LONGTEXT,
    `read` TINYINT DEFAULT 0,
    created_at BIGINT NOT NULL,
    FOREIGN KEY (player_id) REFERENCES players(id) ON DELETE CASCADE,
    INDEX idx_player_read (player_id, `read`),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Daily Tasks Log
CREATE TABLE IF NOT EXISTS daily_tasks (
    id INT PRIMARY KEY AUTO_INCREMENT,
    player_id INT NOT NULL,
    task_id INT,
    completed_date DATE,
    FOREIGN KEY (player_id) REFERENCES players(id) ON DELETE CASCADE,
    FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE CASCADE,
    UNIQUE KEY unique_player_date (player_id, completed_date),
    INDEX idx_player (player_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
