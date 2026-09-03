<?php require_once '/var/www/src/db.php'; require_admin(); $users=db()->query('SELECT id,username,role,created_at FROM users ORDER BY id')->fetchAll(); include '../_header.php'; ?>
<div class="card">
<span class="eyebrow">Admin · Users</span>
<h2>Users</h2>
<table><tr><th>ID</th><th>User</th><th>Role</th><th>Created</th></tr><?php foreach($users as $u): ?><tr><td><?=$u['id']?></td><td><?=htmlspecialchars($u['username'])?></td><td><?=htmlspecialchars($u['role'])?></td><td><?=$u['created_at']?></td></tr><?php endforeach; ?></table>
</div>
<?php include '../_footer.php'; ?>
