-- Database Schema for Sci-Fi Conquest: Awakening
-- Generated on 2026-03-08

-- Drop existing tables if they exist
DROP TABLE IF EXISTS combat_reports;
DROP TABLE IF EXISTS fleet_movements;
DROP TABLE IF EXISTS fleet_ships;
DROP TABLE IF EXISTS fleets;
DROP TABLE IF EXISTS research_queue;
DROP TABLE IF EXISTS research;
DROP TABLE IF EXISTS building_queue;
DROP TABLE IF EXISTS planet_defenses;
DROP TABLE IF EXISTS planet_ships;
DROP TABLE IF EXISTS buildings;
DROP TABLE IF EXISTS planets;
DROP TABLE IF EXISTS market_orders;
DROP TABLE IF EXISTS messages;
DROP TABLE IF EXISTS notifications;
DROP TABLE IF EXISTS activity_log;
DROP TABLE IF EXISTS alliance_members;
DROP TABLE IF EXISTS alliances;
DROP TABLE IF EXISTS players;

-- Players Table
CREATE TABLE players (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    metal BIGINT DEFAULT 500,
    crystal BIGINT DEFAULT 500,
    deuterium BIGINT DEFAULT 0,
    energy BIGINT DEFAULT 0,
    is_admin TINYINT(1) DEFAULT 0,
    is_banned TINYINT(1) DEFAULT 0,
    created_at INT NOT NULL,
    last_activity INT NOT NULL,
    last_resource_update INT NOT NULL,
    avatar VARCHAR(255) DEFAULT NULL,
    INDEX idx_username (username),
    INDEX idx_email (email),
    INDEX idx_last_activity (last_activity)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Alliances Table
CREATE TABLE alliances (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    tag VARCHAR(8) NOT NULL UNIQUE,
    description TEXT,
    founder_id INT NOT NULL,
    created_at INT NOT NULL,
    logo VARCHAR(255) DEFAULT NULL,
    FOREIGN KEY (founder_id) REFERENCES players(id) ON DELETE CASCADE,
    INDEX idx_name (name),
    INDEX idx_tag (tag)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Alliance Members Table
CREATE TABLE alliance_members (
    id INT AUTO_INCREMENT PRIMARY KEY,
    alliance_id INT NOT NULL,
    player_id INT NOT NULL,
    rank VARCHAR(20) DEFAULT 'member',
    joined_at INT NOT NULL,
    FOREIGN KEY (alliance_id) REFERENCES alliances(id) ON DELETE CASCADE,
    FOREIGN KEY (player_id) REFERENCES players(id) ON DELETE CASCADE,
    UNIQUE KEY unique_player (player_id),
    INDEX idx_alliance (alliance_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Planets Table
CREATE TABLE planets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    player_id INT NOT NULL,
    name VARCHAR(50) NOT NULL,
    galaxy INT NOT NULL,
    system INT NOT NULL,
    position INT NOT NULL,
    diameter INT NOT NULL,
    fields INT NOT NULL,
    fields_used INT DEFAULT 0,
    temperature INT NOT NULL,
    is_capital TINYINT(1) DEFAULT 0,
    created_at INT NOT NULL,
    FOREIGN KEY (player_id) REFERENCES players(id) ON DELETE CASCADE,
    UNIQUE KEY unique_position (galaxy, system, position),
    INDEX idx_player (player_id),
    INDEX idx_coordinates (galaxy, system, position)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Buildings Table
CREATE TABLE buildings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    planet_id INT NOT NULL,
    building_type VARCHAR(50) NOT NULL,
    level INT DEFAULT 0,
    FOREIGN KEY (planet_id) REFERENCES planets(id) ON DELETE CASCADE,
    UNIQUE KEY unique_building (planet_id, building_type),
    INDEX idx_planet (planet_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Building Queue Table
CREATE TABLE building_queue (
    id INT AUTO_INCREMENT PRIMARY KEY,
    planet_id INT NOT NULL,
    building_type VARCHAR(50) NOT NULL,
    level INT NOT NULL,
    start_time INT NOT NULL,
    completion_time INT NOT NULL,
    FOREIGN KEY (planet_id) REFERENCES planets(id) ON DELETE CASCADE,
    INDEX idx_planet (planet_id),
    INDEX idx_completion (completion_time)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Research Table
CREATE TABLE research (
    id INT AUTO_INCREMENT PRIMARY KEY,
    player_id INT NOT NULL,
    research_type VARCHAR(50) NOT NULL,
    level INT DEFAULT 0,
    FOREIGN KEY (player_id) REFERENCES players(id) ON DELETE CASCADE,
    UNIQUE KEY unique_research (player_id, research_type),
    INDEX idx_player (player_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Research Queue Table
CREATE TABLE research_queue (
    id INT AUTO_INCREMENT PRIMARY KEY,
    player_id INT NOT NULL,
    research_type VARCHAR(50) NOT NULL,
    level INT NOT NULL,
    start_time INT NOT NULL,
    completion_time INT NOT NULL,
    FOREIGN KEY (player_id) REFERENCES players(id) ON DELETE CASCADE,
    INDEX idx_player (player_id),
    INDEX idx_completion (completion_time)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Planet Ships Table
CREATE TABLE planet_ships (
    id INT AUTO_INCREMENT PRIMARY KEY,
    planet_id INT NOT NULL,
    ship_type VARCHAR(50) NOT NULL,
    amount BIGINT DEFAULT 0,
    FOREIGN KEY (planet_id) REFERENCES planets(id) ON DELETE CASCADE,
    UNIQUE KEY unique_ship (planet_id, ship_type),
    INDEX idx_planet (planet_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Planet Defenses Table
CREATE TABLE planet_defenses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    planet_id INT NOT NULL,
    defense_type VARCHAR(50) NOT NULL,
    amount BIGINT DEFAULT 0,
    FOREIGN KEY (planet_id) REFERENCES planets(id) ON DELETE CASCADE,
    UNIQUE KEY unique_defense (planet_id, defense_type),
    INDEX idx_planet (planet_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Fleets Table
CREATE TABLE fleets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    player_id INT NOT NULL,
    planet_id INT NOT NULL,
    created_at INT NOT NULL,
    FOREIGN KEY (player_id) REFERENCES players(id) ON DELETE CASCADE,
    FOREIGN KEY (planet_id) REFERENCES planets(id) ON DELETE CASCADE,
    INDEX idx_player (player_id),
    INDEX idx_planet (planet_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Fleet Ships Table
CREATE TABLE fleet_ships (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fleet_id INT NOT NULL,
    ship_type VARCHAR(50) NOT NULL,
    amount BIGINT DEFAULT 0,
    FOREIGN KEY (fleet_id) REFERENCES fleets(id) ON DELETE CASCADE,
    INDEX idx_fleet (fleet_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Fleet Movements Table
CREATE TABLE fleet_movements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fleet_id INT NOT NULL,
    player_id INT NOT NULL,
    start_galaxy INT NOT NULL,
    start_system INT NOT NULL,
    start_position INT NOT NULL,
    target_galaxy INT NOT NULL,
    target_system INT NOT NULL,
    target_position INT NOT NULL,
    mission_type VARCHAR(20) NOT NULL,
    departure_time INT NOT NULL,
    arrival_time INT NOT NULL,
    status VARCHAR(20) DEFAULT 'traveling',
    cargo_metal BIGINT DEFAULT 0,
    cargo_crystal BIGINT DEFAULT 0,
    cargo_deuterium BIGINT DEFAULT 0,
    FOREIGN KEY (fleet_id) REFERENCES fleets(id) ON DELETE CASCADE,
    FOREIGN KEY (player_id) REFERENCES players(id) ON DELETE CASCADE,
    INDEX idx_fleet (fleet_id),
    INDEX idx_player (player_id),
    INDEX idx_arrival (arrival_time),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Combat Reports Table
CREATE TABLE combat_reports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    attacker_id INT NOT NULL,
    defender_id INT NOT NULL,
    combat_data TEXT NOT NULL,
    winner VARCHAR(20) NOT NULL,
    created_at INT NOT NULL,
    FOREIGN KEY (attacker_id) REFERENCES players(id) ON DELETE CASCADE,
    FOREIGN KEY (defender_id) REFERENCES players(id) ON DELETE CASCADE,
    INDEX idx_attacker (attacker_id),
    INDEX idx_defender (defender_id),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Messages Table
CREATE TABLE messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,
    recipient_id INT NOT NULL,
    subject VARCHAR(100) NOT NULL,
    message TEXT NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    created_at INT NOT NULL,
    FOREIGN KEY (sender_id) REFERENCES players(id) ON DELETE CASCADE,
    FOREIGN KEY (recipient_id) REFERENCES players(id) ON DELETE CASCADE,
    INDEX idx_recipient (recipient_id),
    INDEX idx_sender (sender_id),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Notifications Table
CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    player_id INT NOT NULL,
    type VARCHAR(20) NOT NULL,
    title VARCHAR(100) NOT NULL,
    message TEXT NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    created_at INT NOT NULL,
    FOREIGN KEY (player_id) REFERENCES players(id) ON DELETE CASCADE,
    INDEX idx_player (player_id),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Market Orders Table
CREATE TABLE market_orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    player_id INT NOT NULL,
    offer_resource VARCHAR(20) NOT NULL,
    offer_amount BIGINT NOT NULL,
    request_resource VARCHAR(20) NOT NULL,
    request_amount BIGINT NOT NULL,
    status VARCHAR(20) DEFAULT 'active',
    created_at INT NOT NULL,
    FOREIGN KEY (player_id) REFERENCES players(id) ON DELETE CASCADE,
    INDEX idx_player (player_id),
    INDEX idx_status (status),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Activity Log Table
CREATE TABLE activity_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    player_id INT NOT NULL,
    action VARCHAR(50) NOT NULL,
    details TEXT,
    ip_address VARCHAR(45),
    created_at INT NOT NULL,
    FOREIGN KEY (player_id) REFERENCES players(id) ON DELETE CASCADE,
    INDEX idx_player (player_id),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default admin user (password: admin123)
INSERT INTO players (username, email, password, metal, crystal, deuterium, is_admin, created_at, last_activity, last_resource_update)
VALUES ('admin', 'admin@scificonquest.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 10000, 10000, 5000, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), UNIX_TIMESTAMP());
