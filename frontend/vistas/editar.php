<?php
require_once "../../backend/config/conexionposgres.php";

$codigo = $_GET['codigo'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $codigo_nuevo = $_POST['codigo_medicamento'];
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $stock = $_POST['stock'];
    $precio = $_POST['precio'];
    $codigo_antiguo = $_POST['codigo_antiguo'];

    try {
        $sql = "UPDATE medicamentos SET codigo_medicamento = ?, nombre = ?, descripcion = ?, stock = ?, precio = ? WHERE codigo_medicamento = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->execute([$codigo_nuevo, $nombre, $descripcion, $stock, $precio, $codigo_antiguo]);

        header("Location: inventario.php");
        exit();
    } catch (PDOException $e) {
        $error = "Error al actualizar: " . $e->getMessage();
    }
} else {
    try {
        $stmt = $conexion->prepare("SELECT * FROM medicamentos WHERE codigo_medicamento = ?");
        $stmt->execute([$codigo]);
        $medicamento = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$medicamento) {
            header("Location: inventario.php");
            exit();
        }
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Medicamento</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f2f2f2; padding: 30px; }
        .contenedor { max-width: 600px; margin: auto; background-color: white; padding: 25px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.15); }
        h1 { color: #333; text-align: center; }
        label { display: block; margin-top: 15px; font-weight: bold; }
        input, textarea { width: 100%; padding: 8px; margin-top: 5px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px; }
        button { background-color: #007bff; color: white; border: none; padding: 10px 15px; margin-top: 20px; cursor: pointer; border-radius: 4px; width: 100%; font-size: 16px; }
        button:hover { background-color: #0056b3; }
        .regresar { display: inline-block; margin-bottom: 10px; color: #007bff; text-decoration: none; }
    </style>
</head>
<body>
    <div class="contenedor">
        <a class="regresar" href="inventario.php">&larr; Regresar</a>
        <h1>Editar Medicamento</h1>

        <?php if (isset($error)): ?>
            <p style="color: red;"><?= $error ?></p>
        <?php endif; ?>

        <form action="editar.php" method="POST">
            <input type="hidden" name="codigo_antiguo" value="<?= htmlspecialchars($medicamento['codigo_medicamento']) ?>">

            <label>Código del Medicamento:</label>
            <input type="text" name="codigo_medicamento" value="<?= htmlspecialchars($medicamento['codigo_medicamento']) ?>" required>

            <label>Nombre:</label>
            <input type="text" name="nombre" value="<?= htmlspecialchars($medicamento['nombre']) ?>" required>

            <label>Descripción:</label>
            <textarea name="descripcion"><?= htmlspecialchars($medicamento['descripcion']) ?></textarea>

            <label>Stock:</label>
            <input type="number" name="stock" value="<?= htmlspecialchars($medicamento['stock']) ?>" required>

            <label>Precio:</label>
            <input type="number" step="0.01" name="precio" value="<?= htmlspecialchars($medicamento['precio']) ?>" required>

            <button type="submit">Guardar Cambios</button>
        </form>
    </div>
</body>
</html>