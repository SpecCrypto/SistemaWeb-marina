<?php
session_start();

if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'taller') {
    header("Location: login.php?error=2");
    exit;
}
include 'Conexion_db.php';

$usuario = $_SESSION['usuario'];
$mensaje = "";

// Procesar validación o rechazo
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ticket_id = intval($_POST['ticket_id']);
    $accion = $_POST['accion'];
    $motivo_rechazo = $accion === 'rechazar' ? trim($_POST['motivo_rechazo']) : null;

    if ($accion === 'validar') {
        $stmt = $conexion->prepare("UPDATE tickets SET estado='validado', fecha_validacion=NOW(), validado_por=?, motivo_rechazo=NULL WHERE id=?");
        $stmt->bind_param("si", $usuario, $ticket_id);
        $stmt->execute();
        $stmt->close();
        $mensaje = "✅ Ticket validado correctamente.";
    } elseif ($accion === 'rechazar' && !empty($motivo_rechazo)) {
        $stmt = $conexion->prepare("UPDATE tickets SET estado='rechazado', motivo_rechazo=?, fecha_validacion=NULL, validado_por=NULL WHERE id=?");
        $stmt->bind_param("si", $motivo_rechazo, $ticket_id);
        $stmt->execute();
        $stmt->close();
        $mensaje = "❌ Ticket rechazado correctamente.";
    } else {
        $mensaje = "⚠️ Debe proporcionar un motivo para el rechazo.";
    }
}

// Consulta de tickets pendientes
$query = "SELECT t.id, t.codigo, t.fecha_creacion, b.nombre AS nombre_buque, b.matricula
          FROM tickets t
          JOIN buques b ON t.buque_id = b.id
          WHERE t.estado = 'pendiente'";
$resultado = $conexion->query($query);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Tickets Pendientes</title>
    <link rel="stylesheet" href="taller_tickets.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        /* Botón Validar (verde) */
        button.validar {
            background-color: #28a745;
            color: white;
            padding: 8px 14px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        button.validar:hover {
            background-color: #218838;
        }

        /* Botón Rechazar (rojo) */
        button.rechazar {
            background-color: #dc3545;
            color: white;
            padding: 8px 14px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s ease;
            margin-left: 5px;
        }

        button.rechazar:hover {
            background-color: #c82333;
        }

        /* Contenedor de motivo de rechazo */
        div[id^="rechazo-motivo-"] {
            display: none;
            margin-top: 10px;
            background-color: #f8d7da;
            padding: 15px;
            border-radius: 6px;
            border: 1px solid #f5c6cb;
            max-width: 500px;
        }

        div[id^="rechazo-motivo-"] textarea {
            width: 100%;
            height: 100px;
            resize: vertical;
            padding: 10px;
            font-size: 14px;
            border-radius: 4px;
            border: 1px solid #ccc;
            margin-top: 8px;
        }
    </style>

    <script>
        function mostrarMotivo(ticketId) {
            const div = document.getElementById("rechazo-motivo-" + ticketId);
            div.style.display = (div.style.display === "none" || div.style.display === "") ? "block" : "none";
        }
    </script>
</head>
<body>

    <header>
        <h1>Panel de Tickets Pendientes</h1>
        <nav>
            <a href="Index_Entrada.php"><i class="fas fa-home"></i> Volver al Inicio</a>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a>
        </nav>
    </header>

    <main>
        <section>
            <h2>Lista de Tickets Pendientes</h2>

            <?php if (!empty($mensaje)): ?>
                <p style="color: green;"><strong><?= htmlspecialchars($mensaje) ?></strong></p>
            <?php endif; ?>

            <?php if ($resultado->num_rows > 0): ?>
                <table class="tabla-tickets">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Código</th>
                            <th>Fecha</th>
                            <th>Buque</th>
                            <th>Matrícula</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($ticket = $resultado->fetch_assoc()): ?>
                            <tr>
                                <td><?= $ticket['id'] ?></td>
                                <td><?= htmlspecialchars($ticket['codigo']) ?></td>
                                <td><?= htmlspecialchars($ticket['fecha_creacion']) ?></td>
                                <td><?= htmlspecialchars($ticket['nombre_buque']) ?></td>
                                <td><?= htmlspecialchars($ticket['matricula']) ?></td>
                                <td>
                                    <!-- Botón VALIDAR -->
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="ticket_id" value="<?= $ticket['id'] ?>">
                                        <input type="hidden" name="accion" value="validar">
                                        <button type="submit" class="validar">Validar</button>
                                    </form>

                                    <!-- Botón RECHAZAR -->
                                    <button onclick="mostrarMotivo(<?= $ticket['id'] ?>)" class="rechazar">Rechazar</button>

                                    <!-- Caja de texto para motivo de rechazo -->
                                    <div id="rechazo-motivo-<?= $ticket['id'] ?>">
                                        <form method="POST">
                                            <input type="hidden" name="ticket_id" value="<?= $ticket['id'] ?>">
                                            <input type="hidden" name="accion" value="rechazar">
                                            <textarea name="motivo_rechazo" placeholder="Escribe el motivo del rechazo..." required></textarea>
                                            <br>
                                            <button type="submit" class="rechazar">Confirmar Rechazo</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p><strong>No hay tickets pendientes actualmente.</strong></p>
            <?php endif; ?>
        </section>
    </main>

    <footer>
        <p>&copy; 2025 Armada Nacional - Sistema de validación de Tickets</p>
    </footer>

</body>
</html>

