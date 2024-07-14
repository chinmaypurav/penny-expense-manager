#!/bin/sh

if [ "$ARTISAN_SCHEDULE_WORKER" != "true" ]; then
    exit 0;
fi

echo "Running Schedule Worker"

php /var/www/html/artisan schedule:work
