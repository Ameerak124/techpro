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
try {
if(!empty($userid)){
	
	
	$stmt=$pdoread->prepare("SELECT `userid`,role FROM `user_logins` WHERE `userid` = :userid and status='Active'");
	$stmt->bindParam(':userid', $userid, PDO::PARAM_STR);
	$stmt -> execute();
	
	if($stmt->rowCount()>0){
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		
	$stmt1=$pdoread->prepare("SELECT `userid`,role FROM `super_logins` WHERE `userid` = :userid and status='Active'");
	$stmt1->bindParam(':userid', $userid, PDO::PARAM_STR);
	$stmt1 -> execute();
	
	if($stmt1->rowCount()>0){
		http_response_code(503);
        $response['error']= true;
        $response['message']= "Already Exists";
	}else{
		
		if($row['role'] == 'AUDIT' || $row['role'] == 'Audit'){
			$androidpermissions = '42,72';
			$androidsubmenu = '';
			$androiddashboard = '72';
			
			
		}else if($row['role'] == 'Doctor'){
			$androidpermissions = '42,22,14,60,61,65,66';
			$androidsubmenu = '';
			$androiddashboard = '22,14,60,61,65,66';
			
			$updstmt=$pdo4->prepare("UPDATE `user_logins` SET `androiddashboard`='28,29,68,69' where `userid`=:userid and status='Active'");
			$updstmt->bindParam(':userid', $userid, PDO::PARAM_STR);
	        $updstmt -> execute();	
		}else if($row['role'] == 'Center Head'){
			$androidpermissions = '42,14,16,25,60,66';
			$androidsubmenu = '';
			$androiddashboard = '14,16,25,60,66';
			
		    $updstmt=$pdo4->prepare("UPDATE `user_logins` SET `androiddashboard`='28,29' where `userid`=:userid and status='Active'");
			$updstmt->bindParam(':userid', $userid, PDO::PARAM_STR);
	        $updstmt -> execute();		
		}else if($row['role'] == 'MEDICAL HEAD'){
			$androidpermissions = '42,14,65,66,60';
			$androidsubmenu = '';
			$androiddashboard = '14,65,66,60';
			
			$updstmt=$pdo4->prepare("UPDATE `user_logins` SET `androiddashboard`='28,29' where `userid`=:userid and status='Active'");
			$updstmt->bindParam(':userid', $userid, PDO::PARAM_STR);
	        $updstmt -> execute();		
		}else if($row['role'] == 'Front Office' || $row['role'] == 'Front Office Executive'){
			$androidpermissions = '42,26';
			$androidsubmenu = '';
			$androiddashboard = '26';
			
			$updstmt=$pdo4->prepare("UPDATE `user_logins` SET `androiddashboard`='10,55' where `userid`=:userid and status='Active'");
			$updstmt->bindParam(':userid', $userid, PDO::PARAM_STR);
	        $updstmt -> execute();		
		}else{
			$androidpermissions = '42,25';
			$androidsubmenu = '';
			$androiddashboard = '25';	
			
		}
		
	$updateid = $pdo4 -> prepare("INSERT INTO `super_logins`( `userid`, `password`, `username`, `emailid`, `mobile`, `role`, `desgination`, `department`, `sp_code`, `shortcutkey_id`, `createdon`, `createdby`, `modifiedon`, `modifiedby`, `status`, `cost_center`, `branch`, `otp`, `accesskey`, `version`, `model`, `udid`, `tokenid`, `mobile_accesskey`, `lastlogin`, `androidpermissions`, `androidsubmenu`, `androiddashboard`)SELECT `userid`,TO_BASE64(`hash_password`)  AS password ,`username`,`emailid`,`mobile`, `role`, `desgination`, `department`, `sp_code`, `shortcutkey_id`, `createdon`, `createdby`, `modifiedon`, `modifiedby`, `status`, `cost_center`, `branch`, `otp`, `accesskey`, `version`, `model`, `udid`, `tokenid`, `mobile_accesskey`, `lastlogin`, :androidpermissions, :androidsubmenu, :androiddashboard FROM `user_logins` WHERE `userid` = :userid");
	$updateid->bindParam(':userid', $userid, PDO::PARAM_STR);
	$updateid->bindParam(':androidpermissions', $androidpermissions, PDO::PARAM_STR);
	$updateid->bindParam(':androidsubmenu', $androidsubmenu, PDO::PARAM_STR);
	$updateid->bindParam(':androiddashboard', $androiddashboard, PDO::PARAM_STR);
	$updateid -> execute();
	if($updateid->rowCount()>0){
	http_response_code(200);
     $response['error']= false;
     $response['message']= "login created";
    
}else{
	http_response_code(503);
     $response['error']= true;
     $response['message']= "Invalid Credentials";
}
	}
	}else{
		http_response_code(503);
     $response['error']= true;
     $response['message']= "please enter proper details";
		
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
$pdo4 = null;
//$himsdemo -null;
?>