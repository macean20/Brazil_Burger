# Dockerfile pour Symfony sur Render
FROM php:8.4-apache

# Installer les extensions PHP nécessaires
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpq-dev \
    libzip-dev \
    && docker-php-ext-install pdo pdo_pgsql zip

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configurer Apache pour Symfony
RUN a2enmod rewrite
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf
RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

# Copier le code de l'application
WORKDIR /var/www/html
COPY . /var/www/html

# Installer les dépendances Composer
RUN composer install --no-dev --optimize-autoloader

# Définir les permissions
RUN chown -R www-data:www-data /var/www/html/var

# Exposer le port 80
EXPOSE 80

# Démarrer Apache
CMD ["apache2-foreground"]
