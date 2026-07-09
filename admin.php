<?php
require_once __DIR__ . '/includes/sesion.php';
require_once __DIR__ . '/includes/conexion.php';
requiereAdmin();

$db = obtenerConexion();
$seccion = $_GET['seccion'] ?? 'cursos';
$accion = $_GET['accion'] ?? '';
$editId = isset($_GET['id']) ? intval($_GET['id']) : null;
$mensaje = '';
$tipoMensaje = '';

// --- PROCESAR ACCIONES POST ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($seccion === 'cursos') {
        $titulo = trim($_POST['titulo'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');
        $precio = floatval($_POST['precio'] ?? 0);
        $duracion = intval($_POST['duracion_semanas'] ?? 3);
        $numModulos = intval($_POST['num_modulos'] ?? 6);
        $badge = trim($_POST['badge'] ?? '');
        $editIdPost = isset($_POST['edit_id']) ? intval($_POST['edit_id']) : null;

        $imagen = '';
        if (isset($_FILES['imagen_file']) && $_FILES['imagen_file']['error'] === UPLOAD_ERR_OK) {
            $ext = pathinfo($_FILES['imagen_file']['name'], PATHINFO_EXTENSION);
            $nombreArchivo = uniqid('curso_') . '.' . strtolower($ext);
            $rutaDestino = __DIR__ . '/uploads/' . $nombreArchivo;
            if (move_uploaded_file($_FILES['imagen_file']['tmp_name'], $rutaDestino)) {
                $imagen = 'uploads/' . $nombreArchivo;
            }
        }

        if ($accion === 'eliminar' && $editId) {
            $db->prepare('DELETE FROM cursos WHERE id = ?')->execute([$editId]);
            $mensaje = 'Curso eliminado.'; $tipoMensaje = 'mensaje-exito';
        } elseif ($editIdPost) {
            if ($imagen) {
                $stmt = $db->prepare('UPDATE cursos SET titulo=?, descripcion=?, precio=?, duracion_semanas=?, num_modulos=?, badge=?, imagen=? WHERE id=?');
                $stmt->execute([$titulo, $descripcion, $precio, $duracion, $numModulos, $badge, $imagen, $editIdPost]);
            } else {
                $stmt = $db->prepare('UPDATE cursos SET titulo=?, descripcion=?, precio=?, duracion_semanas=?, num_modulos=?, badge=? WHERE id=?');
                $stmt->execute([$titulo, $descripcion, $precio, $duracion, $numModulos, $badge, $editIdPost]);
            }
            $mensaje = 'Curso actualizado.'; $tipoMensaje = 'mensaje-exito';
        } else {
            $stmt = $db->prepare('INSERT INTO cursos (titulo, descripcion, precio, duracion_semanas, num_modulos, badge, imagen) VALUES (?,?,?,?,?,?,?)');
            $stmt->execute([$titulo, $descripcion, $precio, $duracion, $numModulos, $badge, $imagen ?: 'imagenes/HTML.jpg']);
            $mensaje = 'Curso creado.'; $tipoMensaje = 'mensaje-exito';
        }
    }

    if ($seccion === 'modulos') {
        $cursoId = intval($_POST['curso_id'] ?? 0);
        $numero = intval($_POST['numero'] ?? 1);
        $modTitulo = trim($_POST['titulo'] ?? '');
        $modDesc = trim($_POST['descripcion'] ?? '');
        $editIdPost = isset($_POST['edit_id']) ? intval($_POST['edit_id']) : null;

        if ($accion === 'eliminar' && $editId) {
            $db->prepare('DELETE FROM modulos WHERE id = ?')->execute([$editId]);
            $mensaje = 'Módulo eliminado.'; $tipoMensaje = 'mensaje-exito';
        } elseif ($editIdPost) {
            $db->prepare('UPDATE modulos SET curso_id=?, numero=?, titulo=?, descripcion=? WHERE id=?')->execute([$cursoId, $numero, $modTitulo, $modDesc, $editIdPost]);
            $mensaje = 'Módulo actualizado.'; $tipoMensaje = 'mensaje-exito';
        } else {
            $db->prepare('INSERT INTO modulos (curso_id, numero, titulo, descripcion) VALUES (?,?,?,?)')->execute([$cursoId, $numero, $modTitulo, $modDesc]);
            $mensaje = 'Módulo creado.'; $tipoMensaje = 'mensaje-exito';
        }
    }

    if ($seccion === 'usuarios' && $accion === 'eliminar' && $editId) {
        $db->prepare('DELETE FROM usuarios WHERE id = ? AND rol != ?')->execute([$editId, 'admin']);
        $mensaje = 'Usuario eliminado.'; $tipoMensaje = 'mensaje-exito';
    }

    header('Location: /admin.php?seccion=' . $seccion . '&msg=' . urlencode($mensaje));
    exit;
}

