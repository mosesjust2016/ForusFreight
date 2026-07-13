#!/bin/bash

set -e

ZIP_NAME="forusfreight-$(date +%Y%m%d-%H%M%S).zip"

echo "Creating deployment zip: $ZIP_NAME"

zip -r "$ZIP_NAME" . \
    -x "node_modules/*" \
    -x "vendor/*" \
    -x ".env" \
    -x ".env.backup" \
    -x ".DS_Store" \
    -x ".claude/*" \
    -x "tests/*" \
    -x "web.zip" \
    -x "*.zip" \
    -x ".phpunit.result.cache" \
    -x "storage/logs/*" \
    -x "storage/framework/cache/*" \
    -x "storage/framework/sessions/*" \
    -x "storage/framework/views/*" \
    -x "bootstrap/cache/*"

echo "Done: $ZIP_NAME"
echo "Size: $(du -h "$ZIP_NAME" | cut -f1)"
