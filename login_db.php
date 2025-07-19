<?php
session_start();
include('Conexion_db.php'); // Conexión a la base de datos

// Validación de campos
if (empty($_POST['usuario']) || empty($_POST['contraseña'])) {
    echo '
    <script>
    alert("Por favor complete todos los campos.");
    window.location = "login.php";
    </script>';
    exit;
}

// Sanitización básica
$usuario = substr($_POST['usuario'], 0, 50);
$clave = $_POST['contraseña'];

// Validar formato del nombre de usuario (ej. PE-001, TA-002, etc.)
if (!preg_match('/^(RE|TA|IN|AD)-[0-9]{3}$/', $usuario)) {
    echo '
    <script>
    alert("Formato de usuario inválido. Use el formato correcto (RE-001, TA-002, etc).");
    window.location = "login.php";
    </script>';
    exit;
}

// Consulta del usuario activo
$query = "SELECT * FROM usuarios WHERE username = '$usuario' AND activo = 1";
$resultado = mysqli_query($conexion, $query);

// Verificar si existe el usuario
if (mysqli_num_rows($resultado) > 0) {
    $datos = mysqli_fetch_assoc($resultado);

    $clave_bd = $datos['password'];
    $rol = $datos['rol'];
    $id_usuario = $datos['id'];

    // Verificación si es contraseña encriptada
    if (password_get_info($clave_bd)['algo']) {
        if (password_verify($clave, $clave_bd)) {
            // Inicio de sesión exitoso
            $_SESSION['usuario'] = $usuario;
            $_SESSION['rol'] = $rol;
            $_SESSION['id_usuario'] = $id_usuario;

            // Registrar último acceso
            $fecha_actual = date('Y-m-d H:i:s');
            mysqli_query($conexion, "UPDATE usuarios SET ultimo_acceso = '$fecha_actual' WHERE id = $id_usuario");

            // Redirección por rol
            switch ($rol) {
                case 'admin':
                    header("Location: Index_Admin.php");
                    break;
                case 'entrada':
                    header("Location: Index_Entrada.php");
                    break;
                case 'taller':
                    header("Location: Index_Taller.php");
                    break;
                case 'infraestructura':
                    header("Location: Index_Infraestructura.php");
                    break;
                default:
                    header("Location: login.php?error=2"); // Rol inválido
            }
            exit;
        } else {
            header("Location: login.php?error=1"); // Contraseña incorrecta
            exit;
        }
    } else {
        // Contraseña en texto plano
        if ($clave === $clave_bd) {
            // Convertir a hash y actualizar
            $hash_nuevo = password_hash($clave, PASSWORD_DEFAULT);
            mysqli_query($conexion, "UPDATE usuarios SET password = '$hash_nuevo' WHERE id = $id_usuario");

            $_SESSION['usuario'] = $usuario;
            $_SESSION['rol'] = $rol;
            $_SESSION['id_usuario'] = $id_usuario;

            $fecha_actual = date('Y-m-d H:i:s');
            mysqli_query($conexion, "UPDATE usuarios SET ultimo_acceso = '$fecha_actual' WHERE id = $id_usuario");

            switch ($rol) {
                case 'admin':
                    header("Location: Index_Admin.php");
                    break;
                case 'entrada':
                    header("Location: Index_Entrada.php");
                    break;
                case 'taller':
                    header("Location: Index_Taller.php");
                    break;
                case 'infraestructura':
                    header("Location: Index_Infraestructura.php");
                    break;
                default:
                    header("Location: login.php?error=2");
            }
            exit;
        } else {
            header("Location: login.php?error=1"); // Contraseña incorrecta
            exit;
        }
    }
} else {
    header("Location: login.php?error=1"); // Usuario no encontrado o inactivo
    exit;
}
?>
