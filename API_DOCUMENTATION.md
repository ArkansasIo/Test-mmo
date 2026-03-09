# Scifi Conquest - API Documentation

## Base URL
```
http://localhost/api
```

## Authentication

### Login
**POST** `/auth/login`

Request:
```json
{
  "email": "user@example.com",
  "password": "password123"
}
```

Response:
```json
{
  "status": "success",
  "message": "Login successful",
  "data": {
    "token": "eyJhbGciOiJIUzI1NiIs...",
    "user_id": 1,
    "username": "PlayerName",
    "expires_in": 3600
  }
}
```

### Register
**POST** `/auth/register`

Request:
```json
{
  "username": "newplayer",
  "email": "player@example.com",
  "password": "SecurePass123"
}
```

Response:
```json
{
  "status": "success",
  "message": "Registration successful",
  "data": {
    "player_id": 123,
    "username": "newplayer"
  }
}
```

### Logout
**POST** `/auth/logout`

Headers: `Authorization: Bearer YOUR_TOKEN`

Response:
```json
{
  "status": "success",
  "message": "Logged out successfully"
}
```

### Refresh Token
**POST** `/auth/refresh-token`

Headers: `Authorization: Bearer YOUR_TOKEN`

Response:
```json
{
  "status": "success",
  "data": {
    "token": "new_token_here",
    "expires_in": 3600
  }
}
```

---

## Player Endpoints

### Get Player Profile
**GET** `/player`

Headers: `Authorization: Bearer YOUR_TOKEN`

Response:
```json
{
  "status": "success",
  "data": {
    "id": 1,
    "username": "PlayerName",
    "email": "player@example.com",
    "level": 5,
    "experience": 12450,
    "achievement_points": 350,
    "total_credits": 50000,
    "total_minerals": 75000,
    "total_gas": 25000,
    "alliance_id": 2,
    "planets_count": 3,
    "fleets_count": 2,
    "created_at": "2024-01-15T10:30:00Z"
  }
}
```

### Update Player Settings
**POST** `/player/settings`

Headers: 
- `Authorization: Bearer YOUR_TOKEN`
- `Content-Type: application/json`

Request:
```json
{
  "theme": "light",
  "language": "en",
  "notifications_enabled": true,
  "sound_enabled": false
}
```

Response:
```json
{
  "status": "success",
  "message": "Settings updated"
}
```

### Get Player Resources
**GET** `/player/resources`

Headers: `Authorization: Bearer YOUR_TOKEN`

Response:
```json
{
  "status": "success",
  "data": {
    "credits": 150000,
    "minerals": 250000,
    "gas": 100000,
    "research_points": 500
  }
}
```

### Get Player Achievements
**GET** `/player/achievements`

Headers: `Authorization: Bearer YOUR_TOKEN`

Response:
```json
{
  "status": "success",
  "data": [
    {
      "key": "first_planet",
      "title": "First Colony",
      "awarded_at": "2024-01-15T12:00:00Z",
      "icon": "/assets/badges/first_planet.png"
    }
  ]
}
```

---

## Planet Endpoints

### Get All Planets
**GET** `/planets`

Headers: `Authorization: Bearer YOUR_TOKEN`

Query Parameters:
- `page` (int, default: 1)
- `limit` (int, default: 10)
- `sort` (string, default: 'id')

Response:
```json
{
  "status": "success",
  "data": [
    {
      "id": 1,
      "name": "Earth Colony",
      "galaxy_id": 1,
      "coordinates_x": 100,
      "coordinates_y": 150,
      "planet_type": "terrestrial",
      "size": "large",
      "population": 500000,
      "morale": 75,
      "buildings_count": 12
    }
  ],
  "meta": {
    "pagination": {
      "page": 1,
      "limit": 10,
      "total": 45,
      "pages": 5
    }
  }
}
```

### Get Planet Details
**GET** `/planets/{planet_id}`

Headers: `Authorization: Bearer YOUR_TOKEN`

