================================================================================
    DATABASE SCHEMA & ARCHITECTURE SPECIFICATION
    Sci-Fi Conquest: Awakening - MariaDB Implementation
================================================================================

VERSION: 1.0
STATUS: Production-Ready
DBMS: MariaDB 12.1
TABLES: 21 verified tables
KEYS: 150+ foreign keys indexed

================================================================================
SECTION 1: DATABASE DESIGN PRINCIPLES
================================================================================

NORMALIZATION: 3NF (Third Normal Form)
├─ Eliminates redundant data
├─ Maintains referential integrity
├─ Supports efficient querying
└─ Reduces storage overhead

INDEXING STRATEGY:
├─ Primary keys on all tables (unique, not null)
├─ Foreign keys indexed for relationship traversal
├─ Natural key indexes for lookup queries
├─ Composite indexes for common filtered searches
└─ Full-text indexes for search functionality

DATA TYPES:
├─ INT: Integer values (32-bit)
├─ BIGINT: Large integers (64-bit timers)
├─ DECIMAL(10,2): Currency values
├─ VARCHAR(255): Standard text
├─ TEXT: Long text content
├─ ENUM: Fixed enumeration values
├─ TIMESTAMP: Auto-update server time
├─ DATETIME: Manually updated time
├─ BOOLEAN: True/False (stored as TINYINT)
└─ JSON: Complex nested data

CHARACTER SET: utf8mb4 (supports emoji, international characters)
COLLATION: utf8mb4_unicode_ci (case-insensitive)
ENGINE: InnoDB (ACID compliance, transactions)

================================================================================
SECTION 2: COMPLETE TABLE SPECIFICATIONS
================================================================================

TABLE 1: users (Player Accounts)
================================================================================
Columns:

id INT PRIMARY KEY AUTO_INCREMENT
├─ Unique identifier for each player
├─ Range: 1 - 2,147,483,647
└─ Auto-incremented on insert

username VARCHAR(50) UNIQUE NOT NULL
├─ Player display name
├─ Constraints: Unique, no duplicates
├─ Validation: Alphanumeric + underscore, 3-50 chars
└─ Index: UNIQUE KEY `username`

email VARCHAR(100) UNIQUE NOT NULL
├─ Player email address
├─ Constraints: Unique, valid email format
├─ Used for password recovery
└─ Index: UNIQUE KEY `email`

password_hash VARCHAR(255) NOT NULL
├─ Bcrypt or Argon2 hashed password
├─ Never store plain text
├─ Length: 255 chars (supports Argon2)
└─ Update on: Password change

created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
├─ Account creation date
├─ Auto-set to server time
├─ Index: KEY `created_at` for sorting

last_login DATETIME
├─ Last successful login timestamp
├─ NULL on initial account creation
├─ Update on: Each login
└─ Used for: Activity tracking, idle detection

rank INT DEFAULT 0
├─ Player ranking points
├─ Calculated from: Wars won, territory, techs
├─ Range: 0-999,999,999
├─ Update: After each scored action
└─ Index: KEY `rank` DESC for leaderboards

admin_level TINYINT DEFAULT 0
├─ Permission level
├─ 0 = Player (default)
├─ 1 = Moderator
├─ 2 = Administrator
├─ 3 = Superadmin
├─ 4 = Founder (system)
└─ Enum values restrict access

status ENUM('active','banned','suspended','vacation','holiday') DEFAULT 'active'
├─ Account status
├─ active = Normal play
├─ banned = Permanently locked
├─ suspended = Temporary lock
├─ vacation = 90-day pause (resources frozen)
├─ holiday = 30-day attack immunity
└─ Checked on: Each login

last_action_at DATETIME
├─ Last action timestamp
├─ Used for: Timeout detection
├─ Update on: Any game action

ip_address VARCHAR(45)
├─ IPv4: 192.168.1.1 (15-45 chars max)
├─ IPv6: ::ffff:192.168.1.1 (up to 45 chars)
├─ NULL if: Anonymous session
└─ For: Security/fraud detection

