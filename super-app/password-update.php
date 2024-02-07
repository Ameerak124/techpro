<?php
header("Content-Type: application/json; charset=UTF-8");
header("Content-Security-Policy: default-src 'none';");
header("X-Frame-Options: SAMEORIGIN");
header("x-content-type-options: nosniff");
header("Referrer-Policy: same-origin");
header("Strict-Transport-Security: max-age=31536000;");
header("Permissions-Policy: geolocation=(self)");
header_remove('X-Powered-By');
include "pdo-db-new.php"; 

$result1 = "update `user_logins` set `hash_password2`=password_hash(userid, PASSWORD_BCRYPT)  WHERE `androidsubmenu` LIKE 'newcreate'";
$sql1 = $pdo4->prepare($result1);
$sql1->execute();	
$pdo4=null;
?>