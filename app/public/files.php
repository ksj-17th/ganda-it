<?php
require_once '/var/www/src/db.php';
$u=require_login();
$st=db()->prepare('SELECT id,original_name,storage_name,created_at FROM files WHERE owner_id=? ORDER BY id DESC');
$st->execute([$u['id']]);
$files=$st->fetchAll();
include '_header.php';
?>
<h2>My Files</h2>
<p>Signed in as <b><?=htmlspecialchars($u['username'])?></b> (<?=htmlspecialchars($u['role'])?>)</p>
<table><tr><th>ID</th><th>Name</th><th>Created</th><th>Action</th></tr>
<?php foreach($files as $f): ?><tr><td><?=$f['id']?></td><td><?=htmlspecialchars($f['original_name'])?></td><td><?=$f['created_at']?></td><td><a href="/download.php?id=<?=$f['id']?>">Download</a> <form method="post" action="/share.php" style="display:inline"><input type="hidden" name="id" value="<?=$f['id']?>"><button type="submit">Share</button></form></td></tr><?php endforeach; ?></table>
<?php include '_footer.php'; ?>
