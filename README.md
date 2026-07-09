# Plataforma de Cursos - Atelier

Aplicación web de gestión de cursos con PHP + SQLite.

## Acceso

**URL de producción (Render):** *(completar tras deploy)*

**Credenciales del administrador:**

| Campo | Valor |
|---|---|
| Correo | `admin@atelier.com` |
| Contraseña | `admin123` |

## Ejecutar localmente

```bash
php crear_bd.php
php -S localhost:7070
```

Abrir: http://localhost:7070

## Estructura del proyecto

```
├── index.php              ← Página principal (cursos desde BD)
├── cursohtml.php?id=X     ← Detalle de curso + inscripción
├── login.php              ← Inicio de sesión
├── registro.php           ← Registro de usuarios
├── miperfil.php           ← Perfil + cursos inscritos
├── admin.php              ← Panel admin (CRUD cursos, módulos, usuarios)
├── cerrar-sesion.php      ← Cerrar sesión
├── crear_bd.php           ← Crear BD + datos de ejemplo + admin
├── includes/
│   ├── conexion.php       ← Conexión PDO a SQLite
│   ├── sesion.php         ← Manejo de sesiones + roles
│   ├── header.php         ← Navbar dinámica
│   └── footer.php         ← Cierre + JS
├── css/                   ← Archivos CSS
├── js/                    ← JavaScript (scroll, validación)
├── imagenes/              ← Imágenes de cursos
├── uploads/               ← Imágenes subidas desde admin
└── legacy/                ← Archivos originales de evaluación 1 y 2
```

## Flujo de navegación

1. Visitante → `index.php` (ver cursos)
2. Registro → `registro.php` (crear cuenta)
3. Login → `login.php` (iniciar sesión)
4. Inscripción → `cursohtml.php?id=X` (inscribirse a un curso)
5. Perfil → `miperfil.php` (ver cursos inscritos)
6. Admin → `admin.php` (CRUD de cursos, módulos, usuarios, inscripciones)

## Base de datos

SQLite con 4 tablas:

- **usuarios** — id, nombre, correo, password (hash), rol (admin/estudiante), fecha
- **cursos** — id, titulo, descripcion, precio, duracion, modulos, imagen, badge
- **modulos** — id, curso_id, numero, titulo, descripcion
- **inscripciones** — id, usuario_id, curso_id, fecha, progreso

## Tecnologías

- PHP 8.2+
- SQLite (PDO)
- HTML5 + CSS3
- Vanilla JavaScript

## Evaluaciones

- **Evaluación 1:** Archivos HTML/CSS originales en `legacy/`
- **Evaluación 2:** Versión PHP con formularios
- **Evaluación 3:** Aplicación completa con BD, sesiones, panel admin CRUD
