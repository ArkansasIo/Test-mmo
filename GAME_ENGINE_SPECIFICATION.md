================================================================================
    SCI-FI CONQUEST: AWAKENING - COMPLETE GAME ENGINE SPECIFICATION
    OGame 0.84 Layout & Design Standard
    Version 1.0 - Production Ready Specification
================================================================================

PROJECT OVERVIEW
================================================================================
A browser-based space empire building game inspired by OGame 0.84 featuring:
- Real-time empire management
- Solar system exploration and conquest
- Technology research trees
- Military combat system
- Economic trading
- Alliance system
- Dynamic turn-based calculations

TECHNOLOGY STACK
================================================================================
Backend:    PHP 8.0+ with PDO/MySQLi abstraction
Database:   MariaDB 12.1 with 21 verified tables
Frontend:   HTML5, CSS3, JavaScript (Vanilla)
Design:     OGame 0.84 sci-fi aesthetic with dark theme
Server:     Built-in PHP server (development) / Apache/Nginx (production)

================================================================================
SECTION 1: COMPLETE PAGE STRUCTURE & ARCHITECTURE
================================================================================

PRIMARY GAME PAGES (Currently Implemented)
-------------------------------------------

1. EMPIRE PAGE (/Index/pages/empire.php)
   Purpose: Player's main dashboard showing overview of all planets/stations
   Current Status: ✅ IMPLEMENTED
   Features:
   - Planet list with resource production (metal, crystal, deuterium, energy)
   - Building queue display
   - Current research display
   - Quick links to major functions
   - Resource totals across all planets
   
   Future Enhancements:
   - Real-time resource ticker
   - Quick-build shortcuts
   - Notifications panel integration
   - Empire stats summary (total troops, fleet power, etc.)

2. SHIPYARD PAGE (/Index/pages/shipyard.php)
   Purpose: Build military ships and defense structures
   Current Status: ✅ IMPLEMENTED
   Features:
   - Ship/defense building queue
   - Available ship types display
   - Construction time calculator
   - Resource cost preview
   
   Sub-sections Needed:
   - Military Fleet (destroyers, cruisers, battleships, etc.)
   - Defense Structures (laser cannons, rocket launchers, shields)
   - Construction queues
   - Build history/logs
   - Analytics (fleet composition, upgrade paths)

3. RESEARCH PAGE (/Index/pages/research.php)
   Purpose: Technology research tree for empire advancement
   Current Status: ✅ IMPLEMENTED
   Features:
   - Available research technologies
   - Technology requirements/dependencies
   - Research queue management
   
   Sub-sections Needed:
   - Weapons research (plasma tech, armor, shields)
   - Fleet technology (speed, capacity, range)
   - Economy research (mining efficiency, resource storage)
   - Espionage technology
   - Defense technology
   - Research tree visualizer

4. FLEET PAGE (/Index/pages/fleet.php)
   Purpose: Fleet management and deployment
   Current Status: ✅ IMPLEMENTED (Basic)
   Features:
   - Display player's ships
   - Fleet composition
   
   Sub-sections Needed:
   - Fleet list with movement tracking
   - Active missions (attack, transport, spy)
   - Fleet deployment interface
   - Expedition targets
   - Battle simulator
   - Campaign management

5. GALAXY PAGE (/Index/pages/galaxy.php)
   Purpose: Explore and interact with galactic map
   Current Status: ✅ IMPLEMENTED (Basic)
   Features:
   - Galaxy/solar system navigation
   - Planet scanning
   - Player location display
   
   Sub-sections Needed:
   - System coordinates (galaxy:system:planet)
   - Debris fields display
   - Alliance territories
   - Resource planets
   - Hostile zones
   - Travel routes optimizer
   - Colonization targets

6. ALLIANCE PAGE (/Index/pages/alliance.php)
   Purpose: Player alliance management and diplomacy
   Current Status: ✅ IMPLEMENTED (Basic)
   Features:
   - Alliance info display
   - member list view
   
   Sub-sections Needed:
   - Alliance creation/joining
   - Diplomacy (relations: peace, allies, enemies)
   - Alliance treasury/resources
   - Alliance technologies (members contribute)
   - War declarations
   - Alliance diplomacy matrix
   - Member permissions/roles

7. RESEARCH TREE PAGE (/Index/research_tree.php)
   Purpose: Visual research technology dependencies
   Current Status: Reference file (needs implementation)
   Features:
   - Technology tree visualization
   - Prerequisites display
   - Research cost calculator
   
   Interactive Elements:
   - Click-to-research functionality
   - Dependency chain display
   - Time-to-complete estimates
   - Cost breakdown

8. MESSAGES PAGE (/Index/pages/messages.php)
   Purpose: In-game messaging and communication
   Current Status: ✅ IMPLEMENTED (Basic)
   Features:
   - Message inbox/outbox
   - Player-to-player messaging
   
   Sub-sections Needed:
   - Espionage reports (spy data)
   - Battle reports (attack results)
   - Mining reports (resource surveys)
   - System notifications
   - Alliance announcements
   - Message categorization/filtering
   - Message archiving

9. RANKINGS PAGE (/Index/pages/rankings.php)
   Purpose: Player rankings and statistics
   Current Status: ✅ IMPLEMENTED
   Features:
   - Player leaderboards
   - Empire statistics
   
   Sub-sections Needed:
   - Overall ranking (by empire strength)
   - Military ranking (by fleet power)
   - Economy ranking (by total resources)
   - Tech level ranking
   - Alliance rankings
   - Personal stats (total destroyed, losses, wins)
   - Historical progression charts

10. MARKETPLACE PAGE (/Index/pages/marketplace.php)
    Purpose: Trading resources and items
    Current Status: ✅ IMPLEMENTED (Basic)
    Features:
    - Buy/sell interface (unimplemented backend)
    
    Sub-sections Needed:
    - Resource trading
    - Ship trading
    - Alliance marketplace (members only)
    - Merchant fleet management
    - Trade contracts/agreements
    - Price fluctuation display
    - Historical price charts
    - Trade route optimization

11. NOTIFICATIONS PAGE (/Index/pages/notifications.php)
    Purpose: Real-time notifications and alerts
    Current Status: ✅ IMPLEMENTED (Basic)
    
    Notification Types Needed:
    - Construction complete
    - Research complete
    - Fleet arriving/returning
    - Under attack
    - Resources low alerts
    - Alliance messages
    - Diplomacy updates
    - Task completion

12. TASKS PAGE (/Index/pages/tasks.php)
    Purpose: Player missions and achievement tracking
    Current Status: ✅ IMPLEMENTED (Basic)
    Features:
    - Tutorial tasks
    - Achievement tracking
    
    Sub-sections Needed:
    - Daily missions (complete 3 researches = bonus)
    - Campaign missions (storyline)
    - Achievement badges
    - Milestone rewards
    - Task progression tracking

13. ADMIN PAGE (/Index/pages/admin.php)
    Purpose: Administrative and server control
    Current Status: ✅ IMPLEMENTED (Basic)
    Features: Basic admin functions
    
    Sub-sections Needed:
    - User management
    - Ban/unban system
    - Game pause/resume
    - Announcement system
    - Event logs
    - Server statistics
    - Database maintenance

14. REGISTER PAGE (/Index/pages/register.php)
    Purpose: New player account creation
    Current Status: ✅ IMPLEMENTED
    Features:
    - Account creation form
    - Initial empire setup
    - Starter resources allocation
    
    Enhancements Needed:
    - Email verification
    - CAPTCHA protection
    - Terms acceptance
    - Universe selection
    - Starting bonus selection

SECONDARY/UTILITY PAGES (Not yet implemented)
----------------------------------------------

15. PLANET DETAILS PAGE /pages/planet-details.php
    Purpose: Detailed planet information and management
    Sub-sections:
    - Buildings on planet
    - Current production
    - Defense structures
    - Population stats
    - Resource storage/capacity
    - Planet type/characteristics
    - Rename planet
    - Colonization options

16. BUILDINGS PAGE /pages/buildings.php
    Purpose: Construct economic buildings for resource production
    Features:
    - Metal mine (upgradeable)
    - Crystal mine (upgradeable)
    - Deuterium refinery (upgradeable)
    - Power plants (various types)
    - Storage facilities (upgrading increases capacity)
    - Lunar base (research on moon)
    - Building queue
    - Requirement validator (energy, resources, etc.)
    
    OGame 0.84 Buildings:
    ├─ Economy Buildings
    │  ├─ Metal Mine (level 1-50)
    │  ├─ Crystal Mine (level 1-50)
    │  ├─ Deuterium Refinery (level 1-50)
    │  ├─ Solar Plant (level 1-50)
    │  └─ Fusion Reactor (level 1-50)
    ├─ Storage Buildings
    │  ├─ Metal Storage (level 1-50)
    │  ├─ Crystal Storage (level 1-50)
    │  └─ Deuterium Tank (level 1-50)
    └─ Special Buildings
       ├─ Robotics Factory (for faster builds)
       ├─ Shipyard (for building ships)
       ├─ Research Lab (for faster research)
       ├─ Alliance Depot (regional storage)
       └─ Lunar Base

