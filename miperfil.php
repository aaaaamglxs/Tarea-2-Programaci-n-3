<?php
require_once __DIR__ . '/includes/sesion.php';
require_once __DIR__ . '/includes/conexion.php';
requiereLogin();

$usuario = usuarioLogueado();
$db = obtenerConexion();

$stmt = $db->prepare('SELECT i.id as inscripcion_id, i.progreso, c.id as curso_id, c.titulo, c.descripcion, c.imagen, c.badge FROM inscripciones i JOIN cursos c ON i.curso_id = c.id WHERE i.usuario_id = ? ORDER BY i.fecha_inscripcion DESC');
$stmt->execute([$usuario['id']]);
$inscripciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

$nombreCompleto = $usuario['nombre'];
$iniciales = '';
foreach (explode(' ', $nombreCompleto) as $parte) {
    if (!empty($parte)) $iniciales .= strtoupper($parte[0]);
}

$tituloPagina = 'Mi Perfil - Atelier';
$cssExtra = '<link rel="stylesheet" href="/css/perfil.css">';
include __DIR__ . '/includes/header.php';
?>

<main class="dashboard-container">
    <aside class="profile-sidebar">
        <div class="profile-card">
            <div class="profile-avatar"><span><?php echo htmlspecialchars($iniciales); ?></span></div>
            <h2 class="profile-name"><?php echo htmlspecialchars($nombreCompleto); ?></h2>
            <p class="profile-id">Correo: <strong><?php echo htmlspecialchars($usuario['correo']); ?></strong></p>
            <span class="status-badge">Estudiante Activo</span>
        </div>
    </aside>

    <section class="main-content">
        <div class="content-section">
            <div class="section-header"><h2>Mis Cursos</h2><p>Tu ruta de aprendizaje actual.</p></div>

            <?php if (empty($inscripciones)): ?>
                <div class="mensaje mensaje-info">No estás inscrito en ningún curso aún. <a href="/index.php" class="link">Ver catálogo</a></div>
            <?php else: ?>
                <?php foreach ($inscripciones as $ins): ?>
                <article class="enrolled-card">
                    <div class="course-info">
                        <div class="course-icon">🌐</div>
                        <div class="course-details">
                            <h3><?php echo htmlspecialchars($ins['titulo']); ?></h3>
                            <p><?php echo htmlspecialchars($ins['descripcion']); ?></p>
                        </div>
                    </div>
                    <div class="course-actions">
                        <div class="progress-text"><?php echo $ins['progreso']; ?>% completado</div>
                        <div class="progress-bar"><div class="progress-fill" style="width:<?php echo $ins['progreso']; ?>%"></div></div>
                        <a href="/cursohtml.php?id=<?php echo $ins['curso_id']; ?>" class="btn-start">Continuar</a>
                    </div>
                </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="content-section">
            <div class="section-header"><h2>Mi Horario de Clases</h2><p>Bloques de estudio programados.</p></div>
            <div class="schedule-grid">
                <div class="day-header">Lun</div><div class="day-header">Mar</div><div class="day-header">Mié</div><div class="day-header">Jue</div><div class="day-header">Vie</div><div class="day-header">Sáb</div><div class="day-header">Dom</div>
                <div class="time-slot empty"></div><div class="time-slot empty"></div><div class="time-slot active-slot"><span class="time">09:00-11:00</span><span class="subject">Estudio</span></div><div class="time-slot empty"></div><div class="time-slot active-slot"><span class="time">09:00-11:00</span><span class="subject">Estudio</span></div><div class="time-slot empty"></div><div class="time-slot empty"></div>
                <div class="time-slot active-slot"><span class="time">14:00-16:00</span><span class="subject">Práctica</span></div><div class="time-slot empty"></div><div class="time-slot empty"></div><div class="time-slot active-slot"><span class="time">14:00-16:00</span><span class="subject">Práctica</span></div><div class="time-slot empty"></div><div class="time-slot empty"></div><div class="time-slot empty"></div>
            </div>
        </div>
    </section>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