Response:
```json
{
  "status": "success",
  "data": {
    "id": 1,
    "name": "Earth Colony",
    "owner_id": 123,
    "planet_type": "terrestrial",
    "population": 500000,
    "morale": 75,
    "defenses_level": 3,
    "buildings": [
      {
        "id": 1,
        "type": "Farm",
        "level": 5,
        "health": 100
      }
    ],
    "resources": {
      "credits": 50000,
      "minerals": 100000,
      "gas": 25000
    }
  }
}
```

### Build Structure
**POST** `/planets/{planet_id}/build`

Headers: `Authorization: Bearer YOUR_TOKEN`

Request:
```json
{
  "building_type": "Farm",
  "quantity": 1
}
```

Response:
```json
{
  "status": "success",
  "message": "Building construction started",
  "data": {
    "building_id": 15,
    "building_type": "Farm",
    "completion_time": 3600,
    "completion_at": "2024-01-15T12:30:00Z"
  }
}
```

### Upgrade Building
**POST** `/planets/{planet_id}/buildings/{building_id}/upgrade`

Headers: `Authorization: Bearer YOUR_TOKEN`

Response:
```json
{
  "status": "success",
  "message": "Building upgrade started",
  "data": {
    "building_id": 15,
    "new_level": 6,
    "completion_time": 3600
  }
}
```

---

## Fleet Endpoints

### Get All Fleets
**GET** `/fleets`

Headers: `Authorization: Bearer YOUR_TOKEN`

Response:
```json
{
  "status": "success",
  "data": [
    {
      "id": 1,
      "name": "Battle Fleet Alpha",
      "status": "stationed",
      "current_location": 1,
      "total_ships": 25,
      "attack_power": 5000,
      "defense_power": 3000,
      "morale": 85
    }
  ]
}
```

### Get Fleet Details
**GET** `/fleets/{fleet_id}`

Headers: `Authorization: Bearer YOUR_TOKEN`

Response:
```json
{
  "status": "success",
  "data": {
    "id": 1,
    "name": "Battle Fleet Alpha",
    "status": "stationed",
    "current_location": 1,
    "total_ships": 25,
    "ships": [
      {
        "type": "Fighter",
        "count": 15,
        "health": 100,
        "attack_power": 100
      },
      {
        "type": "Destroyer",
        "count": 10,
        "health": 150,
        "attack_power": 250
      }
    ],
    "cargo_capacity": {
      "total": 100000,
      "used": 45000,
      "available": 55000
    }
  }
}
```

### Create Fleet
**POST** `/fleets/create`

Headers: `Authorization: Bearer YOUR_TOKEN`

Request:
```json
{
  "name": "New Fleet",
  "location": 1,
  "ships": {
    "Fighter": 10,
    "Destroyer": 5,
    "Cruiser": 2
  }
}
```

Response:
```json
{
  "status": "success",
  "message": "Fleet created",
  "data": {
    "fleet_id": 3,
    "name": "New Fleet"
  }
}
```

### Move Fleet
**POST** `/fleets/{fleet_id}/move`

Headers: `Authorization: Bearer YOUR_TOKEN`

Request:
```json
{
  "destination": 5,
  "speed": "normal"
}
```

Response:
```json
{
  "status": "success",
  "message": "Fleet is moving",
  "data": {
    "fleet_id": 1,
    "destination": 5,
    "eta_arrival": "2024-01-15T14:30:00Z",
    "travel_time": 7200
  }
}
```

### Attack Fleet Launch
**POST** `/fleets/{fleet_id}/attack`

Headers: `Authorization: Bearer YOUR_TOKEN`

Request:
```json
{
  "target_fleet_id": 25,
  "target_planet_id": 5
}
```

Response:
```json
{
  "status": "success",
  "message": "Attack launched",
  "data": {
    "battle_id": 123,
    "fleet_id": 1,
    "target": 5,
    "eta_arrival": "2024-01-15T13:00:00Z"
  }
}
```

---

## Research Endpoints

### Get Technologies
**GET** `/research/technologies`

Headers: `Authorization: Bearer YOUR_TOKEN`