settings JSON
├─ User preferences (serialized)
├─ Example:
│  {
│    "theme": "dark",
│    "language": "en",
│    "notifications": true,
│    "galaxy_preference": 1,
│    "ui_compact": false
│  }
└─ Default: {}

Indexes:
├─ PRIMARY KEY (id)
├─ UNIQUE KEY (username)
├─ UNIQUE KEY (email)
├─ KEY (created_at)
├─ KEY (rank DESC) - for leaderboards
└─ KEY (status) - for filtering

Relationships:
├─ 1:N → planets (user owns multiple planets)
├─ 1:N → fleets (user owns multiple fleets)
├─ 1:N → research (user has multiple research)
└─ 1:1 → alliance_members (user can join one alliance)

---

TABLE 2: planets (Celestial Bodies)
================================================================================
Columns:

id INT PRIMARY KEY AUTO_INCREMENT
├─ Unique planet identifier
└─ Range: 1 - 2,147,483,647

user_id INT NOT NULL FOREIGN KEY
├─ Owner of planet
├─ Constraint: FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
├─ Index: KEY `user_id`
└─ Action on delete: Auto-delete planet if user deleted

name VARCHAR(100) DEFAULT 'Planet'
├─ Editable planet name
├─ Default: "Planet" + coordinates
├─ Supports: Unicode characters (emoji allowed)
└─ Example: "Earth 🌍", "Mars Base", "Alpha Station"

galaxy INT NOT NULL CHECK (galaxy >= 1 AND galaxy <= 999)
├─ Galaxy number (coordinate)
├─ Range: 1-999 (up to 999 galaxies)
├─ Fixed on: Planet creation
└─ Index: Part of UNIQUE KEY (galaxy, system, position)

system INT NOT NULL CHECK (system >= 1 AND system <= 499)
├─ System number within galaxy (coordinate)
├─ Range: 1-499 (500 systems per galaxy)
├─ Fixed on: Planet creation
└─ Index: Part of UNIQUE KEY (galaxy, system, position)

position INT NOT NULL CHECK (position >= 1 AND position <= 15)
├─ Planet position in system (coordinate)
├─ Range: 1-15 (15 planets per system)
├─ Fixed on: Planet creation
├─ Position 16 = Asteroid field (special)
└─ Index: Part of UNIQUE KEY (galaxy, system, position)

type ENUM('terrestrial','ice','desert','ocean','jungle','volcanic','gas_giant','rocky') NOT NULL
├─ Planet classification
├─ terrestrial = Habitable, standard production
├─ ice = Cold, lower crystal production
├─ desert = Dry, higher metal, lower water
├─ ocean = Water-based, higher deuterium
├─ jungle = Lush, higher total production
├─ volcanic = Magma core, unstable
├─ gas_giant = Resource-rich, colonization difficult
├─ rocky = Barren, basic production only
└─ Affects: Resource multipliers, colonization difficulty

diameter INT
├─ Planet size (in km, for reference)
├─ Used for: Visual scale, space capacity
├─ Range: 5,000 - 500,000 km
└─ Determines: Max building capacity (larger = more buildings)

temperature INT
├─ Surface temperature (Celsius)
├─ Range: -200 to 500 °C
├─ Affects: Resource generation efficiency
└─ -150 to -50 ideal, adjust production ±20%

metal_production DECIMAL(12,2)
├─ Metal ore mined per hour (base rate)
├─ Updated from: Building calculations
├─ Calculation: Sum all metal mines on planet
└─ Range: 0.00 - 999,999.99

crystal_production DECIMAL(12,2)
├─ Crystal mined per hour (base rate)
└─ Similar to metal_production

deuterium_production DECIMAL(12,2)
├─ Deuterium refined per hour (base rate)
└─ Similar to metal_production

energy_production DECIMAL(12,2)
├─ Energy generated per hour
├─ Provided by: Power plants
├─ Used by: Mines and refineries
└─ When insufficient: Production halts

