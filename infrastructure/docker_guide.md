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

## Troubleshooting

-   **Port Conflicts**: If port 8080 or 3306 is in use, edit `docker-compose.yml` to change the mapping (e.g., `"8888:80"`).
-   **Database Connection**: Ensure `.env` has `DB_HOST=db`. XAMPP uses `localhost`, but Docker uses the service name `db`.
