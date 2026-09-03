#!/bin/sh
set -eu

mkdir -p /var/log/mft /var/www/storage
chown -R www-data:www-data /var/log/mft /var/www/storage
chmod -R 775 /var/log/mft /var/www/storage

# rsyslog creates /dev/log for PHP's syslog() calls.
rsyslogd
exec php-fpm -F
