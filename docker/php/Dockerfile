FROM php:8.2-fpm-alpine

# Системные зависимости
RUN apk add --no-cache \
    bash git curl unzip icu-dev oniguruma-dev \
    postgresql-dev \
    && docker-php-ext-install \
      intl mbstring opcache pdo pdo_pgsql

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Add User
RUN addgroup -g 1000 app && adduser -G app -g app -s /bin/sh -D -u 1000 app
USER app

WORKDIR /var/www/html
