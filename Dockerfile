FROM php:8.3-cli-bookworm

# System deps + PHP extensions Laravel needs
RUN apt-get update && apt-get install -y --no-install-recommends \
    git unzip curl \
    libzip-dev libpng-dev libjpeg62-turbo-dev libfreetype6-dev \
    libonig-dev libxml2-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql mbstring zip gd bcmath exif pcntl \
    && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Install PHP deps first (better layer cache)
COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --prefer-dist \
    --optimize-autoloader \
    --no-interaction \
    --no-progress \
    --no-scripts

# App source (assets already built in public/build)
COPY . .

RUN composer dump-autoload --optimize --no-dev \
    && mkdir -p \
        storage/framework/cache/data \
        storage/framework/sessions \
        storage/framework/views \
        storage/logs \
        bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache \
    && chmod +x start.sh

ENV PORT=8000
EXPOSE 8000

CMD ["bash", "start.sh"]
