# Use an official PHP 8.1 image with Apache
FROM php:8.1-apache

# Set the working directory inside the container
WORKDIR /var/www/html

# Install the CURL PHP extension, needed for sending photos
RUN docker-php-ext-install curl

# Copy all your bot files into the container's web root
COPY . /var/www/html

# Ensure proper file permissions for the web server (www-data user)
# This allows the web server to access and serve your files
RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html

# Expose port 80, the default port for HTTP traffic
EXPOSE 80

# Command to run the Apache web server in the foreground
CMD ["apache2-foreground"]