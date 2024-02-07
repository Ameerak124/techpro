<?php
header("Content-Type: application/json; charset=UTF-8");
header("Content-Security-Policy: default-src 'none';");
header("X-Frame-Options: SAMEORIGIN");
header("x-content-type-options: nosniff");
header("Referrer-Policy: same-origin");
header("Strict-Transport-Security: max-age=31536000;");
header("Permissions-Policy: geolocation=(self)");
header_remove('X-Powered-By');
//header_remove('Server');
include "pdo-db.php";
$data = json_decode(file_get_contents("php://input"));

$response = array();
try{
$rolecheck ="SELECT `userid`, `password`, `hash_password`, `hash_password2`, `username`  FROM `user_logins` WHERE  `otp`='mahesh' AND `hash_password2`=''";
$stmt1 = $pdoread->prepare($rolecheck);
$stmt1->bindParam(":accesskey", $accesskey, PDO::PARAM_STR);
$accesskey='Active';
$stmt1->execute();
while($results=$stmt1->fetch(PDO::FETCH_ASSOC)){
 $resultt ="UPDATE `user_logins` SET `hash_password2`=:newpwd,`password`=To_base64(:userid), `hash_password`=:userid WHERE `userid`= :userid";
$stmt3 = $pdo4->prepare($resultt);
$stmt3->bindParam(":userid", $results['userid']);
$stmt3->bindParam(":newpwd", $param_newpwd); 
$param_newpwd=password_hash($results['userid'], PASSWORD_BCRYPT);
$stmt3->execute();
}
}catch(PDOEXCEPTION $e){
echo $e;
}
unset($pdo4);
unset($pdoread);
?>