FROM php:8.2-fpm

WORKDIR /var/www/html

RUN apt-get update && apt-get install -y \
    libpng-dev libonig-dev libxml2-dev zip unzip git curl \
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd 

RUN curl -L https://github.com/jwilder/dockerize/releases/download/v0.6.1/dockerize-linux-amd64-v0.6.1.tar.gz \
    | tar -C /usr/local/bin -xz

RUN pecl install redis \
    && docker-php-ext-enable redis

COPY --from=composer:2.5 /usr/bin/composer /usr/bin/composer

COPY ./.env.example ./.env
COPY . /var/www/html

RUN composer install --no-interaction --prefer-dist --optimize-autoloader

COPY .docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage

EXPOSE 8000

ENTRYPOINT ["/entrypoint.sh"]
CMD ["php-fpm"]