// --- CARGAR DATOS PARA VISTA ---
if (isset($_GET['msg'])) { $mensaje = $_GET['msg']; $tipoMensaje = 'mensaje-exito'; }

// Stats
$totalUsuarios = $db->query('SELECT COUNT(*) FROM usuarios')->fetchColumn();
$totalCursos = $db->query('SELECT COUNT(*) FROM cursos')->fetchColumn();
$totalModulos = $db->query('SELECT COUNT(*) FROM modulos')->fetchColumn();
$totalInscripciones = $db->query('SELECT COUNT(*) FROM inscripciones')->fetchColumn();

// Cursos
$cursos = $db->query('SELECT * FROM cursos ORDER BY id')->fetchAll(PDO::FETCH_ASSOC);

// Módulos
$modulos = $db->query('SELECT m.*, c.titulo as curso_titulo FROM modulos m JOIN cursos c ON m.curso_id = c.id ORDER BY c.id, m.numero')->fetchAll(PDO::FETCH_ASSOC);

// Usuarios
$usuarios = $db->query('SELECT id, nombre, correo, rol, fecha_registro FROM usuarios ORDER BY fecha_registro DESC')->fetchAll(PDO::FETCH_ASSOC);

// Inscripciones
$inscripciones = $db->query('SELECT i.id, u.nombre as usuario, c.titulo as curso, i.fecha_inscripcion, i.progreso FROM inscripciones i JOIN usuarios u ON i.usuario_id = u.id JOIN cursos c ON i.curso_id = c.id ORDER BY i.fecha_inscripcion DESC')->fetchAll(PDO::FETCH_ASSOC);

