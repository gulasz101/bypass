FROM php:8-fpm

# Install PECL extensions
RUN \
    pecl install xdebug \
    && docker-php-ext-enable xdebug

# Install composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN \
   apt update \
  && apt install -y \
    unzip

ENV XDEBUG_MODE=off
ENV PHP_IDE_CONFIG='serverName=bypass'
ENV XDEBUG_CONFIG='client_host=host.docker.internal idekey=PHPSTORM'

EXPOSE 9003

VOLUME ["/app"]

WORKDIR /app
