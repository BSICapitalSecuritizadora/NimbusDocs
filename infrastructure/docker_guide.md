# Docker Guide for NimbusDocs

This project fully supports Docker for a consistent development environment.

## Prerequisites
- [Docker Desktop](https://www.docker.com/products/docker-desktop/) (Windows/Mac/Linux)
- [Git](https://git-scm.com/)

## Quick Start

1.  **Clone the repository** (if you haven't already).
2.  **Copy Environment File**:
    ```bash
    cp .env.example .env
    ```
    *Update `.env` with `DB_HOST=db` and `DB_PASSWORD=root` (or whatever you set in docker-compose).*

3.  **Start the Environment**:
    ```bash
    docker-compose up -d
    ```
    First run will take a few minutes to build the image and download MySQL.

4.  **Access the App**:
    -   Web App: [http://localhost:8080](http://localhost:8080)
    -   PhpMyAdmin: [http://localhost:8081](http://localhost:8081)

## Commands

| Action | Command |
|--------|---------|
| Start | `docker-compose up -d` |
| Stop | `docker-compose stop` |
| Rebuild | `docker-compose up -d --build` |
| Shell Access | `docker-compose exec app bash` |
| Run Migrations | `docker-compose exec app php bin/migrate.php` |
| Run Tests | `docker-compose exec app composer test:unit` |

## Scheduled Reports (Cron)

To enable the automatic generation and dispatch of Scheduled Reports (relatórios agendados), you must configure a cron job on your host server. This cron job will execute the internal Worker script inside the Docker container.

1. Open your host's crontab:
   ```bash
   crontab -e
   ```
2. Add the following line to run the scheduler every hour (adjust `cd` path to where your `docker-compose.yml` is located):
   ```bash
   0 * * * * cd /path/to/NimbusDocs && docker-compose exec -T app php bin/run_scheduled_reports.php >> storage/logs/cron.log 2>&1
   ```
   *Note: Using `-T` disables pseudo-TTY allocation, which is required when running from cron.*
