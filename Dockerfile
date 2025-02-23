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

# Set up document root
WORKDIR /var/www/html