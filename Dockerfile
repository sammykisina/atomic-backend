# FROM php:8.3-fpm
# # FastCGI Process Manager

# # Arguments for user and user ID, with default values
# ARG user=appuser
# ARG uid=1000

# # Install necessary packages
# RUN apt update && apt install -y \
#     coreutils \
#     libzip-dev \
#     libsodium-dev \
#     git \
#     curl \
#     libpng-dev \
#     libonig-dev \
#     libxml2-dev \
#     && apt clean && rm -rf /var/lib/apt/lists/*

# # Install PHP extensions
# RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd \
#     && docker-php-ext-install zip sodium

# # Install Composer
# COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# # Create a user and set permissions
# RUN useradd -m -u $uid -g www-data -G www-data $user \
#     && mkdir -p /home/$user/.composer \
#     && chown -R $user:www-data /home/$user

# # Set working directory
# WORKDIR /var/www

# # Copy project files
# COPY . /var/www

# # Set permissions for storage and bootstrap/cache directories
# RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache \
#     && chmod -R u+rwX,g+rwX,o+rwX /var/www/storage /var/www/bootstrap/cache

# # Set ownership for the entire working directory
# RUN chown -R $user:www-data /var/www

# # Copy the entrypoint script
# COPY docker-compose/entrypoint.sh /usr/local/bin/entrypoint.sh

# # Set executable permissions
# RUN chmod +x /usr/local/bin/entrypoint.sh

# # Use the single entrypoint script
# ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]

# # Expose port 80
# EXPOSE 80

# # Start PHP-FPM as default command
# CMD ["php-fpm"]

FROM php:8.3-fpm
# FastCGI Process Manager

# Arguments for user and user ID, with default values
ARG user=appuser
ARG uid=1000

# Switch apt sources to HTTPS and install necessary packages
RUN sed -i -e 's|http://deb.debian.org|https://deb.debian.org|g' \
           -e 's|http://security.debian.org|https://security.debian.org|g' \
           /etc/apt/sources.list && \
    apt update && \
    apt install -y \
        coreutils \
        libzip-dev \
        libsodium-dev \
        git \
        curl \
        libpng-dev \
        libonig-dev \
        libxml2-dev \
    && apt clean \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd \
    && docker-php-ext-install zip sodium

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Create a user and set permissions
RUN useradd -m -u $uid -g www-data -G www-data $user \
    && mkdir -p /home/$user/.composer \
    && chown -R $user:www-data /home/$user

# Set working directory
WORKDIR /var/www

# Copy project files
COPY . /var/www

# Set permissions for storage and bootstrap/cache directories
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache \
    && chmod -R u+rwX,g+rwX,o+rwX /var/www/storage /var/www/bootstrap/cache

# Set ownership for the entire working directory
RUN chown -R $user:www-data /var/www

# Copy the entrypoint script
COPY docker-compose/entrypoint.sh /usr/local/bin/entrypoint.sh

# Set executable permissions
RUN chmod +x /usr/local/bin/entrypoint.sh

# Use the single entrypoint script
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]

# Expose port 80
EXPOSE 80

# Start PHP-FPM as default command
CMD ["php-fpm"]
