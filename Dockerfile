FROM php:7.2-cli

RUN \
    apt-get update \
    && apt-get upgrade -y \
    && apt-get install -y \
        bzip2 \
        curl \
        fontconfig \
        g++ \
        git \
        gnupg \
        libfreetype6 \
        libicu-dev \
        libjpeg-dev \
        libpcre3-dev \
        libpng-dev \
        libzip-dev \
        postgresql-server-dev-all \
        unzip \
        zip \
    && docker-php-ext-install pdo_pgsql

COPY app.php /src/app.php

WORKDIR /src/

CMD ["php", "app.php"]
