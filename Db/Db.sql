CREATE DATABASE testgame;

USE ogame;
-- Forum topics table
CREATE TABLE forum_topics (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    player_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (player_id) REFERENCES players(id)
);

-- Forum posts table
CREATE TABLE forum_posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    topic_id INT NOT NULL,
    player_id INT NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (topic_id) REFERENCES forum_topics(id),
    FOREIGN KEY (player_id) REFERENCES players(id)
);
-- Players table
CREATE TABLE players (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL,
    metal INT DEFAULT 1000,
    crystal INT DEFAULT 1000,
    deuterium INT DEFAULT 1000
);

-- Planets table
CREATE TABLE planets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    player_id INT,
    metal INT DEFAULT 1000,
    crystal INT DEFAULT 1000,
    deuterium INT DEFAULT 1000,
    FOREIGN KEY (player_id) REFERENCES players(id)
);

-- Fleets table
CREATE TABLE fleets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    player_id INT,
    planet_id INT,
    ships INT DEFAULT 0,
    FOREIGN KEY (player_id) REFERENCES players(id),
    FOREIGN KEY (planet_id) REFERENCES planets(id)
);
-- Ships table
CREATE TABLE ships (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    type VARCHAR(50) NOT NULL,
    player_id INT,
    fleet_id INT,
    FOREIGN KEY (player_id) REFERENCES players(id),
    FOREIGN KEY (fleet_id) REFERENCES fleets(id)
);

-- Weapons table
CREATE TABLE weapons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    damage INT NOT NULL,
    range INT NOT NULL,
    ship_id INT,
    FOREIGN KEY (ship_id) REFERENCES ships(id)
);

-- Expanded Planets table
CREATE TABLE planets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    description TEXT,
    temperature INT,
    player_id INT,
    galaxy_id INT,
    metal INT DEFAULT 1000,
    crystal INT DEFAULT 1000,
    deuterium INT DEFAULT 1000,
    FOREIGN KEY (player_id) REFERENCES players(id),
    FOREIGN KEY (galaxy_id) REFERENCES galaxies(id)
);
-- Combat table
CREATE TABLE combats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    attacker_id INT,
    defender_id INT,
    result VARCHAR(50),
    FOREIGN KEY (attacker_id) REFERENCES players(id),
    FOREIGN KEY (defender_id) REFERENCES players(id)
);
