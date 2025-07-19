<?php
include 'Conexion_db.php';

$mensaje_error = '';
$mensaje_exito = '';

// Manejar actualización del estado
if (isset($_POST['actualizar_estado'])) {
    $asignacion_id = $_POST['asignacion_id'];
    $nuevo_estado = $_POST['nuevo_estado'];

    // Solo permitir los estados válidos
    $estados_validos = ['en_progreso', 'completado'];
    if (in_array($nuevo_estado, $estados_validos)) {
        $sql_update = "UPDATE asignaciones_personal SET estado = '$nuevo_estado' WHERE id = $asignacion_id";
        if (mysqli_query($conexion, $sql_update)) {
            $mensaje_exito = "Estado actualizado correctamente.";
        } else {
            $mensaje_error = "Error al actualizar el estado: " . mysqli_error($conexion);
        }
    } else {
        $mensaje_error = "Estado no válido. Solo se permite 'en_progreso' o 'completado'.";
    }
}

// Obtener todas las asignaciones con detalles
$sql = "
    SELECT ap.id AS asignacion_id, ap.estado AS estado_asignacion, ap.responsabilidad, ap.fecha_asignacion,
           ot.codigo AS codigo_orden, ot.descripcion_trabajo,
           p.nombre AS nombre_personal, p.apellidos
    FROM asignaciones_personal ap
    JOIN ordenes_trabajo ot ON ap.orden_id = ot.id
    JOIN personal p ON ap.personal_id = p.id
    ORDER BY ap.fecha_asignacion DESC
";

$resultado = mysqli_query($conexion, $sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Seguimiento de Órdenes - Taller</title>
    <link rel="stylesheet" href="css/admin_materiales.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

</head>
<body>
    <header class="main-header">
        <div class="header-container">
            <h1>Seguimiento de Órdenes Asignadas</h1>
            <ul class="nav-menu">
               <li><a href="Index_Taller.php"><i class="fas fa-home" style="margin-right: 8px;"></i>Inicio</a></li>
               <li><a href="logout.php"><i class="fas fa-sign-out-alt" style="margin-right: 8px;"></i>Cerrar Sesión</a></li>
            </ul>
        </div>
    </header>

    <section class="section-container">
        <?php if (!empty($mensaje_exito)) : ?>
            <div class="mensaje-exito"><?= $mensaje_exito ?></div>
        <?php endif; ?>
        <?php if (!empty($mensaje_error)) : ?>
            <div class="mensaje-error"><?= $mensaje_error ?></div>
        <?php endif; ?>

        <table border="1" cellpadding="10">
            <thead>
                <tr>
                    <th>Orden</th>
                    <th>Responsable</th>
                    <th>Responsabilidad</th>
                    <th>Descripción</th>
                    <th>Fecha Asignación</th>
                    <th>Estado Actual</th>
                    <th>Actualizar Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($resultado)) : ?>
                    <tr>
                        <td><?= htmlspecialchars($row['codigo_orden']) ?></td>
                        <td><?= htmlspecialchars($row['nombre_personal'] . ' ' . $row['apellidos']) ?></td>
                        <td><?= htmlspecialchars($row['responsabilidad']) ?></td>
                        <td><?= htmlspecialchars($row['descripcion_trabajo']) ?></td>
                        <td><?= htmlspecialchars($row['fecha_asignacion']) ?></td>
                        <td>
                            <strong><?= ucfirst(str_replace('_', ' ', $row['estado_asignacion'])) ?></strong>
                        </td>
                        <td>
                            <?php if ($row['estado_asignacion'] !== 'completado') : ?>
                                <form method="POST">
                                    <input type="hidden" name="asignacion_id" value="<?= $row['asignacion_id'] ?>">
                                    <select name="nuevo_estado" required>
                                        <option value="">-- Seleccionar --</option>
                                        <?php if ($row['estado_asignacion'] == 'asignado') : ?>
                                            <option value="en_progreso">En Progreso</option>
                                        <?php endif; ?>
                                        <option value="completado">Completado</option>
                                    </select>
                                    <button type="submit" name="actualizar_estado">Actualizar</button>
                                </form>
                            <?php else : ?>
                                <em>Finalizado</em>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </section>
</body>
</html>
