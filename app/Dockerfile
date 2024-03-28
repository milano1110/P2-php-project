FROM composer:lts AS composer

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --ignore-platform-reqs

FROM php:8.3-cli

WORKDIR /var/www/html

RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

RUN docker-php-ext-install session && docker-php-ext-enable session

COPY --from=composer /app/vendor /var/www/html/vendor

COPY ./src /var/www/html

COPY .env /var/www/html

EXPOSE 80