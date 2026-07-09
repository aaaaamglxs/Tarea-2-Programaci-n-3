FROM php:8.2-cli

# Instalar extensiones SQLite necesarias
RUN docker-php-ext-install pdo pdo_sqlite

WORKDIR /var/www/html

# Copiar todo el proyecto
COPY . .

# Crear la base de datos y datos iniciales (admin + cursos)
RUN php crear_bd.php

# Puerto que usará Render (se sobreescribe con $PORT)
EXPOSE 10000

# Iniciar servidor PHP embebido en el puerto que Render asigne
CMD ["sh", "-c", "php -S 0.0.0.0:${PORT}"]
