<?php
require __DIR__ . '/vendor/autoload.php';
use Dotenv\Dotenv;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

session_start();
header('Content-Type: application/json');

file_put_contents('/tmp/debug_env_path.log', "\n🟡 Inicio del script\n", FILE_APPEND);
file_put_contents('/tmp/debug_env_path.log', "🍪 Cookie: " . json_encode($_COOKIE) . "\n", FILE_APPEND);
file_put_contents('/tmp/debug_env_path.log', "📦 SESSION: " . json_encode($_SESSION) . "\n", FILE_APPEND);

require_once(__DIR__ . '/conexion.php');
file_put_contents('/tmp/debug_env_path.log', "🔹 Conexión incluida\n", FILE_APPEND);

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();
file_put_contents('/tmp/debug_env_path.log', "✅ .env cargado correctamente\n", FILE_APPEND);
file_put_contents('/tmp/debug_env_path.log', "📧 MAIL_USER (env): " . ($_ENV['MAIL_USER'] ?? 'null') . "\n", FILE_APPEND);
file_put_contents('/tmp/debug_env_path.log', "📧 MAIL_FROM: " . $_ENV['MAIL_FROM'] . "\n", FILE_APPEND);
file_put_contents('/tmp/debug_env_path.log', "📧 MAIL_FROM_NAME: " . $_ENV['MAIL_FROM_NAME'] . "\n", FILE_APPEND);

$input = json_decode(file_get_contents('php://input'), true);
file_put_contents('/tmp/debug_env_path.log', "📦 Datos recibidos: " . json_encode($input) . "\n", FILE_APPEND);

if (!isset($_SESSION['usuario']) || !isset($input['facturacion']) || !isset($input['items']) || count($input['items']) === 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Faltan datos para procesar la orden']);
    exit;
}

file_put_contents('/tmp/debug_env_path.log', "🔎 Comprobando sesión... SESSION: " . json_encode($_SESSION) . "\n", FILE_APPEND);

$usuario = $_SESSION['usuario'];
if (is_string($usuario)) {
    $stmt = $conexion->prepare("SELECT id, correo, nombre_completo FROM usuarios WHERE usuario = ?");
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $usuario = $row;
    } else {
        file_put_contents('/tmp/debug_env_path.log', "❌ Usuario no válido\n", FILE_APPEND);
        echo json_encode(['error' => 'Usuario no válido']);
        exit;
    }
}

file_put_contents('/tmp/debug_env_path.log', "👤 Usuario después de validar: " . json_encode($usuario) . "\n", FILE_APPEND);

$facturacion = $input['facturacion'];
$items = $input['items'];
$discount = isset($input['discount']) ? intval($input['discount']) : 0;

$subtotal = 0;
foreach ($items as $item) {
    $subtotal += intval($item['price']) * intval($item['quantity']);
}
$descuento = ($discount > 0) ? intval(($subtotal * $discount) / 100) : 0;
$total = $subtotal - $descuento;

file_put_contents('/tmp/debug_env_path.log', "➡️ Insertando orden...\n", FILE_APPEND);

$query = "INSERT INTO ordenes 
    (user_id, total, descuento, nombre, direccion, ciudad, comunidad_autonoma, codigo_postal, movil, email, comentarios, fecha)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
$stmt = $conexion->prepare($query);
file_put_contents('/tmp/debug_env_path.log', "🧾 Preparando inserción con:\n", FILE_APPEND);
file_put_contents('/tmp/debug_env_path.log', " - nombre: {$facturacion['nombre']}\n - direccion: {$facturacion['direccion']}\n - ciudad: {$facturacion['ciudad']}\n - provincia: {$facturacion['provincia']}\n - codigo_postal: {$facturacion['codigo_postal']}\n - telefono: {$facturacion['telefono']}\n - correo: {$usuario['correo']}\n - comentarios: {$facturacion['comentarios']}\n", FILE_APPEND);
file_put_contents('/tmp/debug_env_path.log', " - direccion: {$facturacion['direccion']}\n", FILE_APPEND);
file_put_contents('/tmp/debug_env_path.log', " - ciudad: {$facturacion['ciudad']}\n", FILE_APPEND);
file_put_contents('/tmp/debug_env_path.log', " - provincia: {$facturacion['provincia']}\n", FILE_APPEND);
file_put_contents('/tmp/debug_env_path.log', " - codigo_postal: {$facturacion['codigo_postal']}\n", FILE_APPEND);
file_put_contents('/tmp/debug_env_path.log', " - telefono: {$facturacion['telefono']}\n", FILE_APPEND);
file_put_contents('/tmp/debug_env_path.log', " - correo: {$usuario['correo']}\n", FILE_APPEND);
file_put_contents('/tmp/debug_env_path.log', " - comentarios: {$facturacion['comentarios']}\n", FILE_APPEND);
$stmt->bind_param(
    "iiissssssss",
    $usuario['id'], $total, $descuento,
    $facturacion['nombre'], $facturacion['direccion'], $facturacion['ciudad'],
    $facturacion['provincia'], $facturacion['codigo_postal'], $facturacion['telefono'],
    $usuario['correo'], $facturacion['comentarios']
);
$stmt->execute();

