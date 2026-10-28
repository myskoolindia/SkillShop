<?php
// Private logical snapshots for an authorised deployment handover. Does not change source data.
require dirname(__DIR__).'/vendor/autoload.php';
$app=require dirname(__DIR__).'/bootstrap/app.php';$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();umask(0077);
$root=dirname(__DIR__);$out=$root.'/.local/devops-export/'.date('Ymd_His');if(!mkdir($out,0700,true))throw new RuntimeException('Cannot create private export directory');
$main=config('database.connections.mysql');
$databases=['skillshop-portal'=>$main,'club-shop'=>['host'=>env('DB_CLUBSHOP_HOST','127.0.0.1'),'port'=>env('DB_CLUBSHOP_PORT',3306),'database'=>env('DB_CLUBSHOP_DATABASE'),'username'=>env('DB_CLUBSHOP_USERNAME'),'password'=>env('DB_CLUBSHOP_PASSWORD')]];
$binary=getenv('MYSQLDUMP_BINARY')?:'/usr/bin/mysqldump';if(!is_executable($binary))throw new RuntimeException('Set MYSQLDUMP_BINARY to the installed MySQL 8 mysqldump client.');
$manifest=[];
foreach($databases as $name=>$c){
    foreach(['host','database','username'] as $field)if(empty($c[$field]))throw new RuntimeException('Missing '.$name.' database configuration');
    $temp=$out.'/.mysql-client.cnf';$cfg="[client]\n";
    foreach(['host'=>'host','port'=>'port','user'=>'username','password'=>'password'] as $key=>$field)$cfg.=$key.'="'.addcslashes((string)($c[$field]??''),"\\\"\n\r")."\"\n";
    file_put_contents($temp,$cfg);chmod($temp,0600);$destination=$out.'/'.$name.'.sql';
    try{$proc=proc_open([$binary,'--defaults-extra-file='.$temp,'--single-transaction','--quick','--hex-blob','--no-tablespaces','--skip-lock-tables','--set-gtid-purged=OFF','--column-statistics=0',$c['database']],[0=>['pipe','r'],1=>['file',$destination,'w'],2=>['pipe','w']],$pipes);if(!is_resource($proc))throw new RuntimeException('Cannot start mysqldump');fclose($pipes[0]);$error=stream_get_contents($pipes[2]);fclose($pipes[2]);if(proc_close($proc)!==0)throw new RuntimeException('Database export failed for '.$name.': '.$error);chmod($destination,0600);$manifest[$name.'.sql']=['sha256'=>hash_file('sha256',$destination),'bytes'=>filesize($destination)];}
    finally{@unlink($temp);}
}
file_put_contents($out.'/manifest.json',json_encode(['createdAt'=>gmdate(DATE_ATOM),'files'=>$manifest],JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES)."\n");
file_put_contents($out.'/README.txt',"PRIVATE DATABASE EXPORT. Contains application/account/order data. Transfer securely; do not publish or commit. Preserve the portal APP_KEY separately when restoring encrypted data. See DEVOPS_HANDOFF.md in the repository root.\n");
echo $out.PHP_EOL;
