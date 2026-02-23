FROM php:8.2-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libicu-dev \
    zip \
    unzip \
    git \
    supervisor \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd intl zip opcache

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Change document root to public folder
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf \
    && echo "PassEnv DB_HOST DB_PASSWORD DB_DATABASE DB_USERNAME" > /etc/apache2/conf-available/passenv.conf \
    && a2enconf passenv

# Set working directory
WORKDIR /var/www/html

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configure PHP settings for production
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# Customize PHP settings (upload size, memory limit)
RUN echo "upload_max_filesize = 200M" > /usr/local/etc/php/conf.d/uploads.ini \
    && echo "post_max_size = 200M" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "memory_limit = 256M" >> /usr/local/etc/php/conf.d/memory.ini

# Add User for non-root execution (optional, but good practice)
RUN groupadd -g 1000 www \
    && useradd -u 1000 -ms /bin/bash -g www www

# Initial permissions
RUN chown -R www-data:www-data /var/www/html

# Copy application source
# (When developing locally with docker-compose, this is overridden by volume mount.
#  When building for production, this copies the code.)
COPY . /var/www/html

# Set directory permissions for storage
RUN mkdir -p /var/www/html/storage/logs /var/www/html/storage/cache /var/www/html/storage/portal_uploads /var/www/html/storage/backups \
    && chown -R www-data:www-data /var/www/html/storage \
    && chmod -R 775 /var/www/html/storage

# We don't expose 80 explicitly, base image does. but good for docs.
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]
