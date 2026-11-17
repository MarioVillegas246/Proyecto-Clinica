<?php
// Incluir conexión a PostgreSQL
require_once "conexionposgres.php";

// Capturar parámetros de búsqueda y filtro
$busqueda = trim($_GET['busqueda'] ?? '');
$filtro = $_GET['filtro'] ?? '';

try {
    if ($busqueda !== '') {
        // Búsqueda por código
        $sql = "SELECT * FROM medicamentos WHERE codigo_medicamento ILIKE ? ORDER BY codigo_medicamento ASC";
        $stmt = $conexion->prepare($sql);
        $stmt->execute(['%' . $busqueda . '%']);
    } elseif ($filtro === 'bajo') {
        // Filtrar stock más bajo (menor o igual a 5 unidades para alinear con el panel)
        $sql = "SELECT * FROM medicamentos WHERE stock <= 5 ORDER BY stock ASC";
        $stmt = $conexion->query($sql);
    } else {
        // Consulta normal mostrando todos
        $sql = "SELECT * FROM medicamentos ORDER BY codigo_medicamento ASC";
        $stmt = $conexion->query($sql);
    }

    $medicamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Error al consultar los productos: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Productos - Clínica Emanuel</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">
    <!-- Vinculación del CSS de Inventario -->
    <link rel="stylesheet" href="estilos/inventario.css">
</head>

<body>

    <div class="contenedor">

        <!-- Cabecera de la página -->
        <div class="encabezado-pagina">
            <a class="regresar" href="principal.index.php">← Regresar</a>
            <h1>Inventario de Almacén</h1>
        </div>

        <!-- Barra de Búsqueda y Filtros -->
        <div class="barra-busqueda">
            <form method="GET" action="inventario.php" class="grupo-busqueda-input">
                <input type="text" name="busqueda" placeholder="Buscar por código (ej. 99A)" value="<?= htmlspecialchars($busqueda) ?>">
                <button type="submit" class="btn-buscar">Buscar</button>
            </form>

            <div class="grupo-filtros">
                
                <?php if ($busqueda !== '' || $filtro !== ''): ?>
                    <a href="inventario.php" class="btn-limpiar">Mostrar todos</a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Contenedor con borde y sombra para la tabla -->
        <div class="tabla-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Stock</th>
                        <th>Precio</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (!empty($medicamentos)): ?>
                        <?php foreach ($medicamentos as $medicamento): ?>
                            <tr>
                                <td>
                                    <span class="codigo-badge"><?= htmlspecialchars((string)$medicamento["codigo_medicamento"]) ?></span>
                                </td>

                                <td class="nombre-producto">
                                    <?= htmlspecialchars($medicamento["nombre"]) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($medicamento["descripcion"] ?? "") ?>
                                </td>

                                <td>
                                    <?php if ($medicamento["stock"] <= 5): ?>
                                        <span class="badge-stock bajo"><?= htmlspecialchars((string)$medicamento["stock"]) ?> (Bajo)</span>
                                    <?php else: ?>
                                        <span class="badge-stock normal"><?= htmlspecialchars((string)$medicamento["stock"]) ?></span>
                                    <?php endif; ?>
                                </td>

                                <td class="precio-col">
                                    $<?= number_format((float)$medicamento["precio"], 2) ?>
                                </td>

                                <td>
                                    <div class="acciones-cell">
                                        <a href="editar.php?codigo=<?= urlencode($medicamento["codigo_medicamento"]) ?>" class="btn-accion-editar">Editar</a>
                                        <a href="eliminar.php?codigo=<?= urlencode($medicamento["codigo_medicamento"]) ?>" onclick="return confirm('¿Estás seguro de que deseas eliminar este medicamento?');" class="btn-accion-eliminar">Eliminar</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="sin-registros">
                                No se encontraron medicamentos registrados o coincidentes.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>

</body>

</html>