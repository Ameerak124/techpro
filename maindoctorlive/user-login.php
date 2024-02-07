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
	
	$list = $pdoread->prepare("SELECT  `userid`, `username`,  `hash_password2`,`emailid`, `mobile`, `role`, `desgination`, `department`, `sp_code`, `createdon`, `createdby`, `modifiedon`, `modifiedby`, `status`, `cost_center`, `branch`,if(`userid` = '022077',`accesskey`,TO_BASE64(CONCAT(`userid`-CURRENT_TIMESTAMP))) AS accesskey FROM `user_logins` WHERE `userid` = :userid AND  `status` = 'Active'");
$list->bindParam(":userid", $userid, PDO::PARAM_STR);
//$list->bindParam(":password", $password, PDO::PARAM_STR);
$list->execute();
	$check =$list->fetch(PDO::FETCH_ASSOC);
	if($list->rowCount() > 0  && password_verify($password, $check['hash_password2'])){

	/* $updateid = $pdo -> prepare("UPDATE `user_logins` SET `lastlogin` = CURRENT_TIMESTAMP,`version` = :mybrowser,`mobile_accesskey` = TO_BASE64(CONCAT(:userid,CURRENT_TIMESTAMP)) WHERE `userid` = :userid  AND `status`= 'Active' LIMIT 1");
	$updateid->bindParam(':userid', $userid, PDO::PARAM_STR);
	$updateid->bindParam(':mybrowser', $version, PDO::PARAM_STR);
	$updateid -> execute(); */
//if($updateid -> rowCount() > 0){
	$fetch = $pdoread -> prepare("SELECT `userid`,`username`,`accesskey`,`branch` AS storeaccess ,cost_center,role,mobile_accesskey FROM `user_logins` WHERE `userid` = :userid  AND `status`= 'Active' LIMIT 1");
	$fetch->bindParam(':userid', $userid, PDO::PARAM_STR);
	//$fetch->bindParam(':password', $password, PDO::PARAM_STR);
	$fetch -> execute();
	$fetchres = $fetch->fetch(PDO::FETCH_ASSOC);
	$stmt=$pdoread->prepare("SELECT `display_name` FROM `branch_master` where  `cost_center`=:costcenter");
	//$fetch->bindParam(':userid', $userid, PDO::PARAM_STR);
	$stmt->bindParam(':costcenter', $fetchres['cost_center'], PDO::PARAM_STR);
	$stmt -> execute();
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	http_response_code(200);
    $response['error']= false;
    $response['message']= "login Successfully";
    $response['name']= $fetchres['username'];
    $response['userid']= $fetchres['userid'];
    $response['branch']= $fetchres['storeaccess'];
    $response['costcentercode']= $fetchres['cost_center'];
    $response['costcenter']= $row['display_name'];
    $response['role']= $row['role'];
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
?>