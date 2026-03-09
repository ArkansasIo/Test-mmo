# Quick Start Guide

## Installation

### One-Command Full Stack
From project root, run:
```powershell
./run-all.ps1
```

This will:
- Start local MariaDB
- Initialize/refresh schema
- Start PHP web server + game tick background jobs
- Run a health check

Stop everything:
```powershell
./stop-all.ps1
```

Run health checks only:
```powershell
./health-check.ps1
```

### Step 0: Initialize Database (Recommended)
From project root, run:
```powershell
./run-local-db.ps1
./setup-database.ps1
```

This command will:
- Start local MariaDB on `127.0.0.1:3306` (if not already running)
- Create database `scifi_conquest` if it does not exist
- Apply `Index/database/schema.sql`

To stop local MariaDB:
```powershell
./stop-local-db.ps1
```

### One-Command Dev Start (Windows PowerShell)
From the project root, run:
```powershell
./run-dev.ps1
```

This starts:
- PHP web server on `http://localhost:8000`
- Background game tick loop (runs `Index/cron/game_tick.php` every 60 seconds)

To stop both background jobs:
```powershell
./stop-dev.ps1
```

### Step 1: Install PHP Development Server
Navigate to your game directory, then run the PHP development server:
```bash
cd scifi-Conquest-Awakening-main
php -S localhost:8000 -t .
```

### Step 2: Start Playing
Open your browser and go to:
```
http://localhost:8000/Index/index.php
```

Use your existing account, or create one at:
```
http://localhost:8000/Index/pages/register.php
```

---

## Creating Additional Players

### Register New Players
New players can register at:
```
http://localhost:8000/Index/pages/register.php
```

Or click "Create Account" on the login page.

---

## Running the Game Engine

For automated resource updates and task processing, set up a cron job or task scheduler:

### Linux/Mac - Cron Job
Add to crontab (`crontab -e`):
```bash
* * * * * /usr/bin/php /path/to/scifi-Conquest-Awakening-main/Index/cron/game_tick.php >> /path/to/logs/cron.log 2>&1
```

### Windows - Task Scheduler
1. Open Task Scheduler
2. Create Basic Task
3. **Program**: `C:\php\php.exe` (or your PHP path)
4. **Arguments**: `C:\path\to\scifi-Conquest-Awakening-main\Index\cron\game_tick.php`
5. **Trigger**: Repeat every 1 minute

### Manual Alternative
For development/testing, open a second terminal and run:
```bash
php Index/cron/game_tick.php
```
Every minute or whenever you want to process game tasks.

---

## Gameplay Guide

### 1. Build Your Economy
- **Metal Mine**: Primary resource for buildings and ships
- **Crystal Mine**: Required for advanced technology and ships
- **Deuterium Synthesizer**: Fuel for ships
- **Solar Plant / Fusion Reactor**: Energy for your facilities

### 2. Research Technologies
- Upgrade your **Research Lab**
- Research technologies to unlock new capabilities
- Focus on drive technologies for faster fleets

### 3. Build Your Fleet
- Go to **Shipyard** page
- Build various ship types:
  - **Cargo Ships**: Transport resources
  - **Fighters**: Basic combat units
  - **Battleships**: Heavy combat vessels
  - **Colony Ships**: Colonize new planets

### 4. Explore the Galaxy
- Visit the **Galaxy** page
- Scout other systems
- Use **Espionage Probes** to gather intelligence

### 5. Join an Alliance
- Go to **Alliance** page
- Browse available alliances or create your own
- Work together with allies

### 6. Trade Resources
- Visit **Marketplace**
- Trade excess resources with other players

### 7. Combat
- Send attack missions from **Fleet** page
- Choose target coordinates
- Send fleets to attack enemy planets
- Defend with static defenses from **Shipyard**

---

## Admin Panel

Access at:
```
http://localhost:8000/Index/index.php?page=admin
```

Features:
- View game statistics
- Manage players (ban/unban/delete)
- Give resources to players
- Send admin messages
- Enable/disable maintenance mode

---

## Troubleshooting

### Database Connection Errors
1. Check MySQL is running
2. Verify credentials in `Index/config.php`
3. Ensure database was created properly

### Resources Not Updating
1. Make sure `game_tick.php` is running periodically
2. Check that buildings are upgraded
3. Verify production rates are > 0

### Login Issues
1. Clear browser cookies
2. Check username/password
3. Verify player exists in database

### Port Already in Use
If port 8000 is busy, use a different port:
```bash
php -S localhost:9000 -t .
```

---

## Default Credentials

After installation:
- **Username**: (what you chose during install)
- **Password**: (what you chose during install)
- **Role**: Administrator

---

## Game URLs Reference

- **Main Game**: `http://localhost:8000/Index/index.php`
- **Login**: `http://localhost:8000/Index/index.php`
- **Register**: `http://localhost:8000/Index/pages/register.php`
- **Empire**: `http://localhost:8000/Index/index.php?page=empire`
- **Fleet**: `http://localhost:8000/Index/index.php?page=fleet`
- **Research**: `http://localhost:8000/Index/index.php?page=research`
- **Shipyard**: `http://localhost:8000/Index/index.php?page=shipyard`
- **Galaxy**: `http://localhost:8000/Index/index.php?page=galaxy`
- **Rankings**: `http://localhost:8000/Index/index.php?page=rankings`
- **Alliance**: `http://localhost:8000/Index/index.php?page=alliance`
- **Marketplace**: `http://localhost:8000/Index/index.php?page=marketplace`
- **Messages**: `http://localhost:8000/Index/index.php?page=messages`
- **Admin**: `http://localhost:8000/Index/index.php?page=admin`

---

## Tips for Development

### Enable Error Reporting
In `Index/config.php`:
```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

### Database Backup
```bash
mysqldump -u root -p scifi_conquest > backup.sql
```

### Database Restore
```bash
mysql -u root -p scifi_conquest < backup.sql
```

### Clear All Data
```sql
TRUNCATE TABLE players;
TRUNCATE TABLE planets;
TRUNCATE TABLE fleets;
TRUNCATE TABLE research;
TRUNCATE TABLE messages;
```

---

Enjoy conquering the universe! 🚀✨
