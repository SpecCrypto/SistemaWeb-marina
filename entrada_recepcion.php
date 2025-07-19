<?php
session_start();

if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'entrada') {
    header("Location: login.php?error=2");
    exit;
}

include 'Conexion_db.php';

$mensaje = "";
$tiposBuques = [
    "Portacontenedores", "Petrolero", "Granelero", "Buque de Pasajeros",
    "Remolcador", "Buque de Carga General", "Buque Pesquero", "Buque de Guerra"
];

function soloLetras($str) {
    return preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/u", $str);
}

function validarObservaciones($str) {
    return preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s.,;:()!?-]*$/u", $str);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST['nombre']);
    $matricula = trim($_POST['matricula']);
    $tipo = $_POST['tipo'];
    $fecha_entrada = $_POST['fecha_entrada'];
    $fecha_salida = $_POST['fecha_salida'];
    $estado = $_POST['estado'];
    $observaciones = trim($_POST['observaciones']);
    $documentacion = $_FILES['documentacion'];

    if (
        empty($nombre) || empty($matricula) || empty($fecha_entrada) || 
        empty($fecha_salida) || empty($estado) || empty($tipo)
    ) {
        $mensaje = "❗ Por favor completa todos los campos obligatorios.";
    } elseif (!soloLetras($nombre)) {
        $mensaje = "❗ El nombre solo puede contener letras y espacios.";
    } elseif (!preg_match("/^[A-Z]{2}-[0-9]{3}$/", $matricula)) {
        $mensaje = "❗ La matrícula debe tener el formato: dos letras mayúsculas, guion y tres números (Ejemplo: MA-125).";
    } elseif (!in_array($tipo, $tiposBuques)) {
        $mensaje = "❗ Selecciona un tipo de buque válido.";
    } elseif ($fecha_entrada !== date("Y-m-d")) {
        $mensaje = "❗ La fecha de entrada debe ser la fecha actual.";
    } elseif ($fecha_salida < $fecha_entrada) {
        $mensaje = "❗ La fecha de salida no puede ser anterior a la fecha de entrada.";
    } elseif (!in_array($estado, ['en_revision', 'en_mantenimiento', 'listo_para_salida'])) {
        $mensaje = "❗ Estado inválido.";
    } elseif (!validarObservaciones($observaciones)) {
        $mensaje = "❗ Las observaciones solo pueden contener letras, espacios y signos básicos.";
    } elseif ($documentacion['error'] !== UPLOAD_ERR_OK) {
        $mensaje = "❗ Debes subir un archivo en formato PDF.";
    } else {
        // Validar si la matrícula ya existe
        $stmt_check = $conexion->prepare("SELECT id FROM buques WHERE matricula = ?");
        $stmt_check->bind_param("s", $matricula);
        $stmt_check->execute();
        $stmt_check->store_result();

        if ($stmt_check->num_rows > 0) {
            $mensaje = "❗ La matrícula ingresada ya está registrada en la base de datos.";
        } else {
            // Validar el archivo PDF
            $tipoArchivo = mime_content_type($documentacion['tmp_name']);
            $tamanioArchivo = $documentacion['size'];

            if ($tipoArchivo !== 'application/pdf') {
                $mensaje = "❗ Solo se permiten archivos PDF para la documentación.";
            } elseif ($tamanioArchivo > 2 * 1024 * 1024) {
                $mensaje = "❗ El archivo PDF no debe superar los 2MB.";
            } else {
                $nombreArchivo = uniqid() . "-" . basename($documentacion['name']);
                $rutaDestino = "uploads/" . $nombreArchivo;

                if (!move_uploaded_file($documentacion['tmp_name'], $rutaDestino)) {
                    $mensaje = "❗ Error al subir el archivo PDF.";
                } else {
                    // Insertar en tabla buques
                    $stmt = $conexion->prepare("INSERT INTO buques (nombre, matricula, tipo, documentacion, fecha_entrada, fecha_salida, estado, observaciones) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param("ssssssss", $nombre, $matricula, $tipo, $nombreArchivo, $fecha_entrada, $fecha_salida, $estado, $observaciones);

                    if ($stmt->execute()) {
                        $id_buque = $stmt->insert_id;

                        // Insertar ticket
                        $stmt_ticket = $conexion->prepare("INSERT INTO tickets (buque_id, codigo, estado, fecha_creacion) VALUES (?, ?, ?, NOW())");
                        $codigo_ticket = "TCK-" . strtoupper(substr($matricula, 0, 2)) . "-" . date("YmdHis");
                        $estado_ticket = 'pendiente';
                        $stmt_ticket->bind_param("iss", $id_buque, $codigo_ticket, $estado_ticket);

                        if ($stmt_ticket->execute()) {
                            $mensaje = "✅ Buque registrado correctamente y ticket generado exitosamente.";
                            $mensaje = "🚨 El ticket se ha enviado al área de taller para ser validado ";
                        } else {
                            $mensaje = "⚠️ Buque registrado, pero no se pudo generar el ticket automáticamente.";
                        }

                        $stmt_ticket->close();
                    } else {
                        $mensaje = "❌ Error al registrar el buque: " . $conexion->error;
                    }

                    $stmt->close();
                }
            }
        }

        $stmt_check->close();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar Buque | Puesto de Entrada</title>
    <link rel="stylesheet" href="entrada_recepcion.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <script>
    function validarFormulario() {
        const matricula = document.forms["formBuque"]["matricula"].value.trim();
        const nombre = document.forms["formBuque"]["nombre"].value.trim();
        const fechaEntrada = document.forms["formBuque"]["fecha_entrada"].value;
        const fechaSalida = document.forms["formBuque"]["fecha_salida"].value;
        const observaciones = document.forms["formBuque"]["observaciones"].value.trim();
        const regexMatricula = /^[A-Z]{2}-[0-9]{3}$/;
        const regexNombre = /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/;
        const regexObs = /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s.,;:()!?-]*$/;

        if (!regexNombre.test(nombre)) {
            alert("El nombre solo puede contener letras y espacios.");
            return false;
        }
        if (!regexMatricula.test(matricula)) {
            alert("La matrícula debe tener el formato: dos letras mayúsculas, guion y tres números (Ejemplo: MA-125).");
            return false;
        }
        if (fechaEntrada !== new Date().toISOString().split('T')[0]) {
            alert("La fecha de entrada debe ser la fecha actual.");
            return false;
        }
        if (fechaSalida < fechaEntrada) {
            alert("La fecha de salida no puede ser anterior a la fecha de entrada.");
            return false;
        }
        if (!regexObs.test(observaciones)) {
            alert("Las observaciones solo pueden contener letras, espacios y signos básicos.");
            return false;
        }
        return true;
    }
    </script>
</head>
<body>

<nav>
    <a href="Index_Entrada.php"><i class="fas fa-home"></i> Volver al Inicio</a>
    <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a>
</nav>

<h2>Registro de Buque</h2>

<?php if ($mensaje): ?>
    <p><?= $mensaje ?></p>
<?php endif; ?>

<form name="formBuque" action="entrada_recepcion.php" method="post" enctype="multipart/form-data" onsubmit="return validarFormulario();">
    <label>Nombre del Buque*:</label>
    <input type="text" name="nombre" required>

    <label>Matrícula* (Ejemplo: MA-125):</label>
    <input type="text" name="matricula" required maxlength="6" placeholder="MA-123">

    <label>Tipo de Buque*:</label>
    <select name="tipo" required>
        <option value="">-- Selecciona un tipo --</option>
        <?php foreach ($tiposBuques as $tipoBuque): ?>
            <option value="<?= $tipoBuque ?>"><?= $tipoBuque ?></option>
        <?php endforeach; ?>
    </select>

    <label>Documentación (PDF, max 2MB)*:</label>
    <input type="file" name="documentacion" accept="application/pdf" required>

    <label>Fecha de Entrada*:</label>
    <input type="date" name="fecha_entrada" required value="<?= date('Y-m-d') ?>" readonly>

    <label>Fecha de Salida*:</label>
    <input type="date" name="fecha_salida" required>

    <label>Estado del Buque*:</label>
    <select name="estado" required>
        <option value="">-- Selecciona un estado --</option>
        <option value="en_revision">En Revisión</option>
        <option value="en_mantenimiento">En Mantenimiento</option>
        <option value="listo_para_salida">Listo para Salida</option>
    </select>

    <label>Observaciones:</label>
    <textarea name="observaciones"></textarea>

    <div class="botones">
    <button type="submit"><i class="fas fa-ship"></i> Registrar</button>
    <button type="button" onclick="limpiarFormulario()"><i class="fas fa-eraser"></i> Limpiar</button>
</div>
 
</form>
<script>
    function limpiarFormulario() {
        if (confirm("¿Estás seguro de que deseas limpiar todos los campos?")) {
            document.forms["formBuque"].reset();
        }
    }
</script>


</body>
</html>

