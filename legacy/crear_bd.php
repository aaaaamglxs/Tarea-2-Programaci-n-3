<?php
require_once __DIR__ . '/includes/conexion.php';

$db = obtenerConexion();

$db->exec("CREATE TABLE IF NOT EXISTS usuarios (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nombre TEXT NOT NULL,
    correo TEXT NOT NULL UNIQUE,
    password TEXT NOT NULL,
    rol TEXT NOT NULL DEFAULT 'estudiante',
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP
)");

$db->exec("CREATE TABLE IF NOT EXISTS cursos (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    titulo TEXT NOT NULL,
    descripcion TEXT NOT NULL,
    precio REAL NOT NULL DEFAULT 0,
    duracion_semanas INTEGER NOT NULL,
    num_modulos INTEGER NOT NULL,
    imagen TEXT NOT NULL,
    badge TEXT NOT NULL
)");

$db->exec("CREATE TABLE IF NOT EXISTS modulos (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    curso_id INTEGER NOT NULL,
    numero INTEGER NOT NULL,
    titulo TEXT NOT NULL,
    descripcion TEXT NOT NULL,
    FOREIGN KEY (curso_id) REFERENCES cursos(id) ON DELETE CASCADE
)");

$db->exec("CREATE TABLE IF NOT EXISTS inscripciones (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    usuario_id INTEGER NOT NULL,
    curso_id INTEGER NOT NULL,
    fecha_inscripcion DATETIME DEFAULT CURRENT_TIMESTAMP,
    progreso INTEGER NOT NULL DEFAULT 0,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (curso_id) REFERENCES cursos(id) ON DELETE CASCADE,
    UNIQUE(usuario_id, curso_id)
)");

// Admin
$stmt = $db->prepare('SELECT id FROM usuarios WHERE correo = ?');
$stmt->execute(['admin@atelier.com']);
if (!$stmt->fetch()) {
    $hash = password_hash('admin123', PASSWORD_DEFAULT);
    $db->prepare('INSERT INTO usuarios (nombre, correo, password, rol) VALUES (?, ?, ?, ?)')->execute(['Administrador', 'admin@atelier.com', $hash, 'admin']);
    echo "Admin creado: admin@atelier.com / admin123\n";
}

// Cursos
$total = $db->query('SELECT COUNT(*) FROM cursos')->fetchColumn();
if ($total == 0) {
    $cursosData = [
        ['HTML Básico','Aprende a estructurar la web con etiquetas, formularios y multimedia. Domina HTML5 desde cero creando tus primeras páginas profesionales.',0,3,6,'imagenes/HTML.jpg','Módulo 1'],
        ['CSS Básico','Dale vida y color a tus estructuras HTML. Aprende selectores, modelo de caja, flexbox, grid y a maquetar interfaces modernas y atractivas.',0,3,6,'imagenes/CSS.jpg','Módulo 2'],
        ['JavaScript Básico','Añade interactividad a tus sitios web. Controla el DOM, maneja eventos, trabaja con APIs y domina la lógica de programación del lado del cliente.',0,4,8,'imagenes/JAVASCRIPT.jpg','Módulo 3'],
        ['PHP Básico','Conecta tu web con el servidor. Aprende manejo de sesiones, formularios, cookies, archivos, envío de correos e integración de backend de forma segura.',0,3,6,'imagenes/PHP.jpg','Módulo 4'],
        ['MySQL Básico','Gestiona la información de tus usuarios. Crea bases de datos relacionales, diseña tablas, realiza consultas avanzadas y aprende a integrar todo con PHP.',0,3,6,'imagenes/MySQL.jpg','Módulo 5'],
    ];
    $stmt = $db->prepare('INSERT INTO cursos (titulo, descripcion, precio, duracion_semanas, num_modulos, imagen, badge) VALUES (?,?,?,?,?,?,?)');
    foreach ($cursosData as $c) { $stmt->execute($c); }
    echo "5 cursos creados.\n";

    $modulosData = [
        [1,1,'Introducción a HTML','Configuración del entorno, etiquetas básicas y estructura de un documento HTML5.'],
        [1,2,'Encabezados y párrafos','Encabezados, párrafos, listas, negritas y manejo de bloques vs. en línea.'],
        [1,3,'Enlaces y rutas','Cómo navegar entre páginas, rutas relativas y absolutas.'],
        [1,4,'Multimedia','Inserción de imágenes, audios, videos y optimización de recursos.'],
        [1,5,'Tablas','Organización de datos tabulares y jerarquización de contenidos.'],
        [1,6,'Formularios','Campos de entrada, botones, validación y recolección de datos.'],
        [2,1,'Selectores CSS','Tipos de selectores, especificidad y herencia.'],
        [2,2,'Modelo de caja','Margin, padding, border y box-sizing.'],
        [2,3,'Flexbox','Diseño flexible con flex-direction, justify-content y align-items.'],
        [2,4,'Grid CSS','Maquetación avanzada con grid-template-columns y grid-template-rows.'],
        [2,5,'Media Queries','Diseño responsivo con breakpoints adaptativos.'],
        [2,6,'Animaciones','Transiciones, transformaciones y keyframes.'],
        [3,1,'Variables y tipos','Declaración, tipos de datos y operadores.'],
        [3,2,'Condicionales','if, else, switch y operadores ternarios.'],
        [3,3,'Bucles','for, while, do-while y forEach.'],
        [3,4,'Funciones','Declaración, parámetros, return y arrow functions.'],
        [3,5,'DOM','Selección de elementos, eventos y manipulación.'],
        [3,6,'Fetch API','Peticiones GET/POST con fetch y promesas.'],
        [3,7,'LocalStorage','Almacenamiento en el navegador.'],
        [3,8,'Proyecto final','App integrando todo lo aprendido.'],
        [4,1,'Sintaxis PHP','Variables, tipos, operadores y estructuras de control.'],
        [4,2,'Formularios','Recepción y validación de datos GET y POST.'],
        [4,3,'Sesiones','Inicio, almacenamiento y destrucción de sesiones.'],
        [4,4,'Cookies','Creación, lectura y eliminación.'],
        [4,5,'Archivos','Subida y manejo de archivos con PHP.'],
        [4,6,'Correo y formularios','Envío de emails y procesamiento completo.'],
        [5,1,'Introducción SQL','Conceptos básicos de bases de datos relacionales.'],
        [5,2,'CREATE TABLE','Creación de tablas con tipos de datos y constraints.'],
        [5,3,'INSERT','Inserción de registros.'],
        [5,4,'SELECT','Consultas con filtros WHERE, LIKE y BETWEEN.'],
        [5,5,'UPDATE y DELETE','Modificación y eliminación segura.'],
        [5,6,'JOIN','Relaciones entre tablas con INNER y LEFT JOIN.'],
    ];
    $stmt = $db->prepare('INSERT INTO modulos (curso_id, numero, titulo, descripcion) VALUES (?,?,?,?)');
    foreach ($modulosData as $m) { $stmt->execute($m); }
    echo "32 módulos creados.\n";
}

echo "Base de datos lista.\n";
