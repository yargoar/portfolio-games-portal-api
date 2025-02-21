# Usar a imagem oficial do PHP com FPM
FROM php:8.2-fpm

# Instalar dependências e Redis
RUN apt-get update && apt-get install -y libpng-dev libjpeg-dev libfreetype6-dev git libssl-dev zlib1g-dev \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql \
    && apt-get clean

# Instalar Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Definir o diretório de trabalho
WORKDIR /var/www

# Copiar arquivos do projeto
COPY . .

# Instalar dependências do Laravel
RUN composer install --no-dev --optimize-autoloader

# Configurar a variável de ambiente
ENV APP_ENV=production

# Expôr a porta do Laravel
EXPOSE 8000

# Executar o Laravel com "artisan serve" e iniciar o Redis
CMD ["sh", "-c", "php artisan serve --host=0.0.0.0 & php artisan queue:work --tries=1 & php artisan pail --timeout=0"]
