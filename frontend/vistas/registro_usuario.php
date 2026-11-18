<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Añadir Usuario - Clínica Emanuel</title>
    <link rel="stylesheet" href="estilos/style.css">
</head>
<body>
    <div class="form-container" style="max-width: 500px; margin: 50px auto; padding: 20px; background: #fff; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        <h2>Registrar Nuevo Usuario</h2>
        
        <!-- El formulario apunta al archivo que procesará la inserción -->
        <form id="formUsuario" style="...">
            
            <div class="input-group" style="margin-bottom: 15px;">
                <label for="nombre">Nombre Completo</label>
                <input type="text" id="nombre" name="nombre" required style="width: 100%; padding: 8px; margin-top: 5px;">
            </div>

            <div class="input-group" style="margin-bottom: 15px;">
                <label for="correo">Correo Electrónico</label>
                <input type="email" id="correo" name="correo" required style="width: 100%; padding: 8px; margin-top: 5px;">
            </div>

            <div class="input-group" style="margin-bottom: 15px;">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" required style="width: 100%; padding: 8px; margin-top: 5px;">
            </div>

            <div class="input-group" style="margin-bottom: 20px;">
                <label for="rol">Rol de Usuario</label>
                <select id="rol" name="rol" required style="width: 100%; padding: 8px; margin-top: 5px;">
                    <option value="usuario">Usuario (Recepción / Normal)</option>
                    <option value="admin">Administrador</option>
                </select>
            </div>

            <button type="submit" style="background: #007bff; color: white; padding: 10px 15px; border: none; border-radius: 5px; cursor: pointer; width: 100%;">Guardar Usuario</button>
        </form>
        
        <p style="margin-top: 15px; text-align: center;"><a href="principal.index.php">← Volver al Panel Principal</a></p>
    </div>
    <script>
document.getElementById('formUsuario').addEventListener('submit', async function(e) {
    e.preventDefault(); // Evita que la página se recargue

    const datos = {
        nombre: document.getElementById('nombre').value,
        correo: document.getElementById('correo').value,
        password: document.getElementById('password').value,
        rol: document.getElementById('rol').value
    };

    try {
        const respuesta = await fetch('http://localhost:8000/backend/api/usuarios.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(datos)
        });

        const resultado = await respuesta.json();

        if (respuesta.ok) {
            alert('¡Usuario guardado correctamente!');
            window.location.href = 'principal.index.php'; // O la vista que corresponda
        } else {
            alert('Error: ' + (resultado.error || 'No se pudo guardar'));
        }
    } catch (error) {
        console.error('Error de red:', error);
        alert('Hubo un error al conectar con el servidor.');
    }
});
</script>
</body>
</html>