FROM php:8.2-cli

# Instalar librerías de sistema necesarias para SQLite
RUN apt-get update && apt-get install -y \
    libsqlite3-dev \
    && rm -rf /var/lib/apt/lists/*

# Instalar extensiones PHP para SQLite
RUN docker-php-ext-install pdo pdo_sqlite sqlite3

WORKDIR /var/www/html

# Copiar todo el proyecto
COPY . .

# Crear la base de datos y datos iniciales (admin + cursos)
RUN php crear_bd.php

# Puerto que usará Render (se sobreescribe con $PORT)
EXPOSE 10000

# Iniciar servidor PHP embebido en el puerto que Render asigne
CMD ["sh", "-c", "php -S 0.0.0.0:${PORT}"]
