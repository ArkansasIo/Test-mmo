# Database Schema Documentation

## Overview
This document describes the complete database schema for Scifi Conquest, including all tables, columns, relationships, and indexes.

---

## Player Management

### `players` Table
Core player/user data.

| Column | Type | Null | Key | Description |
|--------|------|------|-----|-------------|
| id | INT | NO | PK | Player ID |
| username | VARCHAR(50) | NO | UQ | Unique username |
| email | VARCHAR(100) | NO | UQ | Email address |
| password_hash | VARCHAR(255) | NO | | Bcrypt password hash |
| display_name | VARCHAR(100) | YES | | Display name |
| status | ENUM('active','inactive','banned','suspended') | NO | | Account status |
| role | ENUM('player','moderator','admin','super_admin') | NO | | User role |
| level | INT | NO | | Player level |
| experience | INT | NO | | Total experience points |
| achievement_points | INT | NO | | Achievement point total |
| total_credits | BIGINT | NO | | Total in-game currency |
| total_minerals | BIGINT | NO | | Total minerals |
| total_gas | BIGINT | NO | | Total gas |
| alliance_id | INT | YES | FK | Alliance membership |
| ban_reason | VARCHAR(255) | YES | | Reason for ban |
| ban_until | DATETIME | YES | | Ban expiration date |
| profile_picture | VARCHAR(255) | YES | | Profile image path |
| bio | TEXT | YES | | Player biography |
| created_at | DATETIME | NO | | Registration timestamp |
| updated_at | DATETIME | NO | | Last update timestamp |
| last_activity | DATETIME | NO | | Last login/activity |
| last_login | DATETIME | YES | | Last login time |

**Indexes:**
- PRIMARY KEY (id)
- UNIQUE KEY (username)
- UNIQUE KEY (email)
- KEY (alliance_id)
- KEY (status)
- KEY (created_at)

---

### `player_settings` Table
Player preferences and configuration.

| Column | Type | Null | Key | Description |
|--------|------|------|-----|-------------|
| player_id | INT | NO | PK | Player reference |
| settings | JSON | NO | | Settings as JSON object |
| theme | VARCHAR(20) | NO | | UI theme (dark/light) |
| language | VARCHAR(5) | NO | | Language code |
| notifications_enabled | BOOLEAN | NO | | In-game notifications |
| email_notifications | BOOLEAN | NO | | Email notifications |
| sound_enabled | BOOLEAN | NO | | Sound effects |
| animations_enabled | BOOLEAN | NO | | UI animations |
| privacy_level | ENUM('public','friends','private') | NO | | Profile visibility |
| updated_at | DATETIME | NO | | Last update |

**Relationships:**
- Foreign Key: player_id → players.id

---

## Game World

### `planets` Table
Planet data owned by players.

| Column | Type | Null | Key | Description |
|--------|------|------|-----|-------------|
| id | INT | NO | PK | Planet ID |
| owner_id | INT | NO | FK | Player owner |
| name | VARCHAR(100) | NO | | Planet name |
| galaxy_id | INT | NO | FK | Galaxy location |
| star_id | INT | NO | FK | Star system |
| planet_type | ENUM('terrestrial','gas','ice','lava','desert') | NO | | Planet type |
| size | ENUM('small','medium','large','huge') | NO | | Planet size |
| coordinates_x | INT | NO | | Galaxy X position |
| coordinates_y | INT | NO | | Galaxy Y position |
| accessibility | INT | NO | | Distance factor |
| population | BIGINT | NO | | Total population |
| morale | INT | NO | | Population happiness 0-100 |
| defenses_level | INT | NO | | Defense strength |
| captured_from | INT | YES | | Previous owner |
| created_at | DATETIME | NO | | Creation timestamp |
| last_attacked | DATETIME | YES | | Last attack time |

**Indexes:**
- PRIMARY KEY (id)
- KEY (owner_id)
- KEY (galaxy_id, star_id)
- KEY (coordinates_x, coordinates_y)

