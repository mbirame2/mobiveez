FROM php:7.4.1-fpm-alpine


RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN docker-php-ext-install pdo pdo_mysql

# R..pertoire de travail dans le conteneur
WORKDIR /var/www/html

# Copiez les fichiers de votre projet Laravel dans le conteneur
COPY . /var/www/html

# Installer Composer
RUN composer install

# Configuration de Nginx
#COPY iveez.conf /etc/nginx/sites-available/default

COPY iveez.conf /etc/nginx/conf.d/iveez.conf

RUN php artisan storage:link

# Exposer le port HTTP (par d..faut 80)
EXPOSE 8080

# Commande pour d..marrer PHP-FPM et Nginx
CMD php artisan serve --host=0.0.0.0 --port=8080

# # Use PHP 7.4 base image
# FROM php:7.4


# # Install required extensions or dependencies if needed for Laravel

# # Install Composer globally
# RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer


# RUN docker-php-ext-install pdo pdo_mysql

# # R..pertoire de travail dans le conteneur
# WORKDIR /var/www/html

# # Copiez les fichiers de votre projet Laravel dans le conteneur
# COPY . /var/www/html

# # Installer Composer
# RUN composer install

# COPY iveez.conf /etc/nginx/conf.d/iveez.conf

# # Expose port if needed (for example, port 80)
# EXPOSE 8080

# # Start the application (you might need to customize this depending on Laravel's setup)
# CMD php artisan serve --host=0.0.0.0 --port=8080
