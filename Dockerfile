FROM composer:2 AS dependencias

WORKDIR /app
COPY composer.json composer.lock ./
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN composer install --no-dev --prefer-dist --no-interaction --no-progress --optimize-autoloader

FROM php:8.2-apache

RUN docker-php-ext-install pdo_mysql

WORKDIR /var/www/html
COPY . .
COPY --from=dependencias /app/vendor ./vendor

# PHP needs write access to uploaded patient documents.
RUN chown -R www-data:www-data /var/www/html/documentos

EXPOSE 80
