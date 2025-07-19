<?php
include 'Conexion_db.php'; // Tu archivo de conexión a la base de datos

// Funciones para agregar, editar, eliminar y exportar
if (isset($_POST['agregar_material'])) {
    $codigo = $_POST['codigo'];
    $descripcion = $_POST['descripcion'];
    $cantidad = $_POST['cantidad'];
    $unidad_medida = $_POST['unidad_medida'];
    $categoria = $_POST['categoria'];
    $estado = $_POST['estado'];
    $ubicacion = $_POST['ubicacion'];

    // VALIDACIONES
    $errores = [];

    // Validación de código (ejemplo: MAT-01)
    if (!preg_match('/^MAT-\d{2}$/', $codigo)) {
        $errores[] = "El código debe tener el formato MAT-XX (ej. MAT-01)";
    }

    // Validación de descripción (solo letras y espacios)
    if (!preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/', $descripcion)) {
        $errores[] = "La descripción solo debe contener letras y espacios.";
    }

    // Validación de cantidad (número positivo menor o igual a 10000)
    if (!filter_var($cantidad, FILTER_VALIDATE_INT) || $cantidad < 1 || $cantidad > 10000) {
        $errores[] = "La cantidad debe ser un número entre 1 y 10000.";
    }

    // Si hay errores, mostrar (puedes renderizar con HTML si gustas)
    if (!empty($errores)) {
        foreach ($errores as $error) {
            echo "<script>alert('$error');</script>";
        }
    } else {
        // Insertar si todo es válido
        $sql = "INSERT INTO materiales (codigo, descripcion, cantidad, unidad_medida, categoria, estado, ubicacion) 
                VALUES ('$codigo', '$descripcion', $cantidad, '$unidad_medida', '$categoria', '$estado', '$ubicacion')";
        mysqli_query($conexion, $sql);

        header("Location: infraestructura_inventario.php");
        exit();
    }
}

if (isset($_POST['editar_material'])) {
    $id = $_POST['id'];
    $codigo = $_POST['codigo'];
    $descripcion = $_POST['descripcion'];
    $cantidad = $_POST['cantidad'];
    $unidad_medida = $_POST['unidad_medida'];
    $categoria = $_POST['categoria'];
    $estado = $_POST['estado'];
    $ubicacion = $_POST['ubicacion'];

    $sql = "UPDATE materiales SET codigo='$codigo', descripcion='$descripcion', cantidad=$cantidad,
            unidad_medida='$unidad_medida', categoria='$categoria', estado='$estado', ubicacion='$ubicacion'
            WHERE id=$id";
    mysqli_query($conexion, $sql);

    header("Location: infraestructura_inventario.php");
    exit();
}

if (isset($_GET['eliminar'])) {
    $id = $_GET['eliminar'];
    mysqli_query($conexion, "DELETE FROM materiales WHERE id=$id");
    header("Location: infraestructura_inventario.php");
    exit();
}

if (isset($_GET['exportar'])) {
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=materiales.xls");

    $result = mysqli_query($conexion, "SELECT * FROM materiales");
    echo "ID\tCódigo\tDescripción\tCantidad\tUnidad de Medida\tCategoría\tEstado\tUbicación\n";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "{$row['id']}\t{$row['codigo']}\t{$row['descripcion']}\t{$row['cantidad']}\t{$row['unidad_medida']}\t{$row['categoria']}\t{$row['estado']}\t{$row['ubicacion']}\n";
    }
    exit();
}

$material = [];
if (isset($_GET['editar'])) {
    $id = $_GET['editar'];
    $res = mysqli_query($conexion, "SELECT * FROM materiales WHERE id=$id");
    $material = mysqli_fetch_assoc($res);
}

$materiales = mysqli_query($conexion, "SELECT * FROM materiales");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Inventario | Infraestructura Naval</title>
    <link rel="stylesheet" href="inventario.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<header class="main-header">
    <div class="header-container">
        <div class="logo">
            <img src="images/Ancla.png" alt="Logo Marina de México">
            <div class="logo-text">
                <h1>Infraestructura Naval</h1>
                <p>Sistema de Inventario</p>
            </div>
        </div>
        <nav>
            <ul class="nav-menu">
                <li><a href="asignar_materiales.php"><i class="fas fa-tasks" style="margin-right: 8px;"></i> Asignar Materiales</a></li>
                <li><a href="Index_Infraestructura.php"><i class="fas fa-home" style="margin-right: 8px;"></i>Inicio</a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt" style="margin-right: 8px;"></i>Cerrar Sesión</a></li>
            </ul>
        </nav>
    </div>
