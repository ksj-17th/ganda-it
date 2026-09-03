<?php
require_once '/var/www/src/db.php';
$rows=[];$error='';$token=$_GET['token'] ?? '';
$me=current_user();
$actor=$me ? $me['username'] : 'anonymous';
$accessEvent=$me ? 'SHARE_ACCESS' : 'GUEST_ACCESS';
if($token!==''){
    // INTENTIONALLY VULNERABLE: raw string concatenation for SQLi training.
    $sql="SELECT f.id, f.original_name, f.owner_id FROM shares s JOIN files f ON f.id=s.file_id WHERE s.token='".$token."' AND s.active=1";
    try {
        $rows=db()->query($sql)->fetchAll();
        audit_event('WEB',$accessEvent,$actor,$token,true,[
            'result_count'=>count($rows)
        ]);
    } catch(Throwable $e) {
        $error=$e->getMessage();
        audit_event('WEB',$accessEvent,$actor,$token,false,[
            'db_error'=>$e->getMessage()
        ]);
    }
}
include '_header.php';
?>
<div class="card">
<span class="eyebrow">Guest access</span>
<h2>Guest Download</h2>
<p class="lead">토큰을 입력하여 공유받을 파일에 접근할 수 있습니다.</p>
<form method="get" class="share-link-row"><input name="token" value="<?=htmlspecialchars($token)?>" placeholder="share token"><button>Open</button></form>
<?php if($error): ?><pre class="error-box"><?=htmlspecialchars($error)?></pre><?php endif; ?>
<?php if($rows): ?><table><tr><th>ID</th><th>File</th><th>Owner</th><th>Action</th></tr><?php foreach($rows as $r): ?><tr><td><?=htmlspecialchars($r['id'])?></td><td><?=htmlspecialchars($r['original_name'])?></td><td><?=htmlspecialchars($r['owner_id'])?></td><td><a class="btn btn-secondary" href="/guest_download.php?token=<?=rawurlencode($token)?>">Download</a></td></tr><?php endforeach; ?></table><?php endif; ?>
</div>
<?php include '_footer.php'; ?>
