<?php
if (!isset($tituloPagina)) {
    $tituloPagina = 'Atelier';
}
if (!isset($cssExtra)) {
    $cssExtra = '';
}
if (!isset($mostrarNavbar)) {
    $mostrarNavbar = true;
}
if (!isset($mostrarFooter)) {
    $mostrarFooter = true;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $tituloPagina; ?></title>
    <link rel="stylesheet" href="/global.css">
    <?php echo $cssExtra; ?>
</head>
<body>

<?php if ($mostrarNavbar): ?>
<nav class="navbar">
    <div class="nav-container">
        <div class="nav-brand">
            <a href="index.php" style="text-decoration:none;color:inherit;display:flex;align-items:center;gap:8px;">
                <span class="logo-icon">🚀</span> Atelier
            </a>
        </div>
        <div class="nav-search">
            <input type="text" placeholder="Buscar cursos, tecnologías...">
            <button type="button" class="search-btn">
                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
            </button>
        </div>
        <div class="nav-links">
                <a href="/miperfil.php">Mi Perfil</a>
                <a href="/admin.php" style="color: var(--primary-color);">Panel Admin</a>
            </div>
    </div>
</nav>
<?php endif; ?>
