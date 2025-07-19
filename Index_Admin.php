<?php
session_start();

// Verificación de sesión y rol
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'admin') {
    header("Location: login.php?error=2");
    exit;
}

$usuario = strtoupper($_SESSION['usuario']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrador | Proyecto Marina</title>
    <link rel="stylesheet" href="admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<!-- Header -->
<header class="main-header">
    <div class="header-container">
        <div class="logo">
            <img src="images/Ancla.png" alt="Logo Marina de México">
            <div class="logo-text">
                <h1>Panel Administrativo</h1>
                <p>Sistema de Mantenimiento Naval</p>
            </div>
        </div>
        <nav>
            <ul class="nav-menu">
                <li><a href="Index_Admin.php" class="active"><i class="fas fa-home"></i> Inicio</a></li>
                <li><a href="admin_personal.php"><i class="fas fa-user-friends"></i> Personal</a></li>
                <li><a href="admin_bitacora.php"><i class="fas fa-clipboard-list"></i> Bitácora</a></li>
                <li><a href="admin_estatus.php"><i class="fas fa-tasks"></i> Ver Estatus</a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a></li>
            </ul>
        </nav>
    </div>
</header>

<!-- Hero Section -->
<section class="hero">
    <div class="hero-content">
        <h2>Bienvenido Almirante <p> Matricula: <?php echo $usuario; ?></h2>
        <p>Desde este panel puedes administrar todas las funciones del sistema</p>
        <div class="hero-buttons">
            <a href="admin_usuarios.php" class="btn btn-primary"><i class="fas fa-users-cog"></i> Gestionar Usuarios</a>
            <a href="admin_materiales.php" class="btn btn-secondary"><i class="fas fa-tools"></i> Confirmar ordenes</a>
        </div>
    </div>
</section>

<!-- Admin Features Section -->
<section id="features" class="features-section">
    <div class="section-container">
        <h2 class="section-title">Funciones Administrativas</h2>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-user-friends"></i></div>
                <h3>Gestión de Personal</h3>
                <p>Controla, edita y organiza los registros del personal naval.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-clipboard-list"></i></div>
                <h3>Bitácora del Sistema</h3>
                <p>Consulta los registros de acciones realizadas por cada usuario.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-tasks"></i></div>
                <h3>Estatus de Órdenes</h3>
                <p>Accede al seguimiento de las Órdenes que se han asignado.</p>
            </div>
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="main-footer" id="contact">
    <div class="footer-container">
        <div class="footer-logo">
            <img src="images/logo.png" alt="Logo Marina de México">
        </div>
        <div class="footer-links">
            <a href="#">Términos de Servicio</a>
            <a href="#">Política de Privacidad</a>
            <a href="#">Contacto</a>
            <a href="#">Acerca de</a>
        </div>
        <div class="footer-social">
            <a href="#"><i class="fab fa-facebook-f"></i></a>
            <a href="#"><i class="fab fa-twitter"></i></a>
            <a href="#"><i class="fab fa-instagram"></i></a>
        </div>
        <div class="footer-bottom">
            <p>&copy; <?= date('Y') ?> Armada de México. Todos los derechos reservados.</p>
        </div>
    </div>
</footer>

<script src="js/main.js"></script>
</body>
</html>
