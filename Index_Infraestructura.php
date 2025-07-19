<?php
session_start();

// Verificación de sesión y rol
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'infraestructura') {
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
    <title>Infraestructura | Proyecto Marina</title>
    <link rel="stylesheet" href="css/infra.css"> <!-- Asegúrate de crear este archivo -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<!-- Header -->
<header class="main-header">
    <div class="header-container">
        <div class="logo">
            <img src="images/Ancla.png" alt="Logo Marina de México">
            <div class="logo-text">
                <h1>Panel de Infraestructura</h1>
                <p>Gestión de Instalaciones y Recursos</p>
            </div>
        </div>
        <nav>
            <ul class="nav-menu">
                <li><a href="Index_Infraestructura.php" class="active"><i class="fas fa-home"></i> Inicio</a></li>
                <li><a href="infraestructura_ordenes.php"><i class="fas fa-chart-pie"></i> Asignar orden</a></li>
                <li><a href="infraestructura_reportes.php"><i class="fas fa-file-alt"></i> Reportes</a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a></li>
            </ul>
        </nav>
    </div>
</header>

<!-- Hero Section -->
<section class="hero">
    <div class="hero-content">
        <h2>Bienvenido contramaestre <p>Matrícula: <?php echo $usuario; ?></p></h2>
        <p>Desde este panel puedes registrar materiales, administrar el inventario y generar reportes</p>
        <div class="hero-buttons">
            <a href="infraestructura_tickets.php" class="btn btn-primary"><i class="fas fa-clipboard-check"></i> Ver Tickets</a>
            <a href="infraestructura_inventario.php" class="btn btn-secondary"><i class="fas fa-tools"></i> Registrar Materiales</a>
        </div>
    </div>
</section>

<!-- Features Section -->
<section id="features" class="features-section">
    <div class="section-container">
        <h2 class="section-title">Funciones Clave</h2>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-tasks"></i></div>
                <h3>Seguimiento de Tickets</h3>
                <p>Revisa los reportes enviados por las unidades y gestiona su resolución.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-boxes"></i></div>
                <h3>Inventario</h3>
                <p>Consulta, edita y actualiza el inventario de materiales y herramientas.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-chart-pie"></i></div>
                <h3>Reportes</h3>
                <p>Genera informes de recursos utilizados.</p>
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
