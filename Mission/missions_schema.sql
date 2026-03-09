-- Create database
CREATE DATABASE scifi_rts_mmorpg;

-- Use the created database
USE scifi_rts_mmorpg;

-- Create table for missions
CREATE TABLE missions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    level INT NOT NULL,
    objective VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    rewards VARCHAR(255) NOT NULL
);

-- Insert mission objectives for levels 1-50
INSERT INTO missions (level, objective, description, rewards) VALUES
(1, 'Gather Resources', 'Collect 100 units of Titanium and 50 units of Helium-3.', '50 XP, 10 Credits'),
(2, 'Build a Spaceport', 'Construct a Spaceport to launch your ships.', '100 XP, 20 Credits'),
(3, 'Train a Fleet', 'Train 5 fighter ships in the Spaceport.', '150 XP, 30 Credits'),
(4, 'Defend the Colony', 'Defend your colony from a pirate attack.', '200 XP, 40 Credits'),
(5, 'Explore Asteroid Belt', 'Send a scout ship to explore the nearby asteroid belt.', '250 XP, 50 Credits'),
(6, 'Expand Territory', 'Capture the neighboring uninhabited planet.', '300 XP, 60 Credits'),
(7, 'Research Advanced Shields', 'Research the "Advanced Shields" technology.', '350 XP, 70 Credits'),
(8, 'Form an Alliance', 'Form an alliance with another player faction.', '400 XP, 80 Credits'),
(9, 'Gather Intelligence', 'Send spies to gather intelligence on the enemy faction.', '450 XP, 90 Credits'),
(10, 'Build a Starbase', 'Construct a Starbase to protect your sector.', '500 XP, 100 Credits'),
(11, 'Train Elite Pilots', 'Train 5 elite pilot units.', '550 XP, 110 Credits'),
(12, 'Raid Enemy Outpost', 'Perform a raid on an enemy outpost.', '600 XP, 120 Credits'),
(13, 'Collect Trade Taxes', 'Collect taxes from your trade routes.', '650 XP, 130 Credits'),
(14, 'Upgrade Spaceport', 'Upgrade your Spaceport to level 2.', '700 XP, 140 Credits'),
(15, 'Win a Skirmish', 'Win a skirmish against another player faction.', '750 XP, 150 Credits'),
(16, 'Defend the Starbase', 'Defend your Starbase from an enemy siege.', '800 XP, 160 Credits'),
(17, 'Construct a Research Lab', 'Build a Research Lab to enhance technological advancements.', '850 XP, 170 Credits'),
(18, 'Upgrade Starbase', 'Upgrade your Starbase to level 2.', '900 XP, 180 Credits'),
(19, 'Conduct Research', 'Research "Quantum Computing" technology.', '950 XP, 190 Credits'),
(20, 'Form a Coalition', 'Form a coalition with two other player factions.', '1000 XP, 200 Credits'),
(21, 'Capture a Dreadnought', 'Capture an enemy dreadnought class ship.', '1050 XP, 210 Credits'),
(22, 'Spy on Coalition', 'Send spies to gather intelligence on an enemy coalition.', '1100 XP, 220 Credits'),
(23, 'Upgrade Research Lab', 'Upgrade your Research Lab to level 2.', '1150 XP, 230 Credits'),
(24, 'Train Special Forces', 'Train 5 special forces units.', '1200 XP, 240 Credits'),
(25, 'Win a Strategic Battle', 'Win a strategic battle against another player faction.', '1250 XP, 250 Credits'),
(26, 'Protect Trade Convoy', 'Protect your trade convoy from space pirates.', '1300 XP, 260 Credits'),
(27, 'Build a Space Academy', 'Construct a Space Academy to train your officers.', '1350 XP, 270 Credits'),
(28, 'Upgrade Special Forces', 'Upgrade your special forces units.', '1400 XP, 280 Credits'),
(29, 'Defend Coalition', 'Help defend your coalition from an enemy attack.', '1450 XP, 290 Credits'),
(30, 'Build a Shipyard', 'Construct a Shipyard to build advanced ships.', '1500 XP, 300 Credits'),
(31, 'Train Engineers', 'Train 5 space engineers.', '1550 XP, 310 Credits'),
(32, 'Upgrade Space Academy', 'Upgrade your Space Academy to level 2.', '1600 XP, 320 Credits'),
(33, 'Conduct Espionage', 'Conduct espionage on an enemy faction.', '1650 XP, 330 Credits'),
(34, 'Defend Trade Convoy', 'Defend your trade convoy from enemy raids.', '1700 XP, 340 Credits'),
(35, 'Capture a Mining Station', 'Capture an enemy mining station.', '1750 XP, 350 Credits'),
(36, 'Upgrade Shipyard', 'Upgrade your Shipyard to level 2.', '1800 XP, 360 Credits'),
(37, 'Form a Pact', 'Form a pact with another coalition.', '1850 XP, 370 Credits'),
(38, 'Conduct Diplomacy', 'Conduct diplomacy with a neutral alien race.', '1900 XP, 380 Credits'),
(39, 'Build a Space Dock', 'Construct a Space Dock to repair your ships.', '1950 XP, 390 Credits'),
(40, 'Train Naval Units', 'Train 5 naval units.', '2000 XP, 400 Credits'),
(41, 'Upgrade Space Dock', 'Upgrade your Space Dock to level 2.', '2050 XP, 410 Credits'),
(42, 'Defend Space Dock', 'Defend your Space Dock from an enemy attack.', '2100 XP, 420 Credits'),
(43, 'Build a Defense Platform', 'Construct a Defense Platform to boost sector defense.', '2150 XP, 430 Credits'),
(44, 'Upgrade Defense Platform', 'Upgrade your Defense Platform to level 2.', '2200 XP, 440 Credits'),
(45, 'Form a Secret Pact', 'Form a secret pact with another player faction.', '2250 XP, 450 Credits'),
(46, 'Conduct Reconnaissance', 'Conduct reconnaissance on an enemy space dock.', '2300 XP, 460 Credits'),
(47, 'Defend Secret Pact', 'Help defend your secret pact from an enemy attack.', '2350 XP, 470 Credits'),
(48, 'Upgrade Naval Units', 'Upgrade your naval units.', '2400 XP, 480 Credits'),
(49, 'Capture a Star Cluster', 'Capture a strategically important star cluster.', '2450 XP, 490 Credits'),
(50, 'Win a Galactic Battle', 'Win a galactic battle against another player faction.', '2500 XP, 500 Credits');
