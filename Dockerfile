FROM php:7.4-apache
RUN apt-get update && apt-get install -y \
        libonig-dev \
    && docker-php-ext-install mysqli \
    && docker-php-source delete
RUN a2enmod rewrite
RUN service apache2 restart