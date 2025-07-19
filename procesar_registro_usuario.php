<?php
include 'Conexion_db.php';
session_start();

// Validación de campos vacíos
$campos = ['numero_empleado', 'nombre', 'apellidos', 'rol', 'especialidad', 'asignacion'];
foreach ($campos as $campo) {
    if (empty($_POST[$campo])) {
        $_SESSION['mensaje'] = "❌ Error: El campo '" . ucfirst($campo) . "' no puede estar vacío.";
        $_SESSION['tipo_mensaje'] = "error";
        header("Location: admin_usuarios.php");
        exit();
    }
}

// Captura de datos
$numero_empleado = strtoupper(trim($_POST['numero_empleado']));
$nombre = trim($_POST['nombre']);
$apellidos = trim($_POST['apellidos']);
$rol_personal = trim($_POST['rol']);
$especialidad = trim($_POST['especialidad']);
$asignacion = trim($_POST['asignacion']);
$activo = 1;

// Validar formato del número de empleado
if (!preg_match('/^(TA|RE|IN|AD)-\d{3}$/', $numero_empleado)) {
    $_SESSION['mensaje'] = "❌ Error: El número de empleado debe tener el formato correcto por área (ej: TA-001).";
    $_SESSION['tipo_mensaje'] = "error";
    header("Location: admin_usuarios.php");
    exit();
}

// Insertar en tabla 'personal'
$sql_personal = "INSERT INTO personal (numero_empleado, nombre, apellidos, rol, especialidad, asignacion, activo)
                 VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt_personal = $conexion->prepare($sql_personal);

if (!$stmt_personal) {
    $_SESSION['mensaje'] = "❌ Error en la consulta de personal: " . $conexion->error;
    $_SESSION['tipo_mensaje'] = "error";
    header("Location: admin_usuarios.php");
    exit();
}

$stmt_personal->bind_param("ssssssi", $numero_empleado, $nombre, $apellidos, $rol_personal, $especialidad, $asignacion, $activo);

if ($stmt_personal->execute()) {
    $personal_id = $conexion->insert_id;

    $prefijo_area = substr($numero_empleado, 0, 2);
    switch ($prefijo_area) {
        case 'TA': $rol_usuario = "taller"; break;
        case 'RE': $rol_usuario = "entrada"; break;
        case 'IN': $rol_usuario = "infraestructura"; break;
        case 'AD': $rol_usuario = "mando"; break;
        default:   $rol_usuario = "desconocido"; break;
    }

    $username = $numero_empleado;
    $base_email = $rol_usuario . '@armada.mx';
    $email = $base_email;
    $contador = 1;

    while (true) {
        $check_email = $conexion->prepare("SELECT COUNT(*) FROM usuarios WHERE email = ?");
        $check_email->bind_param("s", $email);
        $check_email->execute();
        $check_email->bind_result($count);
        $check_email->fetch();
        $check_email->close();

        if ($count == 0) break;
        $email = $rol_usuario . $contador . '@armada.mx';
        $contador++;
    }

    $password = password_hash("Password123", PASSWORD_DEFAULT);
    $fecha_creacion = date("Y-m-d H:i:s");
    $activo_usuario = 1;

    $sql_usuario = "INSERT INTO usuarios (username, password, email, rol, personal_id, ultimo_acceso, activo, fecha_creacion)
                    VALUES (?, ?, ?, ?, ?, NULL, ?, ?)";
    $stmt_usuario = $conexion->prepare($sql_usuario);

    if (!$stmt_usuario) {
        $_SESSION['mensaje'] = "❌ Error en la consulta de usuario: " . $conexion->error;
        $_SESSION['tipo_mensaje'] = "error";
        header("Location: admin_usuarios.php");
        exit();
    }

    $stmt_usuario->bind_param("sssisis", $username, $password, $email, $rol_usuario, $personal_id, $activo_usuario, $fecha_creacion);

    if ($stmt_usuario->execute()) {
        $_SESSION['mensaje'] = "✅ Personal y usuario registrados exitosamente.";
        $_SESSION['tipo_mensaje'] = "exito";
    } else {
        $_SESSION['mensaje'] = "❌ Error al registrar usuario: " . $stmt_usuario->error;
        $_SESSION['tipo_mensaje'] = "error";
    }

    $stmt_usuario->close();
} else {
    $_SESSION['mensaje'] = "❌ Error al registrar personal: " . $stmt_personal->error;
    $_SESSION['tipo_mensaje'] = "error";
}

$stmt_personal->close();
$conexion->close();
header("Location: admin_usuarios.php");
exit();
?>