---

### `buildings` Table
Structures on planets.

| Column | Type | Null | Key | Description |
|--------|------|------|-----|-------------|
| id | INT | NO | PK | Building ID |
| planet_id | INT | NO | FK | Planet location |
| building_type | VARCHAR(50) | NO | | Building type |
| level | INT | NO | | Building level |
| health | INT | NO | | Current health |
| production_rate | INT | NO | | Resource production |
| storage_capacity | BIGINT | NO | | Resource storage |
| defense_value | INT | NO | | Defense contribution |
| completion_time | INT | NO | | Build time in seconds |
| is_complete | BOOLEAN | NO | | Build status |
| started_at | DATETIME | NO | | Build start time |
| completed_at | DATETIME | YES | | Completion time |

**Relationships:**
- Foreign Key: planet_id → planets.id

**Building Types:**
- Farm, Factory, Laboratory, Academy
- Turret, Missile_Launcher, Shield_Generator
- Warehouse, Treasury, Research_Center
- Spaceport, Shipyard

---

## Fleet & Combat

### `fleets` Table
Player fleet data.

| Column | Type | Null | Key | Description |
|--------|------|------|-----|-------------|
| id | INT | NO | PK | Fleet ID |
| owner_id | INT | NO | FK | Owner player |
| name | VARCHAR(100) | NO | | Fleet name |
| current_location | INT | YES | FK | Current planet |
| destination | INT | YES | FK | Target planet |
| status | ENUM('stationed','traveling','attacking','returning','destroyed') | NO | | Fleet status |
| total_ships | INT | NO | | Number of ships |
| morale | INT | NO | | Fleet morale |
| cargo_capacity | BIGINT | NO | | Cargo space |
| attack_power | INT | NO | | Combat strength |
| defense_power | INT | NO | | Defense strength |
| eta_arrival | DATETIME | YES | | Estimated arrival |
| departure_time | DATETIME | YES | | Departure timestamp |
| fuel_remaining | INT | NO | | Fuel percentage |
| created_at | DATETIME | NO | | Creation time |

**Indexes:**
- PRIMARY KEY (id)
- KEY (owner_id)
- KEY (current_location)
- KEY (status)

---

### `ships` Table
Individual ship records.

| Column | Type | Null | Key | Description |
|--------|------|------|-----|-------------|
| id | INT | NO | PK | Ship ID |
| fleet_id | INT | NO | FK | Fleet reference |
| ship_type | VARCHAR(50) | NO | | Ship class |
| health | INT | NO | | Current health |
| max_health | INT | NO | | Maximum health |
| attack_power | INT | NO | | Attack damage |
| defense_power | INT | NO | | Defense strength |
| cargo_capacity | INT | NO | | Cargo space |
| status | ENUM('operational','damaged','destroyed') | NO | | Ship status |

**Ship Types:**
- Scout, Fighter, Bomber, Destroyer
- Cruiser, Battleship, Carrier
- Transport, Freighter, Tanker

---

### `battles` Table
Combat records.

| Column | Type | Null | Key | Description |
|--------|------|------|-----|-------------|
| id | INT | NO | PK | Battle ID |
| attacker_id | INT | NO | FK | Attacking player |
| defender_id | INT | NO | FK | Defending player |
| attacker_fleet_id | INT | NO | FK | Attacker fleet |
| defender_fleet_id | INT | NO | FK | Defender fleet |
| location | INT | NO | FK | Battle location |
| winner | ENUM('attacker','defender','draw') | NO | | Battle outcome |
| attacker_casualties | INT | NO | | Ships lost (attacker) |
| defender_casualties | INT | NO | | Ships lost (defender) |
| resources_looted | JSON | NO | | Resources won |
| battle_log | JSON | NO | | Detailed battle log |
| started_at | DATETIME | NO | | Battle start |
| ended_at | DATETIME | NO | | Battle end |

