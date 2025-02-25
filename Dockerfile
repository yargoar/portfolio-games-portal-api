# Usar a imagem oficial do PHP com FPM
FROM php:8.2-fpm

# Instalar dependências e Redis
RUN apt-get update && apt-get install -y libpng-dev libjpeg-dev libfreetype6-dev git libssl-dev zlib1g-dev supervisor \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql pcntl \
    && apt-get clean

# Instalar Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Definir o diretório de trabalho
WORKDIR /var/www

# Copiar o arquivo de ambiente para dentro do container
COPY .env /var/www/.env

# Copiar arquivos do projeto
COPY . .

# Instalar dependências do Laravel
RUN composer install --no-dev --optimize-autoloader

# Configurar variáveis de ambiente
ENV APP_ENV=production

# Expôr a porta do Laravel e do Reverb
EXPOSE 8000 6001

# Criar diretório para logs do Supervisor
RUN mkdir -p /var/log/supervisor

# Copiar configuração do supervisord
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Comando de inicialização
CMD ["supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]