<?php
include 'Conexion_db.php'; // Tu conexión a la base de datos

// Manejo del formulario para asignar materiales
if (isset($_POST['asignar_material'])) {
    $orden_id = $_POST['orden_id'];
    $material_id = $_POST['material_id'];
    $cantidad = (int)$_POST['cantidad'];
    $fecha_uso = $_POST['fecha_uso'];
    $responsable_id = $_POST['responsable_id'];
    $observaciones = trim($_POST['observaciones']);

    $errores = [];

    // Validar que la cantidad sea positiva
    if ($cantidad < 1) {
        $errores[] = "La cantidad debe ser al menos 1.";
    }

    // Validar que la cantidad asignada no exceda la disponible
    $result = mysqli_query($conexion, "SELECT cantidad FROM materiales WHERE id = $material_id");
    $material = mysqli_fetch_assoc($result);
    if (!$material) {
        $errores[] = "Material no encontrado.";
    } elseif ($cantidad > $material['cantidad']) {
        $errores[] = "La cantidad asignada excede el stock disponible ({$material['cantidad']}).";
    }

    // Validar que la orden exista y esté activa (por ejemplo estado != 'finalizado')
    $result = mysqli_query($conexion, "SELECT * FROM ordenes_trabajo WHERE id = $orden_id AND estado != 'finalizado'");
    $orden = mysqli_fetch_assoc($result);
    if (!$orden) {
        $errores[] = "Orden de trabajo no válida o finalizada.";
    }

    // Validar responsable activo
    $result = mysqli_query($conexion, "SELECT * FROM personal WHERE id = $responsable_id AND activo = 1");
    $responsable = mysqli_fetch_assoc($result);
    if (!$responsable) {
        $errores[] = "Responsable no válido o inactivo.";
    }

    if (empty($errores)) {
        // Insertar asignación
        $stmt = mysqli_prepare($conexion, "INSERT INTO materiales_utilizados (orden_id, material_id, cantidad, fecha_uso, responsable_id, observaciones) VALUES (?, ?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "iiisss", $orden_id, $material_id, $cantidad, $fecha_uso, $responsable_id, $observaciones);
        mysqli_stmt_execute($stmt);

        // Actualizar cantidad en materiales (restar)
        $nueva_cantidad = $material['cantidad'] - $cantidad;
        mysqli_query($conexion, "UPDATE materiales SET cantidad = $nueva_cantidad WHERE id = $material_id");

        header("Location: asignar_materiales.php");
        exit();
    }
}

// Obtener datos para los selects
$ordenes = mysqli_query($conexion, "SELECT id, codigo FROM ordenes_trabajo WHERE estado != 'finalizado' ORDER BY fecha_creacion DESC");
$materiales = mysqli_query($conexion, "SELECT id, codigo, descripcion, cantidad FROM materiales WHERE cantidad > 0 ORDER BY codigo ASC");
$personal = mysqli_query($conexion, "SELECT id, nombre, apellidos FROM personal WHERE activo = 1 ORDER BY nombre ASC");

// Obtener asignaciones recientes (con JOIN para mostrar info)
$asignaciones = mysqli_query($conexion, "
    SELECT ma.id, ma.cantidad, ma.fecha_uso, ma.observaciones,
           o.codigo AS orden_codigo,
           m.codigo AS material_codigo, m.descripcion AS material_desc,
           p.nombre, p.apellidos
    FROM materiales_utilizados ma
    JOIN ordenes_trabajo o ON ma.orden_id = o.id
    JOIN materiales m ON ma.material_id = m.id
    JOIN personal p ON ma.responsable_id = p.id
    ORDER BY ma.fecha_uso DESC, ma.id DESC
");

// Manejar eliminación de asignación (opcional)
if (isset($_GET['eliminar'])) {
    $id_eliminar = (int)$_GET['eliminar'];

    // Antes de eliminar, sumar la cantidad asignada al inventario
    $res = mysqli_query($conexion, "SELECT material_id, cantidad FROM materiales_utilizados WHERE id = $id_eliminar");
    $asig = mysqli_fetch_assoc($res);
    if ($asig) {
        mysqli_query($conexion, "UPDATE materiales SET cantidad = cantidad + {$asig['cantidad']} WHERE id = {$asig['material_id']}");
    }

    // Eliminar asignación
    mysqli_query($conexion, "DELETE FROM materiales_utilizados WHERE id = $id_eliminar");

    header("Location: asignar_materiales.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Asignar Materiales | Infraestructura Naval</title>
    <link rel="stylesheet" href="inventario.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
     <style>
        /* Pega aquí el CSS que te di antes */
        .asignaciones-section table {
            width: 100%;
            border-collapse: collapse;
            font-family: Arial, sans-serif;
            font-size: 14px;
            color: #333;
        }

        .asignaciones-section th, 
        .asignaciones-section td {
            border: 1px solid #ccc;
            padding: 8px 12px;
            text-align: center; /* Centrar texto */
            vertical-align: middle;
        }

        .asignaciones-section thead {
            background-color: #002366;
            color: white;
            font-weight: bold;
        }

        .asignaciones-section tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .asignaciones-section tbody tr:hover {
           
        }

        .asignaciones-section a i.fas.fa-trash {
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .asignaciones-section a i.fas.fa-trash:hover {
            color: #c82333;
        }
    </style>
</head>
<body>

<header class="main-header">
    <div class="header-container">
        <div class="logo">
            <img src="images/Ancla.png" alt="Logo Marina de México">
            <div class="logo-text">
                <h1>Infraestructura Naval</h1>
                <p>Sistema de Inventario - Asignar Materiales</p>
            </div>
        </div>
        <nav>
            <ul class="nav-menu">
                <li><a href="infraestructura_inventario.php"><i class="fas fa-warehouse" style="margin-right: 8px;"></i>Inventario</a></li>
                <li><a href="Index_Infraestructura.php"><i class="fas fa-home" style="margin-right: 8px;"></i>Inicio</a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt" style="margin-right: 8px;"></i>Cerrar Sesión</a></li>
            </ul>
        </nav>
    </div>
</header>

<section class="inventario-section">
    <div class="section-container">
        <h2>Asignar Material a Orden de Trabajo</h2>

        <?php
        if (!empty($errores)) {
            echo '<div class="error-messages">';
            foreach ($errores as $error) {
                echo "<p style='color: red;'>$error</p>";
            }
            echo '</div>';
        }
        ?>

        <form method="POST" action="">
            <label for="orden_id">Orden de Trabajo:</label>
            <select name="orden_id" id="orden_id" required>
                <option value="">Seleccione una orden</option>
                <?php while ($o = mysqli_fetch_assoc($ordenes)) : ?>
                    <option value="<?= $o['id'] ?>"><?= htmlspecialchars($o['codigo']) ?></option>
                <?php endwhile; ?>
            </select>

            <label for="material_id">Material:</label>
            <select name="material_id" id="material_id" required>
                <option value="">Seleccione un material</option>
                <?php while ($m = mysqli_fetch_assoc($materiales)) : ?>
                    <option value="<?= $m['id'] ?>">
                        <?= htmlspecialchars($m['codigo']) . " - " . htmlspecialchars($m['descripcion']) . " (Disponible: " . $m['cantidad'] . ")" ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <label for="cantidad">Cantidad a asignar:</label>
            <input type="number" name="cantidad" id="cantidad" min="1" required>

            <label for="fecha_uso">Fecha de uso:</label>
            <input type="date" name="fecha_uso" id="fecha_uso" required value="<?= date('Y-m-d') ?>">

            <label for="responsable_id">Responsable:</label>
            <select name="responsable_id" id="responsable_id" required>
                <option value="">Seleccione un responsable</option>
                <?php while ($p = mysqli_fetch_assoc($personal)) : ?>
                    <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nombre'] . " " . $p['apellidos']) ?></option>
                <?php endwhile; ?>
            </select>

            <label for="observaciones">Observaciones:</label>
            <textarea name="observaciones" id="observaciones" rows="3"></textarea>

            <button type="submit" name="asignar_material">Asignar</button>
        </form>
    </div>
</section>

<section class="asignaciones-section">
    <div class="section-container">
        <h2>Materiales Asignados Recientes</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Orden</th>
                    <th>Material</th>
                    <th>Cantidad</th>
                    <th>Fecha de Uso</th>
                    <th>Responsable</th>
                    <th>Observaciones</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($a = mysqli_fetch_assoc($asignaciones)) : ?>
                    <tr>
                        <td><?= $a['id'] ?></td>
                        <td><?= htmlspecialchars($a['orden_codigo']) ?></td>
                        <td><?= htmlspecialchars($a['material_codigo'] . " - " . $a['material_desc']) ?></td>
                        <td><?= $a['cantidad'] ?></td>
                        <td><?= $a['fecha_uso'] ?></td>
                        <td><?= htmlspecialchars($a['nombre'] . " " . $a['apellidos']) ?></td>
                        <td><?= htmlspecialchars($a['observaciones']) ?></td>
                        <td>
                            <a href="asignar_materiales.php?eliminar=<?= $a['id'] ?>" onclick="return confirm('¿Seguro que deseas eliminar esta asignación? Se devolverá el material al inventario.')"><i class="fas fa-trash" style="color: red;"></i></a>
                        </td>
                    </tr>
                <?php endwhile; ?>
                <?php if (mysqli_num_rows($asignaciones) == 0) : ?>
                    <tr><td colspan="8" style="text-align:center;">No hay asignaciones registradas.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

</body>
</html>
