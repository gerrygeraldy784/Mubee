FROM php:8.2-apache

# Install ekstensi yang dibutuhkan Laravel & PostgreSQL
RUN apt-get update && apt-get install -y \
    libpq-dev \
    git \
    unzip \
    && docker-php-ext-install pdo pdo_pgsql

# Aktifkan mod_rewrite Apache
RUN a2enmod rewrite

# Ubah DocumentRoot Apache ke folder public Laravel
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Pasang Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Salin source code project
WORKDIR /var/www/html
COPY . .

# Install dependencies PHP
RUN composer install --no-dev --optimize-autoloader

# Atur perizinan folder storage
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Port default Apache
EXPOSE 80

CMD ["apache2-foreground"]
