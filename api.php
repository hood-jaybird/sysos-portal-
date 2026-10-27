<?php
header('Content-Type: application/json; charset=utf-8');
function out($d,$c=200){http_response_code($c);exit(json_encode($d));}
function sh($c){return trim((string)shell_exec($c.' 2>&1'));}
$a=$_GET['a']??'';
if($a==='metrics'){
 $f=preg_split('/\s+/',sh('free -m | grep Mem'));
 $d=preg_split('/\s+/',sh('df -h / | tail -1'));
 $p=[];
 foreach(array_slice(explode("\n",sh('ps aux --sort=-%cpu | head -n 11')),1) as $l){
  $x=preg_split('/\s+/',$l,11);
  if(count($x)==11)$p[]=['user'=>$x[0],'pid'=>$x[1],'cpu'=>$x[2],'mem'=>$x[3],'cmd'=>$x[10]];}
 $u=sh('uptime -p');
 out(['uptime'=>(stripos($u,'cannot')===false&&$u!=='')?$u:null,'load'=>sys_getloadavg(),'cores'=>max(1,(int)sh('nproc')),
  'ram'=>count($f)>6?['total'=>$f[1],'used'=>$f[2],'free'=>$f[3],'avail'=>$f[6]]:null,
  'disk'=>['fs'=>$d[0],'size'=>$d[1],'used'=>$d[2],'avail'=>$d[3],'pct'=>(int)$d[4],'mount'=>$d[5]],
  'procs'=>$p]);
}
try{$db=new PDO('mysql:host=localhost;dbname=sysos;charset=utf8mb4','sysos','sysos123',
 [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);
}catch(Exception $e){out(['error'=>'Sin conexión a la base de datos'],500);}
if($a!=='list'&&$_SERVER['REQUEST_METHOD']!=='POST')out(['error'=>'Usa POST'],405);
$in=json_decode(file_get_contents('php://input'),true)??[];
$mods=['procesos','memoria','archivos','virtualizacion'];
switch($a){
case 'list':
 $m=$_GET['m']??'';$q='%'.($_GET['q']??'').'%';
 $s=$db->prepare("SELECT * FROM conceptos WHERE (:m='' OR modulo=:m2) AND (titulo LIKE :q OR descripcion LIKE :q2) ORDER BY fav DESC,id DESC");
 $s->execute([':m'=>$m,':m2'=>$m,':q'=>$q,':q2'=>$q]);out($s->fetchAll());
case 'save':
 $t=trim($in['titulo']??'');$d=trim($in['descripcion']??'');$m=$in['modulo']??'';
 if(!$t||!$d||!in_array($m,$mods)||(function_exists('mb_strlen')?mb_strlen($t):strlen($t))>80)out(['error'=>'Completa módulo, título (máx. 80) y descripción'],422);
 if(!empty($in['id']))$db->prepare('UPDATE conceptos SET modulo=?,titulo=?,descripcion=? WHERE id=?')->execute([$m,$t,$d,(int)$in['id']]);
 else $db->prepare('INSERT INTO conceptos(modulo,titulo,descripcion) VALUES(?,?,?)')->execute([$m,$t,$d]);
 out(['ok'=>1]);
case 'fav':$db->prepare('UPDATE conceptos SET fav=1-fav WHERE id=?')->execute([(int)($in['id']??0)]);out(['ok'=>1]);
case 'del':$db->prepare('DELETE FROM conceptos WHERE id=?')->execute([(int)($in['id']??0)]);out(['ok'=>1]);
default:out(['error'=>'Acción inválida'],400);
}
