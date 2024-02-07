<?php
header("Content-Type: application/json; charset=UTF-8");
header("Content-Security-Policy: default-src 'none';");
header("X-Frame-Options: SAMEORIGIN");
header("x-content-type-options: nosniff");
header("Referrer-Policy: same-origin");
header("Strict-Transport-Security: max-age=31536000;");
header("Permissions-Policy: geolocation=(self)");
header_remove('X-Powered-By');
include "pdo-db.php";
$data = json_decode(file_get_contents("php://input"));
$response = array();
$userid = trim($data->userid);
$password = trim($data->password);
$version = trim($data->version);
try {
if(!empty($userid) && !empty($password) ){
$fetch1 = $pdoread -> prepare("SELECT `userid`,`username`,`mobile_accesskey`,`branch` AS storeaccess,hash_password2 FROM `user_logins` WHERE `userid` = :userid  AND `status`= 'Active' LIMIT 1");
	$fetch1->bindParam(':userid', $userid, PDO::PARAM_STR);
	//$fetch1->bindParam(':password', $password, PDO::PARAM_STR);
	$fetch1 -> execute();
	$fetchres1 = $fetch1->fetch(PDO::FETCH_ASSOC);
if($fetch1 -> rowCount() > 0  && password_verify($password, $fetchres1['hash_password2'])){


	$updateid = $pdo4 -> prepare("UPDATE `user_logins` SET `lastlogin` = CURRENT_TIMESTAMP,`version` = :mybrowser,`mobile_accesskey` = TO_BASE64(CONCAT(:userid,CURRENT_TIMESTAMP)) WHERE `userid` = :userid  AND `status`= 'Active' LIMIT 1");
	$updateid->bindParam(':userid', $userid, PDO::PARAM_STR);
	//$updateid->bindParam(':password', $password, PDO::PARAM_STR);
	//$updateid->bindParam(':ipaddress', $ipaddress, PDO::PARAM_STR);
	$updateid->bindParam(':mybrowser', $version, PDO::PARAM_STR);
	$updateid -> execute();

	$fetch = $pdoread -> prepare("SELECT `userid`,`username`,`mobile_accesskey`,`branch` AS storeaccess FROM `user_logins` WHERE `userid` = :userid  AND `status`= 'Active' LIMIT 1");
	$fetch->bindParam(':userid', $userid, PDO::PARAM_STR);
	//$fetch->bindParam(':password', $password, PDO::PARAM_STR);
	$fetch -> execute();
	$fetchres = $fetch->fetch(PDO::FETCH_ASSOC);
	http_response_code(200);
    $response['error']= false;
    $response['message']= "login Successfully";
    $response['name']= $fetchres['username'];
    $response['userid']= $fetchres['userid'];
    $response['branch']= $fetchres['storeaccess'];
    $response['accesskey']= $fetchres['mobile_accesskey'];
	
	
}else{
	http_response_code(503);
    $response['error']= true;
    $response['message']= "Invalid Credentials";
}
//}else{
  //  $response['error']= true;
    //$response['message']= "Access denied";
//}
}else{
	http_response_code(400);
    $response['error']= true;
    $response['message']= "Sorry, some details are missing";
}
} catch(PDOException $e) {
	http_response_code(503);
	$response['error'] = true;
	$response['message']= "Connection failed".$e->getMessage();
}
echo json_encode($response);
$pdoread = null;
$pdo4 = null;
?>