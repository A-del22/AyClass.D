FROM php:8.2-cli

# Install dependensi dan ekstensi PHP yang dibutuhkan
RUN apt-get update && apt-get install -y \
    libpng-dev libjpeg-dev libfreetype6-dev \
    libzip-dev zip git unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql zip

# Copy semua file project ke container
WORKDIR /app
COPY . .

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader

# Jalankan aplikasi Laravel
CMD php artisan serve --host=0.0.0.0 --port=${PORT}
