<?php
session_start();

// Verificar CSRF token
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Token CSRF inválido']);
    exit;
}

require_once(__DIR__ . '/conexion.php');

$correo = $_POST['correo'];
$contrasena = $_POST['contrasena'];

$validar_login = mysqli_query($conexion, "SELECT * FROM usuarios WHERE correo='$correo'");

if (mysqli_num_rows($validar_login) > 0) {
    $usuario = mysqli_fetch_assoc($validar_login);

    // Verificar la contraseña usando password_verify()
    if (password_verify($contrasena, $usuario['contrasena'])) {
        $_SESSION['usuario'] = $usuario['usuario'];
        session_regenerate_id(true); // Prevenir session fixation
        header("location: /perfil_usuario.php");
        exit;
    } else {
        echo '
            <script>
                alert("Contraseña incorrecta");
                window.location = "/index.php";
            </script>
        ';
        exit;
    }
} else {
    echo '
        <script>
            alert("El usuario no existe, por favor verifica los datos");
            window.location = "/index.php";
        </script>
    ';
    exit;
}
?>
