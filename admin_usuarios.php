<?php
session_start();

if (isset($_GET['mensaje'])) {
    echo "<p style='color:green'>" . htmlspecialchars($_GET['mensaje']) . "</p>";
}
if (isset($_GET['error'])) {
    echo "<p style='color:red'>" . htmlspecialchars($_GET['error']) . "</p>";
}
// Verificación básica de rol admin
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header('Location: login.php?error=2');
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Personal - Admin</title>
    <link rel="stylesheet" href="css/admin_usuarios.css">
    
</head>
<body>
    <header>
        <h1>Administración de Personal</h1>
        <nav>
            <a href="Index_Admin.php">Inicio</a>
            <a href="logout.php">Cerrar Sesión</a>
        </nav>
    </header>

    <main>
        <section>
            <h2>Registrar nuevo personal</h2>
            <form action="procesar_registro_usuario.php" method="POST" autocomplete="off">
                <div class="form-grupo">
                    <label for="numero_empleado">Número de Empleado</label>
                    <input type="text" name="numero_empleado" id="numero_empleado" required>
                </div>

                <div class="form-grupo">
                    <label for="nombre">Nombre</label>
                    <input type="text" name="nombre" id="nombre" required>
                </div>

                <div class="form-grupo">
                    <label for="apellidos">Apellidos</label>
                    <input type="text" name="apellidos" id="apellidos" required>
                </div>

                <div class="form-grupo">
                    <label for="rol">Rol</label>
                    <select name="rol" id="rol" required>
                        <option value="" disabled selected>Seleccione un rol</option>
                        <option value="almirante">Almirante</option>
                        <option value="marino">Marino</option>
                        <option value="teniente">Teniente</option>
                        <option value="contramaestre">Contramaestre</option>
                    </select>
                </div>

                <div class="form-grupo">
                    <label for="especialidad">Especialidad</label>
                    <input type="text" name="especialidad" id="especialidad">
                </div>

                <div class="form-grupo">
                    <label for="asignacion">Asignación</label>
                    <input type="text" name="asignacion" id="asignacion">
                </div>

                <input type="hidden" name="activo" value="1">

                <div class="form-botones">
                    <button type="submit">Registrar</button>
                    <button type="reset">Limpiar</button>
                </div>
            </form>
        </section>
    </main>

    <footer>
        &copy; <?php echo date('Y'); ?> Armada de México · Sistema de Mantenimiento Naval
    </footer>
</body>
</html>
