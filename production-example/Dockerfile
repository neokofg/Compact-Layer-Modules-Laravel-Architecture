FROM php:8.2.0
RUN apt-get update && apt-get install -y \
                          libpq-dev \
                          librdkafka-dev \
    && docker-php-ext-install pdo pdo_pgsql pgsql \
    && docker-php-ext-configure pcntl --enable-pcntl \
    && docker-php-ext-install \
      pcntl
RUN apt-get install -y gcc g++ autoconf
RUN pecl install swoole && docker-php-ext-enable swoole
RUN pecl install rdkafka && docker-php-ext-enable rdkafka
COPY . /backend
WORKDIR /backend
ENV APP_ENV development
ENV DB_HOST db
ENV REDIS_HOST redis
CMD ["php", "artisan", "octane:start", "--host=0.0.0.0", "--workers=4"]
