FROM php:8.3-fpm

# Системные зависимости
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg62-turbo-dev \
    libwebp-dev \
    libfreetype6-dev \
    libzip-dev \
    libonig-dev \
    libxml2-dev \
    libcurl4-openssl-dev \
    zip \
    unzip \
    git \
    curl \
    && docker-php-ext-configure gd --with-jpeg --with-webp --with-freetype \
    && docker-php-ext-install \
        pdo \
        pdo_mysql \
        bcmath \
        gd \
        zip \
        mbstring \
        pcntl \
        posix \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

COPY _docker/app/php.ini /usr/local/etc/php/conf.d/php.ini

# Composer
ENV COMPOSER_ALLOW_SUPERUSER=1
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# php-fpm работает от uid хоста (bind mount принадлежит uid 1000)
RUN groupmod -g 1000 www-data && usermod -u 1000 www-data

# Рабочая директория
WORKDIR /var/www/doskaObnawleniya

COPY _docker/app/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 9000

ENTRYPOINT ["entrypoint.sh"]
CMD ["php-fpm"]
