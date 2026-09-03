FROM php:8.2-fpm-alpine
RUN apk add --no-cache rsyslog \
    && docker-php-ext-install pdo_mysql
COPY rsyslog-mft.conf /etc/rsyslog.d/30-mft.conf
COPY uploads.ini /usr/local/etc/php/conf.d/uploads.ini
COPY entrypoint.sh /usr/local/bin/mft-entrypoint.sh
RUN chmod +x /usr/local/bin/mft-entrypoint.sh \
    && mkdir -p /var/log/mft /var/www/storage
WORKDIR /var/www/html
CMD ["/usr/local/bin/mft-entrypoint.sh"]
