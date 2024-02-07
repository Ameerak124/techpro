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
$data = json_decode(file_get_contents("php://input"));
$response = array();
$userid = trim($data->userid);
$password = trim($data->password);
$version = trim($data->version);
$model = trim($data->model);
$udid = trim($data->udid);
$tokenid = trim($data->tokenid);
try {
if(!empty($userid) && !empty($password) ){
	$updateid = $pdo4 -> prepare("UPDATE `super_logins` SET `lastlogin` = CURRENT_TIMESTAMP,`version` = :mybrowser,`model`=:model,`udid`=:udid,`tokenid`=:tokenid,`mobile_accesskey` = TO_BASE64(CONCAT(:userid,CURRENT_TIMESTAMP)) WHERE `userid` = :userid AND `password` = TO_BASE64(:password) AND `status`= 'Active' LIMIT 1");
	$updateid->bindParam(':userid', $userid, PDO::PARAM_STR);
	$updateid->bindParam(':password', $password, PDO::PARAM_STR);
	//$updateid->bindParam(':ipaddress', $ipaddress, PDO::PARAM_STR);
	$updateid->bindParam(':mybrowser', $version, PDO::PARAM_STR);
	$updateid->bindParam(':model', $model, PDO::PARAM_STR);
	$updateid->bindParam(':udid', $udid, PDO::PARAM_STR);
	$updateid->bindParam(':tokenid', $tokenid, PDO::PARAM_STR);
	$updateid -> execute();
	$updateid1 = $pdo1 -> prepare("UPDATE `emp_logins` SET `last-login` = CURRENT_TIMESTAMP,`os_version` = :mybrowser,`model`=:model,`udid`=:udid,`tokenid`=:tokenid,`accesstoken` = TO_BASE64(CONCAT(:userid,CURRENT_TIMESTAMP)) WHERE `emp_id` = :userid  AND `Job_Status`= 'Active' LIMIT 1");
	$updateid1->bindParam(':userid', $userid, PDO::PARAM_STR);
	$updateid1->bindParam(':password', $password, PDO::PARAM_STR);
	$updateid1->bindParam(':mybrowser', $version, PDO::PARAM_STR);
	$updateid1->bindParam(':model', $model, PDO::PARAM_STR);
	$updateid1->bindParam(':udid', $udid, PDO::PARAM_STR);
	$updateid1->bindParam(':tokenid', $tokenid, PDO::PARAM_STR);
	$updateid1 -> execute();
 	$updateid2 = $pdo2 -> prepare("UPDATE `pologins` SET lastlogin = CURRENT_TIMESTAMP,`osversion` = :mybrowser,`model`=:model,`udid`=:udid,`tokenid`=:tokenid,`accesskey` = TO_BASE64(CONCAT(:userid,CURRENT_TIMESTAMP)) WHERE `empid` = :userid  AND `status`= 'Active' LIMIT 1");
	$updateid2->bindParam(':userid', $userid, PDO::PARAM_STR);
	$updateid2->bindParam(':mybrowser', $version, PDO::PARAM_STR);
	$updateid2->bindParam(':model', $model, PDO::PARAM_STR);
	$updateid2->bindParam(':udid', $udid, PDO::PARAM_STR);
	$updateid2->bindParam(':tokenid', $tokenid, PDO::PARAM_STR);
	$updateid2 -> execute(); 
	$insert ="UPDATE `referral_logins` SET `version`=:osversion,`model`=:model,`accesskey`=TO_BASE64(CONCAT(:empid,CURRENT_TIMESTAMP)),`loginstatus`='0'  WHERE `Emp_ID`=:empid";
	$stmt1 = $con->prepare($insert);
     $stmt1->bindParam(":empid", $userid, PDO::PARAM_STR);
     $stmt1->bindParam(":osversion", $version, PDO::PARAM_STR);
	$stmt1->bindParam(':model', $model, PDO::PARAM_STR);
	$stmt1->execute();	
     $insert1 ="UPDATE `logins` SET `version`=:osversion,`model`=:model,`accesskey`=TO_BASE64(CONCAT(:empid,CURRENT_TIMESTAMP)),`loginstatus`='0'  WHERE `Emp_ID`=:empid";			
     $stmt2 = $con->prepare($insert1);
     $stmt2->bindParam(":empid", $userid, PDO::PARAM_STR);
     $stmt2->bindParam(":osversion", $version, PDO::PARAM_STR);
	$stmt2->bindParam(':model', $model, PDO::PARAM_STR);
	$stmt2->execute(); 
	$updateid4 = $pdo4 -> prepare("UPDATE `user_logins` SET `lastlogin` = CURRENT_TIMESTAMP,`version` = :mybrowser,`model`=:model,`udid`=:udid,`tokenid`=:tokenid,`mobile_accesskey` = TO_BASE64(CONCAT(:userid,CURRENT_TIMESTAMP)) WHERE `userid` = :userid  AND `status`= 'Active' LIMIT 1");
	$updateid4->bindParam(':userid', $userid, PDO::PARAM_STR);
	$updateid4->bindParam(':password', $password, PDO::PARAM_STR);
	//$updateid->bindParam(':ipaddress', $ipaddress, PDO::PARAM_STR);
	$updateid4->bindParam(':mybrowser', $version, PDO::PARAM_STR);
	$updateid4->bindParam(':model', $model, PDO::PARAM_STR);
	$updateid4->bindParam(':udid', $udid, PDO::PARAM_STR);
	$updateid4->bindParam(':tokenid', $tokenid, PDO::PARAM_STR);
	$updateid4 -> execute(); 
	$query = $pdo_hrms->prepare("UPDATE `employee_details` SET login_date = CURRENT_DATE,login_time=CURRENT_TIME,`os_version` = :mybrowser,`model`=:model,`udid`=:udid,`tokenid`=:tokenid,`accesskey` = TO_BASE64(CONCAT(:userid,CURRENT_TIMESTAMP)) WHERE `empid` = :userid  AND `status`= 'Active' LIMIT 1");
     $query->bindParam(':userid',$userid,PDO::PARAM_STR);
     $query->bindParam(':mybrowser', $version, PDO::PARAM_STR);
	$query->bindParam(':model', $model, PDO::PARAM_STR);
	$query->bindParam(':udid', $udid, PDO::PARAM_STR);
	$query->bindParam(':tokenid', $tokenid, PDO::PARAM_STR);
     $query->execute(); 
	 
    	/* $query1 = $himsdemo->prepare("UPDATE `user_logins` SET lastlogin = CURRENT_TIMESTAMP,`version` = :mybrowser,`model`=:model,`udid`=:udid,`tokenid`=:tokenid,`mobile_accesskey` = TO_BASE64(CONCAT(:userid,CURRENT_TIMESTAMP)) WHERE `userid` = :userid  AND `status`= 'Active' LIMIT 1");
     $query1->bindParam(':userid',$userid,PDO::PARAM_STR);
     $query1->bindParam(':mybrowser', $version, PDO::PARAM_STR);
	$query1->bindParam(':model', $model, PDO::PARAM_STR);
	$query1->bindParam(':udid', $udid, PDO::PARAM_STR);
	$query1->bindParam(':tokenid', $tokenid, PDO::PARAM_STR);
     $query1->execute(); */
	/* $query = $pdo_newhrms->prepare("UPDATE `Employees` SET `version` = :mybrowser WHERE `empid` = :userid  AND `is_active`= '1' LIMIT 1");
     $query->bindParam(':userid',$userid,PDO::PARAM_STR);
     $query->bindParam(':mybrowser', $version, PDO::PARAM_STR);
     $query->execute();  */

     if($updateid -> rowCount() > 0){
	$fetch = $pdoread -> prepare("SELECT `userid`,`username`,`mobile_accesskey`,`branch` AS storeaccess ,cost_center,role FROM `super_logins` WHERE `userid` = :userid AND `password`= TO_BASE64(:password) AND `status`= 'Active' LIMIT 1");
	$fetch->bindParam(':userid', $userid, PDO::PARAM_STR);
	$fetch->bindParam(':password', $password, PDO::PARAM_STR);
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
     $response['accesskey']= $fetchres['mobile_accesskey'];
     $response['role']= $fetchres['role'];
}else{
	http_response_code(503);
     $response['error']= true;
     $response['message']= "Invalid Credentials";
}
}else{
	http_response_code(400);
    $response['error']= true;
    $response['message']= "Sorry, some details are missing";
}
} catch(PDOException $e) {
	http_response_code(503);
	$response['error'] = true;
	$response['message']= "Connection failed".$e;
}
echo json_encode($response);
$pdoread= null;
$pdo1 = null;
$pdo2 = null;
$pdo4 = null;
$con = null;
$pdo_hrms = null;
//$himsdemo -null;
?>