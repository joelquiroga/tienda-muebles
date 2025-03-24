<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

    session_start();
    require_once(__DIR__ . '/conexion.php');

    //Para verificar si el formulario realmente está enviando datos a registro_usuario.php
    //Si ves un array vacío (array(0) {}) o no aparece nada, significa que los datos NO están llegando.
    //var_dump($_POST);
    //exit;


    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            die("Error: Token CSRF inválido.");
        }

        $nombre_completo = mysqli_real_escape_string($conexion, trim($_POST['nombre_completo']));
        $correo = mysqli_real_escape_string($conexion, trim($_POST['correo']));
        $usuario = mysqli_real_escape_string($conexion, trim($_POST['usuario']));
        $contrasena = mysqli_real_escape_string($conexion, trim($_POST['contrasena']));

        // Verificar si los campos están vacíos
        if (empty($nombre_completo) || empty($correo) || empty($usuario) || empty($contrasena)) {
            echo "Error: Token CSRF inválido.";
        }

        // Encriptar contraseña
        $password_hash = password_hash($contrasena, PASSWORD_BCRYPT);

        // Verificar si el usuario o el correo ya existen en la base de datos
        $verificar_usuario = "SELECT id FROM usuarios WHERE usuario = '$usuario' OR correo = '$correo'";
        $resultado = mysqli_query($conexion, $verificar_usuario);

        if (mysqli_num_rows($resultado) > 0) {
            die("Error: El usuario o el correo ya están registrados.");
        }

        // Insertar datos en la base de datos
        $query = "INSERT INTO usuarios (nombre_completo, correo, usuario, contrasena) 
                  VALUES ('$nombre_completo', '$correo', '$usuario', '$password_hash')";

        if (mysqli_query($conexion, $query)) {
            echo "Registro exitoso.";
        } else {
            echo "Error al registrar el usuario: " . mysqli_error($conexion);
        }

        mysqli_close($conexion);
    } else {
        echo "Método no permitido.";
    }
?>
