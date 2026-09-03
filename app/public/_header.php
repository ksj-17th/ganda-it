<?php require_once '/var/www/src/db.php'; $me = current_user(); ?>
<!doctype html>
<html lang="ko"><head><meta charset="utf-8"><title>Shield Secure Transfer</title>
<style>
body{font-family:system-ui;margin:0;background:#f4f6f8;color:#20242a}nav{background:#17324d;color:#fff;padding:14px 22px}nav a{color:#fff;margin-right:18px;text-decoration:none}.wrap{max-width:980px;margin:28px auto;background:#fff;padding:24px;border-radius:10px;box-shadow:0 2px 10px #0001}table{border-collapse:collapse;width:100%}td,th{border-bottom:1px solid #ddd;padding:10px;text-align:left}input,button{padding:9px;margin:4px 0}code{background:#eef2f5;padding:2px 5px;border-radius:4px}.warn{background:#fff3cd;padding:12px;border-radius:8px}</style></head><body>
<nav><b>Shield Secure Transfer</b> &nbsp; <a href="/">Home</a><a href="/guest.php">Guest Link</a><?php if($me): ?><a href="/files.php">My Files</a><a href="/upload.php">Upload</a><?php if($me['role']==='admin'): ?><a href="/admin/">Admin</a><?php endif; ?><a href="/logout.php">Logout</a><?php else: ?><a href="/login.php">Login</a><?php endif; ?></nav>
<div class="wrap">
