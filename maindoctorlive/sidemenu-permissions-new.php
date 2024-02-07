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
$response2 = array();

if(!empty($accesskey)){
	$accesscheck ="SELECT `userid`,`branch`,`cost_center`,username,role,androidpermissions,androidsubmenu FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'";
	$stmt = $pdoread->prepare($accesscheck);
	$stmt->bindParam(":accesskey", $accesskey, PDO::PARAM_STR);
	$stmt->execute();
	if($stmt->rowCount() == 1){
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	//$empid = $row['employee_id'];
	$mmsss=explode("," ,$row['androidpermissions']);
 foreach($mmsss as $key) { 
     $percheck ="SELECT `sno`, `menu`, `submenu`, `titlename`, `dashboardtitle`, `ioscontrollers`, `storyboardname`,  `packagename`, `classname`, `classtype`, `substatus`, `d-icons`, `side-icons`, `status`,CONCAT('https://65.1.244.68/rtc-hrms/appicons/',`side-icons`) AS icon,ioscontrollers FROM `android_permissions` WHERE `sno`=:key";
	$stmt2 = $pdoread->prepare($percheck);
	$stmt2->bindParam(":key", $key, PDO::PARAM_STR);
	$stmt2->execute();
	$rows = $stmt2->fetch(PDO::FETCH_ASSOC);
	if($stmt2->rowCount() == 1){
if($rows['substatus']=='false'){
	
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


} else{
	unset($response2);
 $mmss=explode("," ,$row['androidsubmenu']);

foreach($mmss as $keys) { 

    $percheck1 ="SELECT `submenu` As name,`submenu` As displayname,`submenu` As displaynames,`titlename` As title,storyboardname,`packagename` As packagename, `classname`, `classtype`,ioscontrollers FROM `android_permissions` WHERE `sno`=:key AND `menu`=:menu AND `status`=:status";
	$stmt3 = $pdoread->prepare($percheck1);
	$stmt3->bindParam(":key", $keys, PDO::PARAM_STR);
	$stmt3->bindParam(":menu", $rows['menu'], PDO::PARAM_STR);
	$stmt3->bindParam(":status", $status, PDO::PARAM_STR);
	$status="Active";
	$stmt3->execute();

if($stmt3->rowCount()!=0){
	
$rowss = $stmt3->fetch(PDO::FETCH_ASSOC);

$response2[]=[
"name"=>$rowss['name'],
"displayname"=>$rowss['displayname'],
"title"=>$rowss['title'],
"packagename"=>$rowss['packagename'],
"ioscontrollers"=>$rowss['ioscontrollers'],
"storyboardname"=>$rowss['storyboardname'],
"classname"=>$rowss['classname'],
"classtype"=>$rowss['classtype']

]; 

}

}

 $temp=[
"name"=>$rows['menu'],
"displayname"=>$rows['menu'],
"title"=>"",
"packagename"=>"",
"ioscontrollers"=>"",
"submenustatus"=>$rows['substatus'],
"classname"=>"",
"classtype"=>"",
"icon"=>$rows['icon'],
"list"=>$response2
];


array_push($response1,$temp);

} 
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