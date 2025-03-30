<?php
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo "Método no permitido";
    exit;
}

require_once(__DIR__ . '/../backend/get_usuario.php');
?>