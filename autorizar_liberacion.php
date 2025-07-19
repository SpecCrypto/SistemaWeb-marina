<?php
include 'Conexion_db.php'; // Tu archivo de conexión a la base de datos

if (isset($_GET['id'])) {
    $orden_id = intval($_GET['id']);

    // Obtener el buque_id desde la orden
    $consulta = "SELECT buque_id FROM ordenes_trabajo WHERE id = $orden_id";
    $resultado = mysqli_query($conexion, $consulta);
    if ($fila = mysqli_fetch_assoc($resultado)) {
        $buque_id = $fila['buque_id'];

        // Cambiar estado de orden a 'completada'
        $updateOrden = "UPDATE ordenes_trabajo SET estado = 'completada', fecha_fin = NOW() WHERE id = $orden_id";
        mysqli_query($conexion, $updateOrden);

        // Cambiar estado del buque a 'liberado'
        $updateBuque = "UPDATE buques SET estado = 'liberado', fecha_salida = NOW() WHERE id = $buque_id";
        mysqli_query($conexion, $updateBuque);

        header("Location: admin_bitacora.php?exito=1");
        exit;
    } else {
        echo "Orden no encontrada.";
    }
} else {
    echo "ID inválido.";
}
?>
