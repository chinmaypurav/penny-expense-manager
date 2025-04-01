#!/bin/sh

############################################################################
# artisan filament:cache
############################################################################

printf "🚀 Caching filament assets\n"
php "$APP_BASE_DIR/artisan" filament:optimize