---

## Resources

### `player_resources` Table
Player resource pool.

| Column | Type | Null | Key | Description |
|--------|------|------|-----|-------------|
| player_id | INT | NO | PK | Player reference |
| credits | BIGINT | NO | | Currency |
| minerals | BIGINT | NO | | Minerals |
| gas | BIGINT | NO | | Gas |
| research_points | INT | NO | | Research points |
| updated_at | DATETIME | NO | | Last update |

**Relationships:**
- Foreign Key: player_id → players.id

---

### `planet_resources` Table
Planetary resource pools.

| Column | Type | Null | Key | Description |
|--------|------|------|-----|-------------|
| planet_id | INT | NO | PK | Planet reference |
| credits | BIGINT | NO | | Stored credits |
| minerals | BIGINT | NO | | Stored minerals |
| gas | BIGINT | NO | | Stored gas |
| storage_used | BIGINT | NO | | Total storage used |
| last_update | DATETIME | NO | | Last calculation |

---

## Technology & Research

### `technologies` Table
Available technologies.

| Column | Type | Null | Key | Description |
|--------|------|------|-----|-------------|
| id | INT | NO | PK | Technology ID |
| name | VARCHAR(100) | NO | | Tech name |
| category | VARCHAR(50) | NO | | Tech category |
| description | TEXT | NO | | Description |
| requirements | JSON | NO | | Required techs |
| cost | JSON | NO | | Research cost |
| research_time | INT | NO | | Time in seconds |
| prerequisites | JSON | NO | | Prerequisite techs |
| active | BOOLEAN | NO | | Is available |

**Categories:**
- Military, Defense, Resource, Production
- Exploration, Diplomacy, Economy, Science

---

### `player_research` Table
Player's researched technologies.

| Column | Type | Null | Key | Description |
|--------|------|------|-----|-------------|
| player_id | INT | NO | PK | Player reference |
| technology_id | INT | NO | PK | Technology reference |
| progress | INT | NO | | Completion % (0-100) |
| started_at | DATETIME | NO | | Research start |
| completed_at | DATETIME | YES | | Completion time |
| status | ENUM('researching','completed','cancelled') | NO | | Status |

---

## Achievements & Progression

### `achievements` Table
Achievement definitions.

| Column | Type | Null | Key | Description |
|--------|------|------|-----|-------------|
| id | INT | NO | PK | Achievement ID |
| key | VARCHAR(50) | NO | UQ | Achievement key |
| title | VARCHAR(100) | NO | | Display title |
| description | TEXT | NO | | Description |
| category | VARCHAR(50) | NO | | Category |
| difficulty | ENUM('easy','medium','hard','legendary') | NO | | Difficulty |
| icon | VARCHAR(255) | NO | | Icon URL |
| reward_points | INT | NO | | Point reward |
| reward_resource | VARCHAR(50) | YES | | Bonus resource |
| reward_amount | INT | YES | | Bonus amount |
| badge_id | INT | YES | FK | Badge award |
| requirements | JSON | NO | | Requirements JSON |
| active | BOOLEAN | NO | | Is active |

---

### `player_achievements` Table
Player achievement tracking.

| Column | Type | Null | Key | Description |
|--------|------|------|-----|-------------|
| player_id | INT | NO | PK | Player reference |
| achievement_key | VARCHAR(50) | NO | PK | Achievement key |
| awarded_at | DATETIME | NO | | Award timestamp |
| progress | INT | NO | | Completion % |

---

## Notifications & Communication

### `notifications` Table
In-game notifications.

| Column | Type | Null | Key | Description |
|--------|------|------|-----|-------------|
| id | INT | NO | PK | Notification ID |
| player_id | INT | NO | FK | Recipient player |
| title | VARCHAR(100) | NO | | Notification title |
| message | TEXT | NO | | Message content |
| type | ENUM('info','success','warning','danger','event') | NO | | Notification type |
| action_url | VARCHAR(255) | YES | | Action link |
| is_read | BOOLEAN | NO | | Read status |
| created_at | DATETIME | NO | | Creation time |
| read_at | DATETIME | YES | | Read timestamp |

