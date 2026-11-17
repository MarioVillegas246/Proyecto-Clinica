<?php
session_start();

// 1. Si no ha iniciado sesión, regresarlo al Login
if (!isset($_SESSION['usuario_id'])) {
    header("Location: Login.php");
    exit();
}
?>

<?php
// Incluir conexión a PostgreSQL para obtener datos reales
require_once "conexionposgres.php";

try {
    // 1. Contar el total de medicamentos en la tabla
    $sqlTotal = "SELECT COUNT(*) FROM medicamentos";
    $totalMedicamentos = $conexion->query($sqlTotal)->fetchColumn();

    // 2. Contar el total de medicamentos con stock crítico (menor o igual a 5)
    $sqlCritico = "SELECT COUNT(*) FROM medicamentos WHERE stock <= 5";
    $totalCriticos = $conexion->query($sqlCritico)->fetchColumn();

} catch (PDOException $e) {
    // Valores por defecto en caso de error de conexión
    $totalMedicamentos = 0;
    $totalCriticos = 0;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Principal - Clínica Emanuel</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="estilos/style.css">
</head>
<body class="app-layout">

    <!-- BARRA LATERAL (SIDEBAR) -->
    <aside class="sidebar">
        <div class="brand-container">
            <img src="img/logo.jpeg" alt="Clínica Emanuel" class="sidebar-logo">
            <span class="brand-name">Clínica Emanuel</span>
        </div>

        <div class="user-profile">
            <div class="avatar-placeholder">👤</div>
            <div class="user-info">
                <!-- Mostramos de forma dinámica el rol actual -->
                <span class="role-badge"><?php echo htmlspecialchars(ucfirst($_SESSION['rol'] ?? 'Recepción')); ?></span>
            </div>
        </div>

        <nav class="sidebar-nav">
            <!-- Módulo Inventario -->
            <div class="nav-dropdown">
                <button class="dropdown-toggle">
                    <span>📦 Inventario</span>
                    <span class="arrow">▼</span>
                </button>
                <div class="dropdown-menu-content">
                    <a href="inventario.php" class="nav-link">Almacén</a>
                </div>
            </div>

            <!-- Módulo Registro -->
            <div class="nav-dropdown">
                <button class="dropdown-toggle">
                    <span>📋 Registro</span>
                    <span class="arrow">▼</span>
                </button>
                <div class="dropdown-menu-content">
                    <a href="registro.php" class="nav-link">Nuevo medicamento</a>
                </div>
            </div>

            <!-- Módulo Caja -->
            <div class="nav-dropdown">
                <button class="dropdown-toggle">
                    <span>💰 Caja</span>
                    <span class="arrow">▼</span>
                </button>
                <div class="dropdown-menu-content">
                    <a href="#" class="nav-link">Hoja de consumo</a>
                </div>
            </div>
        </nav>
    </aside>

    <!-- CONTENEDOR PRINCIPAL -->
    <div class="main-content-wrapper">
        
        <!-- Barra Superior -->
        <header class="top-navbar">
            <div class="navbar-title">
                <h2>Panel de Control</h2>
            </div>
            
            <div class="top-actions" style="display: flex; gap: 10px; align-items: center;">
                
                <!-- 🔒 BOTÓN EXCLUSIVO PARA ADMINISTRADOR -->
                <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin'): ?>
                    <a href="registro_usuario.php" class="btn-admin" style="background-color: #007bff; color: white; padding: 8px 14px; border-radius: 6px; text-decoration: none; font-size: 14px; font-weight: 500; display: inline-flex; align-items: center; gap: 5px;">
                        <span>➕ Añadir Usuario</span>
                    </a>
                <?php endif; ?>

                <!-- Botón de Cerrar Sesión -->
                <a href="login.php" class="btn-logout" onclick="return confirm('¿Estás seguro de cerrar sesión?');">
                    <span>Cerrar sesión</span> 🚪
                </a>
            </div>
        </header>

        <!-- Área de Trabajo (Dashboard Operativo) -->
        <main class="dashboard-workspace">
            
            <!-- Banner de bienvenida -->
            <div class="welcome-banner">
                <div>
                    <h1>¡Bienvenido, <?php echo htmlspecialchars($_SESSION['nombre'] ?? 'Usuario'); ?>!</h1>
                    <p class="quote">"Tu sonrisa y tu orden son el primer paso hacia la tranquilidad de cada paciente."</p>
                </div>
            </div>

            <!-- Tarjetas de Métricas / Resumen dinámicas -->
            <div class="stats-grid">
                <!-- Tarjeta Total Medicamentos -->
                <div class="stat-card">
                    <div class="stat-icon blue">📦</div>
                    <div class="stat-details">
                        <span class="stat-title">Total Medicamentos</span>
                        <h3 class="stat-value"><?= number_format($totalMedicamentos) ?></h3>
                        <span class="stat-subtext">En almacén</span>
                    </div>
                </div>

                <!-- Tarjeta Interactiva de Stock Crítico -->
                <a href="inventario.php?filtro=bajo" class="stat-card clickable-card">
                    <div class="stat-icon warning">⚠️</div>
                    <div class="stat-details">
                        <span class="stat-title">Stock Crítico</span>
                        <h3 class="stat-value text-warning"><?= number_format($totalCriticos) ?></h3>
                        <span class="stat-subtext">Por agotarse (Ver más)</span>
                    </div>
                </a>

                <!-- Tarjeta Consumos Hoy -->
                <div class="stat-card">
                    <div class="stat-icon green">📄</div>
                    <div class="stat-details">
                        <span class="stat-title">Consumos Hoy</span>
                        <h3 class="stat-value">14</h3>
                        <span class="stat-subtext">Hojas registradas</span>
                    </div>
                </div>
            </div>

            <!-- Bloque Inferior: Alertas e Histórico de Actividad -->
            <div class="dashboard-sections">
                <!-- Alertas de Inventario -->
                <div class="content-card">
                    <div class="card-header">
                        <h3>⚠️ Alertas de Almacén</h3>
                    </div>
                    <ul class="alert-list">
                        <li class="alert-item">
                            <span class="badge danger">Agotado</span>
                            <span class="item-name">Paracetamol 500mg (Solución)</span>
                            <span class="item-qty">0 unidades</span>
                        </li>
                        <li class="alert-item">
                            <span class="badge warning">Stock Bajo</span>
                            <span class="item-name">Jeringas 5ml estériles</span>
                            <span class="item-qty">4 unidades</span>
                        </li>
                        <li class="app-item">
                            <span class="badge warning">Stock Bajo</span>
                            <span class="item-name">Solución Salina 0.9%</span>
                            <span class="item-qty">2 unidades</span>
                        </li>
                    </ul>
                </div>

                <!-- Últimos Movimientos -->
                <div class="content-card">
                    <div class="card-header">
                        <h3>🕒 Últimas Acciones</h3>
                    </div>
                    <ul class="activity-list">
                        <li>
                            <span class="time">10:42 AM</span>
                            <span class="desc">Registro de medicamento <b>Amoxicilina 500mg</b></span>
                        </li>
                        <li>
                            <span class="time">09:15 AM</span>
                            <span class="desc">Hoja de consumo generada <b>#HC-104</b></span>
                        </li>
                        <li>
                            <span class="time">08:30 AM</span>
                            <span class="desc">Inicio de sesión en recepción</span>
                        </li>
                    </ul>
                </div>
            </div>

        </main>
    </div>

    <!-- Script para menús desplegables -->
    <script>
        const dropdowns = document.querySelectorAll('.dropdown-toggle');

        dropdowns.forEach(dropdown => {
            dropdown.addEventListener('click', function() {
                this.classList.toggle('active');
                const content = this.nextElementSibling;
                if (content.style.maxHeight) {
                    content.style.maxHeight = null;
                } else {
                    content.style.maxHeight = content.scrollHeight + "px";
                }
            });
        });
    </script>

</body>
</html>