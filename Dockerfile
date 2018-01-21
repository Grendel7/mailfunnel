# PHP 7.0 base image
FROM php:7.0-cli

RUN apt-get update \
    # Install PHP extensions
    && apt-get install -y libmysqlclient-dev \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* \
    && docker-php-ext-install pdo_mysql zip \
    # Install XDebug
    && pecl install xdebug \
    && docker-php-ext-enable xdebug \
    # Install Composer
    && curl https://getcomposer.org/composer.phar > /usr/local/bin/composer \
    && chmod +x /usr/local/bin/composer \
    # Add the unprivileged user
    && useradd -ms /bin/bash mailfunnel

USER mailfunnel

# Setup environment
EXPOSE 8000
WORKDIR /home/mailfunnel/html