**Indexes:**
- PRIMARY KEY (id)
- KEY (player_id, is_read, created_at)

---

### `notification_log` Table
Email notification log.

| Column | Type | Null | Key | Description |
|--------|------|------|-----|-------------|
| id | INT | NO | PK | Log ID |
| recipient | VARCHAR(100) | NO | | Email recipient |
| type | VARCHAR(50) | NO | | Notification type |
| subject | VARCHAR(255) | NO | | Email subject |
| success | BOOLEAN | NO | | Send success |
| timestamp | DATETIME | NO | | Sent time |

---

## Alliances

### `alliances` Table
Player groups/alliances.

| Column | Type | Null | Key | Description |
|--------|------|------|-----|-------------|
| id | INT | NO | PK | Alliance ID |
| name | VARCHAR(50) | NO | UQ | Alliance name |
| tag | VARCHAR(10) | NO | UQ | Alliance tag |
| description | TEXT | YES | | Description |
| leader_id | INT | NO | FK | Alliance leader |
| founded_at | DATETIME | NO | | Creation date |
| members_count | INT | NO | | Members count |
| treasury | BIGINT | NO | | Shared resources |
| level | INT | NO | | Alliance level |
| diplomatic_status | JSON | NO | | Diplomacy data |

---

### `alliance_members` Table
Alliance membership.

| Column | Type | Null | Key | Description |
|--------|------|------|-----|-------------|
| player_id | INT | NO | PK | Player reference |
| alliance_id | INT | NO | PK | Alliance reference |
| rank | ENUM('member','officer','leader') | NO | | Member rank |
| joined_at | DATETIME | NO | | Join date |
| contribution | BIGINT | NO | | Resources contributed |

---

## Admin & Moderation

### `admins` Table
Admin user accounts.

| Column | Type | Null | Key | Description |
|--------|------|------|-----|-------------|
| id | INT | NO | PK | Admin ID |
| username | VARCHAR(50) | NO | UQ | Admin username |
| email | VARCHAR(100) | NO | | Admin email |
| password_hash | VARCHAR(255) | NO | | Password hash |
| role | ENUM('moderator','admin','super_admin') | NO | | Admin role |
| permissions | JSON | NO | | Permissions list |
| active | BOOLEAN | NO | | Is active |
| created_at | DATETIME | NO | | Created date |
| last_login | DATETIME | YES | | Last login time |

---

### `admin_logs` Table
Admin action audit trail.

| Column | Type | Null | Key | Description |
|--------|------|------|-----|-------------|
| id | INT | NO | PK | Log ID |
| admin_id | INT | YES | FK | Admin reference |
| action | VARCHAR(100) | NO | | Action performed |
| details | TEXT | YES | | Action details |
| ip_address | VARCHAR(45) | NO | | IP address |
| timestamp | DATETIME | NO | | Action time |

**Indexes:**
- PRIMARY KEY (id)
- KEY (admin_id, timestamp)

---

### `player_warnings` Table
Player moderation warnings.

| Column | Type | Null | Key | Description |
|--------|------|------|-----|-------------|
| id | INT | NO | PK | Warning ID |
| player_id | INT | NO | FK | Warned player |
| admin_id | INT | YES | FK | Moderator |
| reason | TEXT | NO | | Warning reason |
| severity | ENUM('low','medium','high','critical') | NO | | Severity level |
| created_at | DATETIME | NO | | Warning date |
| resolved | BOOLEAN | NO | | Is resolved |

---

### `system_logs` Table
System and error logs.

