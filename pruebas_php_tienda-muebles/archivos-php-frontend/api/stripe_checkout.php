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

// Crear los productos para Stripe Checkout
$line_items = array_map(function ($product) {
    return [
        'price_data' => [
            'currency' => 'eur',
            'product_data' => [
                'name' => $product['name']
            ],
            'unit_amount' => $product['price'] * 100
        ],
        'quantity' => $product['quantity'] ?? 1
    ];
}, $input['items']);

try {
    $session = $stripe->checkout->sessions->create([
        'payment_method_types' => ['card'],
        'line_items' => $line_items,
        'mode' => 'payment',
        'success_url' => 'https://XXXXXXXXXXXX/success.html',
        'cancel_url' => 'https://XXXXXXXXXXXX/cancel.html'
    ]);

    echo json_encode(['id' => $session->id]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
