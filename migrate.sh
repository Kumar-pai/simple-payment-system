#!/bin/sh

sleep 30

/usr/local/bin/php /var/www/app/artisan migrate

exec "$@"