17. ESPIONAGE PAGE /pages/espionage.php
    Purpose: Spy missions on other players
    Features:
    - Spy probe dispatch
    - Target selection
    - Intelligence reports
    - Counterintelligence defense level
    - Spy technology requirements
    
    Report Types:
    - Fleet composition (ships/count)
    - Defense structures
    - Resource status (hidden in higher levels)
    - Building construction
    - Research activity
    - Defenses deployment

18. DEFENSE REPORT PAGE /pages/defense-report.php
    Purpose: View incoming attacks and defense results
    Features:
    - Incoming attack notifications
    - Defense fleet assignments
    - Battle outcome/casualties
    - Debris field creation
    - Salvage/repair options

19. BATTLE SIMULATOR PAGE /pages/battle-simulator.php
    Purpose: Predict combat outcomes before real battles
    Features:
    - Attacker fleet selection
    - Defender fleet selection
    - Predicted outcome calculation
    - Casualty estimates
    - Debris field calculation
    - Strategic analysis

20. SETTINGS PAGE /pages/settings.php
    Purpose: Player account and gameplay settings
    Sub-pages:
    - Account Settings
      ├─ Change password
      ├─ Update email
      ├─ Two-factor authentication
      ├─ Account deletion
      └─ Session management
    - Game Settings
      ├─ Galaxy/system preference
      ├─ Holiday mode (attack immunity)
      ├─ Vacation mode (time-locked)
      ├─ Notification preferences
      ├─ UI theme selection (light/dark)
      └─ Language selection
    - Privacy Settings
      ├─ Trading permission
      ├─ Alliance invitation permissions
      ├─ Message filtering
      └─ Friend list management

21. HELP PAGE /pages/help.php
    Purpose: Tutorials and game guides
    Sub-sections:
    - Game universe overview
    - Getting started tutorial
    - Building guide
    - Technology tree guide
    - Fleet composition guide
    - Combat mechanics explanation
    - Trading guide
    - Alliance guide
    - FAQ
    - Key shortcuts

22. ACCOUNT PAGE /pages/account.php
    Purpose: Account summary and statistics
    Features:
    - Account overview
    - Total playtime
    - Empire age
    - Personal achievements
    - Statistics dashboard
    - Character name/avatar
    - Ranking position

================================================================================
SECTION 2: GAME ENGINE CORE SYSTEMS
================================================================================

A. PRODUCTION & ECONOMY SYSTEM
================================================================================

Buildings & Resource Production:

Metal Mine (Economy Building)
├─ Level 1-50 (upgradeable)
├─ Base production: 30 metal/hour
├─ Per level: +30 metal/hour (exponential curve)
├─ Energy requirement: Level * 10
├─ Construction cost: Metal=60, Crystal=15, Deuterium=0
├─ Construction time (seconds): 417 * Level * (Level+1) / 2 / speed
└─ Requirements: None for level 1

Crystal Mine (Economy Building)
├─ Level 1-50
├─ Base production: 20 crystal/hour
├─ Per level: +20 crystal/hour (exponential)
├─ Energy requirement: Level * 10
├─ Construction cost: Metal=48, Crystal=24, Deuterium=0
└─ Prerequisites: None

Deuterium Refinery (Economy Building)
├─ Level 1-40
├─ Base production: 10 deuterium/hour
├─ Per level: +20 deuterium/hour (exponential)
├─ Energy requirement: Level * 20
├─ Construction cost: Metal=225, Crystal=75, Deuterium=0
└─ Prerequisites: None

Power Plants:

Solar Plant (Power Generation)
├─ Level 1-50
├─ Base production: 20 energy/hour
├─ Per level: +20 energy/hour
├─ No fuel consumption
└─ Cost: Metal=75, Crystal=30, Deuterium=0

Fusion Reactor (Power Generation)
├─ Level 1-50
├─ Base production: 30 energy/hour
├─ Deuterium consumption: 10 deuterium/hour
├─ Benefits: Higher energy density, compact
└─ Cost: Metal=900, Crystal=360, Deuterium=0
└─ Prerequisites: Research "Fusion Technology" level 3

Storage Buildings:

Metal Storage
├─ Level 1-50
├─ Capacity: 2500 * 1.1^(Level-1) units
└─ Cost: Metal=2000, Crystal=500, Deuterium=0

Crystal Storage
├─ Level 1-50
├─ Capacity: 2500 * 1.1^(Level-1) units
└─ Cost: Metal=1500, Crystal=1000, Deuterium=0

Deuterium Tank
├─ Level 1-50
├─ Capacity: 2500 * 1.1^(Level-1) units
└─ Cost: Metal=500, Crystal=1000, Deuterium=500

Production Multipliers:
- Robotics Factory level: +Robotics% speed building
- Planetary Production Bonus: +Player_Tech_Level^0.5%
- Alliance bonus: +3% per alliance member research
- Server speed factor: Configurable (0.5x, 1x, 2x, 4x, etc.)

B. TECHNOLOGY RESEARCH SYSTEM
================================================================================

Research Tree Structure (OGame 0.84 Standard):

ECONOMY TECHNOLOGIES:
├─ Mining Technology (level 1-10)
│  ├─ Cost: Metal=800, Crystal=400, Deuterium=0
│  ├─ Time: 5000 seconds base
│  ├─ Lab Requirement: Level 1
│  └─ Effect: +10% mining efficiency per level
├─ Resource Refining (level 1-10)
│  ├─ Prerequisites: Mining Tech level 1
│  ├─ Cost: Metal=1200, Crystal=600, Deuterium=0
│  └─ Effect: +10% deuterium refinery efficiency
└─ Storage Optimization (level 1-10)
   ├─ Prerequisites: Mining Tech level 3
   ├─ Cost: Metal=1000, Crystal=500, Deuterium=0
   └─ Effect: +10% storage capacity increase

WARFARE TECHNOLOGIES:
├─ Weapons Technology (level 1-20)
│  ├─ Cost: Metal=400, Crystal=600, Deuterium=200
│  ├─ Time: 3600 seconds base
│  ├─ Lab Requirement: Level 1
│  └─ Effect: +10% ship weapon damage per level
├─ Armor Technology (level 1-20)
│  ├─ Prerequisites: Weapons Tech level 2
│  ├─ Cost: Metal=1000, Crystal=500, Deuterium=0
│  └─ Effect: +10% ship hull strength per level
├─ Shield Technology (level 1-10)
│  ├─ Prerequisites: Weapons Tech level 6, Armor Tech level 3
│  ├─ Cost: Metal=200, Crystal=1000, Deuterium=200
│  └─ Effect: Unlock shield systems, +5% per level
└─ Plasma Technology (level 1-10)
   ├─ Prerequisites: Weapons Tech level 5, Energy Tech level 3
   ├─ Cost: Metal=2000, Crystal=4000, Deuterium=1000
   └─ Effect: Plasma weapons unlock, +20% damage per level

FLEET TECHNOLOGIES:
├─ Speed Technology (level 1-15)
│  ├─ Cost: Metal=400, Crystal=200, Deuterium=100
│  ├─ Lab Requirement: Level 1
│  └─ Effect: +10% fleet speed per level
├─ Jump Gate (level 1-16)
│  ├─ Prerequisites: Energy Tech level 12, Hyperspace Tech level 7
│  ├─ Cost: Metal=2000, Crystal=4000, Deuterium=1000
│  └─ Effect: Instant fleet travel between gates
└─ Hyperspace Technology (level 1-10)
   ├─ Prerequisites: Speed Tech level 5
   ├─ Cost: Metal=1000, Crystal=2000, Deuterium=500
   └─ Effect: +5 systems travel range per level

ECONOMY EXPANSION:
├─ Intergalactic Research (level 1-10)
│  ├─ Prerequisites: Fleet Tech level 5
│  ├─ Cost: Metal=40000, Crystal=120000, Deuterium=20000
│  └─ Effect: Research available across galaxies
└─ Neural Network (level 1-20)
   ├─ Prerequisites: Computers level 8
   ├─ Cost: Metal=240000, Crystal=120000, Deuterium=20000
   └─ Effect: +10% all production per level, +1 fleet slot per level

