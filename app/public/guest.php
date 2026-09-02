<?php
require_once '/var/www/src/db.php';
$rows=[];$error='';$token=$_GET['token'] ?? '';
if($token!==''){
    // INTENTIONALLY VULNERABLE: raw string concatenation for SQLi training.
    $sql="SELECT f.id, f.original_name, f.owner_id FROM shares s JOIN files f ON f.id=s.file_id WHERE s.token='".$token."' AND s.active=1";
    try {
        $rows=db()->query($sql)->fetchAll();
        audit_event('WEB','GUEST_ACCESS','anonymous',$token,true,[
            'result_count'=>count($rows)
        ]);
    } catch(Throwable $e) {
        $error=$e->getMessage();
        audit_event('WEB','GUEST_ACCESS','anonymous',$token,false,[
            'db_error'=>$e->getMessage()
        ]);
    }
}
include '_header.php';
?>
<h2>Guest Download</h2>
<p>외부 공유 토큰을 입력하면 공유된 파일의 메타데이터를 조회한다.</p>
<form method="get"><input name="token" value="<?=htmlspecialchars($token)?>" placeholder="share token"><button>Open</button></form>
<?php if($error): ?><pre><?=htmlspecialchars($error)?></pre><?php endif; ?>
<?php if($rows): ?><table><tr><th>ID</th><th>File</th><th>Owner</th></tr><?php foreach($rows as $r): ?><tr><td><?=htmlspecialchars($r['id'])?></td><td><?=htmlspecialchars($r['original_name'])?></td><td><?=htmlspecialchars($r['owner_id'])?></td></tr><?php endforeach; ?></table><?php endif; ?>
<?php include '_footer.php'; ?>
