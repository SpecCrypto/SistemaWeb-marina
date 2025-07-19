<?php
include 'Conexion_db.php';

// Consulta para obtener el reporte de materiales utilizados
$sql = "
    SELECT 
        mu.id,
        ot.codigo AS orden_codigo,
        m.descripcion AS material_descripcion,
        m.unidad_medida,
        mu.cantidad,
        mu.fecha_uso,
        p.nombre,
        p.apellidos,
        mu.observaciones
    FROM materiales_utilizados mu
    JOIN ordenes_trabajo ot ON mu.orden_id = ot.id
    JOIN materiales m ON mu.material_id = m.id
    JOIN personal p ON mu.responsable_id = p.id
    ORDER BY mu.fecha_uso DESC
";

$resultado = mysqli_query($conexion, $sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Materiales Utilizados</title>
    <link rel="stylesheet" href="css/admin_materiales.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
        }
        h1{
            color: darkgoldenrod;
        }
        .header-container, .main-footer {
            background-color: #002366;
            color: white;
            padding: 15px;
        }
        .nav-menu {
            list-style: none;
            display: flex;
            gap: 20px;
        }
        .nav-menu a {
            color: white;
            text-decoration: none;
        }
        .section-container {
            padding: 20px;
        }
        .titulo-seccion {
            font-size: 24px;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th {
            background-color: #003366;
            color: white;
            padding: 10px;
        }
        td {
            padding: 10px;
            border: 1px solid #ccc;
            text-align: center;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .footer-bottom {
            text-align: center;
        }
    </style>
</head>
<body>

<header class="main-header">
    <div class="header-container">
        
        <h1>Infraestructura Naval</h1>
        
        <ul class="nav-menu">
            <li><a href="Index_Infraestructura.php"><i class="fas fa-home"></i> Inicio</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a></li>
        </ul>
    </div>
</header>

<section class="section-container">
    <h2 class="titulo-seccion">Reporte de Materiales Utilizados</h2>

    <?php if (mysqli_num_rows($resultado) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Orden</th>
                    <th>Material</th>
                    <th>Cantidad</th>
                    <th>Unidad</th>
                    <th>Fecha de Uso</th>
                    <th>Responsable</th>
                    <th>Observaciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($resultado)): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= htmlspecialchars($row['orden_codigo']) ?></td>
                        <td><?= htmlspecialchars($row['material_descripcion']) ?></td>
                        <td><?= $row['cantidad'] ?></td>
                        <td><?= $row['unidad_medida'] ?></td>
                        <td><?= date("d/m/Y", strtotime($row['fecha_uso'])) ?></td>
                        <td><?= htmlspecialchars($row['nombre'] . ' ' . $row['apellidos']) ?></td>
                        <td><?= htmlspecialchars($row['observaciones']) ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No hay registros de materiales utilizados.</p>
    <?php endif; ?>
</section>

<footer class="main-footer">
    <div class="footer-bottom">
        <p>© <?= date("Y") ?> Taller Naval - Infraestructura. Todos los derechos reservados.</p>
    </div>
</footer>

</body>
</html>
