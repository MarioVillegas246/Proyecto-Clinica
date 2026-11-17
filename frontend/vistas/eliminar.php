<?php
require_once "../../backend/config/conexionposgres.php";

$codigo = $_GET['codigo'] ?? '';

if (!empty($codigo)) {
    try {
        $sql = "DELETE FROM medicamentos WHERE codigo_medicamento = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->execute([$codigo]);
    } catch (PDOException $e) {
        die("Error al eliminar el registro: " . $e->getMessage());
    }
}

// Redirigir de regreso al inventario
header("Location: inventario.php");
exit();
?>