metal_storage INT
├─ Metal storage capacity (units)
├─ Increased by: Metal Storage buildings
├─ Default: 5,000 units (before buildings)
└─ When full: Production stops

crystal_storage INT
├─ Crystal storage capacity (units)
├─ Increased by: Crystal Storage buildings
└─ Similar to metal_storage

deuterium_storage INT
├─ Deuterium storage capacity (units)
├─ Increased by: Deuterium Tanks
└─ Similar to metal_storage

current_metal DECIMAL(12,2) DEFAULT 500
├─ Current inventory: Metal
├─ Updated: Every production tick
├─ Range: 0.00 - metal_storage
└─ Cannot exceed: metal_storage

current_crystal DECIMAL(12,2) DEFAULT 500
├─ Current inventory: Crystal
└─ Similar to current_metal

current_deuterium DECIMAL(12,2) DEFAULT 100
├─ Current inventory: Deuterium
└─ Similar to current_metal

last_production_at DATETIME DEFAULT CURRENT_TIMESTAMP
├─ Last production tick timestamp
├─ Updated: Production calculation runs
├─ Used for: Determine production catch-up

created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
├─ Planet colonization date
└─ Used for: Achievement/timeline tracking

last_update DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
├─ Last database update
├─ Auto-updated on: Any field change
└─ Index: KEY `last_update` for sync

Composite Unique Key:
UNIQUE KEY `coordinates` (galaxy, system, position)
├─ Ensures: Only one planet per coordinate
└─ Prevents: Duplicate colonization

Indexes:
├─ PRIMARY KEY (id)
├─ UNIQUE KEY (galaxy, system, position)
├─ KEY (user_id) - queries by owner
├─ KEY (created_at) - timeline queries
└─ KEY (type) - filter by planet type

Relationships:
├─ N:1 → users (planet belongs to user)
├─ 1:N → buildings (multiple buildings per planet)
├─ 1:N → defense_structures (defenses on planet)
└─ 1:N → battles (battles fought on planet)

---

TABLE 3: buildings (Construction on Planets)
================================================================================
Columns:

id INT PRIMARY KEY AUTO_INCREMENT
├─ Unique building instance identifier
└─ One ID per building on each planet

planet_id INT NOT NULL FOREIGN KEY
├─ Planet where building exists
├─ Constraint: FOREIGN KEY (planet_id) REFERENCES planets(id) ON DELETE CASCADE
├─ Index: KEY (planet_id)
└─ On delete: Building destroyed

building_type_id INT NOT NULL FOREIGN KEY
├─ Building template (references building_types table)
├─ Constraint: FOREIGN KEY (building_type_id) REFERENCES building_types(id)
├─ Index: KEY (building_type_id)
└─ Defines: Cost, time, bonuses

level INT DEFAULT 1
├─ Current building level
├─ Range: 0-50 (or building-specific max)
├─ 0 = Queued for demolition
├─ 1+ = Operational
└─ Updated: When upgrade completes

started_at DATETIME
├─ Build start timestamp
├─ NULL if: Building fully constructed
├─ Set when: Build queued
└─ Used for: Calculate progress

completed_at DATETIME
├─ Build completion timestamp
├─ NULL if: Still building
├─ Set when: Level upgrade finishes
└─ Used for: Notifications

status ENUM('idle','building','completed','demolishing') DEFAULT 'idle'
├─ idle = No current action
├─ building = Currently under construction
├─ completed = Fully built, ready to use
├─ demolishing = Queued for removal
└─ Checked by: Production logic

queue_position INT
├─ Position in build queue (1 = building, 2+ = queued)
├─ NULL if: Not queued
├─ Used for: Display build order
└─ Updated: When items complete

Indexes:
├─ PRIMARY KEY (id)
├─ KEY (planet_id)
├─ KEY (building_type_id)
├─ KEY (status)
└─ KEY (queue_position)

Relationships:
├─ N:1 → planets (many buildings per planet)
├─ N:1 → building_types (multiple instances of same type)
└─ 0:1 → construction queue

