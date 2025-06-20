# Use PHP with Apache as the base image
FROM bitnami/laravel:10
# Copy the application code
COPY . /app
# Set the working directory
WORKDIR /app

# Install project dependencies
RUN composer install

# Set permissions
RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache

