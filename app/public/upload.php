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
<div class="card">
<span class="eyebrow">Send files</span>
<h2>Upload File</h2>
<?php if ($msg): ?><p class="error-text"><?=htmlspecialchars($msg)?></p><?php endif; ?>
<form method="post" enctype="multipart/form-data">
    <label class="dropzone">
        <div class="dropzone-icon">&#8593;</div>
        <div class="dropzone-title">클릭하여 파일 선택</div>
        <div class="dropzone-sub">선택한 파일이 내 파일함에 안전하게 업로드됩니다</div>
        <input type="file" name="file" required>
    </label>
    <button type="submit" class="btn-block">Upload</button>
</form>
</div>
<?php include '_footer.php'; ?>