Response:
```json
{
  "status": "success",
  "data": [
    {
      "id": 1,
      "name": "Advanced Mining",
      "category": "Resource",
      "description": "Increases mining output",
      "cost": {
        "credits": 5000,
        "research_points": 100
      },
      "research_time": 1800,
      "available": true,
      "prerequisites": [],
      "progress": 0
    }
  ]
}
```

### Start Research
**POST** `/research/start`

Headers: `Authorization: Bearer YOUR_TOKEN`

Request:
```json
{
  "technology_id": 1
}
```

Response:
```json
{
  "status": "success",
  "message": "Research started",
  "data": {
    "technology_id": 1,
    "research_time": 1800,
    "completion_at": "2024-01-15T12:30:00Z"
  }
}
```

### Get Research Status
**GET** `/research/status`

Headers: `Authorization: Bearer YOUR_TOKEN`

Response:
```json
{
  "status": "success",
  "data": {
    "current_research": {
      "technology_id": 1,
      "name": "Advanced Mining",
      "progress": 45,
      "time_remaining": 990
    },
    "completed": [1, 3, 5],
    "queued": []
  }
}
```

---

## Combat Endpoints

### Get Battle History
**GET** `/battles`

Headers: `Authorization: Bearer YOUR_TOKEN`

Query Parameters:
- `page` (int, default: 1)
- `limit` (int, default: 20)

Response:
```json
{
  "status": "success",
  "data": [
    {
      "id": 123,
      "attacker": {
        "id": 5,
        "username": "Enemy Player",
        "fleet_id": 10
      },
      "defender": {
        "id": 1,
        "username": "Your Name",
        "fleet_id": 1
      },
      "location": 5,
      "winner": "attacker",
      "attacker_casualties": 5,
      "defender_casualties": 12,
      "loot": {
        "credits": 25000,
        "minerals": 50000
      },
      "created_at": "2024-01-15T10:30:00Z"
    }
  ]
}
```

### Get Battle Details
**GET** `/battles/{battle_id}`

Headers: `Authorization: Bearer YOUR_TOKEN`

Response:
```json
{
  "status": "success",
  "data": {
    "id": 123,
    "attacker_fleet": [...],
    "defender_fleet": [...],
    "rounds": [
      {
        "round": 1,
        "attacker_ships_destroyed": 2,
        "defender_ships_destroyed": 3
      }
    ],
    "winner": "attacker",
    "loot": {...}
  }
}
```

---

## Alliance Endpoints

### Get Alliance Info
**GET** `/alliances/{alliance_id}`

Headers: `Authorization: Bearer YOUR_TOKEN`

Response:
```json
{
  "status": "success",
  "data": {
    "id": 2,
    "name": "Phoenix Alliance",
    "tag": "PHX",
    "description": "Elite gaming alliance",
    "leader": {
      "id": 10,
      "username": "LeaderName"
    },
    "members_count": 45,
    "level": 5,
    "treasury": 500000,
    "founded_at": "2023-06-15T00:00:00Z"
  }
}
```

### Create Alliance
**POST** `/alliances/create`

Headers: `Authorization: Bearer YOUR_TOKEN`

Request:
```json
{
  "name": "New Alliance",
  "tag": "TAG",
  "description": "Alliance description"
}
```

Response:
```json
{
  "status": "success",
  "message": "Alliance created",
  "data": {
    "alliance_id": 5,
    "name": "New Alliance"
  }
}
```

### Join Alliance
**POST** `/alliances/{alliance_id}/join`

Headers: `Authorization: Bearer YOUR_TOKEN`

Response:
```json
{
  "status": "success",
  "message": "Joined alliance"
}
```

### Alliance Members
**GET** `/alliances/{alliance_id}/members`

Headers: `Authorization: Bearer YOUR_TOKEN`

Response:
```json
{
  "status": "success",
  "data": [
    {
      "player_id": 1,
      "username": "Player1",
      "rank": "leader",
      "joined_at": "2023-06-15T00:00:00Z",
      "contribution": 500000
    }
  ]
}
```

