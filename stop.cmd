@echo off
if not exist ".env" copy .env-dist .env > NULL
@echo on
docker-compose down --remove-orphans