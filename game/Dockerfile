FROM php:8.3-cli

WORKDIR /usr/src/hive

RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

RUN docker-php-ext-install session && docker-php-ext-enable session

COPY . /usr/src/hive

EXPOSE 80