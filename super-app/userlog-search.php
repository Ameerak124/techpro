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
$data=json_decode(file_get_contents("php://input"));
// $userid =$data->userid;

$searchterm =$data->searchterm;
$response =array();
try {  
$query = $pdoread->prepare("SELECT `sno`, `userid`, `password`, `username`, `emailid`, `mobile`, `role`, `desgination`, `department`, `sp_code`, `shortcutkey_id`, `createdon`, `createdby`, `modifiedon`, `modifiedby`, `status`, `cost_center`, `branch`, `otp`, `accesskey`, `version`, `model`, `udid`, `tokenid`, `lastlogin`, `androidpermissions`, `androidsubmenu`, `androiddashboard`, `stockpoints`, `substockpoint`, `gs_dept` FROM `user_logins` WHERE `userid` like :searchterm AND `status` = 'Active'");
$query->bindValue(':searchterm', "%{$searchterm}%", PDO::PARAM_STR);
$query->execute();
if($query->rowCount()>0){
     $result=$query->fetch(PDO::FETCH_ASSOC);
	http_response_code(200);
 $response['error']=false;
 $response['message']="Data found";
 $response['userdatails']=$result;
}
else{
	http_response_code(503);
$response['error']=true;
$response['message']='No Data Found';
}
}
catch(PDOException $e) {
	http_response_code(503);
	$response['error'] = true;
	$response['message']= "Connection failed: " . $e->getMessage();
}
echo json_encode($response);
$pdoread=null;
?>