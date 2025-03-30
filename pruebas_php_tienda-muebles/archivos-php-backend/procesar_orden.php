<?php
require __DIR__ . '/vendor/autoload.php';
use Dotenv\Dotenv;

session_start();
header('Content-Type: application/json');

// 🔡 Logging inicial
file_put_contents('/tmp/debug_env_path.log', "🔡 Inicio del script\n", FILE_APPEND);
file_put_contents('/tmp/debug_env_path.log', "🍪 Cookie: " . json_encode($_COOKIE) . "\n", FILE_APPEND);
file_put_contents('/tmp/debug_env_path.log', "📦 SESSION: " . json_encode($_SESSION) . "\n", FILE_APPEND);

// ✅ Cargar conexión a la base de datos
require_once(__DIR__ . '/conexion.php');
file_put_contents('/tmp/debug_env_path.log', "🔹 Conexión incluida\n", FILE_APPEND);

// ✅ Cargar .env con la clave de Stripe
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();
file_put_contents('/tmp/debug_env_path.log', "✅ .env cargado correctamente\n", FILE_APPEND);

$stripeSecretKey = $_ENV['STRIPE_SECRET_KEY'];
$stripe = new \Stripe\StripeClient($stripeSecretKey);

// ✅ Leer datos JSON del frontend
$input = json_decode(file_get_contents('php://input'), true);
file_put_contents('/tmp/debug_env_path.log', "📦 Datos recibidos: " . json_encode($input) . "\n", FILE_APPEND);

// Validación inicial
if (
    !isset($_SESSION['usuario']) ||
    !isset($input['facturacion']) ||
    !isset($input['items']) || count($input['items']) === 0
) {
    http_response_code(400);
    echo json_encode(['error' => 'Faltan datos para procesar la orden']);
    exit;
}

file_put_contents('/tmp/debug_env_path.log', "🔎 Comprobando sesión... SESSION: " . json_encode($_SESSION) . "\n", FILE_APPEND);

$usuario = $_SESSION['usuario'];

if (is_numeric($usuario)) {
    $stmt = $conexion->prepare("SELECT id, correo FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $usuario);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $usuario = $row;
    } else {
        file_put_contents('/tmp/debug_env_path.log', "❌ Usuario no encontrado en la base de datos\n", FILE_APPEND);
        http_response_code(400);
        echo json_encode(['error' => 'Usuario no encontrado']);
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

file_put_contents('/tmp/debug_env_path.log', "📓 Preparando inserción con:\n", FILE_APPEND);
file_put_contents('/tmp/debug_env_path.log', " - user_id: " . $usuario['id'] . "\n", FILE_APPEND);
file_put_contents('/tmp/debug_env_path.log', " - total: $total\n", FILE_APPEND);
file_put_contents('/tmp/debug_env_path.log', " - descuento: $descuento\n", FILE_APPEND);
file_put_contents('/tmp/debug_env_path.log', " - nombre: " . $facturacion['nombre'] . "\n", FILE_APPEND);
file_put_contents('/tmp/debug_env_path.log', " - direccion: " . $facturacion['direccion'] . "\n", FILE_APPEND);
file_put_contents('/tmp/debug_env_path.log', " - ciudad: " . $facturacion['ciudad'] . "\n", FILE_APPEND);
file_put_contents('/tmp/debug_env_path.log', " - provincia (comunidad_autonoma): " . $facturacion['provincia'] . "\n", FILE_APPEND);
file_put_contents('/tmp/debug_env_path.log', " - codigo_postal: " . $facturacion['codigo_postal'] . "\n", FILE_APPEND);
file_put_contents('/tmp/debug_env_path.log', " - telefono (movil): " . $facturacion['telefono'] . "\n", FILE_APPEND);
file_put_contents('/tmp/debug_env_path.log', " - correo: " . $usuario['correo'] . "\n", FILE_APPEND);
file_put_contents('/tmp/debug_env_path.log', " - comentarios: " . $facturacion['comentarios'] . "\n", FILE_APPEND);

$stmt = $conexion->prepare($query);
if (!$stmt) {
    file_put_contents('/tmp/debug_env_path.log', "❌ Error prepare ordenes: " . $conexion->error . "\n", FILE_APPEND);
    http_response_code(500);
    echo json_encode(['error' => 'Error en prepare']);
    exit;
}
file_put_contents('/tmp/debug_env_path.log', "✅ Prepare orden OK\n", FILE_APPEND);

$usuario_id = $usuario['id'];
$email = $usuario['correo'];
$ok = $stmt->bind_param(
    "iiissssssss",
    $usuario_id,
    $total,
    $descuento,
    $facturacion['nombre'],
    $facturacion['direccion'],
    $facturacion['ciudad'],
    $facturacion['provincia'],
    $facturacion['codigo_postal'],
    $facturacion['telefono'],
    $email,
    $facturacion['comentarios']
);

if (!$ok) {
    file_put_contents('/tmp/debug_env_path.log', "❌ Error en bind_param: " . $stmt->error . "\n", FILE_APPEND);
    http_response_code(500);
    echo json_encode(['error' => 'Error en bind_param']);
    exit;
}
file_put_contents('/tmp/debug_env_path.log', "✅ bind_param OK\n", FILE_APPEND);

if (!$stmt->execute()) {
    file_put_contents('/tmp/debug_env_path.log', "❌ Error execute ordenes: " . $stmt->error . "\n", FILE_APPEND);
    http_response_code(500);
    echo json_encode(['error' => 'Error al guardar la orden']);
    exit;
}
file_put_contents('/tmp/debug_env_path.log', "✅ Orden insertada con ID: {$stmt->insert_id}\n", FILE_APPEND);

$orden_id = $stmt->insert_id;

$productoStmt = $conexion->prepare("INSERT INTO orden_items (orden_id, producto, cantidad, precio_unitario) VALUES (?, ?, ?, ?)");
foreach ($items as $item) {
    $productoStmt->bind_param(
        "isii",
        $orden_id,
        $item['name'],
        $item['quantity'],
        $item['price']
    );
    $productoStmt->execute();
}
file_put_contents('/tmp/debug_env_path.log', "✅ Productos insertados\n", FILE_APPEND);

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
    $session = $stripe->checkout->sessions->create([
        'payment_method_types' => ['card'],
        'line_items' => $line_items,
        'mode' => 'payment',
        'success_url' => 'http://XXX.XXX.XXX.XX/success.html',
        'cancel_url' => 'http://XXX.XXX.XXX.XX/cancel.html'
    ]);

    file_put_contents('/tmp/debug_env_path.log', "✅ Stripe session creada\n", FILE_APPEND);
    echo json_encode(['id' => $session->id]);
} catch (Exception $e) {
    file_put_contents('/tmp/debug_env_path.log', "❌ Error Stripe: " . $e->getMessage() . "\n", FILE_APPEND);
    http_response_code(500);
    echo json_encode(['error' => 'Error al crear la sesión de pago: ' . $e->getMessage()]);
}
