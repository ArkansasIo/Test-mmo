# Sci-Fi: Conquest - Awakening

> A feature-rich browser-based MMORPG with real-time resource management, fleet combat, research trees, and comprehensive multiplayer gameplay.

**Status:** ✅ Production Ready | **PHP:** 8.0+ | **Database:** MariaDB 12.1+ | **Repository:** [GitHub](https://github.com/ArkansasIo/Test-mmo)

---

## 🚀 Quick Start (2 Minutes)

```powershell
# 1. Navigate to project
cd "D:\scifi-Conquest-Awakening-main\scifi-Conquest-Awakening-main"

# 2. Start the server
.\run-dev.ps1

# 3. Open browser and login
# http://localhost:8000/Index/dev-bypass.php
# OR
# http://localhost:8000/Index/index.php?dev_login=6
```

**Test Account:**
- Username: `testuser_1773040869`
- Password: `test123`

---

## 🎮 Features

### Empire Management
✅ Multiple planets with independent economies  
✅ 11+ building types (mines, factories, reactors)  
✅ Real-time resource production (Metal, Crystal, Deuterium, Energy)  
✅ Build queue with progress tracking  

### Fleet & Combat
✅ Shipyard to design and build spacecraft  
✅ Fleet management and movement  
✅ Real-time combat system with damage calculations  
✅ Defense systems (turrets, shields)  

### Progression & Research
✅ Technology tree system  
✅ Unlock capabilities through research  
✅ Level-based progression  

### Social & Diplomacy
✅ Alliance system  
✅ Player messaging  
✅ Marketplace for trading  
✅ Global rankings  

### Administration
✅ Admin panel for player management  
✅ Event logging and statistics  
✅ In-game notifications  
✅ Comprehensive analytics  

---

## 📋 System Requirements

| Component | Requirement |
|-----------|------------|
| PHP | 8.0+ |
| MariaDB | 12.1+ |
| Browser | Modern (Chrome, Firefox, Edge) |
| RAM | 4GB+ recommended |

---

## 📂 Project Structure

- **Classes:** 29 core game logic classes
- **Pages:** 13 game pages
- **Database:** 21 tables with full schema
- **PHP Files:** 54 total (all validated)
- **Code:** ~15,000+ lines

**See [DEVELOPMENT.md](DEVELOPMENT.md) for detailed documentation.**

---

## 🎨 User Interface

### OGame-Style Design
- Dark sci-fi theme with blue/purple colors
- Top navigation bar with dropdowns
- Collapsible left sidebar (9 sections)
- Real-time resource counters
- Responsive design (desktop/mobile)

### Sidebar Sections
1. Overview | 2. Resources | 3. Construction | 4. Technology | 5. Fleet | 6. Combat | 7. Diplomacy | 8. Information | 9. Account

---

## ✅ Validation Report

```
✅ 54/54 PHP files - Syntax valid
✅ All critical methods present
✅ Database connected and verified
✅ Session management working
✅ No compilation errors
```

---

## 🛠️ Installation

### Option 1: Automatic (Recommended)
```powershell
.\run-dev.ps1
```

### Option 2: Manual
```powershell
# Start database
net start MariaDB

# Create database
mysql -u root < .\Index\Db\Dbgame.sql

# Start PHP server
php -S localhost:8000 -t .\Index

# Access
Start-Process "http://localhost:8000/index.php"
```

---

## 📖 Documentation

- **[DEVELOPMENT.md](DEVELOPMENT.md)** - Comprehensive development guide
- **[SECURITY.md](SECURITY.md)** - Security best practices
- **[QUICKSTART.md](QUICKSTART.md)** - Getting started

---

## 🔐 Security

✅ BCRYPT password hashing  
✅ Prepared statements (SQL injection prevention)  
✅ Session validation (user agent, IP)  
✅ Input validation and sanitization  
✅ Admin access controls  
✅ Event logging for audit trail  

---

## 📊 Game Statistics

| Stat | Value |
|------|-------|
| Total Classes | 29 |
| Database Tables | 21 |
| Game Pages | 13 |
| Buildings | 11+ types |
| Ships | Multiple types |
| Max Players | Unlimited |

---

## 🎯 Gameplay Flow

1. **Create Account** → Register with username/password
2. **Start Empire** → Get first planet with starting resources
3. **Build Infrastructure** → Construct buildings to increase production
4. **Research Technology** → Unlock new capabilities
5. **Build Fleet** → Construct ships for combat
6. **Engage in Combat** → Attack other players or defend
7. **Join Alliance** → Team up with other players
8. **Climb Rankings** → Compete for top positions

---

## 🚀 Running the Game

### Development Server
```powershell
.\run-dev.ps1
```
Starts PHP built-in server on `http://localhost:8000`

### Game Tick (Optional)
```powershell
.\run-game-tick.ps1
```
Runs background tasks and automated gameplay

### Validation
```powershell
.\validate.ps1        # Check all PHP files
.\health-check.ps1    # Verify system health
```

---

## 🔗 Links

- **Play Now:** http://localhost:8000/Index/index.php
- **Dev Bypass:** http://localhost:8000/Index/dev-bypass.php
- **GitHub:** https://github.com/ArkansasIo/Test-mmo

---

## 📝 License

Open source - See LICENSE file for details

---

## 👥 Contributors

Development team behind Sci-Fi: Conquest - Awakening

---

**Last Updated:** March 9, 2026 | **Status:** Production Ready ✅
