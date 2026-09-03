<?php
require_once '/var/www/src/db.php';
$u=require_admin();
audit_event('WEB','ADMIN_ACCESS',$u['username'],'/admin/',true);
include '../_header.php';
?>
<div class="card">
<span class="eyebrow">Administration</span>
<h2>Administration</h2>
<p class="meta-line">Administrator: <b><?=htmlspecialchars($u['username'])?></b></p>
<ul class="feature-list">
<li><a href="/admin/upload.php">Import web extension package</a></li>
<li><a href="/admin/users.php">Users</a></li>
</ul>
</div>
<?php include '../_footer.php'; ?>
