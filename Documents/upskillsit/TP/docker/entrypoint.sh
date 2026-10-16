#!/bin/sh
set -eu

PORT="${PORT:-10000}"
case "$PORT" in
    ''|*[!0-9]*) echo "PORT must be a numeric TCP port" >&2; exit 1 ;;
esac

sed -i "s/^Listen 80$/Listen 0.0.0.0:${PORT}/" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:80>/<VirtualHost 0.0.0.0:${PORT}>/" /etc/apache2/sites-available/000-default.conf

# A Render disk mounted here is owned by root; make uploaded files writable by Apache.
mkdir -p /var/www/html/public/uploads/photos /var/www/html/public/uploads/cv
chown -R www-data:www-data /var/www/html/public/uploads

exec "$@"
