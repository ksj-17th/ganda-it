<?php
require_once '/var/www/src/db.php';

$token = $_GET['token'] ?? '';
$me = current_user();
$actor = $me ? $me['username'] : 'anonymous';
$downloadEvent = $me ? 'FILE_DOWNLOAD' : 'GUEST_FILE_DOWNLOAD';
$st = db()->prepare(
    'SELECT s.id AS share_id,f.id,f.original_name,f.storage_name
     FROM shares s JOIN files f ON f.id=s.file_id
     WHERE s.token=? AND s.active=1
     ORDER BY s.id DESC LIMIT 1'
);
$st->execute([$token]);
$file = $st->fetch();

if (!$file) {
    audit_event('ISAPI', $downloadEvent, $actor, $token, false, [
        'access_type' => $me ? 'shared_link_authenticated' : 'guest',
    ]);
    http_response_code(404);
    exit('Shared file not found');
}

$path = '/var/www/storage/' . $file['storage_name'];
if (!is_file($path)) {
    audit_event('ISAPI', $downloadEvent, $actor, $file['original_name'], false, [
        'reason' => 'storage_missing',
        'access_type' => $me ? 'shared_link_authenticated' : 'guest',
    ]);
    http_response_code(404);
    exit('Stored object missing');
}

audit_event('ISAPI', $downloadEvent, $actor, $file['original_name'], true, [
    'share_id' => $file['share_id'],
    'file_id' => $file['id'],
    'bytes' => filesize($path),
    'access_type' => $me ? 'shared_link_authenticated' : 'guest',
]);

header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . basename($file['original_name']) . '"');
header('Content-Length: ' . filesize($path));
readfile($path);