C. MILITARY SYSTEM - FLEET & DEFENSE
================================================================================

SHIP TYPES & STATISTICS:

Small Cargo Ship
├─ Cost: Metal=2000, Crystal=2000, Deuterium=500
├─ Attack: 5 (base military power)
├─ Defense: 10
├─ Hull: 400 (destroyed after taking 400 damage)
├─ Speed: 5000 (relative speed units)
├─ Capacity: 5000 units (large)
├─ Prerequisites: Shipyard (level 1)
└─ Purpose: Primary resource transportation

Large Cargo Ship
├─ Cost: Metal=6000, Crystal=6000, Deuterium=1000
├─ Attack: 5
├─ Defense: 25
├─ Hull: 1200
├─ Speed: 7500
├─ Capacity: 25000 units
├─ Prerequisites: Shipyard level 2, Combustion Engine level 2
└─ Purpose: Main trader

Light Fighter
├─ Cost: Metal=3000, Crystal=1000, Deuterium=0
├─ Attack: 50 (good offensive)
├─ Defense: 1
├─ Hull: 400
├─ Speed: 12500 (fast)
├─ Capacity: 50
├─ Prerequisites: Shipyard level 3
└─ Purpose: Fighter classification, weak defense

Heavy Fighter
├─ Cost: Metal=6000, Crystal=4000, Deuterium=0
├─ Attack: 150
├─ Defense: 25
├─ Hull: 1000
├─ Speed: 10000
├─ Capacity: 100
├─ Prerequisites: Shipyard level 3, Armor Tech level 2
└─ Purpose: Mid-tier combat vessel

Cruiser
├─ Cost: Metal=20000, Crystal=7000, Deuterium=2000
├─ Attack: 400 (heavy)
├─ Defense: 50
├─ Hull: 2700
├─ Speed: 15000
├─ Capacity: 800
├─ Prerequisites: Shipyard level 5, Armor Tech level 4
└─ Purpose: Capital ship for major battles

Battleship
├─ Cost: Metal=45000, Crystal=15000, Deuterium=0
├─ Attack: 200 (main battery)
├─ Defense: 200 (heavily armored)
├─ Hull: 6000 (very durable)
├─ Speed: 10000 (slow but powerful)
├─ Capacity: 1500
├─ Prerequisites: Shipyard level 8, Armor Tech level 8
└─ Purpose: Ultimate capital ship - rare/expensive

Battlecruiser
├─ Cost: Metal=30000, Crystal=40000, Deuterium=15000
├─ Attack: 700 (attack)
├─ Defense: 50 (speed-based evasion)
├─ Hull: 700
├─ Speed: 40000 (extremely fast)
├─ Capacity: 750
├─ Prerequisites: Shipyard level 8, Hyperspace Tech level 4, Armor Tech level 5
└─ Purpose: Hybrid fast-attack platform

Spy Probe
├─ Cost: Metal=0, Crystal=1000, Deuterium=0
├─ Attack: 0 (no combat ability)
├─ Defense: 0 (fragile)
├─ Hull: 1
├─ Speed: 100000 (fastest ship)
├─ Capacity: 0
├─ Prerequisites: Shipyard level 3, Espionage Tech level 1
└─ Purpose: Gather intelligence on targets

Interceptor
├─ Cost: Metal=0, Crystal=0, Deuterium=1000
├─ Attack: 1
├─ Defense: 1
├─ Hull: 1
├─ Speed: Instant (can intercept any fleet)
├─ Capacity: 0
├─ Prerequisites: Shipyard level 4, Jump Gate Tech level 1
└─ Purpose: Intercept and interdict fleets at jump gates

DEFENSE STRUCTURES:

Rocket Launcher
├─ Cost per unit: Metal=2000, Crystal=0, Deuterium=0
├─ Attack: 80 structure power
├─ Prerequisites: Shipyard level 1
└─ Purpose: Base anti-ship defense

Light Laser Cannon
├─ Cost per unit: Metal=1500, Crystal=500, Deuterium=0
├─ Attack: 30
├─ Prerequisites: Shipyard level 2, Laser Tech level 2
└─ Power consumption: 150 energy

Heavy Laser Cannon
├─ Cost per unit: Metal=6000, Crystal=3000, Deuterium=0
├─ Attack: 150
├─ Prerequisites: Shipyard level 4, Laser Tech level 6
└─ Power consumption: 500 energy

Plasma Cannon
├─ Cost per unit: Metal=50000, Crystal=50000, Deuterium=30000
├─ Attack: 3000 (devastating)
├─ Prerequisites: Shipyard level 6, Plasma Tech level 7
└─ Power consumption: 3000 energy (rare)

Shield Dome (Area)
├─ Cost per unit: Metal=10000, Crystal=10000, Deuterium=0
├─ Protection: 1000 (shields fleet/structures)
├─ Prerequisites: Shield Tech level 1
└─ Note: One per planet max, protects entire planet

Large Shield Dome
├─ Cost per unit: Metal=50000, Crystal=50000, Deuterium=0
├─ Protection: 10000
├─ Prerequisites: Shield Tech level 6
└─ Note: Enhanced shield coverage

D. COMBAT SYSTEM
================================================================================

Combat Calculation System:

1. Round-based battles
   - Each round: All combatants attack each other simultaneously
   - Round duration: Instant or time-based simulation
   - Max 10 rounds before tie/stalemate

2. Damage Calculation:
   Damage = (Attacker_Weapon * Attacker_Tech_Level * Random(0.8-1.2)) 
            - Defender_Armor_Protection - Defender_Shield_Protection

3. Target Selection:
   - Attackers prioritize highest threat targets (highest attack value)
   - Defenders prioritize attackers
   - Collateral damage possible with area weapons

4. Casualty Resolution:
   - Ships destroyed when hull reaches 0
   - Crew saved (1% chance per armor level)
   - Debris field created (33% of destroyed ship metal value)

5. Battle Outcome:
   - Attacker victory: May plunder resources
   - Defender victory: Keep all resources, gains honor points
   - Draw: Debris field created for both

E. TASK & TURN SYSTEM
================================================================================

Dynamic Turn Calculation:

Turn Cycle: Every 6 hours (real time)
├─ 06:00 UTC - Turn 1 executed
├─ 12:00 UTC - Turn 2 executed
├─ 18:00 UTC - Turn 3 executed
└─ 00:00 UTC - Turn 4 executed

Actions processed per turn:
- Fleet movements (calculate arrival)
- Building construction (reduce queue timer)
- Research progress (advance current tech)
- Production calculation (add resources)
- Defense retaliation
- Debris collection
- Resource transactions

Task Queue System:

Task types:
├─ Construction (buildings)
├─ Research (technologies)
├─ Production (resource generation)
├─ Movement (fleet travel)
├─ Combat (battle simulation)
├─ Trade (marketplace)
└─ Custom (scripted events)

Task generator processes:
1. Fetch all active tasks for current turn
2. Process timestamps (check if task ready)
3. Execute task logic
4. Update database
5. Generate notifications
6. Log completion

F. EXPEDITION & EXPLORATION SYSTEM
================================================================================

Expedition Types:

Exploration Mission
├─ Duration: Variable by distance
├─ Resource cost: Fuel (deuterium)
├─ Rewards: Unknown planets, debris fields, rare resources
├─ Risk: Fleet may encounter hostile fleets
└─ Returns: After time limit with exploration report

asteroid Field Expedition
├─ Duration: 1-4 hours
├─ Rewards: Deuterium, Crystal, random items
├─ Risk: Field may be depleted or contaminated
└─ Returns: Salvage resources or intelligence

Debris Field Collection
├─ Duration: Immediate to 1 hour
├─ Action: Send cargo fleet to collect debris
├─ Rewards: Metal + Crystal (from destroyed ships)
├─ Risk: Other players may also target field
└─ Returns: With collected salvage

Colonization Expedition
├─ Duration: 1 day preparation + 6 hours travel
├─ Requirements: Colonist ships, infrastructure
├─ Rewards: New planet under player control
├─ Risk: Hostile territory, colony failure
└─ Returns: New planet with starter buildings

================================================================================
SECTION 3: USER INTERFACE (UI) SYSTEMS
================================================================================

A. LAYOUT STRUCTURE (OGame 0.84 Standard)
================================================================================

Main Layout:

