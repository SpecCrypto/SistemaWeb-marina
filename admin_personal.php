<?php
include 'Conexion_db.php';

// Consulta de personal
$sql = "SELECT * FROM personal";
$resultado = mysqli_query($conexion, $sql);
$personal = [];

if ($resultado && mysqli_num_rows($resultado) > 0) {
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $personal[] = $fila;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Administrar Personal</title>
    <link rel="stylesheet" href="css/admin_personal.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
</head>
<body>
    <header>
        <h1>Panel de Administración - Personal</h1>
        <nav>
           <li><a href="Index_Admin.php"><i class="fas fa-home" style="margin-right: 8px;"></i>Inicio</a></li>
           <li><a href="logout.php"><i class="fas fa-sign-out-alt" style="margin-right: 8px;"></i>Cerrar Sesión</a></li>
        </nav>
    </header>

    <main>
        <section>
            <h2>Lista del Personal Registrado</h2>

            <?php if (count($personal) > 0): ?>
                <table class="tabla-personal">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>No. Empleado</th>
                            <th>Nombre</th>
                            <th>Apellidos</th>
                            <th>Rol</th>
                            <th>Especialidad</th>
                            <th>Asignación</th>
                            <th>Activo</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($personal as $persona): ?>
                            <tr>
                                <td><?= $persona['id'] ?></td>
                                <td><?= htmlspecialchars($persona['numero_empleado']) ?></td>
                                <td><?= htmlspecialchars($persona['nombre']) ?></td>
                                <td><?= htmlspecialchars($persona['apellidos']) ?></td>
                                <td><?= htmlspecialchars($persona['rol']) ?></td>
                                <td><?= htmlspecialchars($persona['especialidad']) ?></td>
                                <td><?= htmlspecialchars($persona['asignacion']) ?></td>
                                <td><?= $persona['activo'] == 1 ? 'Sí' : 'No' ?></td>
                                <td class="acciones">
                                    <a href="editar_personal.php?id=<?= $persona['id'] ?>" class="editar">Editar</a>
                                    <a href="eliminar_personal.php?id=<?= $persona['id'] ?>" class="eliminar" onclick="return confirm('¿Estás seguro de eliminar este registro?');">Eliminar</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No hay registros de personal actualmente.</p>
            <?php endif; ?>
        </section>
    </main>

    <footer>
        <p>&copy; 2025 Armada Nacional - Sistema de Gestión de Personal</p>
    </footer>
</body>
</html>
