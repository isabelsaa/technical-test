FROM php:8.0-fpm-alpine
RUN  docker-php-ext-install  pdo_mysql
RUN apk add --update --no-cache \
    coreutils \
    yarn
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
    