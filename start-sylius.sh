#!/bin/bash

# Start MySQL
echo "Starting MySQL..."
sudo service mysql start

# Wait for MySQL to be ready
sleep 2

# Start PHP development server
echo "Starting PHP development server on port 8000..."
cd /workspaces/Sylius
php -S 0.0.0.0:8000 -t public

