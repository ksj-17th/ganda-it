<?php
require_once '/var/www/src/db.php';
$error='';
if($_SERVER['REQUEST_METHOD']==='POST'){
    $username=$_POST['username'] ?? '';
    $st=db()->prepare('SELECT id,username,password_hash,role FROM users WHERE username=?');
    $st->execute([$username]);
    $u=$st->fetch();
    if($u && password_verify($_POST['password'] ?? '', $u['password_hash'])){
        $sid=bin2hex(random_bytes(16));
        $ins=db()->prepare('INSERT INTO sessions(session_id,user_id,expires_at) VALUES(?,?,DATE_ADD(NOW(),INTERVAL 8 HOUR))');
        $ins->execute([$sid,$u['id']]);
        audit_event('WEB','LOGIN_SUCCESS',$u['username'],'login.php',true,[
            'role'=>$u['role'],
            'session_prefix'=>substr($sid,0,8)
        ]);
        setcookie('mft_session',$sid,['path'=>'/','httponly'=>true,'samesite'=>'Lax']);
        header('Location: /files.php'); exit;
    }
    audit_event('WEB','LOGIN_FAILED',$username,'login.php',false);
    $error='Invalid credentials';
}
include '_header.php';
?>
<div class="card">
<span class="eyebrow">Sign in</span>
<h2>Login</h2>
<?php if($error): ?><p class="error-text"><?=htmlspecialchars($error)?></p><?php endif; ?>
<form method="post">
<div class="field"><label>Username</label><input name="username"></div>
<div class="field"><label>Password</label><input type="password" name="password"></div>
<button class="btn-block">Login</button>
</form>
</div>
<?php include '_footer.php'; ?>
