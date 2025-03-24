<?php
// Para ver errores
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Este archivo sirve de intermediario entre el frontend (Angular)
// y el backend protegido (registro de usuarios).

// Permitir solo peticiones POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['status' => 'error', 'message' => 'Método no permitido']);
    exit;
}

// Cargar la lógica del registro (que está protegida en backend)
require_once(__DIR__ . '/../backend/registro_usuario.php');
?>