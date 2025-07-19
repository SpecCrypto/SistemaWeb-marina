<?php
session_start();

// Verificación de sesión y rol
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'taller') {
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
    <title>Taller | Proyecto Marina</title>
    <link rel="stylesheet" href="css/taller.css"> <!-- Reutilizando el CSS ya ajustado -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<!-- Header -->
<header class="main-header">
    <div class="header-container">
        <div class="logo">
            <img src="images/Ancla.png" alt="Logo Marina">
            <div class="logo-text">
                <h1>Panel del Taller</h1>
                <p>Sistema de Mantenimiento Naval</p>
            </div>
        </div>
        <nav>
            <ul class="nav-menu">
                <li><a href="Index_Taller.php" class="active"><i class="fas fa-home"></i> Inicio</a></li>
                <li><a href="taller_personal.php"><i class="fas fa-user-cog"></i> Asignar Personal</a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a></li>
            </ul>
        </nav>
    </div>
</header>

<!-- Hero Section -->
<section class="hero">
    <div class="hero-content">
        <h2>Bienvenido Teniente <p> Matricula: <?php echo $usuario; ?></h2>
        <p>Desde este panel puedes gestionar tickets, asignar personal y dar seguimiento al mantenimiento</p>
        <div class="hero-buttons">
            <a href="taller_tickets.php" class="btn btn-primary"><i class="fas fa-ticket-alt"></i> Validar Tickets</a>
            <a href="taller_estatus.php" class="btn btn-secondary"><i class="fas fa-tasks"></i> Ver Estatus</a>
        </div>
    </div>
</section>

<!-- Features -->
<section id="features" class="features-section">
    <div class="section-container">
        <h2 class="section-title">Funciones del Taller</h2>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-clipboard-check"></i></div>
                <h3>Validación de Tickets</h3>
                <p>Revisa la información del buque y confirma disponibilidad de recursos.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-users-cog"></i></div>
                <h3>Asignación de Personal</h3>
                <p>Asigna al personal adecuado para realizar las tareas de mantenimiento.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-tasks"></i></div>
                <h3>Monitoreo de Estatus</h3>
                <p>Da seguimiento al progreso del servicio hasta su finalización.</p>
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

<script src="js/taller.js"></script>

</body>
</html>
