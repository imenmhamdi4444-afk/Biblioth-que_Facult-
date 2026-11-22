FROM node:22-bookworm-slim AS assets
WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci

COPY webpack.config.js ./
COPY assets ./assets
RUN npm run build

FROM php:8.3-cli-bookworm AS app

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        libfreetype6-dev \
        libicu-dev \
        libjpeg62-turbo-dev \
        libonig-dev \
        libpng-dev \
        libxml2-dev \
        libzip-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" dom gd intl mbstring pdo_mysql zip \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .
COPY --from=assets /app/public/build ./public/build

ENV APP_ENV=prod
ENV APP_DEBUG=0

RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader --no-scripts \
    && php bin/console cache:clear --env=prod \
    && mkdir -p var public/uploads \
    && chown -R www-data:www-data var public/uploads

USER www-data

EXPOSE 8000

HEALTHCHECK --interval=30s --timeout=3s --start-period=20s --retries=3 \
    CMD php -r '$h=@get_headers("http://127.0.0.1:8000/login"); exit(($h && str_contains($h[0], "200")) ? 0 : 1);'

CMD ["php", "-S", "0.0.0.0:8000", "-t", "public", "public/index.php"]