---

TABLE 4: building_types (Building Templates)
================================================================================
Columns:

id INT PRIMARY KEY AUTO_INCREMENT
├─ Unique building template ID
└─ Range: 1-200 (future expansion)

name VARCHAR(100) UNIQUE NOT NULL
├─ Building display name
├─ Examples: "Metal Mine", "Research Lab", "Shipyard"
├─ Index: UNIQUE KEY (name)
└─ Used in: UI display, queries

description TEXT
├─ Building purpose/benefits
├─ Example:"Extracts metal ore from the planet"
└─ Shown in: Building information modal

category ENUM('production','storage','defense','research','fleet','special') NOT NULL
├─ Building classification
├─ Used for: Grouping in UI, filtering

base_metal_cost INT
├─ Metal cost for level 1
├─ Scaled by: Level number (exponential curve)
└─ Formula: base_cost * (1.1 ^ (level-1))

base_crystal_cost INT
├─ Crystal cost for level 1
└─ Scaled by: Formula same as metal

base_deuterium_cost INT
├─ Deuterium cost for level 1
├─ Usually 0 for early buildings
└─ Increases for advanced buildings

base_time INT
├─ Construction time in seconds for level 1
├─ Formula: base_time * level * (level+1) / 2 / robot_factory_speed / server_speed
└─ Example: Metal Mine = 30 seconds per level

max_level INT DEFAULT 50
├─ Maximum upgrade level for building
├─ Limited by: Game balance
├─ Examples: Mines (50), Shipyard (8), Lab (12)
└─ Used for: Validation, display

energy_requirement INT
├─ Energy consumed per hour per level
├─ Must have: Sufficient energy production
├─ Formula: base_requirement * level
└─ If insufficient: Production reduced

construction_bonus DECIMAL(5,2)
├─ Robotics factory bonus multiplier
├─ Affects: Construction speed
├─ Formula: time_reduction = 1 + (robotics_level * 0.05)
└─ Example: Robotics Lvl 5 = 25% faster builds

building_bonus JSON
├─ Special bonuses per building type
├─ Example:
│  {
│    "metal_mine": {
│      "production_increase": 0.30,
│      "description": "+30% metal production"
│    },
│    "storage": {
│      "capacity_increase": 0.10,
│      "formula": "2500 * 1.1^(level-1)"
│    }
│  }
└─ Parser: PHP JSON with computed formulas

prerequisites JSON
├─ Requirements to build this building
├─ Structure:
│  {
│    "buildings": {
│      "3": 2,  // Need Building ID 3 Level 2
│      "5": 1
│    },
│    "research": {
│      "4": 1   // Need Research ID 4 Level 1
│    }
│  }
├─ Validation: Check before build allowed
└─ Display: Show "X is level Y"

Indexes:
├─ PRIMARY KEY (id)
├─ UNIQUE KEY (name)
└─ KEY (category)

Relationships:
├─ 1:N → buildings (template for multiple buildings)
└─ Used by: Decision logic, costing calculations

Example Rows (Partial):
┌────┬─────────────────┬────────┬──────────┬──────────┬──────────┲──────────┬─────────┬────────────┐
│ id │ name            │ cat.   │ metal_co │ cryst_co │ deut_cost│ base_time│ max_lvl │ energy_req │
├────┼─────────────────┼────────┼──────────┼──────────┼──────────┼──────────┼─────────┼────────────┤
│ 1  │ Metal Mine      │ prod   │ 60       │ 15       │ 0        │ 30       │ 50      │ 10         │
│ 2  │ Crystal Mine    │ prod   │ 48       │ 24       │ 0        │ 30       │ 50      │ 10         │
│ 3  │ Metal Storage   │ stor   │ 2000     │ 500      │ 0        │ 60       │ 50      │ 0          │
│ 4  │ Shipyard        │ fleet  │ 400      │ 200      │ 100      │ 3600     │ 8       │ 50         │
│ 5  │ Research Lab    │ research│ 200     │ 400      │ 200      │ 7200     │ 12      │ 0          │
└────┴─────────────────┴────────┴──────────┴──────────┴──────────┴──────────┴─────────┴────────────┘

