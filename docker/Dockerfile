FROM php:8-fpm

# Install xdebug
RUN \
    pecl install xdebug \
    && docker-php-ext-enable xdebug

# Install composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN \
   apt update \
  && apt install -y \
    unzip

RUN #\
#   pecl install pcov \
#    && docker-php-ext-enable pcov \
#    && file=/usr/local/etc/php/conf.d/docker-php-ext-pcov.ini \
#    && echo pcov.enabled=1 >> $file \
#    && echo pcov.directory=$PWD >> $file \
#    && echo "pcov.exclude=\"~vendor~\"" >> $file

ENV XDEBUG_MODE=off
ENV PHP_IDE_CONFIG='serverName=bypass'
ENV XDEBUG_CONFIG='client_host=host.docker.internal idekey=PHPSTORM'

EXPOSE 9003

VOLUME ["/app"]

WORKDIR /app
