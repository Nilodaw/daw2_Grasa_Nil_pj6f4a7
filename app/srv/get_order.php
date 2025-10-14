<?php
declare(strict_types=1);
const ORDERS_DB = __DIR__ . '/../../onlineOrders/onlineOrders.db';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') { http_response_code(405); exit('Method must be GET'); }
$code = trim($_GET['code'] ?? '');
if ($code==='') { http_response_code(400); exit("Missing 'code'"); }
header('Content-Type: text/plain; charset=utf-8');

if (!is_file(ORDERS_DB)) { http_response_code(404); exit('Order not found (empty DB).'); }

$found = null;
foreach (file(ORDERS_DB, FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES) as $ln) {
  $p = explode('|',$ln);
  if (count($p)===7 && $p[0]===$code) { $found=$p; break; }
}
if (!$found) { http_response_code(404); exit("Order not found: $code"); }

echo "FLOWER ORDER DETAILS\n";
echo " code:   {$found[0]}\n";
echo " name:   {$found[1]}\n";
echo " flower: {$found[2]}\n";
echo " qty:    {$found[3]}\n";
echo " price:  {$found[4]} €\n";
echo " vat21:  {$found[5]} €\n";
echo " total:  {$found[6]} €\n";
