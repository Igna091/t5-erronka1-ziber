#!/bin/sh
# Prepares the application every time the container starts, then runs the command (php-fpm).
# One-off commands (docker compose run app php artisan ...) skip the preparation.
set -e

if [ "$1" = "php-fpm" ]; then
    if [ -z "$APP_KEY" ]; then
        echo "ERROR: APP_KEY is empty in .env" >&2
        echo "Generate one with: echo \"base64:\$(openssl rand -base64 32)\"" >&2
        exit 1
    fi

    if [ "${DB_CONNECTION:-sqlite}" = "sqlite" ]; then
        db="${DB_DATABASE:-database/database.sqlite}"
        if [ ! -f "$db" ]; then
            echo "Creating SQLite database: $db"
            touch "$db"
        fi
        # WAL lets pages be read while another request writes (many users at once)
        php -r '(new PDO("sqlite:" . $argv[1]))->exec("PRAGMA journal_mode=WAL");' -- "$db"
    fi

    php artisan migrate --force --no-interaction
    php artisan optimize --no-interaction
fi

exec "$@"
