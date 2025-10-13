<?php
/* get_order.php
 * GET: recibe ?code=..., busca y responde en texto plano (sin HTML del servidor).
 */
declare(strict_types=1);

define('ORDERS_DIR', __DIR__ . '/../../onlineOrders');
define('ORDERS_DB',  ORDERS_DIR . '/onlineOrders.db');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    header('Content-Type: text/plain; charset=utf-8');
    echo "ERROR: Method must be GET";
    exit;
}

$code = isset($_GET['code']) ? trim($_GET['code']) : '';
if ($code === '') {
    http_response_code(400);
    header('Content-Type: text/plain; charset=utf-8');
    echo "ERROR: Missing 'code' parameter";
    exit;
}

header('Content-Type: text/plain; charset=utf-8');

if (!is_file(ORDERS_DB)) {
    http_response_code(404);
    echo "Order not found (empty DB).";
    exit;
}

/* Buscar primera coincidencia exacta de código */
$found = null;
$lines = file(ORDERS_DB, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
foreach ($lines as $ln) {
    $parts = explode('|', $ln);
    if (count($parts) !== 7) continue;
    if ($parts[0] === $code) {
        $found = $parts; // [code, customer, product, qty, price, vat, total]
        break;
    }
}

if (!$found) {
    http_response_code(404);
    echo "Order not found: $code";
    exit;
}

[$code,$customer,$product,$qty,$price,$vat,$total] = $found;

echo "ORDER DETAILS\n";
echo " code:   $code\n";
echo " name:   $customer\n";
echo " product:$product\n";
echo " qty:    $qty\n";
echo " price:  $price €\n";
echo " vat21:  $vat €\n";
echo " total:  $total €\n";
