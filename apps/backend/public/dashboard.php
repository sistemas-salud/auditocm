<?php
session_start();
// Asegúrate de que esta ruta sea correcta para tu proyecto
require_once __DIR__ . '/../src/auth.php'; 

// Lógica de autenticación (mantener o redirigir)
if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}

// Lógica para mostrar mensajes flotantes (modales)
$show_modal = false;
$modal_type = '';
$modal_title = '';
$modal_message = '';

if (isset($_SESSION['modal_message'])) {
    $show_modal = true;
    $modal_type = $_SESSION['modal_type'] ?? 'alerta';
    $modal_title = $_SESSION['modal_title'] ?? 'Notificación';
    $modal_message = $_SESSION['modal_message'];
    // IMPORTANTE: Borrar el mensaje de la sesión inmediatamente después de leerlo.
    unset($_SESSION['modal_message']);
    unset($_SESSION['modal_type']);
    unset($_SESSION['modal_title']);
}

// CORRECCIÓN PARA EVITAR TypeError: 
// Se verifica si $_SESSION['user'] es un array antes de intentar acceder a 'email'.
// Esto corrige el error cuando $_SESSION['user'] es accidentalmente una cadena (string).
$user_data = $_SESSION['user'];
$user_email = 'Usuario Desconocido'; // Valor por defecto

if (is_array($user_data) && isset($user_data['email'])) {
    $user_email = htmlspecialchars($user_data['email']);
} elseif (is_string($user_data)) {
    // Si es una cadena (el origen del error), asumimos que es el email
    $user_email = htmlspecialchars($user_data);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard URG</title>
    <link rel="stylesheet" href="css/styles.css">
    <script>
        // Función para mostrar/ocultar el modal
        function toggleModal(show) {
            const modal = document.getElementById('myModal');
            if (modal) {
                modal.style.display = show ? 'flex' : 'none';
            }
        }

        // Mostrar el modal si hay un mensaje al cargar la página
        window.onload = function() {
            <?php if ($show_modal): ?>
                toggleModal(true);
            <?php endif; ?>
        };

        // Función para cambiar la acción del formulario antes de enviarlo
        function setFormAction(action) {
            const form = document.getElementById('searchForm');
            form.action = action;
        }
    </script>
</head>
<body class="dashboard-body">

<div class="dashboard-container">
    <h2>Bienvenido al Dashboard, <?= $user_email ?></h2>

    <p>Utiliza las opciones de búsqueda a continuación:</p>

    <!-- Formulario principal de búsqueda -->
    <form id="searchForm" action="consultar_factura.php" method="GET">
        <label for="factura">Ingresar Número de Factura:</label>
        <input type="text" id="factura" name="factura" placeholder="Ej: 2024-XXXXX" required>

        <label for="anio">Ingresar el año:</label>
        <input type="text" id="factura" name="factura" placeholder="Ej: 2024" required>

        <div style="display: flex; gap: 10px; margin-top: 20px; flex-direction: column;">
            
            <!-- Botón 0: Consulta Simple (Original) -->
            <button 
                type="submit" 
                onclick="setFormAction('consultar_concepto.php')"
                >
                Consultar Concepto Migrantes
            </button>
        
            <!-- Botón 1: Consulta Simple (Original) -->
            <button 
                type="submit" 
                onclick="setFormAction('consultar_factura.php')"
                >
                Consultar Factura URG
            </button>

            <!-- Botón 2: Consulta Dual (Nuevo) -->
            <button 
                type="submit" 
                onclick="setFormAction('consultar_factura2.php')" 
                class="boton-guardar"
                style="background-color: #28a745;"
                >
                Prueba dos tablas (URG y NO POS)
            </button>

            <!-- Botón 3: Modifica  -->
            <button 
                type="submit" 
                onclick="setFormAction('modificar_factura.php')" 
                
                style="background-color: #28a745;"
                >
                Modificar factura
            </button>
        </div>

    </form>
    
    <div style="margin-top: 30px;">
        <a href="logout.php" class="volver" style="color: white; background-color: #dc3545; border-color: #dc3545;">Cerrar Sesión</a>
    </div>

</div>

<!-- Contenedor del Modal Flotante -->
<div id="myModal" class="modal-overlay" style="display: <?= $show_modal ? 'flex' : 'none'; ?>">
    <div class="modal-content">
        <h3 class="modal-title <?= $modal_type ?>"><?= $modal_title ?></h3>
        <p><?= $modal_message ?></p>
        <button class="modal-button" onclick="toggleModal(false)">Aceptar</button>
    </div>
</div>

</body>
</html>