┌─────────────────────────────────────────────────────────────────────┐
│ TOP NAVBAR (Sticky)                                                 │
│ [Logo] [Game Name] [Menus] [Resources Display] [Player] [Settings]  │
├─────────────────────────┬───────────────────────────────────────────┤
│                         │                                           │
│   LEFT SIDEBAR (280px)  │         MAIN CONTENT AREA               │
│   - Overview            │         (Scrollable)                     │
│   - Buildings           │         [Page-specific content]          │
│   - Defense             │         [Dynamic based on page]          │
│   - Alliance            │         [Responsive layout]              │
│   - Marketplace         │                                           │
│   - Messages            │                                           │
│   - Rankings            │                                           │
│   - Settings            │                                           │
│   - Help                │                                           │
│                         │                                           │
│                         │                                           │
│                         │                                           │
│                         │                                           │
│                         │                                           │
│                         │                                           │
└─────────────────────────┴───────────────────────────────────────────┘

B. COLOR SCHEME & VISUAL DESIGN (Sci-Fi Dark Theme)
================================================================================

Primary Colors:
├─ Background: #0a0a1a (Very dark blue-black)
├─ Accent: #4a9eff (Cyan blue)
├─ Light Accent: #7ab8ff (Light cyan)
├─ Text: #ffffff (Pure white)
├─ Secondary Text: #aaaaaa (Gray)
└─ Borders: #4a9eff with opacity

Structure Colors:
├─ Active/Completed: #7ab8ff (Bright cyan)
├─ In Progress: #4a9eff (Cyan)
├─ Disabled/Unavailable: #666666 (Dark gray)
├─ Error/Danger: #ff4d4d (Red)
├─ Success: #4dff4d (Green)
├─ Warning: #ffaa00 (Orange)
└─ Info: #4a9eff (Cyan)