---

## Game Status Endpoints

### Get Game Status
**GET** `/game/status`

Response:
```json
{
  "status": "success",
  "data": {
    "server_time": "2024-01-15T11:30:00Z",
    "players_online": 1250,
    "total_players": 5000,
    "server_uptime": 864000,
    "maintenance": false
  }
}
```

### Get Universe
**GET** `/game/universe`

Query Parameters:
- `galaxy_id` (int)
- `x` (int)
- `y` (int)
- `range` (int, default: 10)

Response:
```json
{
  "status": "success",
  "data": {
    "planets": [...],
    "players": [...],
    "coordinates": {
      "center_x": 100,
      "center_y": 150,
      "range": 10
    }
  }
}
```

---

## Admin Endpoints

### Admin Login
**POST** `/admin/login`

Request:
```json
{
  "username": "admin",
  "password": "adminpass"
}
```

Response:
```json
{
  "status": "success",
  "data": {
    "token": "admin_token",
    "role": "admin"
  }
}
```

### Get Server Statistics
**GET** `/admin/stats`

Headers: 
- `Authorization: Bearer ADMIN_TOKEN`
- `X-Admin-Key: admin_key`

Response:
```json
{
  "status": "success",
  "data": {
    "total_players": 5000,
    "active_players": 1250,
    "online_players": 245,
    "banned_players": 42,
    "total_alliances": 150,
    "database_size": "256.45",
    "server_uptime": 864000
  }
}
```

### Get System Logs
**GET** `/admin/logs`

Headers: `Authorization: Bearer ADMIN_TOKEN`

Query Parameters:
- `level` (string: debug|info|warning|error)
- `days` (int, default: 1)
- `limit` (int, default: 100)

Response:
```json
{
  "status": "success",
  "data": [
    {
      "id": 1,
      "level": "error",
      "action": "database_error",
      "message": "Connection timeout",
      "created_at": "2024-01-15T11:00:00Z"
    }
  ]
}
```

### Ban Player
**POST** `/admin/players/{player_id}/ban`

Headers: `Authorization: Bearer ADMIN_TOKEN`

Request:
```json
{
  "reason": "Rule violation",
  "duration": 30
}
```

Response:
```json
{
  "status": "success",
  "message": "Player banned successfully"
}
```

### Unban Player
**POST** `/admin/players/{player_id}/unban`

Headers: `Authorization: Bearer ADMIN_TOKEN`

Request:
```json
{
  "reason": "Appeal accepted"
}
```

Response:
```json
{
  "status": "success",
  "message": "Player unbanned"
}
```

### Issue Warning
**POST** `/admin/players/{player_id}/warn`

Headers: `Authorization: Bearer ADMIN_TOKEN`

Request:
```json
{
  "reason": "Spam",
  "severity": "medium"
}
```

Response:
```json
{
  "status": "success",
  "message": "Warning issued"
}
```

---

## Error Responses

### Authentication Error (401)
```json
{
  "status": "unauthorized",
  "message": "Please login first"
}
```

### Validation Error (422)
```json
{
  "status": "validation_error",
  "message": "Validation failed",
  "errors": {
    "email": "Invalid email format",
    "password": "Password must be at least 8 characters"
  }
}
```

### Not Found Error (404)
```json
{
  "status": "not_found",
  "message": "Resource not found"
}
```

### Server Error (500)
```json
{
  "status": "error",
  "message": "Internal server error"
}
```

---

## Rate Limiting

- Default: 60 requests per minute per IP
- Authenticated: 300 requests per minute per user
- Admin: Unlimited

Response headers:
```
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 59
X-RateLimit-Reset: 1642254660
```

---

## Pagination

Default page size: 10
Maximum page size: 100

Query parameters:
- `page` - Page number (default: 1)
- `limit` - Items per page (default: 10)
- `sort` - Sort field (with optional `-` prefix for DESC)

Example: `/api/players?page=2&limit=20&sort=-created_at`

---

**Last Updated:** 2024
**Version:** 1.0