| Column | Type | Null | Key | Description |
|--------|------|------|-----|-------------|
| id | INT | NO | PK | Log ID |
| level | ENUM('debug','info','warning','error','critical') | NO | | Log level |
| action | VARCHAR(100) | NO | | Action/event |
| message | TEXT | NO | | Log message |
| context | JSON | YES | | Additional context |
| user_id | INT | YES | | Related user |
| ip_address | VARCHAR(45) | YES | | Source IP |
| created_at | DATETIME | NO | | Log time |

**Indexes:**
- KEY (level, created_at)
- KEY (user_id, created_at)

---

## Statistics & Analytics

### `game_statistics` Table
Player action tracking.

| Column | Type | Null | Key | Description |
|--------|------|------|-----|-------------|
| id | INT | NO | PK | Stat ID |
| player_id | INT | NO | FK | Player |
| action | VARCHAR(100) | NO | | Action type |
| metadata | JSON | YES | | Additional data |
| timestamp | DATETIME | NO | | Action time |

**Indexes:**
- KEY (player_id, action, timestamp)
- KEY (timestamp)

---

## Tasks & Scheduler

### `game_tasks` Table
Background task queue.

| Column | Type | Null | Key | Description |
|--------|------|------|-----|-------------|
| id | INT | NO | PK | Task ID |
| player_id | INT | YES | FK | Player reference |
| task_type | VARCHAR(100) | NO | | Task type |
| priority | INT | NO | | Priority level |
| data | JSON | NO | | Task data |
| status | ENUM('pending','running','completed','failed') | NO | | Status |
| result | JSON | YES | | Task result |
| scheduled_at | DATETIME | NO | | Scheduled time |
| started_at | DATETIME | YES | | Start time |
| completed_at | DATETIME | YES | | End time |
| retries | INT | NO | | Retry count |

---

## Entity Relationships Diagram

```
players (1) ──────── (M) planets
    │
    ├──────────────(1) alliance_members
    │
    ├──────────────(1) player_resources
    │
    ├──────────────(1) player_settings
    │
    ├──────────────(1) player_achievements
    │
    ├──────────────(M) fleets
    │
    └──────────────(M) game_statistics

planets (1) ──────── (M) buildings
    │
    └──────────── (M) game_tasks

fleets (1) ────────── (M) ships

battlesForeignKey: (attacker_id, defender_id) → players
technologies (1) ──── (M) player_research

alliances (1) ────── (M) alliance_members
```

---

## Indexes Summary

Key performance indexes:
- `players(username, email)` - User lookup
- `planets(owner_id, coordinates_x, coordinates_y)` - Player planets
- `fleets(owner_id, status)` - Player fleets
- `buildings(planet_id, building_type)` - Planet buildings
- `game_statistics(player_id, timestamp)` - Analytics
- `notifications(player_id, is_read)` - Notification queries
- `game_tasks(status, scheduled_at)` - Task scheduling

---

## Database Maintenance

### Regular Backups
```sql
mysqldump -u user -p scifi_conquest > backup_$(date +%Y%m%d).sql
```

### Archive Old Data
```sql
-- Archive old statistics (older than 90 days)
INSERT INTO game_statistics_archive 
  SELECT * FROM game_statistics WHERE timestamp < DATE_SUB(NOW(), INTERVAL 90 DAY);

DELETE FROM game_statistics WHERE timestamp < DATE_SUB(NOW(), INTERVAL 90 DAY);
```

### Optimize Tables
```sql
OPTIMIZE TABLE players, planets, fleets, buildings;
ANALYZE TABLE game_statistics;
```

---

## Constraints & Triggers

### Important Constraints
- Player email must be unique
- Technology prerequisites must exist
- Building types must be valid categories
- Foreign keys enforced on all references
- Timestamps auto-updated

### Suggested Triggers
- Update player's last_activity on any action
- When building completes, update planet resources
- Auto-delete old logs after 90 days
- Update alliance member count when members join/leave

---

**Last Updated:** 2024
**Version:** 1.0
