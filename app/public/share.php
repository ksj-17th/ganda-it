<?php
require_once '/var/www/src/db.php';
$u = require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method not allowed');
}

$id = (int)($_POST['id'] ?? 0);
$st = db()->prepare('SELECT id,original_name FROM files WHERE id=? AND owner_id=?');
$st->execute([$id, $u['id']]);
$file = $st->fetch();

if (!$file) {
    audit_event('WEB', 'FILE_SHARE_CREATE', $u['username'], 'file_id='.$id, false);
    http_response_code(404);
    exit('File not found');
}

$st = db()->prepare('SELECT token FROM shares WHERE file_id=? AND active=1 ORDER BY id DESC LIMIT 1');
$st->execute([$id]);
$share = $st->fetch();
$reused = (bool)$share;

if ($share) {
    $token = $share['token'];
} else {
    $token = bin2hex(random_bytes(24));
    $st = db()->prepare('INSERT INTO shares(file_id,token,active) VALUES(?,?,1)');
    $st->execute([$id, $token]);
}

audit_event('WEB', 'FILE_SHARE_CREATE', $u['username'], $file['original_name'], true, [
    'file_id' => $id,
    'token_prefix' => substr($token, 0, 8),
    'reused' => $reused ? 1 : 0,
]);

$sharePath = '/guest.php?token=' . rawurlencode($token);
include '_header.php';
?>
<h2>Share File</h2>
<p><b><?=htmlspecialchars($file['original_name'])?></b></p>
<p>Anyone with this link can download the file without logging in.</p>
<p><a href="<?=htmlspecialchars($sharePath)?>"><?=htmlspecialchars($sharePath)?></a></p>
<p><input id="share-link" value="<?=htmlspecialchars($sharePath)?>" readonly style="width:80%"> <button type="button" onclick="navigator.clipboard.writeText(document.getElementById('share-link').value)">Copy</button></p>
<script>document.getElementById('share-link').value = new URL(document.getElementById('share-link').value, window.location.origin).href;</script>
<p><a href="/files.php">Back to My Files</a></p>
<?php include '_footer.php'; ?>
