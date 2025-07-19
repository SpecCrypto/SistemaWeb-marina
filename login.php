<?php
session_start();

// Verificar si ya hay una sesión activa y redirigir según el rol
if(isset($_SESSION['usuario'])) {
    switch($_SESSION['rol']) {
        case 'admin':
            header("Location: Index_Admin.php");
            break;
        case 'entrada':
            header("Location: Index_Entrada.php");
            break;
        case 'taller':
            header("Location: Index_Taller.php");
            break;
        case 'infraestructura':
            header("Location: Index_Infraestructura.php");
            break;
        default:
            header("Location: login.php");
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Mantenimiento de Buques - Armada de México</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <img src="images/Ancla.png" alt="Logo Armada de México">
        </div>
        <h2>Acceso al Sistema</h2>
        
        <?php if(isset($_GET['error'])): ?>
            <div class="error-message">
                <?php 
                    switch($_GET['error']) {
                        case '1': echo "Credenciales incorrectas"; break;
                        case '2': echo "Acceso no autorizado"; break;
                        case '3': echo "Sesión expirada"; break;
                        case '4': echo "Debe iniciar sesión"; break;
                        default: echo "Error al iniciar sesión";
                    }
                ?>
            </div>
        <?php endif; ?>
        
        <form action="login_db.php" method="POST" autocomplete="off">
            <input type="text" name="usuario" placeholder="Ej: RE-001, TA-002, AD-001" 
            pattern="^(RE|TA|IN|AD|)-[0-9]{3}$" 
            title="Formato esperado: RE-001, TA-002, etc." 
            required autofocus>
            <input type="password" name="contraseña" placeholder="Contraseña" required>
            <button type="submit">INGRESAR</button>
        </form>
        
        <div class="version">Versión 1.0 · Sistema de Mantenimiento de Buques</div>
    </div>
    
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const urlParams = new URLSearchParams(window.location.search);
            const logoutParam = urlParams.get('logout');
            const errorParam = urlParams.get('error');

            if (logoutParam === '1') {
                alert("Sesión cerrada exitosamente.");
            }
            
            if (errorParam === '3' || errorParam === '4') {
                alert("Acceso requerido. Por favor ingrese sus credenciales.");
            }
        });
    </script>
</body>
</html>