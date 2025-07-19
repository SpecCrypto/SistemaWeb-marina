<?php
include 'Conexion_db.php'; // Conexión a la base de datos

$mensaje_exito = ""; // Variable para mensaje

if (isset($_POST['registrar_orden'])) {
    $buque_id = $_POST['buque_id'];
    $ticket_id = $_POST['ticket_id'];
    $codigo = $_POST['codigo'];
    $estado = 'confirmada'; // Estado fijo por defecto
    $prioridad = $_POST['prioridad'];
    $fecha_creacion = date('Y-m-d');
    $descripcion_trabajo = $_POST['descripcion_trabajo'];
    $observaciones = $_POST['observaciones'];

    if (!preg_match('/^ORD-\d{2}$/', $codigo)) {
        echo "<script>alert('Código inválido. Debe ser formato ORD-XX.');</script>";
    } elseif (!preg_match('/^[a-zA-ZÁÉÍÓÚáéíóúÑñ\s]+$/', $descripcion_trabajo)) {
        echo "<script>alert('La descripción solo debe contener letras y espacios.');</script>";
    } elseif (!empty($observaciones) && !preg_match('/^[a-zA-ZÁÉÍÓÚáéíóúÑñ\s]+$/', $observaciones)) {
        echo "<script>alert('Las observaciones solo deben contener letras y espacios.');</script>";
    } else {
        $sql = "INSERT INTO ordenes_trabajo 
                (buque_id, ticket_id, codigo, estado, prioridad, fecha_creacion, descripcion_trabajo, observaciones) 
                VALUES 
                ('$buque_id', '$ticket_id', '$codigo', '$estado', '$prioridad', '$fecha_creacion', '$descripcion_trabajo', '$observaciones')";
        mysqli_query($conexion, $sql);
        $mensaje_exito = "Orden registrada exitosamente.";
    }
}

$buques = mysqli_query($conexion, "SELECT id, nombre FROM buques");
$tickets = mysqli_query($conexion, "SELECT id, codigo FROM tickets");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Órdenes de Trabajo</title>
    <link rel="stylesheet" href="css/ordenes.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .success-message {
            color: green;
            text-align: center;
            margin-top: 20px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <header class="main-header">
        <div class="header-container">
            <div class="logo">
                <img src="images/Ancla.png" alt="Logo">
                <div class="logo-text">
                    <h1>Infraestructura Naval</h1>
                    <p>Gestión de Órdenes de Trabajo</p>
                </div>
            </div>
            <ul class="nav-menu">
                <li><a href="Index_Infraestructura.php"><i class="fas fa-home" style="margin-right: 8px;"></i>Inicio</a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt" style="margin-right: 8px;"></i>Cerrar Sesión</a></li>
            </ul>
        </div>
    </header>

    <section class="inventario-section">
        <div class="section-container">
            <h2>Registrar Nueva Orden de Trabajo</h2>

            <?php if (!empty($mensaje_exito)): ?>
                <div class="success-message"><?= $mensaje_exito ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <select name="buque_id" required>
                    <option value="">Seleccione Buque</option>
                    <?php while ($row = mysqli_fetch_assoc($buques)): ?>
                        <option value="<?= $row['id'] ?>"><?= $row['nombre'] ?></option>
                    <?php endwhile; ?>
                </select>

                <select name="ticket_id" required>
                    <option value="">Seleccione Ticket</option>
                    <?php while ($row = mysqli_fetch_assoc($tickets)): ?>
                        <option value="<?= $row['id'] ?>"><?= $row['codigo'] ?></option>
                    <?php endwhile; ?>
                </select>

                <input type="text" name="codigo" placeholder="Código (ORD-XX)" pattern="^ORD-\d{2}$" required>

                <select name="prioridad" required>
                    <option value="">Seleccione Prioridad</option>
                    <option value="baja">Baja</option>
                    <option value="media">Media</option>
                    <option value="alta">Alta</option>
                    <option value="critica">Crítica</option>
                </select>

                <input type="text" name="descripcion_trabajo" placeholder="Descripción del trabajo" required>
                <input type="text" name="observaciones" placeholder="Observaciones (opcional)">

                <div style="grid-column: span 2; text-align: center;">
                    <button type="submit" name="registrar_orden">Registrar Orden</button>
                </div>
            </form>

            <p style="margin-top: 30px; text-align: center;">
                Las órdenes registradas se asignarán al <strong>personal</strong> correspondiente.
            </p>
        </div>
    </section>

    <footer class="main-footer">
        <div class="footer-bottom">
            <p>© <?= date("Y") ?> Infraestructura Naval. Todos los derechos reservados.</p>
        </div>
    </footer>
</body>
</html>
