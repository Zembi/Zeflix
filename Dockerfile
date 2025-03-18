# Use the official PHP 8.3 Apache image as the base
FROM php:8.3-apache

# Install necessary PHP extensions
RUN apt-get update && apt-get install -y \
    unzip \
    libzip-dev \
    libonig-dev \
    libpq-dev \
    && docker-php-ext-install pdo pdo_mysql zip mbstring

# Install Xdebug
RUN pecl install xdebug && docker-php-ext-enable xdebug

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Set ServerName to avoid warnings
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Allow .htaccess overrides
RUN sed -i 's/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

# Set up the document root
WORKDIR /var/www/html

# Copy project files into the container
COPY . /var/www/html

# Ensure correct permissions for Apache
RUN chown -R www-data:www-data /var/www/html

# Restart Apache to apply changes
CMD ["apachectl", "-D", "FOREGROUND"]