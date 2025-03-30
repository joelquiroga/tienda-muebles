<?php
require __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;

session_start();
header('Content-Type: application/json');

// Cargar variables de entorno desde .env
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Leer clave desde variable de entorno
$stripeSecretKey = $_ENV['STRIPE_SECRET_KEY'];
$stripe = new \Stripe\StripeClient($stripeSecretKey);

// Leer datos JSON del frontend
$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['items']) || count($input['items']) === 0) {
    http_response_code(400);
    echo json_encode(['error' => 'El carrito está vacío']);
    exit;
}

// Leer descuento (porcentaje) desde el frontend
$discountPercentage = isset($input['discount']) ? intval($input['discount']) : 0;

// Crear los productos para Stripe Checkout con el descuento aplicado
$line_items = array_map(function ($product) use ($discountPercentage) {
    $unitPrice = intval($product['price']) * 100;
    $discountedPrice = $unitPrice;

    if ($discountPercentage > 0) {
        $discountedPrice = intval($unitPrice - ($unitPrice * $discountPercentage / 100));
    }

    return [
        'price_data' => [
            'currency' => 'eur',
            'product_data' => ['name' => $product['name']],
            'unit_amount' => $discountedPrice,
        ],
        'quantity' => intval($product['quantity'])
    ];
}, $input['items']);

try {
    $session = $stripe->checkout->sessions->create([
        'payment_method_types' => ['card'],
        'line_items' => $line_items,
        'mode' => 'payment',
        'success_url' => 'http://217.160.204.31/success.html',
        'cancel_url' => 'http://217.160.204.31/cancel.html'
    ]);

    echo json_encode(['id' => $session->id]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>