<?php
// CodeIgniter's local front controller, mounted consistently under /club-shop.
$root=dirname(__DIR__).'/club-shop';
$path=rawurldecode(parse_url($_SERVER['REQUEST_URI'] ?? '/',PHP_URL_PATH) ?: '/');
if(str_contains($path,"\0")||preg_match('~(?:^|/)(?:\.[^/]*|\.\.)(?:/|$)~',$path)){http_response_code(404);exit('Not found');}
if($path==='/club-shop'){header('Location: /club-shop/',true,302);exit;}
if(!str_starts_with($path,'/club-shop/')){http_response_code(404);exit('Not found');}
$relative=substr($path,strlen('/club-shop'));
$file=realpath($root.$relative);
$allowed=['png','jpg','jpeg','gif','svg','webp','ico','css','js','woff','woff2','ttf','eot','pdf','json','mp4','webm','mp3'];
if($file&&str_starts_with($file,$root.DIRECTORY_SEPARATOR)&&preg_match('~^/(?:assets|uploads)/~',$relative)&&is_file($file)&&in_array(strtolower(pathinfo($file,PATHINFO_EXTENSION)),$allowed,true)){
 $ext=strtolower(pathinfo($file,PATHINFO_EXTENSION));
 $mime=['css'=>'text/css','js'=>'application/javascript','svg'=>'image/svg+xml','woff'=>'font/woff','woff2'=>'font/woff2','json'=>'application/json'];
 header('Content-Type: '.($mime[$ext]??mime_content_type($file)));readfile($file);exit;
}
if(preg_match('~^/(?:app|system|writable|vendor|tests)(?:/|$)~',$relative)||preg_match('~\.(?:php|sql|ini|log|sh)$~i',$relative)){http_response_code(404);exit('Not found');}
$_SERVER['SCRIPT_NAME']='/club-shop/index.php';
$_SERVER['PHP_SELF']='/club-shop/index.php';
$_SERVER['SCRIPT_FILENAME']=$root.'/index.php';
require $root.'/index.php';
