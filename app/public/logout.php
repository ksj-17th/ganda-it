<?php
require_once '/var/www/src/db.php';
$u=current_user();
if($u){
    audit_event('WEB','LOGOUT',$u['username'],'logout.php',true);
}
setcookie('mft_session','',['expires'=>time()-3600,'path'=>'/']);
header('Location: /');
