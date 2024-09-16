FROM php:8.3-fpm
# FastCGI Process Manager

# Arguments for user and user ID, with default values
ARG user=appuser
ARG uid=1000

# Install necessary packages
RUN apt update && apt install -y \
    coreutils \
    libzip-dev \
    libsodium-dev \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev

RUN apt clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd
RUN docker-php-ext-install zip sodium

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Create user and set permissions
RUN useradd -G www-data,root -u $uid -d /home/$user $user

# Create home directory for the user and set ownership
RUN mkdir -p /home/$user/.composer && \
    chown -R $user:$user /home/$user

# Set working directory
WORKDIR /var/www

# Copy project files
COPY . /var/www

# Set permissions for storage and bootstrap/cache directories
RUN chmod -R u+rwX,g+rwX,o+rwX /var/www/storage && \
    chmod -R u+rwX,g+rwX,o+rwX /var/www/bootstrap/cache && \
    chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Change ownership of the entire working directory to the created user
RUN chown -R $user:www-data /var/www

# Copy the entrypoint script
COPY docker-compose/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Use the entrypoint script
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]

# Expose port 80
EXPOSE 80

# Set the user to the newly created user
USER $user

# Start PHP-FPM
CMD ["php-fpm"]
