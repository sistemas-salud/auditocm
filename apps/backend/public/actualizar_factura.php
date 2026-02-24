<?php
session_start();
require_once __DIR__ . '/../src/auth.php'; // conexión PDO

// 1. Verificar Autenticación
if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}

// 2. Validar que la solicitud sea POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header('Location: dashboard.php'); // Redirigir si no es un envío de formulario
    exit;
}

$pdo = getPDO();
$factura_original = $_POST['factura_original'] ?? null;
$mensaje = '';
$tipo_mensaje = 'error';

// 3. Validar la factura clave
if (empty($factura_original)) {
    $mensaje = "❌ Error crítico: No se encontró la referencia de la factura a modificar.";
    $_SESSION['mensaje'] = ['texto' => $mensaje, 'tipo' => $tipo_mensaje];
    header('Location: dashboard.php');
    exit;
}

// 4. Preparación Dinámica de la Consulta
$campos_a_actualizar = [];
$valores_a_enlazar = [];

// Iterar sobre los datos recibidos, excluyendo el campo de referencia
foreach ($_POST as $campo => $valor) {
    if ($campo !== 'factura_original') {
        // Creamos la parte de 'campo = :campo' para la sentencia SQL
        $campos_a_actualizar[] = "`{$campo}` = ?";
        
        // Agregamos el valor al array de enlace (binding)
        $valores_a_enlazar[] = $valor;
    }
}

// Si no hay campos para actualizar (solo se envió la referencia)
if (empty($campos_a_actualizar)) {
    $mensaje = "ℹ️ No se detectaron cambios para guardar.";
    $tipo_mensaje = 'alerta';
} else {
    // 5. Construcción y Ejecución de la Sentencia UPDATE
    
    // Unimos los campos con comas (Ej: campo1=?, campo2=?)
    $set_clause = implode(', ', $campos_a_actualizar);
    
    // La sentencia final:
    $sql = "UPDATE facturas_urg SET {$set_clause} WHERE TRIM(LOWER(factura)) = TRIM(LOWER(?))";
    
    // Añadimos el valor de la factura original al final de los valores a enlazar (para el WHERE)
    $valores_a_enlazar[] = $factura_original;
    
    try {
        $stmt = $pdo->prepare($sql);
        
        // Ejecutar la sentencia
        $success = $stmt->execute($valores_a_enlazar);
        
        if ($success) {
            $filas_afectadas = $stmt->rowCount();
            
            if ($filas_afectadas > 0) {
                $mensaje = "✅ Factura **{$factura_original}** actualizada exitosamente. ({$filas_afectadas} fila(s) afectada(s)).";
                $tipo_mensaje = 'exito';
            } else {
                $mensaje = "ℹ️ La factura **{$factura_original}** fue enviada, pero no se detectaron cambios en los valores.";
                $tipo_mensaje = 'alerta';
            }
        } else {
            $mensaje = "❌ Fallo al ejecutar la actualización en la base de datos.";
            $tipo_mensaje = 'error';
        }
        
    } catch (PDOException $e) {
        // Error específico de SQL
        $mensaje = "❌ Error SQL: La factura no pudo ser actualizada. Mensaje: " . $e->getMessage();
        $tipo_mensaje = 'error';
    }
}

// 6. Almacenar el mensaje de estado en la sesión
$_SESSION['mensaje'] = ['texto' => $mensaje, 'tipo' => $tipo_mensaje];

// 7. Redirigir al usuario (normalmente al dashboard o a la vista de la factura)
header('Location: dashboard.php'); 
exit;
?>