<?php
// Local restoration only. New databases/accounts; the current databases are retained.
require dirname(__DIR__).'/vendor/autoload.php';
$root=dirname(__DIR__);
Dotenv\Dotenv::createImmutable($root)->load();
$files=array_slice($argv,1);
if(count($files)!==2)throw new RuntimeException('Usage: .local/bin/php scripts/restore-local-databases.php LARAVEL.sql CLUB_SHOP.sql');
foreach($files as $file)if(!is_file($file)||!is_readable($file))throw new RuntimeException('SQL file is not readable');
$tag=date('Ymd_His');$backup=$root.'/.local/backups/'.$tag;
mkdir($backup,0700,true);
foreach(['.env','.local/router.php','run-local.sh','club-shop/.env','club-shop/app/Config/Database.php'] as $file){
 if(is_file($root.'/'.$file)){ $destination=$backup.'/'.$file; if(!is_dir(dirname($destination)))mkdir(dirname($destination),0700,true);copy($root.'/'.$file,$destination);chmod($destination,0600); }
}
$passwordFile=$root.'/.local/mysql-root-password';
$admin=new PDO('mysql:unix_socket='.$root.'/.local/run/mysql.sock','root',trim(file_get_contents($passwordFile)),[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
$connections=[];
foreach(['skillvation','skillshop'] as $i=>$name){
 $database=$name.'_restore_'.$tag;$username='restore_'.$i.'_'.bin2hex(random_bytes(4));$password=bin2hex(random_bytes(24));
 $sql=file_get_contents($files[$i]);
 // Restrict restoration to data/DDL in the chosen database, not dump-supplied users or files.
 if(preg_match('/^(?:USE\s|CREATE\s+(?:DATABASE|USER|TRIGGER|FUNCTION|PROCEDURE|EVENT)\b|GRANT\s|LOAD\s+DATA\b|SOURCE\s|\\\\!)/mi',$sql))throw new RuntimeException('Unexpected administrative SQL; inspect the dump before importing');
 $admin->exec("CREATE DATABASE `$database` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
 $account=$admin->quote($username)."@'127.0.0.1'";
 $admin->exec('CREATE USER '.$account.' IDENTIFIED BY '.$admin->quote($password));
 $admin->exec("GRANT ALL PRIVILEGES ON `$database`.* TO $account");
 $pdo=new PDO('mysql:host=127.0.0.1;port=33307;dbname='.$database,$username,$password,[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::MYSQL_ATTR_MULTI_STATEMENTS=>true]);
 $pdo->exec($sql);
 $count=(int)$pdo->query('SELECT count(*) FROM information_schema.tables WHERE table_schema=database()')->fetchColumn();
 $connections[$name]=['database'=>$database,'username'=>$username,'password'=>$password,'tables'=>$count,'source'=>basename($files[$i]),'sha256'=>hash_file('sha256',$files[$i])];
 echo "Restored $name: $count tables into $database.\n";
}
file_put_contents($backup.'/restored-connections.json',json_encode($connections,JSON_PRETTY_PRINT));chmod($backup.'/restored-connections.json',0600);
// Staging file: app configuration is changed only after both imports succeed.
file_put_contents($root.'/.local/restored-connections.json',json_encode(['backup'=>$backup,'connections'=>$connections],JSON_PRETTY_PRINT));chmod($root.'/.local/restored-connections.json',0600);
echo "Both imports complete; original databases and app configuration retained.\n";
