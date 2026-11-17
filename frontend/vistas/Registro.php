<?php
// Incluir conexión a PostgreSQL
require_once "conexionposgres.php";

$mensaje = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $codigo = trim($_POST['codigo_medicamento']);
    $nombre = trim($_POST['nombre']);
    $descripcion = trim($_POST['descripcion']);
    $stock = trim($_POST['stock']);
    $precio = trim($_POST['precio']);

    // Validar que los campos obligatorios no estén vacíos
    if (!empty($codigo) && !empty($nombre) && $stock !== '' && $precio !== '') {
        try {
            // Verificar si el código ya existe
            $check = $conexion->prepare("SELECT COUNT(*) FROM medicamentos WHERE codigo_medicamento = ?");
            $check->execute([$codigo]);
            
            if ($check->fetchColumn() > 0) {
                // Alerta solicitada cuando el medicamento ya existe
                $error = "medicamento ya existente porfavor revisa la informacion";
            } else {
                // Insertar el nuevo medicamento
                $sql = "INSERT INTO medicamentos (codigo_medicamento, nombre, descripcion, stock, precio) VALUES (?, ?, ?, ?, ?)";
                $stmt = $conexion->prepare($sql);
                $stmt->execute([$codigo, $nombre, $descripcion, $stock, $precio]);

                // Redirigir al inventario después de guardar exitosamente
                header("Location: inventario.php");
                exit();
            }
        } catch (PDOException $e) {
            $error = "Error al registrar el medicamento: " . $e->getMessage();
        }
    } else {
        $error = "Por favor, completa todos los campos obligatorios.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Nuevo Medicamento - Clínica Emanuel</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">
    <!-- Vinculamos el archivo de estilos externo -->
    <link rel="stylesheet" href="estilos/registro.css">
</head>
<body>

    <div class="contenedor">

        <div class="encabezado-pagina">
            <a class="regresar" href="principal.index.php">← Regresar</a>
            <h1>Registrar Medicamentos</h1>
            <p class="subtitulo">Ingresa los detalles del medicamento para incorporarlo al almacén.</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alerta-error">
                <span>⚠️</span> <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form action="registro.php" method="POST" class="form-registro">
            <div class="form-grupo">
                <label class="form-label">Código del Medicamento</label>
                <input type="text" name="codigo_medicamento" placeholder="Ej. MED-005" class="form-input" required>
            </div>

            <div class="form-grupo">
                <label class="form-label">Nombre del Medicamento</label>
                <input type="text" name="nombre" placeholder="Ej. Paracetamol 500mg" class="form-input" required>
            </div>

            <div class="form-grupo">
                <label class="form-label">Descripción</label>
                <textarea name="descripcion" placeholder="Detalles, concentración o usos del medicamento..." rows="3" class="form-textarea"></textarea>
            </div>

            <div class="form-row">
                <div class="form-grupo">
                    <label class="form-label">Stock (Cantidad inicial)</label>
                    <input type="number" name="stock" placeholder="0" min="0" class="form-input" required>
                </div>

                <div class="form-grupo">
                    <label class="form-label">Precio ($)</label>
                    <input type="number" step="0.01" name="precio" placeholder="0.00" min="0" class="form-input" required>
                </div>
            </div>

            <div class="form-acciones">
                <button type="submit" class="form-btn-submit">Guardar Medicamento</button>
            </div>
        </form>

    </div>

</body>
</html>