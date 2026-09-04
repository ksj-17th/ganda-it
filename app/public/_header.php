<?php require_once '/var/www/src/db.php'; $me = current_user(); ?>
<!doctype html>
<html lang="ko"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Ganda-it</title>
<link rel="stylesheet" href="/assets/style.css">
</head><body>
<div class="topnav"><div class="topnav-inner">
<a class="brand" href="/"><span class="brand-mark"> <img src="/images/logo1.png" alt="Ganda-it"> </span><span>Ganda-it</span></a>
<nav class="nav-links">
<a href="/">Home</a><a href="/guest.php">Guest Link</a><?php if($me): ?><a href="/files.php">My Files</a><a href="/upload.php" class="nav-cta">Upload</a><?php if($me['role']==='admin'): ?><a href="/admin/">Admin</a><?php endif; ?><a href="/logout.php">Logout</a><?php else: ?><a href="/login.php" class="nav-cta">Login</a><?php endif; ?>
</nav>
</div></div>
<div class="page">
