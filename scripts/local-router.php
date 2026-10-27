<?php
// Serve the Laravel portal and the real, locally restored CodeIgniter shop.
$root=dirname(__DIR__);
$path=rawurldecode(parse_url($_SERVER['REQUEST_URI'] ?? '/',PHP_URL_PATH) ?: '/');
if(str_contains($path,"\0")||preg_match('~(?:^|/)\.[^/]*(?:/|$)~',$path)){http_response_code(404);exit('Not found');}
if($path==='/club-shop'||str_starts_with($path,'/club-shop/')){require __DIR__.'/local-club-router.php';exit;}
// Kalpana (./kalpana, the Design My Lab planner) is served under /kalpana/ by proxy.
if($path==='/kalpana'){header('Location: /kalpana/',true,301);exit;}
if(str_starts_with($path,'/kalpana/')){require __DIR__.'/local-kalpana-proxy.php';exit;}
$public=realpath($root.'/public');$file=realpath($public.$path);
$allowed=['png','jpg','jpeg','gif','svg','webp','ico','css','js','woff','woff2','ttf','eot','pdf','json','mp4','webm','mp3'];
if($file&&str_starts_with($file,$public.DIRECTORY_SEPARATOR)&&is_file($file)&&in_array(strtolower(pathinfo($file,PATHINFO_EXTENSION)),$allowed,true))return false;
$_SERVER['SCRIPT_NAME']='/index.php';$_SERVER['PHP_SELF']='/index.php';$_SERVER['SCRIPT_FILENAME']=$root.'/public/index.php';
require $root.'/public/index.php';
