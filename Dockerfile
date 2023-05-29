# Base image
FROM php:7.4-fpm-alpine

# Set working directory
WORKDIR /var/www/html

# Install dependencies
RUN docker-php-ext-install pdo pdo_mysql

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copy project files
COPY . .

# Install project dependencies
RUN composer install --optimize-autoloader --no-dev

# Set permissions
RUN chown -R www-data:www-data /var/www/html/storage
RUN chmod -R 775 /var/www/html/storage
# Expose port
EXPOSE 9000

# Start PHP-FPM server
CMD ["php-fpm"]
