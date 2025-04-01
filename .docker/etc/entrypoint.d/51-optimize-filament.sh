#!/bin/sh

############################################################################
# artisan filament:optimize
############################################################################

printf "🚀 Caching filament assets\n"
php "$APP_BASE_DIR/artisan" filament:optimize
