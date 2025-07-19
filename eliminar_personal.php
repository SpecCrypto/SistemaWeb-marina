<?php
require_once 'Conexion_db.php'; // Asegúrate de que $conexion es un objeto mysqli

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Iniciar transacción
    $conexion->begin_transaction();

    try {
        // Eliminar de la tabla usuario
        $stmtUsuario = $conexion->prepare("DELETE FROM usuarios WHERE personal_id = ?");
        $stmtUsuario->bind_param("i", $id);
        $stmtUsuario->execute();
        $stmtUsuario->close();

        // Eliminar de la tabla personal
        $stmtPersonal = $conexion->prepare("DELETE FROM personal WHERE id = ?");
        $stmtPersonal->bind_param("i", $id);
        $stmtPersonal->execute();
        $stmtPersonal->close();

        // Confirmar transacción
        $conexion->commit();

        // Redirigir a la tabla principal
        header("Location: editar_personal.php?msg=eliminado");
        exit;

    } catch (Exception $e) {
        // Revertir cambios si ocurre un error
        $conexion->rollback();
        echo "Error al eliminar el registro: " . $e->getMessage();
    }

} else {
    echo "ID no proporcionado.";
}
?>