</header>

<section class="inventario-section">
    <div class="section-container">
        <h2>Materiales Registrados</h2>

        <!-- Formulario de agregar/editar -->
        <form method="POST" action=""> 
            <input type="hidden" name="id" value="<?= $material['id'] ?? '' ?>">
            <input type="text" name="codigo" placeholder="Código" required pattern="^MAT-\d{2}$" title="Debe tener el formato MAT-XX (ej. MAT-01)">
            <input type="text" name="descripcion" placeholder="Descripción" required pattern="^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$" title="Solo se permiten letras y espacios">
            <input type="number" name="cantidad" placeholder="Cantidad" required min="1" max="10000">

            <select name="unidad_medida" required>
               <option value="">Seleccione una unidad</option>
               <option value="Pieza">Pieza</option>
               <option value="Caja">Caja</option>
               <option value="Litro">Litro</option>
               <option value="Metro">Metro</option>
               <option value="Kilogramo">Kilogramo</option>
               <option value="Paquete">Paquete</option>
               <option value="Juego">Juego</option>
            </select>


            <select name="categoria" required>
                <option value="">Seleccione Categoría</option>
                <option value="herramienta" <?= (isset($material['categoria']) && $material['categoria'] === 'herramienta') ? 'selected' : '' ?>>Herramienta</option>
                <option value="repuesto" <?= (isset($material['categoria']) && $material['categoria'] === 'repuesto') ? 'selected' : '' ?>>Repuesto</option>
                <option value="consumible" <?= (isset($material['categoria']) && $material['categoria'] === 'consumible') ? 'selected' : '' ?>>Consumible</option>
                <option value="equipo" <?= (isset($material['categoria']) && $material['categoria'] === 'equipo') ? 'selected' : '' ?>>Equipo</option>
            </select>

            <select name="estado" required>
                <option value="">Seleccione Estado</option>
                <option value="disponible" <?= (isset($material['estado']) && $material['estado'] === 'disponible') ? 'selected' : '' ?>>Disponible</option>
                <option value="agotado" <?= (isset($material['estado']) && $material['estado'] === 'agotado') ? 'selected' : '' ?>>Agotado</option>
                <option value="reservado" <?= (isset($material['estado']) && $material['estado'] === 'reservado') ? 'selected' : '' ?>>Reservado</option>
            </select>

            <select name="ubicacion" required>
                <option value="">Seleccione una ubicación</option>
                <option value="Almacén Principal">Almacén Principal</option>
                <option value="Oficina Técnica">Oficina Técnica</option>
                <option value="Centro de Cómputo">Centro de Cómputo</option>
                <option value="Laboratorio 1">Laboratorio 1</option>
                <option value="Laboratorio 2">Laboratorio 2</option>
                <option value="Bodega">Bodega</option>
                <option value="Otro">Otro</option>
            </select>

            <button type="submit" name="<?= isset($material['id']) ? 'editar_material' : 'agregar_material' ?>">
                <?= isset($material['id']) ? 'Actualizar' : 'Registrar' ?>
            </button>
        </form>

        <a href="?exportar=1" class="export-button">Exportar a Excel</a>

        <!-- Tabla de materiales -->
        <table class="tickets-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Código</th>
                    <th>Descripción</th>
                    <th>Cantidad</th>
                    <th>Unidad</th>
                    <th>Categoría</th>
                    <th>Estado</th>
                    <th>Ubicación</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($materiales)): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= $row['codigo'] ?></td>
                        <td><?= $row['descripcion'] ?></td>
                        <td><?= $row['cantidad'] ?></td>
                        <td><?= $row['unidad_medida'] ?></td>
                        <td><?= ucfirst($row['categoria']) ?></td>
                        <td><?= ucfirst($row['estado']) ?></td>
                        <td><?= $row['ubicacion'] ?></td>
                        <td>
                            <a href="?editar=<?= $row['id'] ?>">Editar</a> |
                            <a href="?eliminar=<?= $row['id'] ?>" onclick="return confirm('¿Desea eliminar este material?')">Eliminar</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
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
