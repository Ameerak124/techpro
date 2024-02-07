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
$accesskey=$data->accesskey;
$searchterm=$data->searchterm;
$response = array();

if(!empty($accesskey)){
$accesscheck ="SELECT `userid`,`branch`,`cost_center`,username,role,androidpermissions,androidsubmenu,androiddashboard FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'";
	$stmt = $pdoread->prepare($accesscheck);
	$stmt->bindParam(":accesskey", $accesskey, PDO::PARAM_STR);
	$stmt->execute();
	if($stmt->rowCount() > 0){
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	

$query = $pdo_hrms->prepare("SELECT `empid` as userid, `employee_name` as username, concat( `first_name`, `middle_name`, `last_name`) as name, `branch`, `designation` FROM `employee_details` WHERE (empid LIKE :searchterm || employee_name LIKE :searchterm) and status='Active'");
$query->bindValue(':searchterm',"%{$searchterm}%",PDO::PARAM_STR);
$query->execute();
if($query->rowCount()>0){
     $result1=$query->fetchAll(PDO::FETCH_ASSOC);
	http_response_code(200);
 $response['error']=false;
 $response['message']="Data found";
 $response['empdetails']=$result1;
}
else{
	http_response_code(503);
$response['error']=true;
$response['message']='No Data Found';
}	
	
 

}else{
http_response_code(400);
$response['error']= true;
$response['message']="Access denied!";
}
}else{
	
http_response_code(400);
$response['error']= true;
$response['message']="Sorry some details missing";
}

echo json_encode($response);
$pdoread= null;
$pdo_hrms = null;
?>