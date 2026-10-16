FROM php:8.2-apache

RUN apt-get update \
    && apt-get install -y --no-install-recommends libonig-dev \
    && docker-php-ext-install pdo_mysql mbstring \
    && a2enmod rewrite \
    && apt-get purge -y --auto-remove libonig-dev \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html
COPY . /var/www/html

RUN mkdir -p /var/www/html/public/uploads/photos /var/www/html/public/uploads/cv \
    && chown -R www-data:www-data /var/www/html/public/uploads \
    && chmod -R 775 /var/www/html/public/uploads

COPY docker/apache-vhost.conf /etc/apache2/sites-available/000-default.conf
COPY docker/php.ini /usr/local/etc/php/conf.d/upskillsit.ini
COPY docker/entrypoint.sh /usr/local/bin/upskillsit-entrypoint
RUN chmod +x /usr/local/bin/upskillsit-entrypoint

EXPOSE 10000
ENTRYPOINT ["upskillsit-entrypoint"]
CMD ["apache2-foreground"]
