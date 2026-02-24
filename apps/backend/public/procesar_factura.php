<?php
session_start();

// 1. Validar que el usuario esté autenticado
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

// 2. Validar que la solicitud sea POST y que el número de factura exista
if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_POST['factura'])) {
    // Si no es POST o falta la factura, redirigir al formulario
    header('Location: index.php'); 
    exit;
}

$numero_factura = trim($_POST['factura']); // Limpiamos el dato

// 3. Verificamos qué botón se presionó (acción)
if (isset($_POST['accion'])) {
    $accion = $_POST['accion'];

    // Aseguramos que el número de factura no esté vacío antes de continuar
    if (empty($numero_factura)) {
        // Podrías redirigir con un mensaje de error si la factura está vacía
        header('Location: index.php?error=nofactura');
        exit;
    }
    
    // 4. Redirección basada en la acción seleccionada
    if ($accion === 'consultar') {
        // Redirigir a consultar_factura.php pasando la factura por URL
        header("Location: consultar_factura.php?factura=" . urlencode($numero_factura));
        exit;
        
    } elseif ($accion === 'modificar') {
        // Redirigir a modificar_factura.php pasando la factura por URL
        header("Location: modificar_factura.php?factura=" . urlencode($numero_factura));
        exit;
        
    } else {
        // Acción no válida, redirigir a inicio
        header('Location: index.php?error=accioninvalida');
        exit;
    }
} else {
    // No se presionó ningún botón 'accion'
    header('Location: index.php?error=noaccion');
    exit;
}
?>