<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/../config/conexionposgres.php';
require_once __DIR__ . '/../config/auth_middleware.php';

if (!isset($conexion)) {
    http_response_code(500);
    echo json_encode(["error" => "No se pudo establecer la conexión con la base de datos."]);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];
$inputData = json_decode(file_get_contents("php://input"), true);

// PROTECCIÓN CONDICIONAL: 
// Si el método NO es POST (es decir, es GET, PUT o DELETE), exigimos el token.
// Si es POST (registro), lo dejamos pasar libremente.
if ($method !== 'POST') {
    $usuarioAutenticado = verificarToken($conexion);
}

switch ($method) {
    case 'GET':
        try {
            // En lugar de listar todos o buscar por ?id=, 
            // devolvemos directamente los datos del usuario al que pertenece el token
            echo json_encode([
                "exito" => true,
                "usuario" => $usuarioAutenticado
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["error" => "Error en la consulta: " . $e->getMessage()]);
        }
        break;

    case 'POST':
        if (!empty($inputData['nombre']) && !empty($inputData['correo']) && !empty($inputData['password'])) {
            try {
                $passwordHash = password_hash($inputData['password'], PASSWORD_DEFAULT);
                $rol = $inputData['rol'] ?? 'usuario';

                $stmt = $conexion->prepare("INSERT INTO usuarios (nombre, correo, password, rol) VALUES (?, ?, ?, ?)");
                $stmt->execute([$inputData['nombre'], $inputData['correo'], $passwordHash, $rol]);
                
                http_response_code(201);
                echo json_encode(["exito" => true, "mensaje" => "Usuario creado correctamente"]);
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(["error" => "Error al insertar: " . $e->getMessage()]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["error" => "Faltan datos obligatorios (nombre, correo, password)"]);
        }
        break;

    case 'PUT':
        if (isset($_GET['id']) && !empty($inputData['nombre']) && !empty($inputData['correo'])) {
            try {
                $stmt = $conexion->prepare("UPDATE usuarios SET nombre = ?, correo = ? WHERE id = ?");
                $stmt->execute([$inputData['nombre'], $inputData['correo'], $_GET['id']]);
                
                echo json_encode(["exito" => true, "mensaje" => "Usuario actualizado correctamente"]);
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(["error" => "Error al actualizar: " . $e->getMessage()]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["error" => "Falta el ID en la URL o datos incompletos"]);
        }
        break;

    case 'DELETE':
        if (isset($_GET['id'])) {
            try {
                $stmt = $conexion->prepare("DELETE FROM usuarios WHERE id = ?");
                $stmt->execute([$_GET['id']]);
                
                echo json_encode(["exito" => true, "mensaje" => "Usuario eliminado correctamente"]);
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(["error" => "Error al eliminar: " . $e->getMessage()]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["error" => "ID no especificado en la URL"]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["error" => "Método no permitido"]);
        break;
}
?>