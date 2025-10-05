# Gunakan image PHP dengan Apache bawaan
FROM php:8.2-apache

# Install ekstensi yang dibutuhkan Laravel
RUN apt-get update && apt-get install -y \
    libpng-dev libjpeg-dev libfreetype6-dev libzip-dev zip git unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql zip

# Set working directory
WORKDIR /var/www/html

# Copy semua file ke dalam container
COPY . .

# Install composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Install dependencies Laravel
RUN composer install --no-dev --optimize-autoloader

# Beri izin ke folder storage dan cache
RUN chmod -R 755 storage bootstrap/cache

# Expose port 80 untuk Railway
EXPOSE 80

# Jalankan Apache di folder public Laravel
CMD ["apache2-foreground"]
