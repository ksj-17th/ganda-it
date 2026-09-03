<?php
require_once '/var/www/src/db.php';
$u = require_login();
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
        $msg = 'Upload failed';
    } else {
        $originalName = basename($_FILES['file']['name']);
        $storageName = 'f_' . bin2hex(random_bytes(8)) . '.bin';
        $path = '/var/www/storage/' . $storageName;

        if ($originalName !== '' && move_uploaded_file($_FILES['file']['tmp_name'], $path)) {
            try {
                $st = db()->prepare(
                    'INSERT INTO files(owner_id,original_name,storage_name) VALUES(?,?,?)'
                );
                $st->execute([$u['id'], $originalName, $storageName]);
                audit_event('ISAPI', 'FILE_UPLOAD', $u['username'], $originalName, true, [
                    'file_id' => db()->lastInsertId(),
                    'storage_name' => $storageName,
                ]);
                header('Location: /files.php');
                exit;
            } catch (Throwable $e) {
                @unlink($path);
                $msg = 'Upload failed';
                audit_event('ISAPI', 'FILE_UPLOAD', $u['username'], $originalName, false);
            }
        } else {
            $msg = 'Upload failed';
            audit_event('ISAPI', 'FILE_UPLOAD', $u['username'], $originalName, false);
        }
    }
}

include '_header.php';
?>
<h2>Upload File</h2>
<?php if ($msg): ?><p><?=htmlspecialchars($msg)?></p><?php endif; ?>
<form method="post" enctype="multipart/form-data">
    <input type="file" name="file" required>
    <button type="submit">Upload</button>
</form>
<?php include '_footer.php'; ?>
