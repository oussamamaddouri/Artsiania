FROM php:8.2-apache

# Install PDO MySQL driver
RUN docker-php-ext-install pdo pdo_mysql

# Enable Apache mod_rewrite for clean URLs
RUN a2enmod rewrite

# IMPORTANT: Ensure the directory structure is copied correctly
# We copy everything to /var/www/html
COPY . /var/www/html/

# Update Apache to point to the 'web' directory as the DocumentRoot
RUN sed -i 's|/var/www/html|/var/www/html/web/public|g' /etc/apache2/sites-available/000-default.conf


# Set correct permissions so PHP can write to folders if needed
RUN chown -R www-data:www-data /var/www/html
