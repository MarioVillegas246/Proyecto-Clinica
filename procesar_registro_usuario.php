<?php
session_start();
require_once 'conexionposgres.php';

// Opcional: Asegurar que solo un administrador pueda registrar nuevos usuarios
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: principal.index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = trim($_POST['nombre']);
    $correo = trim($_POST['correo']);
    $password_plana = trim($_POST['password']);
    $rol = trim($_POST['rol']); // Recoge 'admin' o 'usuario' del select

    try {
        // 1. Encriptar la contraseña de forma segura para que funcione con password_verify()
        $password_hash = password_hash($password_plana, PASSWORD_DEFAULT);

        // 2. Insertar en la base de datos PostgreSQL usando PDO
        $sql = "INSERT INTO public.usuarios (nombre, correo, password, rol) VALUES (:nombre, :correo, :password, :rol)";
        $stmt = $conexion->prepare($sql);
        
        $stmt->execute([
            'nombre' => $nombre,
            'correo' => $correo,
            'password' => $password_hash,
            'rol' => $rol
        ]);

        echo "<script>alert('¡Usuario registrado con éxito!'); window.location='principal.index.php';</script>";

    } catch (PDOException $e) {
        // Si el correo ya existe (violación de índice único)
        if ($e->getCode() == 23505) {
            echo "<script>alert('Error: El correo electrónico ya está registrado.'); window.history.back();</script>";
        } else {
            echo "Error en la base de datos: " . $e->getMessage();
        }
    }
}
?>