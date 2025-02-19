# Menggunakan image PHP-FPM versi 8.2
FROM php:8.2-fpm

# Install dependencies sistem dan ekstensi PHP yang dibutuhkan Laravel
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip
RUN apt-get clean && rm -rf /var/lib/apt/lists/*
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Set direktori kerja
WORKDIR /var/www/html/api_recipe

# Copy file aplikasi ke dalam container
COPY . .

# Jika kamu butuh Composer (opsional), kamu bisa install di sini
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
