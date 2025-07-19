<?php
include 'Conexion_db.php';

// Obtener órdenes confirmadas que no tienen personal asignado
defined("ORDENES_CONFIRMADAS_SIN_ASIGNACION") or define("ORDENES_CONFIRMADAS_SIN_ASIGNACION", "
    SELECT ot.id, ot.codigo, b.nombre AS buque_nombre, t.codigo AS ticket_codigo, ot.descripcion_trabajo
    FROM ordenes_trabajo ot
    INNER JOIN buques b ON ot.buque_id = b.id
    INNER JOIN tickets t ON ot.ticket_id = t.id
    WHERE ot.estado = 'confirmada'
    AND ot.id NOT IN (
        SELECT orden_id FROM asignaciones_personal
    )
");

$ordenes = mysqli_query($conexion, ORDENES_CONFIRMADAS_SIN_ASIGNACION);
$personal = mysqli_query($conexion, "SELECT * FROM personal WHERE activo = 1");

if (isset($_POST['asignar_personal'])) {
    $orden_id = $_POST['orden_id'];
    $personal_id = $_POST['personal_id'];
    $responsabilidad = $_POST['responsabilidad'];
    $fecha_asignacion = date('Y-m-d');

    $sql = "INSERT INTO asignaciones_personal (orden_id, personal_id, fecha_asignacion, responsabilidad, estado)
            VALUES ('$orden_id', '$personal_id', '$fecha_asignacion', '$responsabilidad', 'asignado')";

    if (mysqli_query($conexion, $sql)) {
        $mensaje = "Personal asignado exitosamente a la orden de trabajo.";
    } else {
        $mensaje_error = "Error al asignar personal.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Asignación de Personal</title>
    <link rel="stylesheet" href="css/admin_materiales.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        .mensaje {
            background-color: #d4edda;
            color: #155724;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #c3e6cb;
            border-radius: 5px;
            animation: desaparecer 4s forwards;
        }

        .mensaje-error {
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #f5c6cb;
            border-radius: 5px;
        }

        @keyframes desaparecer {
            0% { opacity: 1; }
            90% { opacity: 1; }
            100% { opacity: 0; display: none; }
        }
    </style>
</head>
<body>
    <header class="main-header">
        <div class="header-container">
            <div class="logo">
                <img src="images/Ancla.png" alt="Logo">
                <div class="logo-text">
                    <h1>Taller Naval</h1>
                    <p>Asignación de Personal</p>
                </div>
            </div>
            <ul class="nav-menu">
                <li><a href="Index_Taller.php"><i class="fas fa-home" style="margin-right: 8px;"></i>Inicio</a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt" style="margin-right: 8px;"></i>Cerrar Sesión</a></li>
            </ul>
        </div>
    </header>

    <section class="section-container">
        <h2>Asignar Personal a Ordenes Confirmadas</h2>

        <?php if (isset($mensaje)) : ?>
            <div class="mensaje"><?= $mensaje ?></div>
        <?php elseif (isset($mensaje_error)) : ?>
            <div class="mensaje-error"><?= $mensaje_error ?></div>
        <?php endif; ?>

        <form method="POST">
            <label for="orden_id">Orden de Trabajo:</label>
            <select name="orden_id" required>
                <option value="">Seleccione una orden</option>
                <?php while ($orden = mysqli_fetch_assoc($ordenes)) : ?>
                    <option value="<?= $orden['id'] ?>">
                        <?= $orden['codigo'] ?> - <?= $orden['buque_nombre'] ?> (<?= $orden['ticket_codigo'] ?>)
                    </option>
                <?php endwhile; ?>
            </select>

            <label for="personal_id">Personal:</label>
            <select name="personal_id" required>
                <option value="">Seleccione personal</option>
                <?php while ($pers = mysqli_fetch_assoc($personal)) : ?>
                    <option value="<?= $pers['id'] ?>">
                        <?= $pers['numero_empleado'] ?> - <?= $pers['nombre'] . ' ' . $pers['apellidos'] ?> (<?= $pers['especialidad'] ?>)
                    </option>
                <?php endwhile; ?>
            </select>

            <label for="responsabilidad">Responsabilidad:</label>
            <input type="text" name="responsabilidad" required>

            <button type="submit" name="asignar_personal" class="confirm-button">Asignar</button>
        </form>
    </section>

    <footer class="main-footer">
        <div class="footer-bottom">
            <p>© <?= date("Y") ?> Taller Naval. Todos los derechos reservados.</p>
        </div>
    </footer>
</body>
</html>
