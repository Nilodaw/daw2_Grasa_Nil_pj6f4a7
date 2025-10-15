<?php
declare(strict_types=1);
const ORDERS_DB = __DIR__.'/../../onlineOrders/onlineOrders.db';

function parse_order_line(string $ln): ?array {
  $p=explode('|',trim($ln)); if(count($p)!==9) return null;
  $items=[]; foreach(array_filter(explode(',',$p[5])) as $ch){ [$n,$q]=array_pad(explode('x',$ch,2),2,'0'); $items[]=['name'=>$n,'qty'=>(int)$q]; }
  return ['code'=>$p[0],'customer'=>$p[1],'address'=>$p[2],'email'=>$p[3],'phone'=>$p[4],
          'items'=>$items,'items_str'=>$p[5],'subtotal'=>$p[6],'vat'=>$p[7],'total'=>$p[8]];
}
function find_order_by_code(string $db,string $code):?array{ // llama a parse_order_line()
  if(!is_file($db)) return null; $fp=fopen($db,'rb');
  while(($ln=fgets($fp))!==false){ $o=parse_order_line($ln); if($o && $o['code']===$code){ fclose($fp); return $o; } }
  fclose($fp); return null;
}

if($_SERVER['REQUEST_METHOD']!=='GET'){ http_response_code(405); exit('Method must be GET'); }
$code=trim($_GET['code']??''); if($code===''){ http_response_code(400); exit("Missing 'code'"); }

$found=find_order_by_code(ORDERS_DB,$code);
if(!$found){ http_response_code(404); exit("Order not found: $code"); }

header('Content-Type:text/plain; charset=utf-8');
echo "FLOWER ORDER DETAILS\n";
echo " code: {$found['code']}\n name: {$found['customer']}\n address: {$found['address']}\n email: {$found['email']}\n phone: {$found['phone']}\n";
echo " items: {$found['items_str']}\n subtotal: € {$found['subtotal']}\n vat21:   € {$found['vat']}\n total:   € {$found['total']}\n";
