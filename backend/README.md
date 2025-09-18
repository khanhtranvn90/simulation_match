# Backend Service - Tournament Predictor

## Overview

This is the **backend module** of the Tournament Predictor project.  
It handles Monte Carlo simulation for a 4-team league, manages league data, caches predictions, and provides API endpoints for the frontend.

### Technical Stack

- **Laravel** (PHP Framework)  
- **Repository Pattern** & **Service Layer** for clean architecture  
- **DTOs (Data Transfer Objects)** for structured API responses  
- **Redis** for caching predicted standings  
- **MySQL** as the main database  
- **Unit Testing** with PHPUnit and Mockery  
- **Docker** for containerized development and deployment  
- **API-first design** (RESTful endpoints)  
- **PSR-12** code style & formatting  
- **Dependency Injection** for services and repositories  
- **Monte Carlo Simulation** for match predictions  

---

## Main Features

- League scheduling and match simulation  
- Standings calculation and caching in Redis  
- Prediction API with Monte Carlo simulation  
- Champion calculation and API  
- Full unit test coverage for service layer  

---

## Backend Commands

| Command | Description |
|---------|-------------|
| `league:generate-schedule {weeks=6}` | Generate league schedule for a given number of weeks |
| `league:restart` | Truncate all league data, flush Redis, and reseed teams & standings |

Run any command with:

```bash
php artisan <command>
```

Example:

```bash
php artisan league:generate-schedule
php artisan league:restart
```

---

## Folder Structure

- `app/Services` - Service layer classes (business logic)  
- `app/Repositories` - Repository interfaces and implementations  
- `app/DTO` - Data Transfer Objects  
- `tests/Unit` - Unit tests for services  
- `routes/api.php` - API route definitions  

---

## Simulation Logic

- Each team has a **strength** value.  
- For each match, goals are simulated based on:
  - `MAX_GOAL` (maximum goals)  
  - `DRAW_PROBABILITY` (chance of draw)  
  - Relative team strength  
- Simulation runs **`MC_SIMULATIONS` times** for all matches over `LEAGUE_DURATION` weeks.  
- Outputs:
  - Predicted match outcomes  
  - Expected points and standings  
  - Champion probabilities  
- Predictions are cached in **Redis** for quick retrieval.  

---

## Database Tables & Relationships

### `teams`
- `team_id` (PK)  
- `name`  
- `strength`  
- `created_at`  
**Relationships:**  
- 1-to-1 with `standings`  
- 1-to-many with `matches` (as home or away team)  

### `matches`
- `match_id` (PK)  
- `team1_id` (FK → teams.team_id)  
- `team2_id` (FK → teams.team_id)  
- `team1_score`  
- `team2_score`  
- `week`  
- `status` (`scheduled`/`finished`)  
**Relationships:**  
- Many-to-1 with `teams` (team1 and team2)  

### `standings`
- `team_id` (PK, FK → teams.team_id)  
- `played`, `wins`, `draws`, `losses`  
- `goals_for`, `goals_against`, `goal_difference`, `points`  
**Relationships:**  
- 1-to-1 with `teams`  

---

## Unit Tests

- Written using **PHPUnit**.  
- Tested areas:
  - Simulation logic correctness  
  - Standings update after matches  
  - Repository operations  
- Run tests with:

```bash
php artisan test
```

or:

```bash
vendor/bin/phpunit
```

---

## Notes

- Backend is **API-driven** and decoupled from frontend.  
- Redis is used for caching predicted standings for performance.  
- Monte Carlo parameters are configurable via `.env`.
