<?php
declare(strict_types=1);

const ORDERS_DIR = __DIR__ . '/../../onlineOrders';
const ORDERS_DB  = ORDERS_DIR . '/onlineOrders.db';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit('Method must be POST'); }

$code = trim($_POST['code'] ?? '');
$customer = trim($_POST['customer'] ?? '');
$product = trim($_POST['product'] ?? '');
$qty = (int)($_POST['qty'] ?? 0);
$price = (float)($_POST['price'] ?? 0);

$errors = [];
if ($code==='')     $errors[]='code';
if ($customer==='') $errors[]='customer';
if ($product==='')  $errors[]='product';
if ($qty<=0)        $errors[]='qty';
if ($price<0)       $errors[]='price';
if ($errors) { http_response_code(400); exit('Missing/invalid: '.implode(', ',$errors)); }

$vat   = round($price*$qty*0.21, 2);
$total = round($price*$qty + $vat, 2);

if (!is_dir(ORDERS_DIR)) mkdir(ORDERS_DIR, 0775, true);

$line = implode('|', [
  $code, str_replace('|','/',$customer), str_replace('|','/',$product),
  $qty, number_format($price,2,'.',''), number_format($vat,2,'.',''), number_format($total,2,'.','')
]) . PHP_EOL;

if (file_put_contents(ORDERS_DB, $line, FILE_APPEND | LOCK_EX) === false) {
  http_response_code(500); exit('Write error');
}

http_response_code(303);
header('Location: ../cli/menu.html');
