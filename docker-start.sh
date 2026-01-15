#!/bin/bash

echo "========================================"
echo "VoiceTrackBpl - Docker Startup Script"
echo "========================================"
echo ""

echo "[1/5] Copying .env file..."
cp .env.docker .env

echo ""
echo "[2/5] Building Docker images..."
docker-compose up -d --build

echo ""
echo "[3/5] Waiting for MySQL to be ready..."
sleep 30

echo ""
echo "[4/5] Running database migrations..."
docker-compose exec -T app php artisan migrate --force

echo ""
echo "[5/5] Creating admin user..."
docker-compose exec -T app php artisan db:seed --class=AdminSeeder --force

echo ""
echo "========================================"
echo "Setup Complete!"
echo "========================================"
echo ""
echo "Website: http://localhost:8086"
echo "phpMyAdmin: http://localhost:8090"
echo ""
echo "Admin Login:"
echo "  Username: admin"
echo "  Password: admin123"
echo ""
