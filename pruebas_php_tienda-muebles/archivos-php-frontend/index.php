<?php
    session_start();
    
    if(isset($_SESSION['usuario'])){
        header("location: perfil_usuario.php");
    }
    // asegura formularios con token CSRF
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro e Inicio de Sesión</title>
    <link rel="stylesheet" href="registro.css">
</head>
<body>
    <div class="container">
        <div class="form-container sign-up-container">
            <form action="/api_registro.php" method="POST" id="registerForm">
                <h1>Crear Cuenta</h1>
                <input type="text" placeholder="Nombre Completo" id="registerName" name="nombre_completo" required />
                <input type="email" placeholder="Correo Electrónico" id="registerEmail" name="correo" required />
                <input type="text" placeholder="Usuario" id="registerUser" name="usuario" required />
                <input type="password" placeholder="Contraseña" id="registerPassword" name="contrasena" required />
                 <!-- CSRF token oculto -->
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <button type="submit">Registrarse</button>
            </form>
        </div>
        <div class="form-container sign-in-container">
            <form method="POST" action="/api_login.php" >
                    <h1>Iniciar Sesión</h1>
                    <input type="email" placeholder="Correo Electrónico" name="correo"  required />
                    <input type="password" placeholder="Contraseña" name="contrasena"  required />
                    <!-- CSRF token oculto -->
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <button type="submit">Iniciar Sesión</button>
            </form>
        </div>
        <div class="overlay-container">
            <div class="overlay">
                <div class="overlay-panel overlay-left">
                    <h1>¡Bienvenido de nuevo!</h1>
                    <p>Para mantenerte conectado, por favor inicia sesión con tus datos</p>
                    <button class="ghost" id="signIn">Iniciar Sesión</button>
                </div>
                <div class="overlay-panel overlay-right">
                    <h1>¡Hola, Amigo!</h1>
                    <p>Regístrate con tus datos personales y únete a nuestra comunidad</p>
                    <button class="ghost" id="signUp">Registrarse</button>
                </div>
            </div>
        </div>
    </div>
    <script src="registro.js"></script>
</body>
</html>
