<?php
session_start();
require_once __DIR__ . '/../src/auth.php'; // conexión PDO

if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}

$pdo = getPDO();
// ... (Lógica PHP para la consulta de factura) ...
$factura_numero = $_GET['factura'] ?? '';
$factura_datos = null;
$mensaje_error = '';

try {
    if (!empty($factura_numero)) {
        $stmt = $pdo->prepare("
            SELECT * FROM facturas_urg 
            WHERE TRIM(LOWER(factura)) = TRIM(LOWER(?))
            LIMIT 1
        ");
        $stmt->execute([$factura_numero]);
        $factura_datos = $stmt->fetch(PDO::FETCH_ASSOC); 
        if (!$factura_datos) {
            $mensaje_error = "⚠️ No se encontró la factura **" . htmlspecialchars($factura_numero) . "** para modificar.";
        }
    } else {
        $mensaje_error = "ℹ️ No se especificó un número de factura.";
    }
} catch (PDOException $e) {
    $mensaje_error = "❌ Error de consulta a la base de datos: " . $e->getMessage();
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Modificar Factura URG: <?= htmlspecialchars($factura_numero) ?></title>
    <link rel="stylesheet" href="css/styles.css"> 
</head>
<body class="consulta"> 

<div class="detalle-container">

    <h2 class="consulta">Modificar Factura: <?= htmlspecialchars($factura_numero) ?></h2>

    <?php if (!empty($mensaje_error)): ?>
        <div class="mensaje error"><?= $mensaje_error ?></div>
        <div class="volver-container"><a class="volver" href="index.php">Volver</a></div>
    <?php elseif ($factura_datos): ?>
        
        <form method="post" action="actualizar_factura.php"> 
            <input type="hidden" name="factura_original" value="<?= htmlspecialchars($factura_datos['factura']) ?>">
            
            <!-- ✅ Contenedor de la cuadrícula de edición -->
            <div class="campos-edicion-contenedor">
            <?php 
            foreach ($factura_datos as $campo => $valor): 
                if ($campo === 'id' || $campo === 'fecha_creacion'): continue; endif; 
            ?>
                <div class="campo-edicion">
                    <label for="<?= htmlspecialchars($campo) ?>"><?= htmlspecialchars(ucwords(str_replace('_', ' ', $campo))) ?>:</label>
                    <input type="text" 
                        id="<?= htmlspecialchars($campo) ?>" 
                        name="<?= htmlspecialchars($campo) ?>" 
                        value="<?= htmlspecialchars($valor ?? '') ?>" 
                        > 
                </div>
            <?php endforeach; ?>
            </div>
            
            <button type="submit" class="boton-guardar">Guardar Cambios</button>
        </form>
    <?php endif; ?>

    <div class="volver-container">
        <a class="volver" href="dashboard.php">Volver al inicio</a>
    </div>

</div> 
</body>
</html>