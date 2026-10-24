<?php
// Read-only checks against the running local servers and the restored databases.
require dirname(__DIR__).'/vendor/autoload.php';
$root=dirname(__DIR__);Dotenv\Dotenv::createImmutable($root)->load();
function expect($ok,$message){if(!$ok)throw new RuntimeException($message);}
function httpGet($path,$expected=200){
 $curl=curl_init('http://127.0.0.1:8081'.$path);curl_setopt_array($curl,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_CONNECTTIMEOUT=>3,CURLOPT_TIMEOUT=>15]);
 $body=curl_exec($curl);$status=(int)curl_getinfo($curl,CURLINFO_RESPONSE_CODE);curl_close($curl);
 expect($body!==false&&$status===$expected,"HTTP check failed: $path ($status)");return $body;
}
$portal=new PDO('mysql:host=127.0.0.1;port=33307;dbname='.$_ENV['DB_DATABASE'],$_ENV['DB_USERNAME'],$_ENV['DB_PASSWORD'],[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
$shop=new PDO('mysql:host=127.0.0.1;port=33307;dbname='.$_ENV['DB_CLUBSHOP_DATABASE'],$_ENV['DB_CLUBSHOP_USERNAME'],$_ENV['DB_CLUBSHOP_PASSWORD'],[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
foreach(['/','/shop','/courses','/login','/club-shop/'] as $path)httpGet($path);
expect(str_contains(httpGet('/club-shop/'),'loginModal'),'Shop sign-in form missing');
$admin=$shop->query("SELECT route FROM routes WHERE route_key='admin' LIMIT 1")->fetchColumn();
expect(is_string($admin)&&preg_match('~^[a-zA-Z0-9_-]+$~',$admin),'Invalid configured admin path');
httpGet('/club-shop/'.$admin.'/login');
httpGet('/club-shop/assets/css/style-2.6.css');
foreach(['/club-shop/.env','/club-shop/app/Config/Database.php','/.env'] as $path)httpGet($path,404);
$first=json_decode(httpGet('/club-shop/api/products'),true,512,JSON_THROW_ON_ERROR);$total=$first['pagination']['total'];$ids=[];
for($page=1;$page<=$first['pagination']['pages'];$page++){
 $data=$page===1?$first:json_decode(httpGet('/club-shop/api/products?page='.$page),true,512,JSON_THROW_ON_ERROR);
 foreach($data['data'] as $product){expect(!isset($ids[$product['id']]),'Duplicate product across pages');$ids[$product['id']]=true;}
}
expect(count($ids)===$total,'Incomplete product pagination');
$product=$first['data'][0];$query=$shop->prepare('SELECT price,currency FROM products WHERE id=?');$query->execute([$product['id']]);$record=$query->fetch(PDO::FETCH_ASSOC);
expect($record&&(float)$record['price']===(float)$product['base_price']&&$record['currency']===$product['currency'],'API differs from restored database');
$detail=json_decode(httpGet('/club-shop/api/products/'.$product['id']),true,512,JSON_THROW_ON_ERROR);expect((int)$detail['data']['id']===(int)$product['id'],'Product detail differs from list');
httpGet('/club-shop/api/products/99999999',404);
httpGet('/api/shop-products');httpGet('/api/shop-categories');httpGet('/api/product-details/'.$product['id']);
httpGet('/ProductDetails/'.$product['id']);
$categories=json_decode(httpGet('/club-shop/api/categories'),true,512,JSON_THROW_ON_ERROR);expect(!empty($categories['data']),'Categories missing');
echo 'Passed: portal/shop pages, sign-in forms, static assets, private-file blocking, catalog pagination, restored prices, product details, and portal-to-shop API calls.'.PHP_EOL;
echo 'Local courses: '.$portal->query('SELECT count(*) FROM courses')->fetchColumn().'; published shop products: '.$total.'.'.PHP_EOL;
