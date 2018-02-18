FROM php:7.2-cli

RUN apt-get update \
    # Install PHP extensions
    && apt-get install -y git libzip-dev gnupg2 \
    && docker-php-ext-install pdo_mysql zip \
    # Install XDebug
    && pecl install xdebug \
    && docker-php-ext-enable xdebug \
    # Install Composer
    && curl https://getcomposer.org/composer.phar > /usr/local/bin/composer \
    && chmod +x /usr/local/bin/composer \
    # Install Node.js and Yarn
    && curl -sL https://deb.nodesource.com/setup_8.x | bash - \
    && curl -sS https://dl.yarnpkg.com/debian/pubkey.gpg | apt-key add - \
    && echo "deb https://dl.yarnpkg.com/debian/ stable main" | tee /etc/apt/sources.list.d/yarn.list \
    && apt-get update \
    && apt-get install -y nodejs yarn \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /root/.cache /root/.npm \
    # Add the unprivileged user
    && useradd -ms /bin/bash mailfunnel

USER mailfunnel

# Setup environment
EXPOSE 8000
WORKDIR /home/mailfunnel/html
