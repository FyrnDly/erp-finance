# -------------------------------------------------------
# 1) Build Stage (Composer & NPM)
# -------------------------------------------------------
FROM serversideup/php:8.3 AS build

USER root
WORKDIR /app

RUN apt-get update && apt-get install -y nodejs npm && \
    apt-get clean && rm -rf /var/lib/apt/lists/*

COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --no-interaction --no-progress --ignore-platform-reqs

COPY . .
RUN npm install && npm run build && \
    php artisan key:generate # Temp Develop build

# -------------------------------------------------------
# 2) Production stage
# -------------------------------------------------------
FROM serversideup/php:8.3

USER root
WORKDIR /app

RUN apt update && apt install -y supervisor \
    libicu-dev zlib1g-dev libpng-dev && \
    docker-php-ext-install intl gd bcmath && \
    apt-get clean && rm -rf /var/lib/apt/lists/*

COPY --from=build /app /app
COPY ./deployment/php.ini /usr/local/etc/php/conf.d/php.ini
COPY ./deployment/supervisord.conf /etc/supervisor/supervisord.conf
COPY ./deployment/start.sh /usr/local/bin/start.sh

RUN chmod +x /usr/local/bin/start.sh && \
    chown -R www-data:www-data /app/storage /app/bootstrap/cache && \
    rm -rf /app/deployment 

USER www-data
EXPOSE 80

CMD ["/usr/local/bin/start.sh"]
