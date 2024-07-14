#!/bin/sh

if [ "$ARTISAN_QUEUE_WORKER" != "true" ]; then
    exit 0;
fi

echo "Running Queue Worker"

php artisan queue:work
