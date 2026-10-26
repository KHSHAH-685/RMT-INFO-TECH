FROM php:8.2-apache

# Install MySQL PDO extension
RUN docker-php-ext-install pdo pdo_mysql

# Enable Apache rewrite module
RUN a2enmod rewrite

# Point Apache DocumentRoot to the public directory inside project_marketplace
ENV APACHE_DOCUMENT_ROOT /var/www/html/project_marketplace/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Copy project files into container
COPY . /var/www/html/

# Ensure web server has permissions
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
