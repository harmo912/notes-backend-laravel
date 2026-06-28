FROM php:8.2-apache
RUN apt-get update && apt-get install -y libpng-dev libzip-dev zip unzip
RUN docker-php-ext-install pdo_mysql gd zip
COPY . /var/www/html
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install --working-dir=/var/www/html
RUN chown -R www-data:www-data /var/www/html/storage