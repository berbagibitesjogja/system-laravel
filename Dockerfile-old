FROM dunglas/frankenphp:php8.4

ENV SERVER_NAME=":80"

WORKDIR /app

RUN apt-get update && apt-get install -y \
    supervisor \
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

COPY --from=composer:2.2 /usr/bin/composer /usr/bin/composer

COPY . /app

RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction

RUN php artisan storage:link || true

COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY php.ini /usr/local/etc/php/conf.d/custom.ini

CMD ["supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
