# Use the official PHP image
FROM php:8.2-cli

# Update system and install basic packages
RUN apt-get update && \
    apt-get upgrade -y && \
    apt-get install -y git libzip-dev unzip wget curl vim

# Install PHP extensions
RUN docker-php-ext-install zip pdo pdo_mysql

# Install and configure XDebug
RUN pecl install xdebug && \
    docker-php-ext-enable xdebug && \
    { \
        echo "xdebug.mode=debug"; \
        echo "xdebug.start_with_request=yes"; \
        echo "xdebug.client_host=host.docker.internal"; \
        echo "xdebug.client_port=9003"; \
        echo "xdebug.log=/tmp/xdebug.log"; \
    } > /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Clean up
RUN apt-get clean && \
    rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Set the working directory to your library's directory
WORKDIR /library

CMD ["bash"]
