# Menggunakan image PHP 8.2 FPM sebagai base image
FROM php:8.2-fpm

# Menyalin binary Composer dari image Composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# Memperbarui APT package index dan menginstal dependensi yang diperlukan
RUN apt-get update && apt-get install -y \
    zip \
    unzip \
    git \
    && rm -rf /var/lib/apt/lists/*

# Menginstal ekstensi PHP yang diperlukan
RUN docker-php-ext-install pdo pdo_mysql

# Instal npm (tanpa Node.js)
RUN apt-get update && apt-get install -y npm

# Verifikasi instalasi npm
RUN npm -v
