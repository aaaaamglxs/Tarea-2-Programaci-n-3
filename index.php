<?php
require_once __DIR__ . '/includes/conexion.php';

$db = obtenerConexion();
$stmt = $db->query('SELECT id, titulo, descripcion, precio, duracion_semanas, num_modulos, imagen, badge FROM cursos ORDER BY id');
$cursos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$tituloPagina = 'Inicio - Atelier';
$cssExtra = '<link rel="stylesheet" href="/css/menuprincipal.css">';
include __DIR__ . '/includes/header.php';
?>

<header class="hero-banner">
    <div class="hero-content">
        <h1>Aprende rápido, construye tu futuro</h1>
        <p>Domina el desarrollo web paso a paso con módulos prácticos y directos al grano. Una plataforma optimizada para que estudies a tu propio ritmo, desde cualquier dispositivo y sin consumir muchos datos.</p>
        <div class="hero-stats">
            <span>✅ Proyectos reales</span>
            <span>✅ Acceso 24/7</span>
            <span>✅ Certificados</span>
        </div>
    </div>
</header>

<main class="courses-section" id="cursos">
    <div class="section-header">
        <h2>Ruta de Desarrollo Web</h2>
        <p>Comienza desde cero hasta dominar el backend y bases de datos.</p>
    </div>

    <div class="courses-grid">
        <?php foreach ($cursos as $curso): ?>
        <article class="course-card">
            <img src="<?php echo htmlspecialchars($curso['imagen']); ?>" alt="Curso de <?php echo htmlspecialchars($curso['titulo']); ?>" class="course-cover-img" onerror="this.src='https://placehold.co/600x300/4f46e5/ffffff?text=<?php echo urlencode($curso['titulo']); ?>'">
            <div class="card-content">
                <span class="badge"><?php echo htmlspecialchars($curso['badge']); ?></span>
                <h3><?php echo htmlspecialchars($curso['titulo']); ?></h3>
                <p><?php echo htmlspecialchars($curso['descripcion']); ?></p>
                <a href="/cursohtml.php?id=<?php echo $curso['id']; ?>" class="btn-course">Ver curso</a>
            </div>
        </article>
        <?php endforeach; ?>
    </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
