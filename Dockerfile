FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo pdo_mysql zip

RUN echo "upload_max_filesize = 100M" > /usr/local/etc/php/conf.d/uploads.ini \
    && echo "post_max_size = 100M" >> /usr/local/etc/php/conf.d/uploads.ini

RUN a2enmod rewrite


WORKDIR /var/www/html

COPY . .

RUN chown -R www-data:www-data /var/www/html/storage \
    && chmod -R 777 /var/www/html/storage

EXPOSE 80