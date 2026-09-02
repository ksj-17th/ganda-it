<?php
require_once '/var/www/src/db.php';
$u=require_admin();
$msg='';
if($_SERVER['REQUEST_METHOD']==='POST' && isset($_FILES['package'])){
    $name=basename($_FILES['package']['name']);
    $dest='/var/www/html/uploads/'.$name;
    // INTENTIONALLY VULNERABLE: no extension/MIME/content validation.
    if(move_uploaded_file($_FILES['package']['tmp_name'],$dest)){
        $msg="Imported to /uploads/".$name;
        audit_event('ISAPI','PACKAGE_IMPORT',$u['username'],'/uploads/'.$name,true,[
            'filename'=>$name,
            'size'=>$_FILES['package']['size'] ?? 0
        ]);
    } else {
        $msg='Upload failed';
        audit_event('ISAPI','PACKAGE_IMPORT',$u['username'],$name,false);
    }
}
include '../_header.php';
?>
<h2>Import Web Extension Package</h2>
<div class="warn">Legacy compatibility mode is enabled. Package validation is intentionally disabled in this training build.</div>
<?php if($msg): ?><p><?=htmlspecialchars($msg)?></p><?php endif; ?>
<form method="post" enctype="multipart/form-data"><input type="file" name="package"><button>Import</button></form>
<?php include '../_footer.php'; ?>
