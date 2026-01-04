FROM php:8.2-fpm-alpine

# Install PostgreSQL client and development libraries
RUN apk add --no-cache postgresql-dev \
    && docker-php-ext-install pdo_pgsql

WORKDIR /var/www/html
