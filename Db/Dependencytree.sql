CREATE TABLE buildings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    level INT DEFAULT 0
);

CREATE TABLE ships (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    speed INT,
    power INT
);

CREATE TABLE technologies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    research_time INT
);

CREATE TABLE dependencies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    item_id INT,
    dependency_id INT,
    FOREIGN KEY (item_id) REFERENCES buildings(id) ON DELETE CASCADE,
    FOREIGN KEY (dependency_id) REFERENCES buildings(id) ON DELETE CASCADE
);