Gradients Used:
├─ Navbar: linear-gradient(to right, rgba(10,10,30,0.98), rgba(20,20,50,0.98))
├─ Sidebar: linear-gradient(to right, rgba(10,10,30,0.95), rgba(15,15,35,0.95))
├─ Buttons: linear-gradient(135deg, #4a9eff, #2a7fff)
└─ Headers: linear-gradient(135deg, #4a9eff, #2a6eff)

C. COMPONENT LIBRARY
================================================================================

Core Components (OGame 0.84 Style):

1. RESOURCE DISPLAY WIDGET
   Shows: [Metal: 50,000] [Crystal: 30,000] [Deuterium: 5,000] [Energy: 100/500]
   Updates: Real-time (via tick system)
   Features: Bar graphs for capacity usage

2. BUILDING QUEUE CARD
   Shows:
   ├─ Building icon + name
   ├─ Current level → Next level
   ├─ Time remaining (countdown timer)
   ├─ Resource cost breakdown
   ├─ Progress bar (visual)
   └─ [Cancel] button
   
3. RESEARCH PROGRESS CARD
   Shows:
   ├─ Research icon + name
   ├─ Current tech level
   ├─ Time until completion
   ├─ Resource cost
   ├─ Next tech preview
   └─ Prerequisite chain

4. FLEET STATUS CARD
   Shows:
   ├─ Fleet name/ID
   ├─ Ship count breakdown (light fighters: 50, carriers: 3, etc.)
   ├─ Current location
   ├─ Status (In Transit, Attacking, Returning, Idle)
   ├─ Arrival time (if applicable)
   └─ [Details] [Redirect] [Recall] buttons

5. TECHNOLOGY GRID
   Shows:
   ├─ 3-4 tech buttons per row
   ├─ Tech icon (visual representation)
   ├─ Tech name + current level
   ├─ Lock icon (if prerequisites not met)
   ├─ Research time estimate
   ├─ Click to research
   └─ Hover shows full details

6. PLANET INFO PANEL
   Shows:
   ├─ Planet name (editable)
   ├─ Coordinates (Galaxy:System:Planet)
   ├─ Planet type/class
   ├─ Temperature
   ├─ Resource abundance %
   ├─ Current population
   ├─ Feature list (farms, labs, etc.)
   └─ [Manage] [Colonize] [Rename] buttons

7. NOTIFICATION ALERT
   Shows:
   ├─ Alert icon
   ├─ Alert type (Warning, Success, Info, Error)
   ├─ Message text
   ├─ Timestamp
   ├─ [Details] link
   └─ [Dismiss] button
   
   Animation: Slide in from top-right, auto-dismiss after 5s

8. DIALOG/MODAL WINDOW
   OGame-style popup with:
   ├─ Title bar with icon
   ├─ Content area (scrollable if needed)
   ├─ Button area ([OK] [Cancel])
   ├─ Escape key closes
   └─ Click-outside closes

9. DROPDOWN MENU (Navigation)
   Shows:
   ├─ Section header
   ├─ Menu items (hoverable)
   ├─ Indent for sub-items
   ├─ Icon + label per item
   └─ Smooth transitions

10. INPUT FIELDS & FORMS
    Styling:
    ├─ Background: rgba(20,20,40,0.8)
    ├─ Border: 1px solid #4a9eff
    ├─ Border-radius: 5px
    ├─ Text color: #ffffff
    ├─ Focus state: Glow effect, border enlarged
    └─ Disabled state: Opacity 0.5, cursor not-allowed

D. PAGE LAYOUTS (Template Examples)
================================================================================

EMPIRE PAGE LAYOUT (Main Dashboard):
┌────────────────────────────────────────────────┐
│ Page Header: "Your Empire"                     │
├────────────────────────────────────────────────┤
│ [Stats Summary]                                │
│ ├─ Total Planets: 3                           │
│ ├─ Total Fleet Power: 50,000                  │
│ ├─ Tech Research: Physics 5                    │
│ └─ Empire Rank: #145                          │
├────────────────────────────────────────────────┤
│ [Planet List]                                  │
│ ├─ Earth (Main World)                         │
│ │  ├─ Metal/hr: 1,200  Crystal/hr: 800       │
│ │  ├─ Buildings: 15  Defense: Active         │
│ │  └─ [Manage] [Details]                     │
│ ├─ Mars (Colony)                             │
│ │  ├─ Metal/hr: 600   Crystal/hr: 400        │
│ │  └─ [Manage] [Details]                     │
│ └─ Titan (Outpost)                           │
│    ├─ Metal/hr: 300   Crystal/hr: 200        │
│    └─ [Manage] [Details]                     │
├────────────────────────────────────────────────┤
│ [Active Tasks]                                 │
│ ├─ Metal Storage Upgrade (Level 3→4)         │
│ │  Time: 1h 23m  Cancel                      │
│ ├─ Physics Tech Research                      │
│ │  Time: 2h 45m  Cancel                      │
│ └─ Fleet Returning (to Main World)           │
│    Time: 30m     View Details                │
├────────────────────────────────────────────────┤
│ [Recent Events Log]                           │
│ ├─ [12:30] Spy probes eliminated 5 enemy SPY │
│ ├─ [12:15] Resources storage full warning    │
│ └─ [12:00] Fleet attack repelled              │
└────────────────────────────────────────────────┘

BUILDINGS PAGE LAYOUT:
┌────────────────────────────────────────────────┐
│ Page Header: "[Planet Name] - Buildings"      │
│ Planet selector: [Earth ▼]                    │
├────────────────────────────────────────────────┤
│ [Building Grid - 4 columns]                   │
│ ┌──────┐ ┌──────┐ ┌──────┐ ┌──────┐         │
│ │Metal │ │Metal │ │Crystal│ │Crystal│        │
│ │Mine  │ │Storage││Mine   │ │Storage│        │
│ │Lvl 5 │ │Lvl 3  │ │Lvl 4  │ │Lvl 2  │        │
│ │Res:  │ │Res:  │ │Res:   │ │Res:   │        │
│ │M:600 │ │M:2000│ │M:900  │ │M:1500 │        │
│ │C:300 │ │C:500 │ │C:300  │ │C:1000 │        │
│ │D:0   │ │D:0   │ │D:0    │ │D:500  │        │
│ │Time: │ │[Can] │ │Time:  │ │[Can]  │        │
│ │45min │ │      │ │1h 15m │ │       │        │
│ │[Esc] │ │      │ │[Esc]  │ │       │        │
│ └──────┘ └──────┘ └──────┘ └──────┘         │
│ (More buildings below)                        │
├────────────────────────────────────────────────┤
│ Legend: [Can] = Build info  [Esc] = Insufficient resources
└────────────────────────────────────────────────┘

TECHNOLOGY PAGE LAYOUT:
┌────────────────────────────────────────────────┐
│ Research Tree: Economy | Warfare | Fleet       │
│ [Economy Tab Selected]                        │
├────────────────────────────────────────────────┤
│ [Technology Grid - 3 columns]                 │
│ ┌────────┐ ┌────────┐ ┌────────┐             │
│ │Mining  │ │Resource│ │Storage │             │
│ │Tech    │ │Refining│ │Opt     │             │
│ │Lvl 3/10│ │Locked* │ │Lvl 2/10│             │
│ │Time:12h│ │Req:Mining│Time:8h │             │
│ │Cost:M→ │ │Tech Lv1  │Cost:M→ │             │
│ │  800   │ │ (Need L1)│  1000  │             │
│ │  C:400 │ │ [Details]│ C:500  │             │
│ │[Research]│ └────────┘ [Research]             │
│ └────────┘            └────────┘             │
│ (* = Prerequisites not met, locked for now)   │
├────────────────────────────────────────────────┤
│ [Current Research]                            │
│ Mining Technology Level 3→4                   │
│ Time remaining: 6h 43m ████████░░             │
│ Cost: Metal: 3,200 / Crystal: 1,600           │
│ [Cancel Research] [Queue Next]                │
└────────────────────────────────────────────────┘

FLEET/SHIPYARD PAGE LAYOUT:
┌────────────────────────────────────────────────┐
│ Page Header: "Shipyard & Fleet Management"    │
├────────────────────────────────────────────────┤
│ [Ship Construction Section]                   │
│ ├─ Light Fighter:     [ 25 ]  units           │
│ │  Cost: Metal: 75K Crystal: 25K              │
│ │  Time: 30m  [+] [-] [Build]                 │
│ ├─ Heavy Fighter:    [  5 ]  units            │
│ │  Cost: Metal: 30K Crystal: 20K              │
│ │  Time: 45m  [+] [-] [Build]                 │
│ ├─ Cruiser:         [  0 ]  units (Locked*)   │
│ │  Req: Armor Tech Lvl 4                      │
│ │  [Research prerequisite]                    │
│ └─ [Continue scrolling for more ships]       │
├────────────────────────────────────────────────┤
│ [Building Queue]                              │
│ ├─ 50 Light Fighters (35 remaining)           │
│ │  Time: 25m ... [Cancel] [Pause]            │
│ └─ 10 Heavy Fighters (30 queued)             │
│    Time: 52m total ... [Cancel] [Pause]      │
├────────────────────────────────────────────────┤
│ [Fleet Status]                                │
│ ├─ Fleet #1: "Strike Force"                  │
│ │  Location: Earth
│ │ Ships: LF: 50, HF: 10, Cruiser: 2          │
│ │ Power: 3,500  [Details] [Move] [Attack]    │
│ ├─ Fleet #2: "Exploration"                    │
│ │  Location: (In Transit to Mars)            │
│ │ Ships: Cargo: 5, Scout: 1                  │
│ │ ETA: 2h 30m  [Recall] [Details]            │
│ └─ Fleet #3: "Defense"                       │
│    Location: orbital_station_001             │
│    Ships: Defense turrets active              │
└────────────────────────────────────────────────┘

COMBAT BATTLE REPORT LAYOUT:
┌────────────────────────────────────────────────┐
│ Battle Report: Attack on Player123             │
│ Date: 2026-03-09 14:32 UTC                   │
├────────────────────────────────────────────────┤
│ ATTACKER (You)              DEFENDER (Enemy)  │
│ ├─ Fleet: Strike Force      ├─ Planet: Alpha │
│ ├─ Ships: 50 LF, 10 HF, 2C  ├─ Defense: 20RL,│
│ ├─ Power: 3,500             │  10 LLC, 5HC   │
│ └─ Losses: 8 LF (160dmg), 1 HF (450 dmg)     │
│                                               │
│ ├─ Ships: 42 LF, 9 HF, 2C   ├─ Losses: 15RL, │
│ └─ Returned with resources  │  8 LLC, 2HC    │
│                                               │
├────────────────────────────────────────────────┤
│ PLUNDER:                    DEBRIS FIELD:     │
│ ├─ Metal: 50,000            ├─ Metal: 25,000 │
│ ├─ Crystal: 30,000          ├─ Crystal: 15,000
│ └─ Deuterium: 5,000         └─ Deuterium: 0  │
│                                               │
│ [Salvage] [Add to fleet] [Return to attacker]│
└────────────────────────────────────────────────┘

================================================================================
SECTION 4: SYSTEM ARCHITECTURE & DATABASE
================================================================================

A. DATABASE SCHEMA (21 Tables - Currently Verified)
================================================================================

TABLE: users
├─ id (PK)
├─ username (UNIQUE)
├─ password (hashed)
├─ email (UNIQUE)
├─ created_at
├─ last_login
├─ rank (rating points)
├─ admin_level (0=player, 1=moderator, 2=admin, 3=superadmin)
└─ status (active, banned, suspended, vacation, holiday)

TABLE: planets
├─ id (PK)
├─ user_id (FK → users)
├─ name (editable)
├─ galaxy (1-Galaxies total)
├─ system (1-499)
├─ position (1-15)
├─ type (terrestrial, ice, gas, rocky, water)
├─ diameter (size/capacity)
├─ temperature
├─ metal_production (base + buildings)
├─ crystal_production
├─ deuterium_production
├─ energy_production
├─ metal_storage
├─ crystal_storage
├─ deuterium_storage
├─ current_metal, current_crystal, current_deuterium (inventory)
├─ created_at
└─ last_update

TABLE: buildings
├─ id (PK)
├─ planet_id (FK → planets)
├─ building_type_id (FK → building_types)
├─ level
├─ started_at
├─ completed_at (NULL if incomplete)
├─ status (idle, building, completed)
└─ queue_position (for build queue)

TABLE: building_types
├─ id (PK)
├─ name (Metal Mine, Crystal Mine, etc.)
├─ description
├─ base_metal_cost
├─ base_crystal_cost
├─ base_deuterium_cost
├─ base_time (seconds)
├─ max_level (50, 60, etc.)
├─ energy_requirement (per level)
└─ prerequisites (JSON: {tech_id: level, building_id: level})

TABLE: research
├─ id (PK)
├─ user_id (FK → users)
├─ research_id (FK → research_types)
├─ level
├─ started_at
├─ completed_at (NULL if incomplete)
└─ status (idle, researching, completed)

TABLE: research_types
├─ id (PK)
├─ name (Mining Technology, Weapons, etc.)
├─ category (economy, warfare, fleet, etc.)
├─ description
├─ base_metal_cost
├─ base_crystal_cost
├─ base_deuterium_cost
├─ base_time (seconds)
├─ max_level
└─ prerequisites (JSON)

TABLE: fleets
├─ id (PK)
├─ user_id (FK → users)
├─ name
├─ status (idle, moving, attacking, returning, defending)
├─ current_planet_id (FK → planets)
├─ target_planet_id (NULL if not moving)
├─ departure_time
├─ arrival_time (NULL if not moving)
├─ mission_type (transport, attack, spy, defend, etc.)
└─ created_at

TABLE: fleet_ships
├─ id (PK)
├─ fleet_id (FK → fleets)
├─ ship_type_id (FK → ship_types)
├─ quantity
└─ status (healthy, damaged, destroyed)

TABLE: ship_types
├─ id (PK)
├─ name (Light Fighter, Cruiser, etc.)
├─ description
├─ attack (combat power)
├─ defense
├─ hull (health)
├─ speed
├─ cargo_capacity
├─ base_metal_cost
├─ base_crystal_cost
├─ base_deuterium_cost
├─ base_time (seconds to build)
└─ prerequisites

TABLE: defense_structures
├─ id (PK)
├─ planet_id (FK → planets)
├─ defense_type_id (FK → defense_types)
├─ quantity (number of this defense on planet)
└─ status (active, damaged, offline)

TABLE: defense_types
├─ id (PK)
├─ name (Rocket Launcher, Laser Cannon, etc.)
├─ attack
├─ defense
├─ energy_consumption
├─ metal_cost (per unit)
├─ crystal_cost (per unit)
├─ deuterium_cost (per unit)
└─ prerequisites

TABLE: battles
├─ id (PK)
├─ attacker_id (FK → users)
├─ defender_id (FK → users)
├─ planet_id (FK → planets)
├─ attacker_fleet_id (FK → fleets)
├─ defender_fleet_id (FK → fleets)
├─ started_at
├─ result (attacker_win, defender_win, draw)
├─ attacker_losses (JSON: {ship_type_id: quantity})
├─ defender_losses (JSON)
├─ plunder_metal
├─ plunder_crystal
├─ plunder_deuterium
├─ debris_metal
├─ debris_crystal
├─ debris_deuterium
└─ report_text (detailed combat log)

TABLE: messages
├─ id (PK)
├─ sender_id (FK → users, NULL if system)
├─ recipient_id (FK → users)
├─ subject
├─ body
├─ type (private, battle_report, spy_report, alert, system)
├─ created_at
├─ read_at (NULL if unread)
└─ folder (inbox, outbox, archive)

TABLE: alliances
├─ id (PK)
├─ name (UNIQUE)
├─ founder_id (FK → users)
├─ description
├─ logo_url
├─ created_at
├─ treasury_metal
├─ treasury_crystal
├─ treasury_deuterium
├─ diplomacy_level => Array (structure TBD)
└─ member_count

TABLE: alliance_members
├─ id (PK)
├─ alliance_id (FK → alliances)
├─ user_id (FK → users)
├─ rank (member, officer, leader)
├─ joined_at
└─ permissions (JSON: {can_trade, can_war, can_invite, etc.})

TABLE: debris_fields
├─ id (PK)
├─ galaxy
├─ system
├─ position
├─ metal_amount
├─ crystal_amount
├─ deuterium_amount
├─ created_at
├─ expires_at (TTL - deleted after 15 days)
└─ claimed_by (NULL if unclaimed)

TABLE: transactions
├─ id (PK)
├─ type (marketplace_trade, alliance_resource, tax, etc.)
├─ sender_id (FK → users)
├─ recipient_id (FK → users)
├─ metal, crystal, deuterium (quantities)
├─ created_at
└─ status (pending, completed, failed)

TABLE: logs
├─ id (PK)
├─ user_id (FK → users)
├─ action (building_constructed, research_completed, battle_fought, etc.)
├─ details (JSON: {planet_id, target_id, resources, etc.})
├─ created_at
└─ ip_address

TABLE: server_settings
├─ id (PK)
├─ setting_key (game_speed, max_planets, etc.)
├─ setting_value
├─ description
├─ last_updated
└─ updated_by (admin_id)

B. CLASS STRUCTURE (PHP OOP Architecture)
================================================================================

Core Classes:

Database (Singleton)
├─ Methods:
│  ├─ connect()
│  ├─ query($sql, $params)
│  ├─ execute($sql, $params)
│  ├─ fetch($sql, $params)
│  ├─ fetchAll($sql, $params)
│  ├─ insert($table, $data) → INSERT ID
│  ├─ update($table, $data, $where)
│  ├─ delete($table, $where)
│  └─ transaction() / commit() / rollback()
└─ Connection: PDO via db_config.php

Player (Core Entity)
├─ Properties:
│  ├─ $id, $username, $email
│  ├─ $rank, $admin_level
│  ├─ $planets[], $research[], $fleets[]
│  └─ $resources (metal, crystal, deuterium)
├─ Methods:
│  ├─ getData() → Full player data
│  ├─ getResources() → [metal, crystal, deuterium, energy]
│  ├─ addResources($metal, $crystal, $deuterium)
│  ├─ removeResources($metal, $crystal, $deuterium)
│  ├─ getPlanets() → Planet[]
│  ├─ createFleet($name, $planet_id) → Fleet
│  ├─ getFleets() → Fleet[]
│  ├─ startResearch($tech_id, $planet_id) → Research
│  ├─ getResearch() → Research[]
│  ├─ getRank() → Rank info
│  └─ delete() → Delete account
└─ Database: Table 'users'

Planet (Entity)
├─ Properties:
│  ├─ $id, $user_id, $name
│  ├─ $coordinates (galaxy, system, position)
│  ├─ $buildings[], $defenses[]
│  ├─ $resources (metal, crystal, deuterium)
│  ├─ $resource_production (per-hour rates)
│  └─ $type, $diameter, $temperature
├─ Methods:
│  ├─ getData() → Full planet data
│  ├─ getBuildings() → Building[]
│  ├─ addBuilding($building_type_id, $level)
│  ├─ upgradeBuilding($building_id) → Start build
│  ├─ calculateProduction() → Resources/hr
│  ├─ getDefenses() → Defense[]
│  ├─ addDefense($defense_type_id, $quantity)
│  ├─ getResources() → [metal, crystal, deuterium]
│  ├─ addResources($m, $c, $d)
│  ├─ removeResources($m, $c, $d)
│  ├─ checkResourceStorage() → bool (full?)
│  ├─ rename($new_name)
│  ├─ colonize($player_id) → Colonize planet
│  └─ destroy() → Obliterate planet
└─ Database: Table 'planets'

Building (Entity)
├─ Properties:
│  ├─ $id, $planet_id
│  ├─ $type_id, $level, $status
│  ├─ $started_at, $completed_at
│  └─ $queue_position
├─ Methods:
│  ├─ getData() → Building info
│  ├─ getType() → BuildingType
│  ├─ upgrade() → Start upgrade
│  ├─ calculateTime() → Seconds
│  ├─ calculateCost() → [metal, crystal, deuterium]
│  ├─ cancel() → Kill build queue entry
│  ├─ isComplete() → bool
│  └─ complete() → Finish construction
└─ Database: Table 'buildings'

Ship (Entity)
├─ Properties:
│  ├─ $id, $fleet_id
│  ├─ $type_id, $quantity, $status
│  └─ $hull_health (damage tracking)
├─ Methods:
│  ├─ getData() → Ship info
│  ├─ getType() → ShipType
│  ├─ calculateAttackPower() → Damage with techs
│  ├─ calculateDefense() → Defense value
│  ├─ damage($amount) → Reduce hull
│  ├─ isDestroyed() → bool
│  └─ repair() → Heal damage
└─ Database: Table 'fleet_ships'

Fleet (Entity)
├─ Properties:
│  ├─ $id, $user_id, $name
│  ├─ $status (idle, moving, attacking, etc.)
│  ├─ $current_planet_id, $target_planet_id
│  ├─ $ships[], $mission_type
│  ├─ $departure_time, $arrival_time
│  └─ $power (total combat power)
├─ Methods:
│  ├─ getData() → Fleet info
│  ├─ getShips() → Ship[]
│  ├─ addShip($ship_type_id, $quantity)
│  ├─ removeShip($ship_id, $quantity)
│  ├─ calculateTravelTime($from, $to, $speed_tech)
│  ├─ move($target_planet) → Start movement
│  ├─ attack($target_planet) → Start attack
│  ├─ arrive() → Complete movement
│  ├─ retreat() → Return to origin
│  ├─ calculateCombatPower() → int
│  └─ delete() → Remove fleet
└─ Database: Table 'fleets'

Research (Entity)
├─ Properties:
│  ├─ $id, $user_id
│  ├─ $research_type_id, $level, $status
│  ├─ $started_at, $completed_at
│  └─ $planet_id (research lab location)
├─ Methods:
│  ├─ getData() → Research info
│  ├─ getType() → ResearchType
│  ├─ start() → Begin research
│  ├─ calculateTime() → Seconds to complete
│  ├─ calculateCost() → [metal, crystal, deuterium]
│  ├─ isComplete() → bool
│  ├─ complete() → Finish research
│  ├─ cancel() → Stop research
│  ├─ checkPrerequisites() → bool (can start?)
│  └─ getNextLevel() → int
└─ Database: Table 'research'

Battle (Logic Engine)
├─ Properties:
│  ├─ $attacker_fleet, $defender_fleet
│  ├─ $planet (battle location)
│  ├─ $rounds = 10 max
│  └─ $terrain_modifier (planet type affects combat)
├─ Methods:
│  ├─ simulate() → Run combat simulation
│  ├─ calculateRound() → Execute one round of combat
│  ├─ selectTargets() → Determine targets
│  ├─ calculateDamage($attacker, $defender) → int
│  ├─ resolveHits() → Apply casualties
│  ├─ calculateDebris() → Debris field
│  ├─ getResult() → Battle outcome
│  ├─ generateReport() → Battle report
│  └─ createDebrisField() → Add to database
└─ Logic: Combat calculations (no DB persistence on-compute)

TaskGenerator (Cron-like System)
├─ Properties:
│  ├─ $tasks = []
│  ├─ $current_turn
│  └─ $update_interval (6 hours)
├─ Methods:
│  ├─ fetch() → Get all pending tasks
│  ├─ processProductionTasks() → Update resources
│  ├─ processBuildingTasks() → Complete builds
│  ├─ processResearchTasks() → Complete research
│  ├─ processMovementTasks() → Process fleet arrivals
│  ├─ generateNotifications() → Alert players
│  ├─ execute() → Run all task processing
│  ├─ logTaskCompletion($task_id) → DB logging
│  └─ getNextUpdateTime() → Timestamp
└─ Database: Reads/writes to multiple tables

SessionManager (Session Handler)
├─ Properties:
│  ├─ $session_timeout = 2 hours
│  ├─ $user_id (current)
│  └─ $session_data
├─ Methods:
│  ├─ start() → Initialize session
│  ├─ create($user_id) → Create new session
│  ├─ verify() → Check if valid
│  ├─ getUser() → Current player
│  ├─ refresh() → Update timeout
│  ├─ destroy() → Logout
│  └─ isAdmin() → Permission check
└─ Storage: PHP $_SESSION superglobal

Logger (Static Utility)
├─ Methods:
│  ├─ log($level, $message, $context) → static
│  ├─ info($message, $context = [])
│  ├─ warning($message, $context = [])
│  ├─ error($message, $context = [])
│  ├─ debug($message, $context = [])
│  └─ emergency($message, $context = [])
└─ Output: Logs to file (Logs/game.log)

================================================================================
SECTION 5: SETTINGS & OPTIONS PAGES
================================================================================

A. SETTINGS PAGE STRUCTURE (/pages/settings.php)
================================================================================

ACCOUNT SETTINGS
┌─────────────────────────────────────────┐
│ Account Settings                        │
├─────────────────────────────────────────┤
│ [Username] ■ (username_current)        │
│ [Change password]                       │
│ ├─ Old password: [_________] Required  │
│ ├─ New password: [_________] Min 8 ch  │
│ ├─ Confirm:     [_________]             │
│ └─ [Update]                             │
│                                         │
│ [Email] ■ (email@example.com)          │
│ [Change email]                          │
│ ├─ New email:   [_________]            │
│ ├─ Confirm:     [_________]            │
│ └─ [Verify] (sends confirmation)       │
│                                         │
│ [Two-Factor Authentication]             │
│ ├─ Status: Not Enabled [Enable]        │
│ └─ Method: SMS / Authenticator App     │
│                                         │
│ [API Keys] (for tools/scripts)         │
│ ├─ Active keys: 1 [Generate New] [List]│
│ └─ Last used: 2 hours ago             │
│                                         │
│ [Session Management]                   │
│ ├─ Current session: Active (last 5min) │
│ ├─ Other sessions: 1 other device     │
│ └─ [Logout All] [Manage Sessions]     │
│                                         │
│ [Danger Zone]                          │
│ ├─ Delete account permanently          │
│ ├─ Warning: Cannot be undone!         │
│ └─ [Delete Account]                   │
└─────────────────────────────────────────┘

GAME SETTINGS
┌─────────────────────────────────────────┐
│ Game Settings                           │
├─────────────────────────────────────────┤
│ [Galaxy & System]                       │
│ ├─ Current galaxy: Galaxy 1 [Change]   │
│ ├─ Preferred system: System 100         │
│ └─ [Find Uncolonized Planet] [Migrate] │
│                                         │
│ [Game Modes]                            │
│ ├─ Holiday Mode (30-day immunity)      │
│ │  Status: ○ Inactive [ACTIVATE]       │
│ ├─ Vacation Mode (90-day pause)        │
│ │  Status: ○ Inactive [ACTIVATE]       │
│ └─ Note: Can activate once per year    │
│                                         │
│ [Notification Settings]                 │
│ ├─ ☑ Building complete                 │
│ ├─ ☑ Research complete                 │
│ ├─ ☑ Fleet arrived                     │
│ ├─ ☑ Under attack                      │
│ ├─ ☑ Resources full                    │
│ ├─ ☑ Alliance messages                 │
│ ├─ ☑ Player messages                   │
│ ├─ ☑ Trading updates                   │
│ └─ [Email notifications] (☑ enabled)   │
│                                         │
│ [UI & Display]                         │
│ ├─ Theme: [Dark ▼] (Dark/Light)        │
│ ├─ Language: [English ▼]               │
│ ├─ Confirm actions: [Yes ▼]            │
│ ├─ Animation: [On ▼]                   │
│ ├─ Resource ticker: [On ▼]             │
│ └─ Grid layout: [4 columns ▼]          │
│                                         │
│ [Keyboard Shortcuts]                   │
│ ├─ Quick fleet view: [Alt+F]           │
│ ├─ Quick galaxy: [Alt+G]               │
│ ├─ Quick messages: [Alt+M]             │
│ └─ [View all shortcuts] [Customize]    │
│                                         │
│ [Save Changes] [Reset to Default]      │
└─────────────────────────────────────────┘

PRIVACY SETTINGS
┌─────────────────────────────────────────┐
│ Privacy & Permissions                   │
├─────────────────────────────────────────┤
│ [Trading]                               │
│ ├─ Allow trading: ☑ Enabled            │
│ ├─ Restrict to: ○ Anyone               │
│ │              ○ Alliance members       │
│ │              ○ Friends only           │
│ └─ ○ Disabled                           │
│                                         │
│ [Alliance Invitations]                  │
│ ├─ ○ Allow all                          │
│ ├─ ☑ Alliance officers only             │
│ └─ ○ Decline all                        │
│                                         │
│ [Friend List]                           │
│ ├─ Public visibility: ○ Yes  ☑ No     │
│ ├─ Allow adds: ☑ Yes  ○ No             │
│ ├─ Current friends: 23                  │
│ └─ [Manage friends] [Block list]        │
│                                         │
│ [Message Filtering]                     │
│ ├─ Blocked players: 5                   │
│ │  ├─ [Player1]   [Unblock]            │
│ │  ├─ [Player2]   [Unblock]            │
│ │  └─ ...                               │
│ ├─ [Block new player] [Manage blocks]  │
│ └─ Auto-delete old messages: 180 days   │
│                                         │
│ [Espionage & Combat]                    │
│ ├─ ☑ Allow espionage attacks           │
│ ├─ ☑ Allow military attacks            │
│ ├─ Revenge timer: 24 hours              │
│ └─ [Battle history] [Losses]           │
│                                         │
│ [Save Changes]                         │
└─────────────────────────────────────────┘

B. ADDITIONAL FEATURE PAGES
================================================================================

23. CHARACTER/PROFILE PAGE (/pages/profile.php)
    Purpose: Public player profile and statistics
    Shows:
    ├─ Avatar/Character image
    ├─ Username & alliance
    ├─ Overall rank (with rating points)
    ├─ Account age & playtime
    ├─ Empire statistics
    │  ├─ Number of planets
    │  ├─ Fleet power
    │  ├─ Technology level
    │  └─ Defense rating
    ├─ Battle record
    │  ├─ Wins: X
    │  ├─ Losses: Y
    │  ├─ Draws: Z
    │  └─ Win ratio: X%
    ├─ Achievements (badges earned)
    ├─ Friend request button
    ├─ Private message button
    ├─ Diplomatic relations
    └─ Edit [Your own profile only]

24. UNIVERSE & EVENT LOG (/pages/universe-log.php)
    Purpose: Live server events and announcements
    Shows:
    ├─ Server announcements (stickied)
    ├─ Universe-wide events
    │  ├─ Major alliance battles
    │  ├─ New top players
    │  ├─ Server maintenance
    │  └─ Special events/bonuses
    ├─ Historical events (searchable/filterable)
    └─ Server statistics dashboard

25. PREMIUM/SHOP PAGE (/pages/shop.php) [Optional]
    Purpose: Optional in-game premium features
    Features (if implemented):
    ├─ Premium currency (Credits/Tokens)
    ├─ Cosmetic items (skins, colors, animations)
    ├─ Account upgrades (larger storage, more planets, etc.)
    ├─ Battle pass system (seasonal rewards)
    ├─ Booster packs (temporary production/speed boost)
    └─ Note: Avoid pay-to-win mechanics

================================================================================
SECTION 6: NAVIGATION MENU STRUCTURE
================================================================================

TOP NAVBAR MENUS (OGame 0.84 Style):

1. [Logo] Sci-Fi Conquest

2. [MENU] ▼
   ├─ Overview
   ├─ Empire
   ├─ Galaxy Map
   └─ Alliance

3. [PRODUCTION] ▼
   ├─ Buildings
   ├─ Mines
   ├─ Power Plants
   └─ Storage

4. [MILITARY] ▼
   ├─ Shipyard
   ├─ Fleets
   ├─ Defense
   ├─ Battles
   └─ Battle Reports

5. [RESEARCH] ▼
   ├─ Technology Tree
   ├─ Current Research
   ├─ Economy Tech
   ├─ Warfare Tech
   └─ Fleet Tech

6. [TRADING] ▼
   ├─ Marketplace
   ├─ Trade Offers
   ├─ Transactions
   └─ Trade History

7. [COMMUNICATION] ▼
   ├─ Messages (3)
   ├─ Battle Reports (1)
   ├─ Espionage (2)
   └─ Alliance Chat

LEFT SIDEBAR MENU (Collapsible Sections):

OVERVIEW
├─ Empire Dashboard
├─ My Profile
├─ Statistics
└─ Achievements

BUILDINGS
├─ Planet Selection
├─ Building List
├─ Build Queue
└─ Upgrade Calculator

MILITARY
├─ My Fleets
├─ Shipyard
├─ Defense Status
├─ Battle Simulator
└─ War History

RESEARCH
├─ Technology Tree
├─ Current Research
├─ Research Queue
└─ Tech Calculator

EXPLORATION
├─ Galaxy Map
├─ Debris Fields
├─ Colonization
└─ Expeditions

TRADING & ECONOMY
├─ Marketplace
├─ Resources
├─ Transactions
└─ Trading Contracts

ALLIANCE
├─ Alliance Info
├─ Members
├─ Diplomacy
└─ Alliance Resources

MESSAGES
├─ Inbox
├─ Battle Reports
├─ Spy Reports
└─ System Alerts

SETTINGS
├─ Account
├─ Game Settings
├─ Privacy
├─ Shortcuts
└─ Help & FAQ

================================================================================
SECTION 7: GAME MECHANICS & FORMULAS
================================================================================

A. PRODUCTION FORMULAS
================================================================================

Building Production (Base):
Production/Hour = Base_Value * (1 + Tech_Bonus) * (1 + Planetary_Bonus) * Server_Speed

Example - Metal Mine Level 5:
├─ Base: 30 * 5 = 150 metal/hour
├─ Mining Tech bonus (Level 3): 150 * 1.30 = 195 metal/hour
├─ Planetary bonus (5%): 195 * 1.05 = 204.75 metal/hour
└─ Server speed (2x): 204.75 * 2 = 409.5 metal/hour

Construction Time:
Time (seconds) = Base_Time * Level * (Level + 1) / 2 / Robot_Speed_Factor / Server_Speed

Example - Metal Storage Upgrade (Lvl 3→4):
├─ Base: 2000 seconds
├─ Level calc: 2000 * 4 * 5 / 2 = 20000 seconds
├─ Robot factory (Lvl 2): 20000 / 1.5 = 13333 seconds
└─ Server speed (2x): 13333 / 2 = 6666 seconds (~111 minutes)

Research Time:
Time (seconds) = Base_Time * Level * (Level + 1) / 2 / Lab_Speed_Factor

Example - Weapons Tech (Lvl 3→4):
├─ Base: 3600 seconds
├─ Level calc: 3600 * 4 * 5 / 2 = 36000 seconds
├─ Research Lab (Lvl 3): 36000 / 1.3 = 27692 seconds
└─ ~7.7 hours

B. FLEET MECHANICS
================================================================================

Fleet Speed Calculation:
Actual_Speed = Base_Speed * (1 + Speed_Tech * 0.1) * Server_Speed_Factor

Example - Light Fighter at Speed Tech Lvl 5:
├─ Base: 12,500
├─ Speed tech: 12,500 * (1 + 5 * 0.1) = 12,500 * 1.5 = 18,750
└─ On 2x server: 18,750 * 2 = 37,500 units/hour

Travel Time Calculator:
Distance (systems) = |Target_System - Current_System|
Travel_Speed = Slowest_Ship_In_Fleet_Speed
Travel_Time (minutes) = Distance / Travel_Speed * 60

Combat Power:
Fleet_Power = Sum(Ship_Count * Ship_Attack * (1 + Weapon_Tech_Level * 0.1))

Example - 50 Light Fighters at Weapons Tech 3:
├─ Base: 50 * 50 (LF attack) = 2,500
├─ Weapons tech (Lvl 3): 2,500 * (1 + 0.3) = 3,250 power
└─ With Heavy Fighter (1): 3,250 + (1 * 150 * 1.3) = 3,445 power

C. COMBAT FORMULAS
================================================================================

Damage Per Round:
Damage = Attacker_Fleet_Power * (0.8 + Random(0, 0.4)) - Defender_Armor_Bonus

Example Combat:
├─ Attacker: 3,250 power
├─ Random: 0.9 (90% of range)
├─ Damage: 3,250 * 0.9 = 2,925
├─ Defender armor (Tech lvl 2): 2,925 - 200 = 2,725 damage taken

Target Destruction:
When cumulative damage ≥ Ship_Hull_Points, ship destroyed
Partial damage carries over to next ship of same type

Example - Light Fighters (400 hull each):
├─ 1st hit: 2,725 damage - 400 = 2,325 remaining
├─ 2nd LF destroyed, 2,325 - 400 = 1,925 remaining
├─ ...continues until minimum reached

Casualty Calculation (Post-Battle Survivor Chance):
Survivors = Random(1, 3) % Math.ceil(Damage / Ship_Hull)
If result < 1, 0 survivors (all destroyed)

Example - Fleet taking 2,725 damage:
├─ LF (400 HP): 2,725 / 400 = 6.8 → Need 7 ships destroyed
├─ Chance: Random(1-3) % 7 = Random(14-21%, ~17%)
└─ Each destroyed ship has 17% chance 1 crew survives

D. RESOURCE PLUNDER
================================================================================

Plunder Formula (Post-Battle):
Cargo_Capacity = Sum(Attacking_Ships_Cargo)
Plunder_Available = Defender_Resources (up to capacity)
Plunder_Taken = Min(Cargo_Capacity, Plunder_Available)

Allocation:
├─ Metal: 50% priority
├─ Crystal: 30% priority
├─ Deuterium: 20% priority

Example - 5 Large Cargo Ships (25,000 capacity each):
├─ Total capacity: 125,000
├─ Defending resources: Metal=50,000 Crystal=40,000 Deuterium=10,000
├─ Total: 100,000 available
├─ All resources stolen (100,000 taken)

E. DEBRIS FIELD
================================================================================

Debris Creation:
Metal_Debris = (Destroyed_Metal_Value + Destroyed_Ships_Metal_Value) * 0.33
Crystal_Debris = (Destroyed_Metal_Value + Destroyed_Ships_Metal_Value) * 0.33

Example - 5 destroyed ships worth 15,000 metal each:
├─ Total destroyed value: 75,000
├─ Debris generated: 75,000 * 0.33 = 24,750
├─ Can be harvested by any player's cargo fleet
└─ Expires after 15 real-world days if unclaimed

================================================================================
SECTION 8: IMPLEMENTATION PRIORITY & ROADMAP
================================================================================

PHASE 1: CORE PAGES (Currently Done ✅)
✅ Empire (Overview)
✅ Shipyard (Basic build interface)
✅ Research (Tech selection)
✅ Fleet (Fleet display)
✅ Galaxy (Map navigation)
✅ Alliance (Basic)
✅ Messages (Basic)
✅ Rankings (Leaderboards)
✅ Marketplace (Basic)
✅ Notifications (Alert system)
✅ Tasks (Mission tracking)
✅ Admin (Server control)
✅ Register (Account creation)

PHASE 2: INTERMEDIATE PAGES (Next Priority 🔜)
⏳ Planet Details (Planet management)
⏳ Buildings (Detailed build queue)
⏳ Espionage (Spy missions)
⏳ Defense Reports (Attack tracking)
⏳ Battle Simulator (Combat prediction)
⏳ Settings (Account & game options)
⏳ Help (Tutorials & guides)
⏳ Account (Profile & stats)

PHASE 3: ADVANCED FEATURES (Extended)
⏳ Real-time production tick system
⏳ Battle reports with detailed combat log
⏳ Alliance diplomacy matrix
⏳ Trading contract system
⏳ Character progression & achievements
⏳ Event system & server events
⏳ Premium shop (if monetization desired)
⏳ Mobile responsive optimization

PHASE 4: POLISH & OPTIMIZATION (Final)
⏳ Performance optimizations
⏳ Browser caching & CDN
⏳ Database query optimization
⏳ Security hardening
⏳ XSS & CSRF protection
⏳ Rate limiting on APIs
⏳ Automated backups
⏳ Monitoring & analytics

================================================================================
CONCLUSION
================================================================================

This comprehensive specification provides:

1. COMPLETE PAGE STRUCTURE: All 25+ game pages with detailed layouts
2. CORE SYSTEMS: Economy, research, military, and exploration systems
3. DATABASE SCHEMA: 21 tables with complete field definitions
4. CLASS ARCHITECTURE: OOP design for all game entities
5. UI COMPONENTS: Reusable components following OGame 0.84 design
6. GAME MECHANICS: Detailed formulas for production, combat, travel
7. SETTINGS & OPTIONS: Complete player customization options
8. IMPLEMENTATION ROADMAP: Phased development approach

STATUS: Production-ready specification
NEXT STEP: Implement Phase 2 pages and integrate Phase 1 pages with backend logic

The game is now ready for:
- Full backend integration
- Database operations
- API development
- Real-time systems
- Client-side scripting
- Performance optimization
- Production deployment
