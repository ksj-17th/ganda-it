<?php
require_once '/var/www/src/db.php';
$u=require_admin();
audit_event('WEB','ADMIN_ACCESS',$u['username'],'/admin/',true);
include '../_header.php';
?>
<h2>Administration</h2>
<p>Administrator: <?=htmlspecialchars($u['username'])?></p>
<ul><li><a href="/admin/upload.php">Import web extension package</a></li><li><a href="/admin/users.php">Users</a></li></ul>
<?php include '../_footer.php'; ?>
