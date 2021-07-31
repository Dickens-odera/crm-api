FROM php:7.4-fpm

RUN apt-get update && apt-get install -Y \
        git \
        curl \
        libpng-dev \
        linonig-dev \
        libxml-dev \
        zip \
        unzip

RUN curl -sS https://getcomposer.org/installer |php --install-dir=/usr/local/bin --filename=composer

RUN docker-php-ext-install pdo_mysql mbstring

WORKDIR /app

COPY composer.json .

RUN composer install --no-scripts

COPY . .

CMD php artisan serve --host=0.0.0.0 --port 80


