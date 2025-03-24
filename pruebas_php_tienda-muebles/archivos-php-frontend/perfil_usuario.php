<?php
session_start();
include '../backend/conexion.php';

// Permitir solicitudes desde cualquier dominio - (Para proyectos con Angular)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario'])) {
    header("location: index.php");
    exit;
}

$usuario = $_SESSION['usuario'];

// Obtener datos del usuario si existen
$query = "SELECT direccion, ciudad, comunidad_autonoma, informacion_opcional, movil, fecha_nacimiento, sexo, codigo_postal FROM usuarios WHERE usuario = ?";
$stmt = $conexion->prepare($query);
$stmt->bind_param("s", $usuario);
$stmt->execute();
$result = $stmt->get_result();
$datos = $result->fetch_assoc();

// Guardar datos si se envía el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $direccion = $_POST['direccion'];
    $ciudad = $_POST['ciudad'];
    $comunidad_autonoma = $_POST['comunidad_autonoma'];
    $informacion_opcional = $_POST['informacion_opcional'];
    $movil = $_POST['movil'];
    $fecha_nacimiento = $_POST['fecha_nacimiento'];
    $sexo = $_POST['sexo'];
    $codigo_postal = $_POST['codigo_postal'];

    $updateQuery = "UPDATE usuarios SET direccion=?, ciudad=?, comunidad_autonoma=?, informacion_opcional=?, movil=?, fecha_nacimiento=?, sexo=?, codigo_postal=? WHERE usuario=?";
    $stmt = $conexion->prepare($updateQuery);
    $stmt->bind_param("sssssssss", $direccion, $ciudad, $comunidad_autonoma, $informacion_opcional, $movil, $fecha_nacimiento, $sexo, $codigo_postal, $usuario);
    if ($stmt->execute()) {
        echo "<script>alert('Datos guardados correctamente'); window.location.href='perfil_usuario.php';</script>";
    } else {
        echo "<script>alert('Error al guardar los datos');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de Usuario</title>
    <style>
body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    background-color:rgba(213, 232, 251, 0);
    height: 100%;
    border-radius:20px;
}
.main {
    width: 60%;
    background: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}
.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}
h1 {
font-family: 'Poppins', sans-serif; /* Fuente más natural y moderna */
font-size: 2rem; /* Tamaño moderado */
font-weight: 600;
color: #2c2c2c; /* Color oscuro suave */
text-align: center;
text-transform: none; /* No mayúsculas forzadas */
letter-spacing: 1px;
margin: 20px 0;
padding: 5px 15px;
display: inline-block;
transition: color 0.3s ease, transform 0.3s ease; /* Transición suave */
cursor: pointer;
position: relative;
}

/* Línea decorativa debajo del título */
h1::after {
    content: "";
    width: 50px;
    height: 3px;
    background-color: #6a8d73; /* Verde natural tipo madera */
    position: absolute;
    bottom: -5px;
    left: 50%;
    transform: translateX(-50%);
    border-radius: 2px;
    transition: width 0.3s ease; /* Efecto al pasar el mouse */
}

/* Efecto al pasar el mouse */
h1:hover {
    color: #6a8d73; /* Cambia el color del texto */
    transform: scale(1.05); /* Efecto de crecimiento sutil */
}

h1:hover::after {
    width: 200px; /* Expande la línea decorativa */
}

.form-group {
    margin-bottom: 10px;
}
label {
    display: block;
    font-weight: bold;
}
input, select, textarea {
    width: 100%;
    padding: 8px;
    margin-top: 5px;
    border: 1px solid #ccc;
    border-radius: 5px;
}
button {
    background-color: #28a745;
    color: white;
    border: none;
    padding: 10px;
    width: 100%;
    cursor: pointer;
    border-radius: 5px;
}
button:hover {
    background-color: #218838;
}
.logout {
    background-color: red;
    padding: 8px 15px;
    color: white;
    text-decoration: none;
    border-radius: 5px;
    text-align: center;
}
@media (max-width: 768px) {
    .main {
        width: 90%;
    }

    h1 {
    font-size: 1.5rem; /* Tamaño moderado */
    }

    h2 {
    font-size: 1.3rem; /* Tamaño moderado */
    }
}
    </style>
</head>
<body>
    <div class="main">
        <div class="header">
            <h1>Bienvenido, <?php echo htmlspecialchars($usuario); ?>!</h1>
            <a class="logout" href="cerrar_sesion.php">Cerrar sesión</a>
        </div>

        <h2>Perfil de Usuario</h2>
        <form method="POST">
            <div class="form-group">
                <label>Dirección:</label>
                <input type="text" name="direccion" value="<?php echo $datos['direccion'] ?? ''; ?>" required>
            </div>
            <div class="form-group">
                <label>Ciudad:</label>
                <input type="text" name="ciudad" value="<?php echo $datos['ciudad'] ?? ''; ?>" required>
            </div>
            <div class="form-group">
                <label>Comunidad Autónoma:</label>
                <input type="text" name="comunidad_autonoma" value="<?php echo $datos['comunidad_autonoma'] ?? ''; ?>" required>
            </div>
            <div class="form-group">
                <label>Código Postal:</label>
                <input type="text" name="codigo_postal" value="<?php echo $datos['codigo_postal'] ?? ''; ?>" required>
            </div>
            <div class="form-group">
                <label>Información Opcional:</label>
                <textarea name="informacion_opcional"><?php echo $datos['informacion_opcional'] ?? ''; ?></textarea>
            </div>
            <div class="form-group">
                <label>Móvil:</label>
                <input type="text" name="movil" value="<?php echo $datos['movil'] ?? ''; ?>" required>
            </div>
            <div class="form-group">
                <label>Fecha de Nacimiento:</label>
                <input type="date" name="fecha_nacimiento" value="<?php echo $datos['fecha_nacimiento'] ?? ''; ?>" required>
            </div>
            <div class="form-group">
                <label>Sexo:</label>
                <select name="sexo" required>
                    <option value="Masculino" <?php if ($datos['sexo'] == 'Masculino') echo 'selected'; ?>>Masculino</option>
                    <option value="Femenino" <?php if ($datos['sexo'] == 'Femenino') echo 'selected'; ?>>Femenino</option>
                    <option value="Otro" <?php if ($datos['sexo'] == 'Otro') echo 'selected'; ?>>Otro</option>
                </select>
            </div>
            
            <button type="submit">Guardar Datos</button>
        </form>
    </div>
</body>
</html>