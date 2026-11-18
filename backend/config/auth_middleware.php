<?php
function verificarToken($conexion) {
    $headers = apache_request_headers();
    $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? '';

    if (!empty($authHeader) && preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
        $tokenEnviado = $matches[1];

        // Buscar el usuario que tenga este token
        $stmt = $conexion->prepare("SELECT id, nombre, correo, token_expiration FROM usuarios WHERE token = ?");
        $stmt->execute([$tokenEnviado]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario) {
            $tiempoActual = time();
            $tiempoExpiracion = strtotime($usuario['token_expiration']);

            // Validar si el token sigue dentro del tiempo permitido
            if ($tiempoActual < $tiempoExpiracion) {
                return $usuario; // ¡Token válido! Retorna los datos del usuario
            } else {
                // El token expiró: Limpiamos los campos en la base de datos automáticamente
                $stmtClear = $conexion->prepare("UPDATE usuarios SET token = NULL, token_expiration = NULL WHERE id = ?");
                $stmtClear->execute([$usuario['id']]);

                http_response_code(401);
                echo json_encode(["error" => "El token ha expirado. Por favor, inicia sesión nuevamente."]);
                exit();
            }
        }
    }

    http_response_code(401);
    echo json_encode(["error" => "Acceso no autorizado. Token inválido o ausente."]);
    exit();
}
?>