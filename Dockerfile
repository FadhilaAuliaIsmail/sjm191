FROM php:8.3-fpm AS base

RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev libzip-dev zip unzip nodejs npm nginx

RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . .

RUN composer install --optimize-autoloader --no-dev
RUN npm install && npm run build

CMD sh -c "echo '--- DEBUG ENV ---'; echo DB_HOST=$DB_HOST; echo DB_PORT=$DB_PORT; echo DB_DATABASE=$DB_DATABASE; echo DB_USERNAME=$DB_USERNAME; echo '--- END DEBUG ---'; php artisan config:clear && php artisan migrate:fresh --force && php artisan serve --host=0.0.0.0 --port=${PORT:-8080}"
