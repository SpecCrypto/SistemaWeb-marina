<?php
session_start();

if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'infraestructura') {
    header("Location: login.php?error=2");
    exit;
}

$usuario = strtoupper($_SESSION['usuario']);
include("Conexion_db.php");

// Filtros
$estado = $_GET['estado'] ?? '';
$buque = $_GET['buque'] ?? '';
$fecha_inicio = $_GET['fecha_inicio'] ?? '';
$fecha_fin = $_GET['fecha_fin'] ?? '';

$query = "SELECT t.id, t.codigo, t.estado, t.fecha_creacion, t.fecha_validacion, t.validado_por, t.motivo_rechazo, b.nombre AS nombre_buque
          FROM tickets t
          INNER JOIN buques b ON t.buque_id = b.id
          WHERE 1=1";

if ($estado !== '') {
    $query .= " AND t.estado = '" . mysqli_real_escape_string($conexion, $estado) . "'";
}
if ($buque !== '') {
    $query .= " AND b.id = '" . intval($buque) . "'";
}
if ($fecha_inicio !== '' && $fecha_fin !== '') {
    $query .= " AND DATE(t.fecha_creacion) BETWEEN '$fecha_inicio' AND '$fecha_fin'";
}

$query .= " ORDER BY t.fecha_creacion DESC";
$resultado = mysqli_query($conexion, $query);

// Obtener buques para el filtro
$buques_result = mysqli_query($conexion, "SELECT id, nombre FROM buques ORDER BY nombre ASC");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Tickets | Infraestructura Naval</title>
    <link rel="stylesheet" href="css/infra_tickets.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<header class="main-header">
    <div class="header-container">
        <div class="logo">
            <img src="images/Ancla.png" alt="Logo Marina de México">
            <div class="logo-text">
                <h1>Infraestructura Naval</h1>
                <p>Sistema de Mantenimiento</p>
            </div>
        </div>
        <nav>
            <ul class="nav-menu">
                <li><a href="Index_Infraestructura.php"><i class="fas fa-home"></i> Inicio</a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a></li>
            </ul>
        </nav>
    </div>
</header>

<section class="tickets-section">
    <div class="section-container">
        <h2 class="section-title">Listado de Tickets</h2>

        <!-- Formulario de filtros -->
        <form method="GET" class="filtros">
            <select name="estado">
                <option value="">Todos los Estados</option>
                <option value="pendiente" <?= $estado === 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
                <option value="validado" <?= $estado === 'validado' ? 'selected' : '' ?>>Validado</option>
                <option value="rechazado" <?= $estado === 'rechazado' ? 'selected' : '' ?>>Rechazado</option>
            </select>

            <select name="buque">
                <option value="">Todos los Buques</option>
                <?php while ($b = mysqli_fetch_assoc($buques_result)) { ?>
                    <option value="<?= $b['id'] ?>" <?= $buque == $b['id'] ? 'selected' : '' ?>>
                        <?= $b['nombre'] ?>
                    </option>
                <?php } ?>
            </select>

            <input type="date" name="fecha_inicio" value="<?= $fecha_inicio ?>" placeholder="Desde">
            <input type="date" name="fecha_fin" value="<?= $fecha_fin ?>" placeholder="Hasta">

            <button type="submit">Filtrar</button>
        </form>

        <!-- Tabla de resultados -->
        <table class="tickets-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Buque</th>
                    <th>Código</th>
                    <th>Estado</th>
                    <th>Fecha Creación</th>
                    <th>Fecha Validación</th>
                    <th>Validado Por</th>
                    <th>Motivo Rechazo</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($resultado) > 0): ?>
                    <?php while ($fila = mysqli_fetch_assoc($resultado)): ?>
                        <tr>
                            <td><?= $fila['id'] ?></td>
                            <td><?= $fila['nombre_buque'] ?></td>
                            <td><?= $fila['codigo'] ?></td>
                            <td><span class="estado <?= $fila['estado'] ?>"><?= ucfirst($fila['estado']) ?></span></td>
                            <td><?= $fila['fecha_creacion'] ?></td>
                            <td><?= $fila['fecha_validacion'] ?: '-' ?></td>
                            <td><?= $fila['validado_por'] ?: '-' ?></td>
                            <td><?= $fila['motivo_rechazo'] ?: '-' ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="8">No se encontraron tickets con esos criterios.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<footer class="main-footer">
    <div class="footer-container">
        <div class="footer-logo">
            <img src="images/logo.png" alt="Logo Marina de México">
        </div>
        <div class="footer-bottom">
            <p>&copy; <?= date('Y') ?> Infraestructura Naval. Todos los derechos reservados.</p>
        </div>
    </div>
</footer>

</body>
</html>
