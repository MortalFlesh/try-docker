#!/bin/bash
set -e

psql -v ON_ERROR_STOP=1 --username "$POSTGRES_USER" --dbname "$POSTGRES_DB" <<-EOSQL
    CREATE USER movies;
    CREATE DATABASE movies;
    GRANT ALL PRIVILEGES ON DATABASE movies TO movies;
EOSQL
