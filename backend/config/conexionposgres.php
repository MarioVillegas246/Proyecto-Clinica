<?php

// Datos de conexión a PostgreSQL
$servidor = "localhost";
$puerto = "5432";
$usuario = "postgres";
$contrasena = "Mario20406";
$base_datos = "clinica";

try {

    // Crear conexión con PostgreSQL
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