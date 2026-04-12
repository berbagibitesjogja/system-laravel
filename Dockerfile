FROM dunglas/frankenphp:php8.4 AS php_base

ENV SERVER_NAME=":80"
WORKDIR /app

RUN apt-get update && apt-get install -y --no-install-recommends \
    supervisor \
    git \
    unzip \
    && rm -rf /var/lib/apt/lists/*

RUN install-php-extensions \
    pdo_mysql \
    mbstring \
    tokenizer \
    intl \
    pcntl \
    bcmath \
    exif \
    gd \
    zip

COPY --from=composer:2.8 /usr/bin/composer /usr/bin/composer

FROM php_base AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --prefer-dist \
    --no-interaction \
    --no-progress \
    --optimize-autoloader \
    --classmap-authoritative \
    --no-scripts

FROM node:22-alpine AS frontend
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY resources ./resources
COPY public ./public
COPY vite.config.js postcss.config.js tailwind.config.js ./
RUN npm run build

FROM php_base AS runtime
WORKDIR /app

ENV APP_ENV=production \
    APP_DEBUG=false \
    SERVER_NAME=":80"

COPY . /app
COPY --from=vendor /app/vendor /app/vendor
COPY --from=frontend /app/public/build /app/public/build

COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY php.ini /usr/local/etc/php/conf.d/custom.ini

RUN mkdir -p storage/framework/cache storage/framework/sessions storage/framework/testing storage/framework/views storage/logs bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && php artisan storage:link || true \
    && php artisan config:cache || true \
    && php artisan route:cache || true \
    && php artisan view:cache || true

EXPOSE 80
CMD ["supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