$orden_id = $stmt->insert_id;
file_put_contents('/tmp/debug_env_path.log', "✅ Orden insertada con ID: $orden_id\n", FILE_APPEND);

$productoStmt = $conexion->prepare("INSERT INTO orden_items (orden_id, producto, cantidad, precio_unitario) VALUES (?, ?, ?, ?)");
foreach ($items as $item) {
    $productoStmt->bind_param("isii", $orden_id, $item['name'], $item['quantity'], $item['price']);
    $productoStmt->execute();
}
file_put_contents('/tmp/debug_env_path.log', "✅ Productos insertados\n", FILE_APPEND);

$mail = new PHPMailer(true);
$mail->CharSet = 'UTF-8'; // ✅ Para que se vean bien tildes y caracteres especiales
try {
    $mail->isSMTP();
    $mail->Host       = $_ENV['MAIL_HOST'];
    $mail->SMTPAuth   = true;
    $mail->Username   = $_ENV['MAIL_USERNAME'];
    $mail->Password   = $_ENV['MAIL_PASSWORD'];
    $mail->SMTPSecure = 'tls';
    $mail->Port       = $_ENV['MAIL_PORT'];

    $mail->setFrom($_ENV['MAIL_FROM'], $_ENV['MAIL_FROM_NAME']);
    $mail->addAddress($usuario['correo']);
    $mail->addBCC($_ENV['MAIL_FROM']);
    $mail->isHTML(true);
    $mail->Subject = 'Confirmación de tu pedido #' . $orden_id;

    $html = "<h2>Gracias por tu compra, {$facturacion['nombre']}</h2>";
    $html .= "<p>Tu pedido <strong>#{$orden_id}</strong> ha sido recibido correctamente.</p>";
    $html .= "<h3>Datos de envío:</h3><ul>";
    $html .= "<li><strong>Nombre:</strong> {$facturacion['nombre']}</li>";
    $html .= "<li><strong>Dirección:</strong> {$facturacion['direccion']}</li>";
    $html .= "<li><strong>Ciudad:</strong> {$facturacion['ciudad']}</li>";
    $html .= "<li><strong>Provincia:</strong> {$facturacion['provincia']}</li>";
    $html .= "<li><strong>Código Postal:</strong> {$facturacion['codigo_postal']}</li>";
    $html .= "<li><strong>Teléfono:</strong> {$facturacion['telefono']}</li>";
    $html .= "<li><strong>Email:</strong> {$usuario['correo']}</li>";
    $html .= "</ul>";
    $html .= "<h3>Resumen del pedido:</h3><table style='width:100%; border-collapse:collapse;'>";
    $html .= "<thead><tr><th style='border:1px solid #ddd;padding:8px;'>Producto</th><th style='border:1px solid #ddd;padding:8px;'>Imagen</th><th style='border:1px solid #ddd;padding:8px;'>Cantidad</th><th style='border:1px solid #ddd;padding:8px;'>Precio</th></tr></thead><tbody>";
    foreach ($items as $item) {
        $img = htmlspecialchars($item['images'][0] ?? '');
        $html .= "<tr><td style='border:1px solid #ddd;padding:8px;'>{$item['name']}</td><td style='border:1px solid #ddd;padding:8px;'><img src='$img' width='50'></td><td style='border:1px solid #ddd;padding:8px;'>{$item['quantity']}</td><td style='border:1px solid #ddd;padding:8px;'>{$item['price']} €</td></tr>";
    }
    $html .= "</tbody></table><h3>Total: <span style='color:green;'>{$total} €</span></h3>";

    $mail->Body = $html;
    $mail->send();
    file_put_contents('/tmp/debug_env_path.log', "✅ Correo enviado a {$usuario['correo']}\n", FILE_APPEND);
} catch (Exception $e) {
    file_put_contents('/tmp/debug_env_path.log', "❌ Error al enviar email: {$mail->ErrorInfo}\n", FILE_APPEND);
}

$line_items = array_map(function ($product) use ($discount) {
    $unitPrice = intval($product['price']) * 100;
    if ($discount > 0) {
        $unitPrice = intval($unitPrice - ($unitPrice * $discount / 100));
    }
    return [
        'price_data' => [
            'currency' => 'eur',
            'product_data' => ['name' => $product['name']],
            'unit_amount' => $unitPrice,
        ],
        'quantity' => intval($product['quantity'])
    ];
}, $items);

try {
    $session = (new \Stripe\StripeClient($_ENV['STRIPE_SECRET_KEY']))->checkout->sessions->create([
        'payment_method_types' => ['card'],
        'line_items' => $line_items,
        'mode' => 'payment',
        'success_url' => 'http://XXX.XXX.XXX.XX/success.html',
        'cancel_url' => 'http://XXX.XXX.XXX.XX/cancel.html'
    ]);

    file_put_contents('/tmp/debug_env_path.log', "✅ Stripe session creada\n", FILE_APPEND);
    echo json_encode(['id' => $session->id]);
} catch (Exception $e) {
    file_put_contents('/tmp/debug_env_path.log', "❌ Error Stripe: {$e->getMessage()}\n", FILE_APPEND);
    http_response_code(500);
    echo json_encode(['error' => 'Error al crear la sesión de pago: ' . $e->getMessage()]);
}
