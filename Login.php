<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión - Clínica Emanuel</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">
    <!-- Archivo de estilos externo -->
    <link rel="stylesheet" href="estilos/login.css">
</head>
<body>

    <div class="page-container">
    
        <!-- Lado Izquierdo: Imagen Institucional / Fondo -->
        <div class="left-side">
            <div class="brand-overlay">
                <div class="brand-badge">ClinicWeb</div>
                <p>Gestión eficiente de inventarios y almacén </p>
            </div>
        </div>

        <!-- Lado Derecho: Formulario de Acceso -->
        <div class="right-side">
            <div class="login-card">
                <div class="login-header">
                    <h2>Bienvenido</h2>
                </div>
                
                <form action="validar_login.php" method="POST">
                    <div class="input-group">
                        <label for="correo">Usuario</label>
                        <input type="text" id="correo" name="correo" placeholder="Admi@gmail.com" required>
                    </div>

                    <div class="input-group">
                        <label for="password">Contraseña</label>
                        <input type="password" id="password" name="password" placeholder="••••••••" required>
                    </div>

                    <button type="submit" class="btn-login">Acceder al Sistema</button>
                </form>

                <div class="footer-link">
                    <a href="#">¿No tienes cuenta? Crear Cuenta</a>
                </div>
            </div>
        </div>

    </div>

</body>
</html>