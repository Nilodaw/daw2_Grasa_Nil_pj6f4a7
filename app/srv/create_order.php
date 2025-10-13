<?php
/* create_order.php
 * POST: guarda pedido en fichero y redirige al menú (sin HTML del servidor).
 */
declare(strict_types=1);

define('ORDERS_DIR', __DIR__ . '/../../onlineOrders');
define('ORDERS_DB',  ORDERS_DIR . '/onlineOrders.db');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Content-Type: text/plain; charset=utf-8');
    echo "ERROR: Method must be POST";
    exit;
}

/* 1) Recogida + validación simple */
$code     = isset($_POST['code'])     ? trim($_POST['code'])     : '';
$customer = isset($_POST['customer']) ? trim($_POST['customer']) : '';
$product  = isset($_POST['product'])  ? trim($_POST['product'])  : '';
$qty      = isset($_POST['qty'])      ? (int)$_POST['qty']       : 0;
$price    = isset($_POST['price'])    ? (float)$_POST['price']   : 0.0;

$errors = [];
if ($code === '')     $errors[] = "Missing: code";
if ($customer === '') $errors[] = "Missing: customer";
if ($product === '')  $errors[] = "Missing: product";
if ($qty <= 0)        $errors[] = "Quantity must be > 0";
if ($price < 0)       $errors[] = "Price must be ≥ 0";

if ($errors) {
    http_response_code(400);
    header('Content-Type: text/plain; charset=utf-8');
    echo "CREATE ORDER — VALIDATION ERRORS:\n- " . implode("\n- ", $errors);
    exit;
}

/* 2) Cálculos (IVA 21%) */
$vat   = round($price * $qty * 0.21, 2);
$total = round($price * $qty + $vat, 2);

/* 3) Asegurar carpeta y guardar en fichero (una línea por pedido) */
if (!is_dir(ORDERS_DIR)) {
    mkdir(ORDERS_DIR, 0775, true);
}

$line = implode('|', [
    $code,
    str_replace('|','/',$customer),
    str_replace('|','/',$product),
    (string)$qty,
    number_format($price, 2, '.', ''), // 2 decimales
    number_format($vat,   2, '.', ''),
    number_format($total, 2, '.', ''),
]) . PHP_EOL;

$fileOk = (bool)file_put_contents(ORDERS_DB, $line, FILE_APPEND | LOCK_EX);
if (!$fileOk) {
    http_response_code(500);
    header('Content-Type: text/plain; charset=utf-8');
    echo "ERROR: could not write to DB file.";
    exit;
}

/* 4) Redirección (servidor no genera HTML aquí) */
http_response_code(303); // See Other
header('Location: ../cli/menu.html');
exit;
