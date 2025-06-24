FROM php:8.3-fpm-alpine

RUN apk add --no-cache \
    nginx \
    bash \
    git \
    unzip \
    curl \
    libzip-dev \
    libpng-dev \
    oniguruma-dev \
    libxml2-dev \
    supervisor \
    && docker-php-ext-install pdo pdo_mysql zip

WORKDIR /var/www

COPY . .

RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 storage bootstrap/cache

COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/supervisord.conf /etc/supervisord.conf
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

EXPOSE 80

ENV APP_ENV=production
ENV APP_DEBUG=false

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