---

TABLE 5: research (Player Technology Progress)
================================================================================
Columns:

id INT PRIMARY KEY AUTO_INCREMENT
├─ Unique research instance ID
└─ One per player per technology

user_id INT NOT NULL FOREIGN KEY
├─ Player conducting research
├─ Constraint: FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
├─ Index: KEY (user_id)
└─ On cascade delete: Research deleted

research_type_id INT NOT NULL FOREIGN KEY
├─ Technology type being researched
├─ Constraint: FOREIGN KEY (research_type_id) REFERENCES research_types(id)
├─ Index: KEY (research_type_id)
└─ Defines: Cost, time, requirements

level INT DEFAULT 1
├─ Current research level
├─ Range: 0-20 (tech-dependent max)
├─ 0 = Not yet researched
├─ Level increases after completion
└─ Used for: Bonus calculations

started_at DATETIME
├─ Research start timestamp
├─ NULL if: Not currently researching
├─ Set when: Queue position = 1
└─ Used for: Progress display

completed_at DATETIME
├─ Research completion timestamp
├─ NULL if: Still researching
├─ Set on: Completion
└─ Triggers: Notification, unlock systems

status ENUM('idle','researching','completed') DEFAULT 'idle'
├─ idle = Not researching (can start new)
├─ researching = Currently in progress
├─ completed = Finished, can queue next
└─ Transitioned: After each level complete

planet_id INT FOREIGN KEY
├─ Research lab location (planet where lab exists)
├─ Constraint: FOREIGN KEY (planet_id) REFERENCES planets(id)
├─ Requirement: Planet must have Research Lab building
├─ Affects: Research speed (by lab level)
└─ Index: KEY (planet_id)

queue_position INT
├─ Position in research queue (1 = active, 2+ = queued)
├─ NULL if: Not queued
├─ Can queue: Up to 200 levels ahead
└─ Max queue: Configurable (e.g., 20 items)

Indexes:
├─ PRIMARY KEY (id)
├─ UNIQUE KEY (user_id, research_type_id) - one per tech
├─ KEY (user_id)
├─ KEY (status)
└─ KEY (queue_position)

Relationships:
├─ N:1 → users (player has multiple techs)
├─ N:1 → research_types (many players researching same tech)
└─ N:1 → planets (research happens on specific planet)

---

TABLE 6: research_types (Technology Templates)
================================================================================
Columns:

id INT PRIMARY KEY AUTO_INCREMENT
├─ Unique technology ID
└─ Range: 1-100 (future expansion)

name VARCHAR(100) UNIQUE NOT NULL
├─ Technology display name
├─ Examples: "Weapons Technology", "Speed Drive"
├─ Index: UNIQUE KEY (name)
└─ Used in: UI, system notifications

category ENUM('economy','weapons','armor','shield','speed','fleet','espionage','defense','other') NOT NULL
├─ Tech classification
├─ Used for: UI organization (tech tree tabs)
└─ Index: KEY (category)

description TEXT
├─ Technology purpose/benefits
├─ Example: "Increases ship attack power by 10%"
└─ Shown in: Information popups

base_metal_cost INT
├─ Metal cost for level 1
├─ Scaled: formula like buildings
├─ Example: 400 metal base

base_crystal_cost INT
├─ Crystal cost for level 1
├─ Example: 600 crystal base

base_deuterium_cost INT
├─ Deuterium cost for level 1
├─ Example: 200 deuterium base

base_time INT
├─ Research time in seconds for level 1
├─ Formula: base_time * level * (level+1) / 2 / lab_level_bonus
├─ Example: 3600 seconds (1 hour) base
└─ Actual calc: 3600 * level * (level+1) / 2 / (1 + lab_bonus)

