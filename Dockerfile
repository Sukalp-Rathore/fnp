FROM php:8.2-apache

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Install system dependencies and MongoDB extension
RUN apt-get update && apt-get install -y \
    git zip unzip libpng-dev libonig-dev libxml2-dev libssl-dev \
    && docker-php-ext-install pdo mbstring exif pcntl bcmath gd \
    && pecl install mongodb \
    && echo "extension=mongodb.so" > /usr/local/etc/php/conf.d/mongodb.ini

# Set working directory
WORKDIR /var/www/html

# Copy Laravel project files to Apache's root
COPY . /var/www/html

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Update Apache config for Laravel
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

# Replace hardcoded EXPOSE
EXPOSE 10000

# Set default Apache port using ENV
ENV PORT=10000

# Update Apache to use the dynamic PORT
CMD /bin/bash -c "sed -i 's/Listen 80/Listen ${PORT}/' /etc/apache2/ports.conf && apache2-foreground"

