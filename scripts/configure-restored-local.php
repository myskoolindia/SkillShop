<?php
$root=dirname(__DIR__);$report=json_decode(file_get_contents($root.'/.local/restored-connections.json'),true,512,JSON_THROW_ON_ERROR);
copy(__DIR__.'/templates/club-database.php',$root.'/club-shop/app/Config/Database.php');
$main=$report['connections']['skillvation'];$shop=$report['connections']['skillshop'];
function updateEnv($file,$values){
 $s=file_exists($file)?file_get_contents($file):'';
 foreach($values as $k=>$v){$line=$k.'='.json_encode((string)$v,JSON_UNESCAPED_SLASHES);$pattern='/^'.preg_quote($k,'/').'=.*$/m';if(preg_match($pattern,$s))$s=preg_replace_callback($pattern,fn()=>$line,$s);else $s=rtrim($s)."\n".$line."\n";}
 file_put_contents($file,$s);chmod($file,0600);
}
updateEnv($root.'/.env',[
 'DB_DATABASE'=>$main['database'],'DB_USERNAME'=>$main['username'],'DB_PASSWORD'=>$main['password'],
 'DB_CLUBSHOP_HOST'=>'127.0.0.1','DB_CLUBSHOP_PORT'=>'33307','DB_CLUBSHOP_DATABASE'=>$shop['database'],'DB_CLUBSHOP_USERNAME'=>$shop['username'],'DB_CLUBSHOP_PASSWORD'=>$shop['password'],
 'SHOP_API_BASE_URL'=>'http://127.0.0.1:8082/club-shop/api/','MAIL_MAILER'=>'log',
]);
updateEnv($root.'/club-shop/.env',[
 'CI_ENVIRONMENT'=>'development','app.baseURL'=>'http://localhost:8081/club-shop/','app.forceGlobalSecureRequests'=>'false',
 'database.default.hostname'=>'127.0.0.1','database.default.port'=>'33307','database.default.database'=>$shop['database'],'database.default.username'=>$shop['username'],'database.default.password'=>$shop['password'],'database.default.DBDriver'=>'MySQLi',
 'encryption.key'=>bin2hex(random_bytes(32)),'MAIN_PORTAL_URL'=>'http://localhost:8081',
]);
foreach(['skillvation'=>['courses','users','admins'],'skillshop'=>['products','product_bundles','categories','users']] as $which=>$tables){
 $c=$report['connections'][$which];$pdo=new PDO('mysql:host=127.0.0.1;port=33307;dbname='.$c['database'],$c['username'],$c['password'],[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
 foreach($tables as $table)echo $which.'.'.$table.': '.$pdo->query('SELECT count(*) FROM `'.$table.'`')->fetchColumn()." rows\n";
}
echo "Updated local app connections. Credentials are stored only in ignored local files.\n";
