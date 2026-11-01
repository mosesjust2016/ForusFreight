#!/bin/bash

set -e

bash scripts/check-deployment.sh

ZIP_NAME="forusfreight-$(date +%Y%m%d-%H%M%S).zip"

echo "Creating deployment zip: $ZIP_NAME"

zip -0rq "$ZIP_NAME" \
    app bootstrap config database public resources routes \
    artisan composer.json composer.lock \
    -x "public/storage" \
    -x "public/storage/*" \
    -x "storage/app/public/*" \
    -x "storage/logs/*" \
    -x "storage/framework/cache/*" \
    -x "storage/framework/sessions/*" \
    -x "storage/framework/views/*" \
    -x "bootstrap/cache/*" \
    -x "database/database.sqlite"

echo "Done: $ZIP_NAME"
echo "Size: $(du -h "$ZIP_NAME" | cut -f1)"
