<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['usuario'])) {
    echo json_encode(["status" => "error", "message" => "No autenticado"]);
    exit;
}

require_once(__DIR__ . '/conexion.php');

$usuario = $_SESSION['usuario'];

$query = "SELECT nombre_completo, correo, usuario, direccion, ciudad, comunidad_autonoma, codigo_postal, movil FROM usuarios WHERE usuario = ?";
$stmt = mysqli_prepare($conexion, $query);
mysqli_stmt_bind_param($stmt, "s", $usuario);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);

if ($fila = mysqli_fetch_assoc($resultado)) {
    echo json_encode([
        "status" => "success",
        "usuario" => $fila
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Usuario no encontrado"
    ]);
}

mysqli_close($conexion);
?>