FROM php:8.2-apache

# Enable required Apache modules
RUN a2enmod rewrite

# Install PHP extensions and MongoDB
RUN apt-get update && apt-get install -y \
    git zip unzip libpng-dev libonig-dev libxml2-dev libssl-dev \
    && docker-php-ext-install pdo mbstring exif pcntl bcmath gd \
    && pecl install mongodb \
    && echo "extension=mongodb.so" > /usr/local/etc/php/conf.d/mongodb.ini

# Set working directory
WORKDIR /var/www/html

# Copy Laravel app
COPY . .

# Trust the directory for Git (fixes ownership issue)
RUN git config --global --add safe.directory /var/www/html

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader

# Fix permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Update Apache to point to Laravel public folder
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

# Expose port
EXPOSE 10000

# Set dynamic port (for Render)
ENV PORT=10000
CMD ["bash", "-c", "sed -i 's/Listen 80/Listen ${PORT}/' /etc/apache2/ports.conf && apache2-foreground"]
