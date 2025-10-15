<?php
declare(strict_types=1);
const ORDERS_DB = __DIR__.'/../../onlineOrders/onlineOrders.db';

function calc_totals(array $items): array { // items: [ ['name'=>..,'price'=>f,'qty'=>i], ... ]
  $sub=0.0; foreach($items as $p) $sub+=$p['price']*$p['qty'];
  $vat=round($sub*0.21,2); $tot=round($sub+$vat,2);
  return ['subtotal'=>number_format($sub,2,'.',''),'vat'=>number_format($vat,2,'.',''),'total'=>number_format($tot,2,'.','')];
}
function save_order(array $in, array $items): array { // llama a calc_totals()
  $t=calc_totals($items);
  if(!is_dir(dirname(ORDERS_DB))) mkdir(dirname(ORDERS_DB),0775,true);
  $itemsStr=implode(',',array_map(fn($p)=>$p['name'].'x'.$p['qty'],$items));
  $line=implode('|',[$in['code'],$in['customer'],$in['address'],$in['email'],$in['phone'],$itemsStr,$t['subtotal'],$t['vat'],$t['total']]).PHP_EOL;
  $fp=fopen(ORDERS_DB,'ab'); fwrite($fp,$line); fclose($fp);
  return $t;
}

if($_SERVER['REQUEST_METHOD']!=='POST'){ http_response_code(405); exit('Method must be POST'); }
$code=trim($_POST['code']??''); $customer=trim($_POST['customer']??''); $address=trim($_POST['address']??'');
$email=trim($_POST['email']??''); $phone=trim($_POST['phone']??'');
$items=[];
for($i=1;$i<=4;$i++){
  if(!empty($_POST["prod$i"])){ [$name,$price]=explode('|',$_POST["prod$i"]); $qty=(int)($_POST["qty$i"]??0);
    if($qty>0) $items[]=['name'=>$name,'price'=>(float)$price,'qty'=>$qty];
  }
}
if(!$code||!$customer||!$address||!$email||!$phone||empty($items)){ http_response_code(400); exit('Missing data'); }

$tot=save_order(compact('code','customer','address','email','phone'),$items);
header('Content-Type:text/plain; charset=utf-8');
echo "ORDER CREATED SUCCESSFULLY\nCustomer: $customer\nAddress: $address\nTotal (VAT included): € {$tot['total']}\n";
