<?php
include 'Conexion_db.php';

$mensaje = ''; // Inicializar mensaje

if (isset($_POST['confirmar_orden'])) {
    $id = $_POST['orden_id'];
    $fecha_inicio = $_POST['fecha_inicio'];
    $fecha_fin = $_POST['fecha_fin'];
    $fecha_confirmacion = date('Y-m-d H:i:s');

    $sql = "UPDATE ordenes_trabajo SET 
                fecha_confirmacion = '$fecha_confirmacion',
                fecha_inicio = '$fecha_inicio',
                fecha_fin = '$fecha_fin',
                estado = 'confirmada'
            WHERE id = $id";

    if (mysqli_query($conexion, $sql)) {
        $mensaje = "La orden de trabajo se confirmó exitosamente y está lista para que se le asigne un personal.";
    }
}

// Obtener las órdenes que aún no están confirmadas
$ordenes_pendientes = mysqli_query($conexion, "
    SELECT ot.*, b.nombre AS buque_nombre, t.codigo AS ticket_codigo 
    FROM ordenes_trabajo ot
    JOIN buques b ON ot.buque_id = b.id
    JOIN tickets t ON ot.ticket_id = t.id
    WHERE ot.fecha_confirmacion IS NULL
");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Confirmar Órdenes</title>
    <link rel="stylesheet" href="css/admin_materiales.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

</head>
<body>
    <header class="main-header">
        <div class="header-container">
            <div class="logo">
                <img src="images/Ancla.png" alt="Logo">
                <div class="logo-text">
                    <h1>Ordenes Navales</h1>
                    <p>Confirmación de Órdenes</p>
                </div>
            </div>
            <ul class="nav-menu">
                <li><a href="Index_Admin.php"><i class="fas fa-home" style="margin-right: 8px;"></i>Inicio</a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt" style="margin-right: 8px;"></i>Cerrar Sesión</a></li>
            </ul>
        </div>
    </header>

    <section class="section-container">
        <h2>Órdenes Pendientes de Confirmación</h2>

        <?php if (isset($mensaje)) : ?>
            <p class="success-message"><?= $mensaje ?></p>
        <?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>Buque</th>
                    <th>Ticket</th>
                    <th>Código</th>
                    <th>Fecha Inicio</th>
                    <th>Fecha Fin</th>
                    <th>Acción</th>
                </tr>
            </thead>
           <tbody>
<?php if (mysqli_num_rows($ordenes_pendientes) > 0): ?>
    <?php while ($orden = mysqli_fetch_assoc($ordenes_pendientes)) : ?>
        <tr>
            <form method="POST">
                <input type="hidden" name="orden_id" value="<?= $orden['id'] ?>">
                <td><?= $orden['buque_nombre'] ?></td>
                <td><?= $orden['ticket_codigo'] ?></td>
                <td><?= $orden['codigo'] ?></td>
                <td><input type="date" name="fecha_inicio" required></td>
                <td><input type="date" name="fecha_fin" required></td>
                <td><button type="submit" name="confirmar_orden" class="confirm-button">Confirmar</button></td>
            </form>
        </tr>
    <?php endwhile; ?>
<?php else: ?>
    <tr>
        <td colspan="6" style="text-align: center;">No hay órdenes pendientes</td>
    </tr>
<?php endif; ?>
</tbody>
        </table>
    </section>

    <footer class="main-footer">
        <div class="footer-bottom">
            <p>© <?= date("Y") ?> Infraestructura Naval. Todos los derechos reservados.</p>
        </div>
    </footer>
    <script>
    // Espera a que se cargue el DOM
    document.addEventListener("DOMContentLoaded", function () {
        const mensaje = document.querySelector(".success-message");
        if (mensaje) {
            setTimeout(() => {
                mensaje.style.opacity = "0";  // Efecto de desvanecimiento
                setTimeout(() => {
                    mensaje.remove();          // Elimina el elemento del DOM
                }, 500); // Tiempo para completar el fade-out
            }, 3000); // Mostrar durante 5 segundos
        }
    });
</script>
</body>
</html>
