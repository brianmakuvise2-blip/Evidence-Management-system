#!/bin/bash
export DB_CONNECTION=sqlite
export DB_DATABASE=/home/runner/workspace/database/database.sqlite
export SESSION_DRIVER=file
export CACHE_STORE=file
export QUEUE_CONNECTION=sync
export SESSION_SECURE_COOKIE=true
export SESSION_SAME_SITE=none
export APP_URL="https://${REPLIT_DEV_DOMAIN}"
php artisan config:clear
php artisan view:clear
php artisan serve --host=0.0.0.0 --port=5000
