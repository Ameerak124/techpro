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
$empid = $data->empid;
$password = $data->password;
$version = $data->version;
$model = $data->model;
$ucid = $data->ucid;
$tokenid = $data->tokenid;
$response = array();
try
{
if(!empty($empid) && !empty($password) && !empty($version) && !empty($model)  && !empty($ucid) && !empty($tokenid)){
$list = $pdoread->prepare("SELECT  `userid`, `username`,  `hash_password2`,`emailid`, `mobile`, `role`, `desgination`, `department`, `sp_code`, `createdon`, `createdby`, `modifiedon`, `modifiedby`, `status`, `cost_center`, `branch`,if(`userid` = '022077',`accesskey`,TO_BASE64(CONCAT(`userid`-CURRENT_TIMESTAMP))) AS accesskey FROM `user_logins` WHERE `userid` = :empid AND  `status` = 'Active'");
$list->bindParam(":empid", $empid, PDO::PARAM_STR);
$list->execute();
	$check =$list->fetch(PDO::FETCH_ASSOC);
	if($list->rowCount() > 0  && password_verify($password, $check['hash_password2'])){
		
		http_response_code(200);
	    $response['error'] = false;
	    $response['userid'] = $check['userid'];
	    $response['username'] = $check['username'];
	    $response['role'] = $check['role'];
	    $response['desgination'] = $check['desgination'];
	    $response['department'] = $check['department'];
	    $response['mobile'] = $check['mobile'];
	    $response['sp_code'] = $check['sp_code'];
	    $response['branch'] = $check['branch'];
	    $response['accesskey'] = $check['accesskey'];
		$response['message'] ="Welcome ".$check['username'].", this is your dashboard";
	$stmt=$pdo4->prepare("UPDATE `user_logins` SET `version` = :version,`model` = :model,`udid` =:ucid,`mobile_accesskey` =:accesskey,`tokenid`=:tokenid,`lastlogin` = CURRENT_TIMESTAMP WHERE `userid` =:empid  AND `status` = 'Active'");
	$stmt->bindParam(":version", $version, PDO::PARAM_STR);
	$stmt->bindParam(":model", $model, PDO::PARAM_STR);
	$stmt->bindParam(":ucid", $ucid, PDO::PARAM_STR);
	$stmt->bindParam(":accesskey", $check['accesskey'], PDO::PARAM_STR);
	$stmt->bindParam(":tokenid", $tokenid, PDO::PARAM_STR);
	$stmt->bindParam(":empid", $empid, PDO::PARAM_STR);
	
$stmt->execute();

}else{
		http_response_code(400);
	    $response['error'] = true;
		$response['message']="Access denied";
	}

}else{
	    http_response_code(400);
	    $response['error'] = true;
		$response['message']="Sorry! Some details are missing";
					}
}catch(PDOException $e)
		{
        echo $e;
		}
	echo json_encode($response);
$pdo4 = null;
$pdoread = null;
?>