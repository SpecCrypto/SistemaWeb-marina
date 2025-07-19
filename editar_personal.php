<?php
include 'Conexion_db.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: admin_personal.php");
    exit;
}

$id = intval($_GET['id']);
$error = '';
$exito = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $numero_empleado = mysqli_real_escape_string($conexion, $_POST['numero_empleado']);
    $nombre = mysqli_real_escape_string($conexion, $_POST['nombre']);
    $apellidos = mysqli_real_escape_string($conexion, $_POST['apellidos']);
    $rol = mysqli_real_escape_string($conexion, $_POST['rol']);
    $especialidad = mysqli_real_escape_string($conexion, $_POST['especialidad']);
    $asignacion = mysqli_real_escape_string($conexion, $_POST['asignacion']);
    $activo = isset($_POST['activo']) ? 1 : 0;

    if (empty($numero_empleado) || empty($nombre) || empty($apellidos)) {
        $error = "Los campos No. Empleado, Nombre y Apellidos son obligatorios.";
    } else {
        // Verificar si ya existe otro registro con ese número_empleado
        $verificar_sql = "SELECT id FROM personal WHERE numero_empleado = '$numero_empleado' AND id != $id LIMIT 1";
        $verificar_resultado = mysqli_query($conexion, $verificar_sql);

        if (mysqli_num_rows($verificar_resultado) > 0) {
            $error = "El número de empleado '$numero_empleado' ya está en uso por otro registro.";
        } else {
            // Actualizar datos en tabla personal
            $sql_update = "UPDATE personal SET 
                numero_empleado='$numero_empleado', 
                nombre='$nombre', 
                apellidos='$apellidos', 
                rol='$rol', 
                especialidad='$especialidad', 
                asignacion='$asignacion', 
                activo=$activo
                WHERE id=$id";

            if (mysqli_query($conexion, $sql_update)) {
                // Generar correo base según el rol
                switch (strtolower($rol)) {
                    case 'marino':
                        $correo_base = 'entrada@armada.mx';
                        break;
                    case 'teniente':
                        $correo_base = 'taller@armada.mx';
                        break;
                    case 'contramaestre':
                        $correo_base = 'infraestructura@armada.mx';
                        break;
                    case 'almirante':
                        $correo_base = 'admin@armada.mx';
                        break;
                    default:
                        $correo_base = 'usuario@armada.mx';
                        break;
                }

                // Evitar duplicados en el correo electrónico
                $correo = $correo_base;
                $contador = 1;
                while (true) {
                    $check_email = mysqli_query($conexion, "SELECT id FROM usuarios WHERE email = '$correo' AND personal_id != $id");
                    if (mysqli_num_rows($check_email) == 0) {
                        break;
                    }
                    $correo_partes = explode('@', $correo_base);
                    $correo = $correo_partes[0] . $contador . '@' . $correo_partes[1];
                    $contador++;
                }

                // Actualizar usuario relacionado con ese personal
                $sql_update_user = "UPDATE usuarios SET 
                    username = '$numero_empleado',
                    email = '$correo',
                    rol = '$rol',
                    activo = $activo
                    WHERE personal_id = $id";

                mysqli_query($conexion, $sql_update_user);

                $exito = "Registro actualizado correctamente.";
                $persona = [
                    'numero_empleado' => $numero_empleado,
                    'nombre' => $nombre,
                    'apellidos' => $apellidos,
                    'rol' => $rol,
                    'especialidad' => $especialidad,
                    'asignacion' => $asignacion,
                    'activo' => $activo
                ];
            } else {
                $error = "Error al actualizar el registro: " . mysqli_error($conexion);
            }
        }
    }
}

// Cargar datos solo si aún no se han actualizado o si es GET
if (empty($persona)) {
    $sql = "SELECT * FROM personal WHERE id = $id LIMIT 1";
    $result = mysqli_query($conexion, $sql);

    if (!$result || mysqli_num_rows($result) === 0) {
        header("Location: admin_personal.php");
        exit;
    }

    $persona = mysqli_fetch_assoc($result);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Personal</title>
    <link rel="stylesheet" href="css/admin_personal.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

</head>
<body>
    <header>
        <h1>Editar Registro de Personal</h1>
        <nav>
            <ul style="list-style:none; padding:0; margin:0;">
                <li><a href="Index_Infraestructura.php"><i class="fas fa-home" style="margin-right: 8px;"></i>Inicio</a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt" style="margin-right: 8px;"></i>Cerrar Sesión</a></li>
            </ul>
        </nav>
    </header>

     <main>
        <section>
            <?php if ($error): ?>
                <p style="color: var(--rojo); font-weight: bold;"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>

            <?php if ($exito): ?>
                <p style="color: var(--verde); font-weight: bold;"><?= htmlspecialchars($exito) ?></p>
            <?php endif; ?>

            <form method="POST" action="" id="formulario">
                <label for="numero_empleado">No. Empleado:</label>
                <input type="text" id="numero_empleado" name="numero_empleado" value="<?= htmlspecialchars($persona['numero_empleado']) ?>" required>

                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" value="<?= htmlspecialchars($persona['nombre']) ?>" required>

                <label for="apellidos">Apellidos:</label>
                <input type="text" id="apellidos" name="apellidos" value="<?= htmlspecialchars($persona['apellidos']) ?>" required>

                <label for="rol">Rol:</label>
                <input type="text" id="rol" name="rol" value="<?= htmlspecialchars($persona['rol']) ?>">

                <label for="especialidad">Especialidad:</label>
                <input type="text" id="especialidad" name="especialidad" value="<?= htmlspecialchars($persona['especialidad']) ?>">

                <label for="asignacion">Asignación:</label>
                <input type="text" id="asignacion" name="asignacion" value="<?= htmlspecialchars($persona['asignacion']) ?>">

                <label for="activo">
                    <input type="checkbox" id="activo" name="activo" <?= $persona['activo'] == 1 ? 'checked' : '' ?>>
                    Activo
                </label>

                <button type="submit">Guardar Cambios</button>
            </form>
        </section>
    </main>

    <footer>
        <p>&copy; 2025 Armada Nacional - Sistema de Gestión de Personal</p>
    </footer>

    <script>
        // Validación para que solo se ingresen letras
        document.getElementById('nombre').addEventListener('input', function () {
            this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '');
        });

        document.getElementById('apellidos').addEventListener('input', function () {
            this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '');
        });

        document.getElementById('especialidad').addEventListener('input', function () {
            this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '');
        });

        document.getElementById('asignacion').addEventListener('input', function () {
            this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '');
        });
    </script>
</body>
