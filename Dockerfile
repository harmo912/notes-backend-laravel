FROM php:8.2-apache

# Extensions système nécessaires
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libzip-dev \
    libonig-dev \
    zip \
    unzip \
    git \
    curl \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Extensions PHP
RUN docker-php-ext-install pdo pdo_mysql mbstring gd zip

# Augmenter la mémoire PHP pour composer
RUN echo "memory_limit = 512M" > /usr/local/etc/php/conf.d/memory.ini

# Installer Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copier les fichiers projet
COPY . /var/www/html/

# Installer les dépendances Laravel
WORKDIR /var/www/html
RUN composer install --no-interaction --no-dev --optimize-autoloader --prefer-dist

# Permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Config Apache — pointer vers /public
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Activer mod_rewrite pour Laravel
RUN a2enmod rewrite

EXPOSE 80