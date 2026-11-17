<?php
session_start();
// Ruta de conexión corregida (de controller/ a config/)
require_once '../config/conexionposgres.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $correo = trim($_POST['correo']);
    $password = trim($_POST['password']);

    try {
        $sql = "SELECT * FROM usuarios WHERE correo = :correo";
        $stmt = $conexion->prepare($sql);
        $stmt->execute(['correo' => $correo]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario) {
            // 🔒 AQUÍ ESTÁ EL SECRETO: password_verify compara tu texto con el hash de la BD
            if (password_verify($password, $usuario['password'])) {
                
                // Si la contraseña coincide, guardamos las sesiones
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['nombre'] = $usuario['nombre'];
                $_SESSION['rol'] = $usuario['rol']; // 'admin'

                // Ruta corregida hacia el panel principal en frontend/vistas/
                header("Location: ../../frontend/vistas/principal.index.php");
                exit();

            } else {
                // Ruta corregida de retorno al Login
                echo "<script>alert('Contraseña incorrecta'); window.location='../../frontend/vistas/Login.php';</script>";
            }
        } else {
            // Ruta corregida de retorno al Login
            echo "<script>alert('El correo no está registrado'); window.location='../../frontend/vistas/Login.php';</script>";
        }

    } catch (PDOException $e) {
        echo "Error en la base de datos: " . $e->getMessage();
    }
}
?>