// Datos para edición
$cursoEdit = null;
$moduloEdit = null;
if ($accion === 'editar' && $editId) {
    if ($seccion === 'cursos') {
        $stmt = $db->prepare('SELECT * FROM cursos WHERE id = ?');
        $stmt->execute([$editId]);
        $cursoEdit = $stmt->fetch(PDO::FETCH_ASSOC);
    }
    if ($seccion === 'modulos') {
        $stmt = $db->prepare('SELECT * FROM modulos WHERE id = ?');
        $stmt->execute([$editId]);
        $moduloEdit = $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

$tituloPagina = 'Panel Admin - Atelier';
$cssExtra = '<link rel="stylesheet" href="/css/admin.css">';
include __DIR__ . '/includes/header.php';
?>

<main class="admin-container">
    <div class="admin-header">
        <h1>Panel de Administración</h1>
        <div class="stats-grid">
            <div class="stat-card"><span class="stat-number"><?php echo $totalUsuarios; ?></span><span class="stat-label">Usuarios</span></div>
            <div class="stat-card"><span class="stat-number"><?php echo $totalCursos; ?></span><span class="stat-label">Cursos</span></div>
            <div class="stat-card"><span class="stat-number"><?php echo $totalModulos; ?></span><span class="stat-label">Módulos</span></div>
            <div class="stat-card"><span class="stat-number"><?php echo $totalInscripciones; ?></span><span class="stat-label">Inscripciones</span></div>
        </div>
    </div>

    <div class="admin-tabs">
        <a href="?seccion=cursos" class="admin-tab <?php echo $seccion === 'cursos' ? 'active' : ''; ?>">Cursos</a>
        <a href="?seccion=modulos" class="admin-tab <?php echo $seccion === 'modulos' ? 'active' : ''; ?>">Módulos</a>
        <a href="?seccion=usuarios" class="admin-tab <?php echo $seccion === 'usuarios' ? 'active' : ''; ?>">Usuarios</a>
        <a href="?seccion=inscripciones" class="admin-tab <?php echo $seccion === 'inscripciones' ? 'active' : ''; ?>">Inscripciones</a>
    </div>

    <?php if ($mensaje): ?>
        <div class="mensaje <?php echo $tipoMensaje; ?>"><?php echo htmlspecialchars($mensaje); ?></div>
    <?php endif; ?>

    <?php if ($seccion === 'cursos'): ?>
        <div class="admin-form-container">
            <h3><?php echo $cursoEdit ? 'Editar curso' : 'Nuevo curso'; ?></h3>
            <form method="POST" enctype="multipart/form-data" class="admin-form">
                <?php if ($cursoEdit): ?><input type="hidden" name="edit_id" value="<?php echo $cursoEdit['id']; ?>"><?php endif; ?>
                <div class="input-group"><label>Título</label><input type="text" name="titulo" required value="<?php echo $cursoEdit ? htmlspecialchars($cursoEdit['titulo']) : ''; ?>"></div>
                <div class="input-group"><label>Badge</label><input type="text" name="badge" required value="<?php echo $cursoEdit ? htmlspecialchars($cursoEdit['badge']) : 'Módulo 1'; ?>"></div>
                <div class="input-group full-width"><label>Descripción</label><textarea name="descripcion" required><?php echo $cursoEdit ? htmlspecialchars($cursoEdit['descripcion']) : ''; ?></textarea></div>
                <div class="input-group"><label>Precio (USD)</label><input type="number" name="precio" step="0.01" min="0" value="<?php echo $cursoEdit ? $cursoEdit['precio'] : '0'; ?>"></div>
                <div class="input-group"><label>Duración (semanas)</label><input type="number" name="duracion_semanas" min="1" required value="<?php echo $cursoEdit ? $cursoEdit['duracion_semanas'] : '3'; ?>"></div>
                <div class="input-group"><label>Nº Módulos</label><input type="number" name="num_modulos" min="1" required value="<?php echo $cursoEdit ? $cursoEdit['num_modulos'] : '6'; ?>"></div>
                <div class="input-group"><label>Imagen <?php if ($cursoEdit): ?>(dejar vacío para mantener)<?php endif; ?></label><input type="file" name="imagen_file" accept="image/*"></div>
                <div class="form-actions full-width">
                    <button type="submit" class="btn-save"><?php echo $cursoEdit ? 'Actualizar' : 'Crear curso'; ?></button>
                    <?php if ($cursoEdit): ?><a href="?seccion=cursos" class="btn-cancel">Cancelar</a><?php endif; ?>
                </div>
            </form>
        </div>

        <h2 class="admin-section-title">Cursos registrados</h2>
        <div class="table-wrapper">
            <table class="data-table">
                <thead><tr><th>ID</th><th>Imagen</th><th>Título</th><th>Precio</th><th>Duración</th><th>Módulos</th><th>Acciones</th></tr></thead>
                <tbody>
                    <?php foreach ($cursos as $c): ?>
                    <tr>
                        <td><?php echo $c['id']; ?></td>
                        <td><img src="/<?php echo htmlspecialchars($c['imagen']); ?>" style="width:40px;height:28px;object-fit:cover;border-radius:4px" onerror="this.style.display='none'"></td>
                        <td><?php echo htmlspecialchars($c['titulo']); ?></td>
                        <td><?php echo $c['precio'] > 0 ? '$' . number_format($c['precio'], 2) : 'Gratis'; ?></td>
                        <td><?php echo $c['duracion_semanas']; ?> sem</td>
                        <td><?php echo $c['num_modulos']; ?></td>
                        <td class="acciones">
                            <a href="?seccion=cursos&accion=editar&id=<?php echo $c['id']; ?>" class="btn-edit">Editar</a>
                            <a href="?seccion=cursos&accion=eliminar&id=<?php echo $c['id']; ?>" class="btn-del" onclick="return confirm('¿Eliminar este curso y todos sus módulos?')">Eliminar</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    <?php elseif ($seccion === 'modulos'): ?>
        <div class="admin-form-container">
            <h3><?php echo $moduloEdit ? 'Editar módulo' : 'Nuevo módulo'; ?></h3>
            <form method="POST" class="admin-form">
                <?php if ($moduloEdit): ?><input type="hidden" name="edit_id" value="<?php echo $moduloEdit['id']; ?>"><?php endif; ?>
                <div class="input-group">
                    <label>Curso</label>
                    <select name="curso_id" required>
                        <?php foreach ($cursos as $c): ?>
                            <option value="<?php echo $c['id']; ?>" <?php echo ($moduloEdit && $moduloEdit['curso_id'] == $c['id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($c['titulo']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="input-group"><label>Número</label><input type="number" name="numero" min="1" required value="<?php echo $moduloEdit ? $moduloEdit['numero'] : '1'; ?>"></div>
                <div class="input-group"><label>Título</label><input type="text" name="titulo" required value="<?php echo $moduloEdit ? htmlspecialchars($moduloEdit['titulo']) : ''; ?>"></div>
                <div class="input-group full-width"><label>Descripción</label><textarea name="descripcion" required><?php echo $moduloEdit ? htmlspecialchars($moduloEdit['descripcion']) : ''; ?></textarea></div>
                <div class="form-actions full-width">
                    <button type="submit" class="btn-save"><?php echo $moduloEdit ? 'Actualizar' : 'Crear módulo'; ?></button>
                    <?php if ($moduloEdit): ?><a href="?seccion=modulos" class="btn-cancel">Cancelar</a><?php endif; ?>
                </div>
            </form>
        </div>

        <h2 class="admin-section-title">Módulos registrados</h2>
        <div class="table-wrapper">
            <table class="data-table">
                <thead><tr><th>ID</th><th>Curso</th><th>Nº</th><th>Título</th><th>Descripción</th><th>Acciones</th></tr></thead>
                <tbody>
                    <?php foreach ($modulos as $m): ?>
                    <tr>
                        <td><?php echo $m['id']; ?></td>
                        <td><?php echo htmlspecialchars($m['curso_titulo']); ?></td>
                        <td><?php echo $m['numero']; ?></td>
                        <td><?php echo htmlspecialchars($m['titulo']); ?></td>
                        <td style="max-width:250px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?php echo htmlspecialchars($m['descripcion']); ?></td>
                        <td class="acciones">
                            <a href="?seccion=modulos&accion=editar&id=<?php echo $m['id']; ?>" class="btn-edit">Editar</a>
                            <a href="?seccion=modulos&accion=eliminar&id=<?php echo $m['id']; ?>" class="btn-del" onclick="return confirm('¿Eliminar este módulo?')">Eliminar</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    <?php elseif ($seccion === 'usuarios'): ?>
        <h2 class="admin-section-title">Usuarios registrados</h2>
        <div class="table-wrapper">
            <table class="data-table">
                <thead><tr><th>ID</th><th>Nombre</th><th>Correo</th><th>Rol</th><th>Fecha registro</th><th>Acciones</th></tr></thead>
                <tbody>
                    <?php foreach ($usuarios as $u): ?>
                    <tr>
                        <td><?php echo $u['id']; ?></td>
                        <td><?php echo htmlspecialchars($u['nombre']); ?></td>
                        <td><?php echo htmlspecialchars($u['correo']); ?></td>
                        <td><span style="color:<?php echo $u['rol'] === 'admin' ? '#ef4444' : '#10b981'; ?>;font-weight:600;"><?php echo $u['rol']; ?></span></td>
                        <td><?php echo $u['fecha_registro']; ?></td>
                        <td class="acciones">
                            <?php if ($u['rol'] !== 'admin'): ?>
                                <a href="?seccion=usuarios&accion=eliminar&id=<?php echo $u['id']; ?>" class="btn-del" onclick="return confirm('¿Eliminar este usuario?')">Eliminar</a>
                            <?php else: ?>
                                <span style="color:var(--text-muted);font-size:0.85rem">Admin</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    <?php elseif ($seccion === 'inscripciones'): ?>
        <h2 class="admin-section-title">Inscripciones</h2>
        <div class="table-wrapper">
            <table class="data-table">
                <thead><tr><th>ID</th><th>Usuario</th><th>Curso</th><th>Fecha</th><th>Progreso</th></tr></thead>
                <tbody>
                    <?php if (empty($inscripciones)): ?>
                        <tr><td colspan="5" style="text-align:center;color:var(--text-muted)">Sin inscripciones aún</td></tr>
                    <?php else: ?>
                        <?php foreach ($inscripciones as $i): ?>
                        <tr>
                            <td><?php echo $i['id']; ?></td>
                            <td><?php echo htmlspecialchars($i['usuario']); ?></td>
                            <td><?php echo htmlspecialchars($i['curso']); ?></td>
                            <td><?php echo $i['fecha_inscripcion']; ?></td>
                            <td><?php echo $i['progreso']; ?>%</td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
