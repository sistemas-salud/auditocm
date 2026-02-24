<?php
session_start();
require_once __DIR__ . '/../src/auth.php'; // conexión PDO

if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}

$pdo = getPDO();

$busqueda = $_GET['factura'] ?? ''; 
$facturas_urg_list = []; // Ahora es una lista (array de registros)
$facturas_no_pos_list = []; // Ahora es una lista (array de registros)
$mensaje_error = '';

try {
    if (!empty($busqueda)) {
        // --- 1. Consulta a facturas_urg: Se elimina LIMIT 1 y se usa fetchAll para obtener todos los registros ---
        $stmt_urg = $pdo->prepare("
            SELECT * FROM facturas_urg 
            WHERE TRIM(LOWER(factura)) = TRIM(LOWER(?))
        ");
        $stmt_urg->execute([$busqueda]);
        $facturas_urg_list = $stmt_urg->fetchAll(PDO::FETCH_ASSOC);

        // --- 2. Consulta a no_pos: Se elimina LIMIT 1 y se usa fetchAll para obtener todos los registros ---
        $stmt_nopos = $pdo->prepare("
            SELECT * FROM no_pos
            WHERE TRIM(LOWER(factura)) = TRIM(LOWER(?))
        ");
        $stmt_nopos->execute([$busqueda]);
        $facturas_no_pos_list = $stmt_nopos->fetchAll(PDO::FETCH_ASSOC);

        // Verificación de resultados (usamos empty() para arrays)
        if (empty($facturas_urg_list) && empty($facturas_no_pos_list)) {
             $mensaje_error = "⚠️ No se encontró la factura **" . htmlspecialchars($busqueda) . "** en ninguna de las dos tablas (URG o NO POS).";
        }

    } else {
        $mensaje_error = "ℹ️ No se ha especificado ningún número de factura para la consulta.";
    }
} catch (PDOException $e) {
    // Manejo de errores de conexión/consulta
    $mensaje_error = "❌ Error al consultar las facturas en la base de datos: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Consulta Dual de Facturas URG y NO POS</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body class="consulta">

<h2 class="consulta">Consulta Dual de Facturas: URG y NO POS</h2>

<?php 
// Muestra el mensaje de error si existe
if (!empty($mensaje_error)): ?>
    <div class="mensaje error">
        <?= $mensaje_error ?>
    </div>
<?php endif; 

// Muestra el contenido si se encontró al menos una factura en alguna de las listas
if (!empty($facturas_urg_list) || !empty($facturas_no_pos_list)): ?>

    <div class="mensaje exito">
        ✅ Resultados encontrados para la factura <strong><?= htmlspecialchars($busqueda) ?></strong>.
    </div>

    <!-- Contenedor de doble columna -->
    <div class="facturas-dobles-contenedor">
        
        <!-- COLUMNA 1: Factura URG -->
        <div class="factura-contenedor">
            <h3>Resultados Factura URG</h3>
            <?php if (!empty($facturas_urg_list)): ?>
                <!-- Bucle para mostrar cada registro individualmente -->
                <?php foreach ($facturas_urg_list as $index => $registro): ?>
                    <div style="border-bottom: 2px solid #ccc; padding-bottom: 10px; margin-bottom: 15px;">
                        <h4 style="color: #d70012; margin-top: 0;">Registro #<?= $index + 1 ?></h4>
                        <?php foreach ($registro as $campo => $valor): ?>
                            <div class="campo">
                                <label><?= htmlspecialchars(ucwords(str_replace('_', ' ', $campo))) ?>:</label>
                                <span><?= htmlspecialchars($valor ?? 'N/A') ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="mensaje error" style="margin: 5px; border: none;">No se encontraron datos en la tabla `facturas_urg`.</p>
            <?php endif; ?>
        </div>
        
        <!-- COLUMNA 2: Factura NO POS -->
        <div class="factura-contenedor">
            <h3>Resultados Factura NO POS</h3>
            <?php if (!empty($facturas_no_pos_list)): ?>
                <!-- Bucle para mostrar cada registro individualmente -->
                <?php foreach ($facturas_no_pos_list as $index => $registro): ?>
                    <div style="border-bottom: 2px solid #ccc; padding-bottom: 10px; margin-bottom: 15px;">
                        <h4 style="color: #d70012; margin-top: 0;">Registro #<?= $index + 1 ?></h4>
                        <?php foreach ($registro as $campo => $valor): ?>
                            <div class="campo">
                                <label><?= htmlspecialchars(ucwords(str_replace('_', ' ', $campo))) ?>:</label>
                                <span><?= htmlspecialchars($valor ?? 'N/A') ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="mensaje error" style="margin: 5px; border: none;">No se encontraron datos en la tabla `no_pos`.</p>
            <?php endif; ?>
        </div>

    </div>

<?php endif; ?>

<div class="volver-container">
    <a class="volver" href="dashboard.php">Volver al inicio</a>
</div>

</body>
</html>