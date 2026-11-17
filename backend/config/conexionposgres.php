<?php

$envPath = __DIR__ . '/../../.env';
if (file_exists($envPath)) {
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        list($key, $value) = explode('=', $line, 2);
        $_ENV[trim($key)] = trim($value);
    }
}

$servidor = $_ENV['DB_HOST'] ?? 'localhost';
$puerto = $_ENV['DB_PORT'] ?? '5432';
$base_datos = $_ENV['DB_NAME'] ?? '';
$usuario = $_ENV['DB_USER'] ?? '';
$contrasena = $_ENV['DB_PASS'] ?? '';

try {
    
    $conexion = new PDO(
        "pgsql:host=$servidor;port=$puerto;dbname=$base_datos",
        $usuario,
        $contrasena
    );

    // Configurar errores
    $conexion->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );

    // Configurar codificación
    $conexion->exec("SET CLIENT_ENCODING TO 'UTF8'");

} catch (PDOException $e) {

    die("Error de conexión: " . $e->getMessage());

}

?>