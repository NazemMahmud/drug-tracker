FROM php:8.2-fpm

RUN apt-get update && apt-get install -y libpq-dev libldap2-dev libonig-dev  locales openssl libicu-dev libzip-dev zip unzip git  \
    && docker-php-ext-install gettext pdo pdo_mysql intl mbstring mysqli bcmath zip \
    && pecl install redis \
    && docker-php-ext-enable redis


RUN curl -sS https://getcomposer.org/installer | \
php -- --install-dir=/usr/bin/ --filename=composer
