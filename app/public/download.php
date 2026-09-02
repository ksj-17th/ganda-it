<?php
require_once '/var/www/src/db.php';
$u=require_login();
$id=(int)($_GET['id'] ?? 0);
$st=db()->prepare('SELECT id,owner_id,original_name,storage_name FROM files WHERE id=? AND owner_id=?');
$st->execute([$id,$u['id']]);
$f=$st->fetch();
if(!$f){
    audit_event('ISAPI','FILE_DOWNLOAD',$u['username'],'file_id='.$id,false);
    http_response_code(404); exit('File not found');
}
$path='/var/www/storage/'.$f['storage_name'];
if(!is_file($path)){
    audit_event('ISAPI','FILE_DOWNLOAD',$u['username'],$f['original_name'],false,['reason'=>'storage_missing']);
    http_response_code(404); exit('Stored object missing');
}
audit_event('ISAPI','FILE_DOWNLOAD',$u['username'],$f['original_name'],true,[
    'file_id'=>$f['id'],
    'storage_name'=>$f['storage_name'],
    'bytes'=>filesize($path)
]);
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="'.basename($f['original_name']).'"');
header('Content-Length: '.filesize($path));
readfile($path);
