<?php
session_start();

// Verificación de sesión y rol
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'entrada') {
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
    <title>Puesto de Entrada | Proyecto Marina</title>
    <link rel="stylesheet" href="css/entrada.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
</head>
<body>

<!-- Header -->
<header class="main-header">
    <div class="header-container">
        <div class="logo">
            <img src="images/Ancla.png" alt="Logo Marina de México">
            <div class="logo-text">
                <h1>Puesto de Entrada</h1>
                <p>Sistema de Mantenimiento Naval</p>
            </div>
        </div>
        <nav>
            <ul class="nav-menu">
                <li><a href="Index_Entrada.php" class="active"><i class="fas fa-home"></i> Inicio</a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a></li>
            </ul>
        </nav>
    </div>
</header>

<!-- Hero Section -->
<section class="hero">
    <div class="hero-content">
        <h2>Bienvenido Marino <p> Matricula: <?php echo $usuario; ?></h2>
        <p>Desde este panel puedes realizar las funciones asignadas al Puesto de Entrada.</p>
        <div class="hero-buttons">
            <a href="entrada_recepcion.php" class="btn btn-primary"><i class="fas fa-ship"></i> Registrar Buque</a>
        </div>
    </div>
</section>

<!-- Funciones del puesto -->
<section id="features" class="features-section">
    <div class="section-container">
        <h2 class="section-title">Funciones del Puesto de Entrada</h2>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-id-card"></i></div>
                <h3>Recepción del Buque</h3>
                <p>Identifica el buque mediante documentación oficial y registra su entrada en el sistema.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-envelope-open-text"></i></div>
                <h3>Notificación al Taller</h3>
                <p>Envía una notificación al taller correspondiente y genera un ticket de entrada.</p>
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
