<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/../config/conexionposgres.php';

if (!isset($conexion)) {
    http_response_code(500);
    echo json_encode(["error" => "Error de conexión a la base de datos."]);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];
$inputData = json_decode(file_get_contents("php://input"), true);

if ($method === 'POST') {
    if (!empty($inputData['correo']) && !empty($inputData['password'])) {
        try {
            $stmt = $conexion->prepare("SELECT * FROM usuarios WHERE correo = ?");
            $stmt->execute([$inputData['correo']]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($usuario && password_verify($inputData['password'], $usuario['password'])) {
                
                // 1. Generar token único
                $token = bin2hex(random_bytes(32));

                // 2. Definir tiempo de expiración (Ej: expira en 2 horas)
                $token_expiration = date('Y-m-d H:i:s', strtotime('+2 hours'));

                // 3. Guardar token y expiración en la base de datos
                $updateStmt = $conexion->prepare("UPDATE usuarios SET token = ?, token_expiration = ? WHERE id = ?");
                $updateStmt->execute([$token, $token_expiration, $usuario['id']]);

                http_response_code(200);
                echo json_encode([
                    "exito" => true,
                    "mensaje" => "Inicio de sesión exitoso",
                    "token" => $token,
                    "expira_en" => "2 horas"
                ]);

            } else {
                http_response_code(401);
                echo json_encode(["error" => "Correo o contraseña incorrectos"]);
            }

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["error" => "Error en el servidor: " . $e->getMessage()]);
        }
    } else {
        http_response_code(400);
        echo json_encode(["error" => "Faltan datos (correo y password son obligatorios)"]);
    }
} else {
    http_response_code(405);
    echo json_encode(["error" => "Método no permitido"]);
}
?>