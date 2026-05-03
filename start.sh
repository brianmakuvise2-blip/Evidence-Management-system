#!/bin/bash
export DB_CONNECTION=sqlite
export DB_DATABASE=/home/runner/workspace/database/database.sqlite
export SESSION_DRIVER=file
export CACHE_STORE=file
export QUEUE_CONNECTION=sync
php artisan config:clear
php artisan serve --host=0.0.0.0 --port=5000
