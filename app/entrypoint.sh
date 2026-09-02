#!/bin/sh
set -eu
mkdir -p /var/log/mft
# rsyslog creates /dev/log for PHP's syslog() calls.
rsyslogd
exec php-fpm -F
