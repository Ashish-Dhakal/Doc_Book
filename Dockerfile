# Use an official PHP image with the desired version
FROM php:8.2-fpm

# Set working directory
WORKDIR /app

# Install dependencies and libraries for MySQL and other extensions
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    unzip \
    git \
    curl \
    libzip-dev \
    libonig-dev \
    libpdo-mysql

# Clear apt cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring zip exif pcntl
RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy existing application directory contents
COPY . .

# Install composer dependencies (without updating)
RUN composer install --no-dev --prefer-dist --no-scripts --no-progress --no-suggest

# Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["php-fpm"]