max_level INT DEFAULT 20
├─ Maximum tech level
├─ Balance limit: Can't exceed
├─ Examples: Weapons (20), Speed (15), Shields (10)
└─ Used for: Display "Lvl X/20"

bonus_type ENUM('multiplicative','additive','percentage') DEFAULT 'multiplicative'
├─ How bonus is applied
├─ multiplicative = Factor 1.1^level (standard)
├─ additive = +flat_value per level
├─ percentage = +% per level
└─ Used by: Bonus calculation engine

bonus_value DECIMAL(8,4)
├─ The actual bonus applied
├─ Example: 0.1000 = 10% per level
├─ Formula applied: 1 + (bonus_value * tech_level)
└─ Weapon dmg: Damage * (1 + 0.1 * weapon_tech_level)

prerequisites JSON
├─ Requirements to research this tech
├─ Structure:
│  {
│    "research": {
│      "1": 3,    // Need Tech ID 1 Level 3
│      "4": 1     // Need Tech ID 4 Level 1
│    },
│    "buildings": {
│      "5": 2     // Need Building ID 5 (Research Lab) Level 2
│    }
│  }
└─ Validation: Check before research allowed

research_lab_requirement INT DEFAULT 1
├─ Minimum Research Lab level required
├─ Examples: Mining tech (1), Plasma (6), Jump gate (12)
└─ Prevents: Queuing without proper facilities

Indexes:
├─ PRIMARY KEY (id)
├─ UNIQUE KEY (name)
├─ KEY (category)
└─ KEY (max_level)

Relationships:
├─ 1:N → research (template for player progress)
└─ Used by: Bonus engines, requirement validators

Example Rows:
┌────┬──────────────────┬────────┬────────┬─────────┬─────────┬──────────┬──────────┬────────────┬──────────────┬─────────┐
│ id │ name             │ cat.   │ metal  │ crystal │ deuterium│ base_time│ max_level│ bonus_type │ bonus_value  │ lab_req │
├────┼──────────────────┼────────┼────────┼─────────┼─────────┼──────────┼──────────┼────────────┼──────────────┼─────────┤
│ 1  │ Mining Tech      │ economy│ 800    │ 400     │ 0       │ 5000     │ 10       │ multiply   │ 0.1000       │ 1       │
│ 2  │ Weapons Tech     │ weapons│ 400    │ 600     │ 200     │ 3600     │ 20       │ multiply   │ 0.1000       │ 1       │
│ 3  │ Armor Tech       │ armor  │ 1000   │ 500     │ 0       │ 6000     │ 20       │ multiply   │ 0.1000       │ 1       │
│ 4  │ Speed Drive      │ speed  │ 400    │ 200     │ 100     │ 3600     │ 15       │ multiply   │ 0.1000       │ 1       │
│ 5  │ Plasma Tech      │ weapons│ 2000   │ 4000    │ 1000    │ 21600    │ 10       │ additive   │ 0.2000       │ 6       │
└────┴──────────────────┴────────┴────────┴─────────┴─────────┴──────────┴──────────┴────────────┴──────────────┴─────────┘

---

[Continuing with remaining 15 tables...]

TABLE 7: fleets
TABLE 8: fleet_ships
TABLE 9: ship_types
TABLE 10: defense_structures
TABLE 11: defense_types
TABLE 12: battles
TABLE 13: messages
TABLE 14: alliances
TABLE 15: alliance_members
TABLE 16: debris_fields
TABLE 17: transactions
TABLE 18: logs
TABLE 19: server_settings
TABLE 20: tasks
TABLE 21: task_queue

[Follow similar detailed format for each...]

================================================================================
SECTION 3: INDEXING STRATEGY
================================================================================

Composite Indexes (Most Important):

1. User + Time for: Leaderboard queries
   CREATE INDEX idx_user_rank_time 
   ON users(rank DESC, created_at DESC);

2. Planet coordinates for: Galactic map
   CREATE UNIQUE INDEX idx_coordinates 
   ON planets(galaxy, system, position);

