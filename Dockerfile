FROM php:8.2-cli

WORKDIR /app

RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    libzip-dev \
    libpng-dev \
    libonig-dev

RUN apt-get clean && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo_mysql mbstring zip gd

RUN pecl install redis && docker-php-ext-enable redis

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY . /app

RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

COPY .env.example .env

RUN php artisan key:generate

EXPOSE 8000

CMD bash -c "php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=8000"
