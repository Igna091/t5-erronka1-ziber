# EduCenter production images (multi-stage):
#   app -> PHP-FPM with the application, runs as www-data
#   web -> nginx: serves public/ over HTTPS and sends PHP requests to "app"
# Built by compose.yaml: docker compose up -d --build

ARG PHP_VERSION=8.5

# ---------- PHP runtime shared by every PHP stage ----------
FROM php:${PHP_VERSION}-fpm-alpine AS php-base

# Alpine security fixes the base image doesn't have yet, production php.ini,
# and OPcache (built in from PHP 8.5; older versions only need it enabled)
RUN apk upgrade --no-cache \
    && mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" \
    && if ! php -m | grep -qi opcache; then docker-php-ext-enable opcache; fi

WORKDIR /var/www/html

# ---------- Composer dependencies, without dev packages ----------
FROM php-base AS vendor

ENV COMPOSER_ALLOW_SUPERUSER=1
RUN apk add --no-cache git unzip
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Dependencies first, so this layer stays cached when only the code changes
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --no-interaction --no-progress --prefer-dist

COPY . .
RUN mkdir -p database/data storage/app/private storage/app/public storage/framework/cache/data \
        storage/framework/sessions storage/framework/views storage/logs \
    && composer dump-autoload --no-dev --optimize --no-interaction

# ---------- Application: PHP-FPM ----------
FROM php-base AS app

COPY docker/php/app.ini "$PHP_INI_DIR/conf.d/zz-app.ini"
COPY docker/php/fpm-pool.conf /usr/local/etc/php-fpm.d/zz-app.conf
COPY docker/php/entrypoint.sh /usr/local/bin/app-entrypoint
COPY --from=vendor /var/www/html /var/www/html

# The code stays owned by root so PHP can't modify it; PHP only writes to these folders
RUN chmod 755 /usr/local/bin/app-entrypoint \
    && chown root:root /var/www/html && chmod 755 /var/www/html \
    && chown -R www-data:www-data storage bootstrap/cache database/data

USER www-data

ENTRYPOINT ["app-entrypoint"]
CMD ["php-fpm"]

# ---------- Web server: nginx ----------
FROM nginx:stable-alpine AS web

# Alpine security fixes the base image doesn't have yet; the stock site is replaced by our template
RUN apk upgrade --no-cache && apk add --no-cache openssl \
    && rm /etc/nginx/conf.d/default.conf
COPY docker/nginx/default.conf.template /etc/nginx/templates/default.conf.template
COPY docker/nginx/self-signed-cert.sh /docker-entrypoint.d/40-self-signed-cert.sh
RUN chmod 755 /docker-entrypoint.d/40-self-signed-cert.sh

# Only public/ is copied here: .env, the database, the logs and the code never reach this container
COPY public /var/www/html/public