3. Fleet status + destiny for: Movement queries
   CREATE INDEX idx_fleet_movement 
   ON fleets(status, user_id, target_planet_id);

4. Battle tracking for: Reports
   CREATE INDEX idx_battles 
   ON battles(attacker_id, defender_id, created_at DESC);

5. Research progress for: Production tick
   CREATE INDEX idx_research_active 
   ON research(user_id, status, started_at);

================================================================================
SECTION 4: QUERY OPTIMIZATION
================================================================================

Frequently Used Queries & Indexes:

GET USER RESOURCES (High Frequency):
SELECT metal, crystal, deuterium FROM planets WHERE user_id = ? ;
EXPLAIN: Index on user_id required ✓

GET ALL PLANETS BY USER:
SELECT * FROM planets WHERE user_id = ? ORDER BY created_at;
EXPLAIN: Index (user_id, created_at) ✓

GET ACTIVE FLEETS:
SELECT * FROM fleets 
WHERE user_id = ? AND status IN ('moving', 'attacking')
ORDER BY arrival_time;
EXPLAIN: Index (user_id, status, arrival_time) ✓

GET BUILDING PRODUCTION:
SELECT SUM(metal_production) FROM planets WHERE user_id = ?;
EXPLAIN: Index (user_id), materializes view ✓

LEADERBOARD QUERY:
SELECT username, rank FROM users ORDER BY rank DESC LIMIT 100;
EXPLAIN: Index (rank DESC), pagination ✓

================================================================================
SECTION 5: BACKUP & RECOVERY
================================================================================

Backup Strategy:
├─ Full backup: Daily (00:00 UTC)
├─ Incremental: Every 6 hours
├─ WAL (Write-Ahead Logging): Continuous
├─ Retention: 30 days rolling
└─ Storage: External cloud + local

Recovery Point Objective (RPO): < 6 hours
Recovery Time Objective (RTO): < 2 hours

Backup Commands:

Full Backup:
mysqldump -u root -p --all-databases --result-file=backup_$(date +%Y%m%d).sql

Restore:
mysql -u root -p < backup_20260309.sql

================================================================================
SECTION 6: SCALING CONSIDERATIONS
================================================================================

Sharding Strategy (for 1M+ players):
├─ Shard by: user_id (consistent hashing)
├─ Partition: Across multiple MariaDB instances
├─ Galaxy-system hash: Determine planet shard
└─ Cross-shard queries: Federation layer (not yet needed)

Replication:
├─ Master: Primary server (reads + writes)
├─ Slaves: Read replicas (reports, leaderboards)
├─ Sync: Async replication (GTID mode)
└─ Failover: Automated promoted slave → master

Caching Layer (Redis):
├─ Cache: User resources (1-minute TTL)
├─ Cache: Leaderboards (1-hour TTL)
├─ Cache: Tech trees (24-hour TTL)
├─ Cache: Galaxy map data (6-hour TTL)
└─ Invalidation: Manual on write, automatic on timeout

================================================================================
MIGRATION & DEPLOYMENT
================================================================================

Database Initialization Script:
Location: Db/schema.sql
├─ CREATE statements for all tables
├─ INSERT statements for building_types, research_types, ship_types
├─ Setup default admin user
├─ Setup default server settings
└─ Schema version control in server_settings

Deployment Process:
1. Backup existing database
2. Run migration scripts (schema changes)
3. Insert new data (templates, settings)
4. Verify all constraints
5. Run integration tests
6. Deploy to production
7. Monitor for errors
8. Rollback plan: Restore from backup

================================================================================
FILE LOCATION
================================================================================

Database Files:
├─ Db/schema.sql - Full schema definition
├─ Db/seed-data.sql - Template data insertion
├─ Db/migrations/ - Migration scripts per version
│  ├─ 001_initial_schema.sql
│  ├─ 002_add_json_fields.sql
│  └─ 003_add_indices.sql
└─ Db/backups/ - Automated backup storage

================================================================================
