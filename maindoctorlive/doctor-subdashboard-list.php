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
$accesskey = $data->accesskey;
$response = array();
$response1 = array();

if(!empty($accesskey)){
	$accesscheck ="SELECT `userid`,`branch`,`cost_center`,username,role,androidpermissions,androidsubmenu,androiddashboard FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'";
	$stmt = $pdoread->prepare($accesscheck);
	$stmt->bindParam(":accesskey", $accesskey, PDO::PARAM_STR);
	$stmt->execute();
	if($stmt->rowCount() == 1){
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	//$empid = $row['employee_id'];
	$mmsss=explode("," ,$row['androiddashboard']);
 foreach($mmsss as $key) { 
     $percheck ="SELECT `sno`, `menu`, `submenu`, `titlename`, `dashboardtitle`, `ioscontrollers`, `storyboardname`,  `packagename`, `classname`, `classtype`, `substatus`, `d-icons`, `side-icons`, `status`,CONCAT('http://alb-app-1655237078.ap-south-1.elb.amazonaws.com/mobile-api/super-app/appicons/',`d-icons`) AS icon,ioscontrollers FROM `android_permissions_new` WHERE `sno`=:key AND source='Doctor SubDashboard'";
	$stmt2 = $pdoread->prepare($percheck);
	$stmt2->bindParam(":key", $key, PDO::PARAM_STR);
	$stmt2->execute();
	$rows = $stmt2->fetch(PDO::FETCH_ASSOC);
	if($stmt2->rowCount() > 0){
	
$temp=[
"name"=>$rows['menu'],
"displayname"=>$rows['menu'],
"title"=>$rows['titlename'],
"packagename"=>$rows['packagename'],
"ioscontrollers"=>$rows['ioscontrollers'],
"storyboardname"=>$rows['storyboardname'],
"submenustatus"=>$rows['substatus'],
"classname"=>$rows['classname'],
"classtype"=>$rows['classtype'],
"icon"=>$rows['icon'],
"list"=>[]
];

array_push($response1,$temp);

 }else{
http_response_code(503);
$response['error'] = true;
$response['message']="No Data Found";
 }
 http_response_code(200);
$response['error'] = false;
$response['message']="Data found";
$response['mylist']=$response1;
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
$pdoread = null;
?>