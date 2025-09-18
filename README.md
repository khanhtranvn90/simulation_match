# Tournament Predictor

## Overview

This project is a **tournament result predictor** for a 4-team league using the **Monte Carlo simulation method**.  
It is built with **Laravel** (backend), **VueJS** (frontend), **Redis** (cache/queue), and **MySQL** (database).

The simulation parameters are configurable via `.env`:

- `LEAGUE_DURATION=8` → Number of weeks in the league  
- `MC_SIMULATIONS=10000` → Number of Monte Carlo simulations  
- `MAX_GOAL=5` → Maximum goals a team can score in a match  
- `DRAW_PROBABILITY=0.05` → Probability of a draw in a match  

## Getting Started

### Build & Run

You can build and start the project using:

```bash
make build
make up
```

Once running, access the app in your browser at:

```
http://127.0.0.1:5173
```

### Makefile Commands

The project includes a Makefile for common tasks. You can see all available commands by running:

```bash
make help
```

Example output:

```
Available commands:
  make up        - Start Docker Compose services in detached mode
  make down      - Stop and remove Docker Compose services
  make build     - Build Docker Compose services
  make restart   - Restart Docker Compose services
  make migrate   - Run Laravel migrations
  make seed      - Run Laravel seeders
  make shell     - Access Laravel container shell
  make redis-cli - Access Redis CLI
  make remove-all - Remove all containers, images, and volumes
  make rebuild   - Remove all resources and rebuild from scratch
  make help      - Show this help message
```

## Simulation Logic

The tournament simulator works as follows:

- Each team is assigned a **strength** (evenly distributed by default).  
- For each match, the simulator generates the number of goals for both teams based on:
  - `MAX_GOAL` (maximum goals possible)  
  - `DRAW_PROBABILITY` (chance of draw)  
  - Relative team strength to bias the result toward stronger teams.  
- The simulation runs **`MC_SIMULATIONS` times** for the entire league schedule (`LEAGUE_DURATION` weeks).  
- After all simulations, it calculates **probabilities for each team**:
  - Likelihood to win, draw, or lose each match  
  - Predicted points and final standings  
  - Champion probabilities  
- The simulator outputs a **predicted league table** showing expected standings.

## Features

- Monte Carlo simulation to predict league outcomes  
- Configurable league duration and simulation count  
- Adjustable goal limits and draw probability  
- Simple frontend dashboard to view predicted standings

## Notes

For more details about backend implementation, commands, Redis caching, and tests, see `backend/README.md`.

