#!/bin/sh
set -e
APP_PORT=${PORT:-10000}
sed -i "s/__PORT__/$APP_PORT/g" /etc/apache2/sites-available/000-default.conf
sed -i "s/Listen 80/Listen $APP_PORT/g" /etc/apache2/ports.conf
exec "$@"