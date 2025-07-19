<?php
include 'Conexion_db.php';

// Consulta original: solo órdenes confirmadas (la dejamos intacta por si la necesitas)
$ordenes_confirmadas = mysqli_query($conexion, "
    SELECT ot.id AS orden_id, ot.codigo, ot.descripcion_trabajo, ot.fecha_inicio, 
           b.nombre AS buque_nombre, b.matricula, b.tipo, b.fecha_entrada, 
           b.estado AS buque_estado
    FROM ordenes_trabajo ot
    INNER JOIN buques b ON ot.buque_id = b.id
    WHERE ot.estado = 'confirmada'
    ORDER BY ot.fecha_inicio DESC
");

// Consulta corregida: incluye confirmadas y completadas
$ordenes_mostradas = mysqli_query($conexion, "
    SELECT ot.id AS orden_id, ot.codigo, ot.descripcion_trabajo, ot.fecha_inicio, ot.estado AS orden_estado,
           b.nombre AS buque_nombre, b.matricula, b.tipo, b.fecha_entrada, 
           b.estado AS buque_estado
    FROM ordenes_trabajo ot
    INNER JOIN buques b ON ot.buque_id = b.id
    WHERE ot.estado IN ('confirmada', 'completada')
    ORDER BY ot.fecha_inicio DESC
");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Autorización de Liberación de Buques</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="css/admin_bitacora.css">
</head>
<body>
    <header class="main-header">
        <div class="header-container">
            <div class="logo">
                <img src="images/Ancla.png" alt="Logo">
                <div class="logo-text">
                    <h1>Bitácora Naval</h1>
                    <p>Liberación de buques</p>
                </div>
            </div>
            <ul class="nav-menu">
                <li><a href="Index_Admin.php"><i class="fas fa-home" style="margin-right: 8px;"></i>Inicio</a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt" style="margin-right: 8px;"></i>Cerrar Sesión</a></li>
            </ul>
        </div>
    </header>

    <section class="bitacora-section">
        <div class="section-container">
            <h2>Buques con Órdenes Confirmadas o Completadas</h2>
            <table>
                <thead>
                    <tr>
                        <th>Código Orden</th>
                        <th>Buque</th>
                        <th>Matrícula</th>
                        <th>Tipo</th>
                        <th>Fecha Entrada</th>
                        <th>Descripción del Trabajo</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($o = mysqli_fetch_assoc($ordenes_mostradas)) : ?>
                        <tr>
                            <td><?= $o['codigo'] ?></td>
                            <td><?= htmlspecialchars($o['buque_nombre']) ?></td>
                            <td><?= $o['matricula'] ?></td>
                            <td><?= $o['tipo'] ?></td>
                            <td><?= $o['fecha_entrada'] ?></td>
                            <td><?= htmlspecialchars($o['descripcion_trabajo']) ?></td>
                            <td>
                                <!-- Mostrar botón "Autorizar" solo si la orden está confirmada -->
                                <?php if ($o['orden_estado'] === 'confirmada') : ?>
                                    <a href="autorizar_liberacion.php?id=<?= $o['orden_id'] ?>" class="btn-liberar" onclick="return confirm('¿Deseas completar y liberar este buque?')">
                                        <i class="fas fa-flag-checkered"></i> Autorizar
                                    </a>
                                <?php endif; ?>

                                <!-- Mostrar botón PDF solo si el buque está liberado -->
                                <?php if ($o['buque_estado'] === 'liberado') : ?>
                                    <a href="generar_cierre.php?id=<?= $o['orden_id'] ?>" class="btn-pdf" target="_blank">
                                        <i class="fas fa-file-pdf"></i> PDF Cierre
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>

                    <?php if (mysqli_num_rows($ordenes_mostradas) == 0) : ?>
                        <tr><td colspan="7" style="text-align:center;">No hay buques con órdenes disponibles.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</body>
</html>
