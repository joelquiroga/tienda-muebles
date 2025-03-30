<?php

session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo "Método no permitido";
    exit;
}

require_once(__DIR__ . '/../backend/conexion.php');
?>